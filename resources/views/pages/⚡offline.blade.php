<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div class="mx-auto max-w-xl rounded-2xl border border-stone-200 bg-white p-8 text-center shadow-sm dark:border-stone-800 dark:bg-stone-900">
    <svg class="mx-auto h-10 w-10 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M2 8.82a15 15 0 0 1 4.17-2.65"/>
        <path d="M10.66 5c4.01-.36 8.14.9 11.34 3.76"/>
        <path d="M16.85 11.25a10 10 0 0 1 2.22 1.68"/>
        <path d="M5 12.86a10 10 0 0 1 3.26-2.2"/>
        <path d="m2 2 20 20"/>
        <path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>
        <circle cx="12" cy="20" r="1" fill="currentColor" stroke="none"/>
    </svg>
    <h1 class="mt-4 font-display text-xl font-semibold">{{ __('offline.title') }}</h1>
    <p class="mt-2 text-sm text-stone-600 dark:text-stone-400">{{ __('offline.description') }}</p>
    <a href="{{ route('home') }}" class="mt-6 inline-block rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-amber-700 active:scale-[.98]">
        {{ __('offline.retry') }}
    </a>
</div>
