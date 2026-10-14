<?php

use App\Models\Note;
use App\Services\Notes\NoteSnapshotWriter;
use Illuminate\Support\Str;
use Livewire\Component;

new class extends Component
{
    /**
     * @var list<array<string, mixed>>
     */
    public array $entries = [];

    public function mount(): void
    {
        $this->entries = app(NoteSnapshotWriter::class)->readManifest()['entries'];
    }
};
?>

<div>
    <section>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="font-display text-2xl font-semibold tracking-tight">{{ __('home.board_title') }}</h1>

            <a href="{{ auth()->check() ? route('notes.wall', ['new' => 1]) : route('register') }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-amber-600 px-3.5 py-1.5 text-sm font-medium text-white transition hover:bg-amber-700 active:scale-[.98]">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 3a1 1 0 0 1 1 1v5h5a1 1 0 1 1 0 2h-5v5a1 1 0 1 1-2 0v-5H4a1 1 0 1 1 0-2h5V4a1 1 0 0 1 1-1Z"/>
                </svg>
                {{ __('home.create') }}
            </a>
        </div>

        @if ($entries === [])
            <div class="mt-6 rounded-2xl border border-dashed border-stone-300 bg-white/50 px-6 py-12 text-center dark:border-stone-700 dark:bg-stone-900/40">
                <svg class="mx-auto h-8 w-8 text-stone-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M3.5 2.5A1.5 1.5 0 0 1 5 1h10a1.5 1.5 0 0 1 1.5 1.5v11L12 17H5a1.5 1.5 0 0 1-1.5-1.5v-13Z"/>
                    <path d="M16.5 12H13a1 1 0 0 0-1 1v3.5h3.5a1 1 0 0 0 1-1V12Z" fill-opacity=".4"/>
                </svg>
                <p class="mt-3 text-sm text-stone-500 dark:text-stone-400">{{ __('home.board_empty') }}</p>
            </div>
        @else
            <div class="mt-6 grid gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3">
                @foreach ($entries as $entry)
                    <a href="{{ route('notes.show', $entry['slug']) }}"
                       wire:key="entry-{{ $entry['slug'] }}"
                       class="block rounded-xl border p-5 text-stone-800 shadow-sm transition hover:-rotate-1 hover:shadow-md active:rotate-0 dark:text-stone-200 {{ Note::colorClass((string) ($entry['color'] ?? 'yellow')) }}">
                        @if ($entry['title'] ?? null)
                            <h3 class="font-semibold">{{ $entry['title'] }}</h3>
                        @endif
                        <p class="mt-1 line-clamp-3 text-sm text-stone-700">{{ $entry['excerpt'] ?? '' }}</p>
                        <p class="mt-3 text-xs text-stone-600">
                            {{ __('home.board_by', ['user' => $entry['user_name'] ?? '—']) }}
                            · {{ Str::of((string) ($entry['updated_at'] ?? ''))->substr(0, 10) }}
                        </p>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
</div>
