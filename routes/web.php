<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::home')->name('home');

Route::livewire('/login', 'pages::auth.login')->name('login')->middleware('guest');
Route::livewire('/register', 'pages::auth.register')->name('register')->middleware('guest');
Route::livewire('/forgot-password', 'pages::auth.forgot-password')->name('password.request')->middleware('guest');
Route::livewire('/reset-password/{token}', 'pages::auth.reset-password')->name('password.reset')->middleware('guest');

Route::livewire('/offline', 'pages::offline')->name('offline');

Route::livewire('/notes', 'pages::notes.wall')->name('notes.wall')->middleware('auth');
Route::livewire('/notes/{slug}', 'pages::notes.show')->name('notes.show');

Route::post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('home');
})->middleware('auth')->name('logout');

Route::get('/locale/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['id', 'en'], true), 404);

    session(['locale' => $locale]);

    return redirect()->to(request()->header('referer') ?: route('home'));
})->name('locale.switch');
