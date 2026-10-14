<?php

use Illuminate\Support\Facades\Password;
use Livewire\Component;

new class extends Component
{
    public string $email = '';

    public function sendResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'email'],
        ], attributes: [
            'email' => __('auth.email'),
        ]);

        $status = Password::broker()->sendResetLink(['email' => $this->email]);

        if ($status !== Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        session()->flash('status', __($status));
    }
};
?>

<div class="mx-auto max-w-md">
    <div class="rounded-2xl border border-t-4 border-stone-200 border-t-amber-400 bg-white p-6 shadow-sm dark:border-stone-800 dark:border-t-amber-500 dark:bg-stone-900">
        <h1 class="font-display text-xl font-semibold">{{ __('auth.forgot') }}</h1>
        <p class="mt-1 text-sm text-stone-600 dark:text-stone-400">{{ __('auth.forgot_hint') }}</p>

        @if (session('status'))
            <div class="mt-4 rounded-lg bg-emerald-50 px-3 py-2 text-sm text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                {{ session('status') }}
            </div>
        @endif

        <form wire:submit="sendResetLink" class="mt-4 space-y-4">
            <div>
                <label for="email" class="mb-1 block text-sm font-medium">{{ __('auth.email') }}</label>
                <input id="email" type="email" wire:model="email" autocomplete="email"
                       class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm text-stone-900 placeholder:text-stone-400 transition focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100 dark:focus:ring-amber-900/50">
                @error('email')
                    <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-70"
                    class="w-full rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-amber-700 active:scale-[.99] disabled:cursor-not-allowed">
                {{ __('auth.send_reset_link') }}
            </button>
        </form>

        <p class="mt-4 text-sm text-stone-600 dark:text-stone-400">
            <a href="{{ route('login') }}" class="font-medium text-amber-600 hover:underline dark:text-amber-400">{{ __('auth.back_to_login') }}</a>
        </p>
    </div>
</div>
