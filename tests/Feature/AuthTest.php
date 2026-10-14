<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_can_be_rendered(): void
    {
        $this->get(route('home'))->assertOk();
    }

    public function test_guest_can_register(): void
    {
        Livewire::test('pages::auth.register')
            ->set('name', 'Test Player')
            ->set('email', 'player@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('register')
            ->assertRedirect(route('notes.wall'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'player@example.com']);
    }

    public function test_guest_can_login(): void
    {
        $user = User::factory()->create([
            'password' => 'secret123',
        ]);

        Livewire::test('pages::auth.login')
            ->set('email', $user->email)
            ->set('password', 'secret123')
            ->call('login')
            ->assertRedirect(route('notes.wall'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => 'secret123',
        ]);

        Livewire::test('pages::auth.login')
            ->set('email', $user->email)
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }

    public function test_guest_cannot_access_notes_wall(): void
    {
        $this->get(route('notes.wall'))->assertRedirect(route('login'));
    }

    public function test_locale_can_be_switched(): void
    {
        $this->get(route('locale.switch', 'id'))->assertRedirect('/');
        $this->assertSame('id', session('locale'));

        $this->get(route('locale.switch', 'en'))->assertRedirect('/');
        $this->assertSame('en', session('locale'));
    }

    public function test_invalid_locale_is_rejected(): void
    {
        $this->get(route('locale.switch', 'fr'))->assertNotFound();
    }
}
