<?php

use App\Models\Note;
use App\Services\Notes\NoteSnapshotWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

new class extends Component
{
    /**
     * @var list<array<string, mixed>>
     */
    public array $notes = [];

    public bool $creating = false;
    public string $title = '';
    public string $body = '';
    public string $color = 'yellow';
    public string $visibility = 'private';

    public function mount(Request $request): void
    {
        $this->refreshNotes();

        if ($request->boolean('new')) {
            $this->startCreate();
        }
    }

    public function startCreate(): void
    {
        $this->reset(['title', 'body']);
        $this->color = 'yellow';
        $this->visibility = 'private';
        $this->creating = true;
    }

    public function cancelCreate(): void
    {
        $this->creating = false;
    }

    public function create(): void
    {
        $this->validateNote();

        $note = Note::create([
            'user_id' => Auth::id(),
            'title' => $this->title !== '' ? $this->title : null,
            'body' => $this->body,
            'visibility' => $this->visibility,
            'slug' => $this->uniqueSlug(),
            'color' => $this->color,
        ]);

        app(NoteSnapshotWriter::class)->sync($note);

        $this->creating = false;
        $this->reset(['title', 'body']);

        $this->refreshNotes();

        session()->flash('status', __('notes.created'));
    }

    public function delete(int $id): void
    {
        $note = Note::query()
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        app(NoteSnapshotWriter::class)->forget($note->slug);
        $note->delete();

        $this->refreshNotes();

        session()->flash('status', __('notes.deleted'));
    }

    private function refreshNotes(): void
    {
        $this->notes = Note::query()
            ->where('user_id', Auth::id())
            ->latest('updated_at')
            ->get()
            ->map(fn (Note $note): array => [
                'id' => $note->id,
                'slug' => $note->slug,
                'title' => $note->title,
                'excerpt' => Str::limit(preg_replace('/\s+/', ' ', trim($note->body)) ?? '', 140),
                'visibility' => $note->visibility,
                'color' => $note->color,
                'updated_at' => $note->updated_at?->format('Y-m-d H:i'),
            ])
            ->all();
    }

    private function validateNote(): void
    {
        $this->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:20000'],
            'color' => ['required', Rule::in(Note::COLORS)],
            'visibility' => ['required', Rule::in(Note::VISIBILITIES)],
        ], attributes: [
            'title' => __('notes.attr_title'),
            'body' => __('notes.attr_body'),
            'color' => __('notes.attr_color'),
            'visibility' => __('notes.attr_visibility'),
        ]);
    }

    private function uniqueSlug(): string
    {
        do {
            $slug = Str::lower(Str::random(12));
        } while (Note::query()->where('slug', $slug)->exists());

        return $slug;
    }
};
?>

<div class="space-y-6">
    <div class="rounded-2xl border border-stone-200 bg-white p-5 shadow-sm dark:border-stone-800 dark:bg-stone-900 sm:p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight">{{ __('notes.title') }}</h1>
                <p class="mt-1 text-sm text-stone-600 dark:text-stone-400">{{ __('notes.subtitle') }}</p>
            </div>
            <button type="button" wire:click="startCreate"
                    class="rounded-lg bg-amber-600 px-3.5 py-1.5 text-sm font-medium text-white transition hover:bg-amber-700 active:scale-[.98]">
                + {{ __('notes.new') }}
            </button>
        </div>

        @if (session('status'))
            <div class="mt-4 rounded-lg bg-emerald-50 px-4 py-2 text-sm text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                {{ session('status') }}
            </div>
        @endif
    </div>

    @if ($creating)
        <div class="rounded-2xl border p-5 shadow-sm sm:p-6 {{ Note::colorClass($color) }}">
            <h2 class="font-semibold text-stone-900">{{ __('notes.new_title') }}</h2>

            <div class="mt-4 space-y-4">
                <div>
                    <label for="note-title" class="mb-1 block text-sm font-medium text-stone-800">{{ __('notes.field_title') }}</label>
                    <input id="note-title" type="text" wire:model="title"
                           class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm text-stone-900 placeholder:text-stone-400 transition focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-stone-400 dark:focus:ring-amber-400">
                    @error('title') <p class="mt-1 text-xs font-medium text-red-700">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="note-body" class="mb-1 block text-sm font-medium text-stone-800">{{ __('notes.field_body') }}</label>
                    <textarea id="note-body" rows="5" wire:model="body"
                              class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm text-stone-900 placeholder:text-stone-400 transition focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-stone-400 dark:focus:ring-amber-400"></textarea>
                    @error('body') <p class="mt-1 text-xs font-medium text-red-700">{{ $message }}</p> @enderror
                </div>

                <div class="grid gap-4 sm:flex sm:flex-wrap sm:items-end">
                    <div>
                        <span class="mb-1 block text-sm font-medium text-stone-800">{{ __('notes.field_color') }}</span>
                        <div class="flex gap-2.5">
                            @foreach (Note::COLORS as $paletteColor)
                                <button type="button" wire:click="$set('color', '{{ $paletteColor }}')"
                                        title="{{ __('notes.color.'.$paletteColor) }}"
                                        aria-label="{{ __('notes.color.'.$paletteColor) }}"
                                        aria-pressed="{{ $color === $paletteColor ? 'true' : 'false' }}"
                                        class="h-8 w-8 rounded-full border-2 {{ Note::colorClass($paletteColor) }} {{ $color === $paletteColor ? 'outline outline-2 outline-offset-2 outline-stone-900 dark:outline-stone-900' : '' }}"></button>
                            @endforeach
                        </div>
                        @error('color') <p class="mt-1 text-xs font-medium text-red-700">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:min-w-52">
                        <label for="note-visibility" class="mb-1 block text-sm font-medium text-stone-800">{{ __('notes.field_visibility') }}</label>
                        <select id="note-visibility" wire:model="visibility"
                                class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm text-stone-900 transition focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-stone-400 dark:focus:ring-amber-400">
                            <option value="private">{{ __('notes.visibility.private') }}</option>
                            <option value="unlisted">{{ __('notes.visibility.unlisted') }}</option>
                            <option value="public">{{ __('notes.visibility.public') }}</option>
                        </select>
                        <p class="mt-1 text-xs text-stone-600">{{ __('notes.visibility_hint.'.$visibility) }}</p>
                        @error('visibility') <p class="mt-1 text-xs font-medium text-red-700">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-2 sm:ml-auto">
                        <button type="button" wire:click="create" wire:loading.attr="disabled" wire:loading.class="opacity-70"
                                class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-amber-700 active:scale-[.98] disabled:cursor-not-allowed">
                            {{ __('notes.save') }}
                        </button>
                        <button type="button" wire:click="cancelCreate"
                                class="rounded-lg border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-100 active:scale-[.98] dark:border-stone-400 dark:bg-white dark:text-stone-800 dark:hover:bg-stone-100">
                            {{ __('notes.cancel') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($notes === [])
        <div class="rounded-2xl border border-dashed border-stone-300 bg-white/50 px-6 py-12 text-center dark:border-stone-700 dark:bg-stone-900/40">
            <svg class="mx-auto h-8 w-8 text-stone-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M3.5 2.5A1.5 1.5 0 0 1 5 1h10a1.5 1.5 0 0 1 1.5 1.5v11L12 17H5a1.5 1.5 0 0 1-1.5-1.5v-13Z"/>
                <path d="M16.5 12H13a1 1 0 0 0-1 1v3.5h3.5a1 1 0 0 0 1-1V12Z" fill-opacity=".4"/>
            </svg>
            <p class="mt-3 text-sm text-stone-500 dark:text-stone-400">{{ __('notes.empty') }}</p>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3">
            @foreach ($notes as $note)
                <div wire:key="note-{{ $note['id'] }}"
                     class="group relative rounded-xl border p-5 text-stone-800 shadow-sm transition hover:-rotate-1 hover:shadow-md active:rotate-0 {{ Note::colorClass($note['color']) }}">
                    <a href="{{ route('notes.show', $note['slug']) }}" class="block">
                        @if ($note['title'])
                            <h3 class="font-semibold">{{ $note['title'] }}</h3>
                        @endif
                        <p class="mt-1 line-clamp-4 text-sm text-stone-700">{{ $note['excerpt'] }}</p>
                    </a>

                    <div class="mt-3 flex items-center justify-between text-xs text-stone-600">
                        <span class="rounded-full bg-white/70 px-2 py-0.5">{{ __('notes.visibility.'.$note['visibility']) }}</span>
                        <span>{{ $note['updated_at'] }}</span>
                    </div>

                    <button type="button" x-data
                            x-on:click="confirm('{{ __('notes.confirm_delete') }}') && $wire.call('delete', {{ $note['id'] }})"
                            aria-label="{{ __('notes.delete') }}"
                            class="absolute right-2 top-2 rounded-lg border border-red-200 bg-white/90 px-2 py-1 text-xs font-medium text-red-600 transition hover:bg-red-50 sm:opacity-0 sm:group-hover:opacity-100 sm:group-focus-within:opacity-100">
                        {{ __('notes.delete') }}
                    </button>
                </div>
            @endforeach
        </div>
    @endif
</div>
