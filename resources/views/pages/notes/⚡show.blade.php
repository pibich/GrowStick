<?php

use App\Models\Note;
use App\Services\Notes\NoteSnapshotWriter;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

new class extends Component
{
    public string $slug = '';
    public string $title = '';
    public string $body = '';
    public string $color = 'yellow';
    public string $visibility = 'private';
    public ?string $userName = null;
    public ?string $updatedAt = null;
    public bool $isOwner = false;
    public bool $dbAvailable = true;

    public bool $editing = false;
    public string $editTitle = '';
    public string $editBody = '';
    public string $editColor = 'yellow';
    public string $editVisibility = 'private';

    public function mount(string $slug): void
    {
        abort_unless(preg_match('/^[a-z0-9]{6,20}$/', $slug) === 1, 404);

        $this->slug = $slug;

        $snapshot = app(NoteSnapshotWriter::class)->latestSnapshot($slug);

        if ($snapshot !== null) {
            $this->fillFromSnapshot($snapshot);
        } else {
            $note = Note::query()->where('slug', $slug)->first();

            abort_if($note === null, 404);
            abort_unless($note->accessibleBy(Auth::user()), 404);

            $this->fillFromNote($note);
        }

        try {
            $ownerId = Note::query()->where('slug', $slug)->value('user_id');
            $this->isOwner = $ownerId !== null && Auth::id() === $ownerId;
        } catch (QueryException) {
            $this->dbAvailable = false;
            $this->isOwner = false;
        }

        if ($this->isOwner) {
            $this->startEdit();
        }
    }

    public function startEdit(): void
    {
        if (! $this->isOwner) {
            return;
        }

        $this->editTitle = $this->title;
        $this->editBody = $this->body;
        $this->editColor = $this->color;
        $this->editVisibility = $this->visibility;
        $this->editing = true;
    }

    public function cancelEdit(): void
    {
        $this->editing = false;
    }

    public function save(): void
    {
        if (! $this->isOwner) {
            return;
        }

        $this->validate([
            'editTitle' => ['nullable', 'string', 'max:255'],
            'editBody' => ['required', 'string', 'max:20000'],
            'editColor' => ['required', Rule::in(Note::COLORS)],
            'editVisibility' => ['required', Rule::in(Note::VISIBILITIES)],
        ], attributes: [
            'editTitle' => __('notes.attr_title'),
            'editBody' => __('notes.attr_body'),
            'editColor' => __('notes.attr_color'),
            'editVisibility' => __('notes.attr_visibility'),
        ]);

        $note = Note::query()
            ->where('slug', $this->slug)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $note->update([
            'title' => $this->editTitle !== '' ? $this->editTitle : null,
            'body' => $this->editBody,
            'color' => $this->editColor,
            'visibility' => $this->editVisibility,
        ]);

        app(NoteSnapshotWriter::class)->sync($note);

        $this->fillFromNote($note->fresh(['user']));
        $this->editing = false;

        session()->flash('status', __('notes.saved'));
    }

    public function delete(): void
    {
        if (! $this->isOwner) {
            return;
        }

        $note = Note::query()
            ->where('slug', $this->slug)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        app(NoteSnapshotWriter::class)->forget($note->slug);
        $note->delete();

        $this->redirect(route('notes.wall'));
    }

    /**
     * @param  array<string, mixed>  $snapshot
     */
    private function fillFromSnapshot(array $snapshot): void
    {
        $this->title = (string) ($snapshot['title'] ?? '');
        $this->body = (string) ($snapshot['body'] ?? '');
        $this->color = (string) ($snapshot['color'] ?? 'yellow');
        $this->visibility = (string) ($snapshot['visibility'] ?? 'private');
        $this->userName = isset($snapshot['user_name']) ? (string) $snapshot['user_name'] : null;
        $this->updatedAt = isset($snapshot['updated_at'])
            ? \Illuminate\Support\Str::of((string) $snapshot['updated_at'])->substr(0, 10)->toString()
            : null;
    }

    private function fillFromNote(Note $note): void
    {
        $this->title = (string) $note->title;
        $this->body = $note->body;
        $this->color = $note->color;
        $this->visibility = $note->visibility;
        $this->userName = $note->user?->name;
        $this->updatedAt = $note->updated_at?->format('Y-m-d');
    }
};
?>

<div class="mx-auto max-w-3xl space-y-6" data-note-color="{{ $color }}">
    <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm dark:border-stone-800 dark:bg-stone-900 sm:p-6">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight">{{ $title !== '' ? $title : __('notes.untitled') }}</h1>
                <p class="mt-1 text-sm text-stone-500">
                    {{ __('notes.by', ['user' => $userName ?? '—']) }}
                    @if ($updatedAt)
                        · {{ $updatedAt }}
                    @endif
                    · <span class="rounded-full bg-stone-100 px-2 py-0.5 dark:bg-stone-800">{{ __('notes.visibility.'.$visibility) }}</span>
                </p>
            </div>
            <a href="{{ route('home') }}" class="shrink-0 text-sm text-stone-500 transition hover:text-stone-900 dark:hover:text-stone-100">
                {{ __('notes.back_board') }}
            </a>
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
            @if (! $dbAvailable)
                <span class="text-sm text-stone-400">{{ __('notes.db_unavailable') }}</span>
            @elseif ($isOwner)
                <button type="button" x-data="{ copied: false }"
                        x-on:click="navigator.clipboard.writeText(window.location.href).then(() => { copied = true; setTimeout(() => copied = false, 1500); })"
                        class="rounded-lg border border-stone-300 bg-white px-3.5 py-1.5 text-sm font-medium text-stone-700 transition hover:bg-stone-100 active:scale-[.98] dark:border-stone-700 dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-stone-800">
                    <span x-show="!copied">{{ __('notes.copy_link') }}</span>
                    <span x-show="copied" x-cloak class="text-emerald-600">{{ __('notes.link_copied') }}</span>
                </button>

                @if ($visibility === 'private')
                    <a href="{{ route('notes.wall') }}"
                       class="rounded-lg border border-stone-300 bg-white px-3.5 py-1.5 text-sm font-medium text-stone-700 transition hover:bg-stone-100 active:scale-[.98] dark:border-stone-700 dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-stone-800">
                        {{ __('notes.back_wall') }}
                    </a>
                @endif
            @elseif (! Auth::check())
                <a href="{{ route('login') }}" class="text-sm font-medium text-amber-600 hover:underline dark:text-amber-400">{{ __('notes.login_hint') }}</a>
            @endif
        </div>

        @if (session('status'))
            <div class="mt-4 rounded-lg bg-emerald-50 px-4 py-2 text-sm text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                {{ session('status') }}
            </div>
        @endif
    </div>

    <div class="rounded-2xl border p-5 text-stone-800 shadow-sm sm:p-6 {{ Note::colorClass($color) }}">
        @if ($editing)
            <div class="space-y-3">
                <input type="text" wire:model="editTitle" placeholder="{{ __('notes.field_title') }}"
                       class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm font-semibold text-stone-900 placeholder:font-normal placeholder:text-stone-400 transition focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-stone-400 dark:focus:ring-amber-400">
                @error('editTitle') <p class="text-xs font-medium text-red-700">{{ $message }}</p> @enderror

                <textarea rows="10" wire:model="editBody" placeholder="{{ __('notes.field_body') }}"
                          class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm text-stone-900 placeholder:text-stone-400 transition focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-stone-400 dark:focus:ring-amber-400"></textarea>
                @error('editBody') <p class="text-xs font-medium text-red-700">{{ $message }}</p> @enderror

                <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
                    <div class="flex gap-2.5">
                        @foreach (Note::COLORS as $paletteColor)
                            <button type="button" wire:click="$set('editColor', '{{ $paletteColor }}')"
                                    title="{{ __('notes.color.'.$paletteColor) }}"
                                    aria-label="{{ __('notes.color.'.$paletteColor) }}"
                                    aria-pressed="{{ $editColor === $paletteColor ? 'true' : 'false' }}"
                                    class="h-7 w-7 rounded-full border-2 {{ Note::colorClass($paletteColor) }} {{ $editColor === $paletteColor ? 'outline outline-2 outline-offset-2 outline-stone-900 dark:outline-stone-900' : '' }}"></button>
                        @endforeach
                    </div>

                    <div class="sm:min-w-52">
                        <select wire:model="editVisibility"
                                class="w-full rounded-lg border border-stone-300 bg-white px-3 py-1.5 text-sm text-stone-900 transition focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-stone-400 dark:focus:ring-amber-400">
                            <option value="private">{{ __('notes.visibility.private') }}</option>
                            <option value="unlisted">{{ __('notes.visibility.unlisted') }}</option>
                            <option value="public">{{ __('notes.visibility.public') }}</option>
                        </select>
                        <p class="mt-1 text-xs text-stone-600">{{ __('notes.visibility_hint.'.$editVisibility) }}</p>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row sm:ml-auto">
                        <button type="button" wire:click="save" wire:loading.attr="disabled" wire:loading.class="opacity-70"
                                class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-amber-700 active:scale-[.98] disabled:cursor-not-allowed">
                            {{ __('notes.save') }}
                        </button>
                        <button type="button" wire:click="cancelEdit"
                                class="rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-100 active:scale-[.98] dark:border-stone-400 dark:bg-white dark:text-stone-800 dark:hover:bg-stone-100">
                            {{ __('notes.cancel') }}
                        </button>
                        <button type="button" x-data
                                x-on:click="confirm('{{ __('notes.confirm_delete') }}') && $wire.call('delete')"
                                class="rounded-lg border border-red-200 bg-white px-4 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50 active:scale-[.98] dark:border-red-300 dark:bg-white dark:hover:bg-red-50">
                            {{ __('notes.delete') }}
                        </button>
                    </div>
                </div>
            </div>
        @else
            <p class="whitespace-pre-wrap text-sm leading-6 text-stone-700">{{ $body }}</p>
        @endif
    </div>

    @if (! $editing && $isOwner)
        <div class="text-center">
            <button type="button" wire:click="startEdit"
                    class="rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-100 active:scale-[.98] dark:border-stone-700 dark:bg-stone-900 dark:text-stone-200 dark:hover:bg-stone-800">
                {{ __('notes.edit') }}
            </button>
        </div>
    @endif
</div>
