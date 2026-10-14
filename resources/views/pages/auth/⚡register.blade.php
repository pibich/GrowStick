<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], attributes: [
            'name' => __('auth.name'),
            'email' => __('auth.email'),
            'password' => __('auth.password'),
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'locale' => app()->getLocale(),
        ]);

        Auth::login($user);

        session()->regenerate();

        $this->redirect(route('notes.wall'));
    }
};
?>

<div class="mx-auto max-w-md">
    <div class="rounded-2xl border border-t-4 border-stone-200 border-t-amber-400 bg-white p-6 shadow-sm dark:border-stone-800 dark:border-t-amber-500 dark:bg-stone-900">
        <h1 class="font-display text-xl font-semibold">{{ __('auth.register') }}</h1>

        <form wire:submit="register" class="mt-4 space-y-4">
            <div>
                <label for="name" class="mb-1 block text-sm font-medium">{{ __('auth.name') }}</label>
                <input id="name" type="text" wire:model="name" autocomplete="name"
                       class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm text-stone-900 placeholder:text-stone-400 transition focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100 dark:focus:ring-amber-900/50">
                @error('name')
                    <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="mb-1 block text-sm font-medium">{{ __('auth.email') }}</label>
                <input id="email" type="email" wire:model="email" autocomplete="email"
                       class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm text-stone-900 placeholder:text-stone-400 transition focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100 dark:focus:ring-amber-900/50">
                @error('email')
                    <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-medium">{{ __('auth.password') }}</label>
                <input id="password" type="password" wire:model="password" autocomplete="new-password"
                       class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm text-stone-900 placeholder:text-stone-400 transition focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100 dark:focus:ring-amber-900/50">
                @error('password')
                    <p class="mt-1 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="mb-1 block text-sm font-medium">{{ __('auth.confirm_password') }}</label>
                <input id="password_confirmation" type="password" wire:model="password_confirmation" autocomplete="new-password"
                       class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm text-stone-900 placeholder:text-stone-400 transition focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-100 dark:focus:ring-amber-900/50">
            </div>

            <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-70"
                    class="w-full rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-amber-700 active:scale-[.99] disabled:cursor-not-allowed">
                {{ __('auth.register') }}
            </button>
        </form>

        <p class="mt-4 text-sm text-stone-600 dark:text-stone-400">
            {{ __('auth.already_have_account') }}
            <a href="{{ route('login') }}" class="font-medium text-amber-600 hover:underline dark:text-amber-400">{{ __('auth.login') }}</a>
        </p>
    </div>
</div>
