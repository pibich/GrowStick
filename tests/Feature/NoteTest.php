<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use App\Services\Notes\NoteSnapshotWriter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;
use Tests\TestCase;

class NoteTest extends TestCase
{
    use RefreshDatabase;

    private string $snapshotDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->snapshotDir = sys_get_temp_dir().'/gtp-notes-'.uniqid();
        config(['notes.snapshots_path' => $this->snapshotDir]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->snapshotDir);

        parent::tearDown();
    }

    public function test_guest_is_redirected_from_the_wall(): void
    {
        $this->get(route('notes.wall'))->assertRedirect(route('login'));
    }

    public function test_user_can_create_a_private_note(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test('pages::notes.wall')
            ->call('startCreate')
            ->set('title', 'Belanja mingguan')
            ->set('body', 'Beli roti\nJangan lupa telur')
            ->call('create')
            ->assertHasNoErrors();

        $note = Note::query()->where('user_id', $user->id)->firstOrFail();

        $this->assertSame('Belanja mingguan', $note->title);
        $this->assertSame('private', $note->visibility);
        $this->assertSame([], glob($this->snapshotDir.'/'.$note->slug.'-v*.json') ?: []);

        $this->actingAs($user)
            ->get(route('notes.wall'))
            ->assertOk()
            ->assertSee('Belanja mingguan');
    }

    public function test_body_is_required(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test('pages::notes.wall')
            ->call('startCreate')
            ->set('body', '')
            ->call('create')
            ->assertHasErrors(['body']);

        $this->assertSame(0, Note::count());
    }

    public function test_private_note_returns_404_for_everyone_except_the_owner(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $note = Note::factory()->for($owner)->create(['visibility' => 'private']);

        $this->actingAs($owner)
            ->get(route('notes.show', $note->slug))
            ->assertOk();

        $this->actingAs($intruder)
            ->get(route('notes.show', $note->slug))
            ->assertNotFound();

        $this->app['auth']->forgetGuards();

        $this->get(route('notes.show', $note->slug))->assertNotFound();
    }

    public function test_unlisted_note_is_readable_via_link_but_absent_from_the_board(): void
    {
        $owner = User::factory()->create();
        $note = Note::factory()->for($owner)->unlisted()->create(['title' => 'Secret Salsa', 'body' => 'Resep rahasia']);
        app(NoteSnapshotWriter::class)->sync($note);

        $this->get(route('notes.show', $note->slug))
            ->assertOk()
            ->assertSee('Resep rahasia');

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Secret Salsa');
    }

    public function test_public_note_appears_on_the_board(): void
    {
        $owner = User::factory()->create();
        $note = Note::factory()->for($owner)->public()->create(['title' => 'Tips Berkebun']);
        app(NoteSnapshotWriter::class)->sync($note);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Tips Berkebun');
    }

    public function test_board_survives_a_database_wipe(): void
    {
        $owner = User::factory()->create();
        $note = Note::factory()->for($owner)->public()->create(['title' => 'Offline Survivor']);
        app(NoteSnapshotWriter::class)->sync($note);

        Note::query()->delete();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Offline Survivor');

        $this->get(route('notes.show', $note->slug))
            ->assertOk()
            ->assertSee('Offline Survivor');
    }

    public function test_editing_bumps_the_snapshot_version(): void
    {
        $owner = User::factory()->create();
        $note = Note::factory()->for($owner)->public()->create(['body' => 'Versi satu']);
        app(NoteSnapshotWriter::class)->sync($note);

        Livewire::actingAs($owner)
            ->test('pages::notes.show', ['slug' => $note->slug])
            ->set('editBody', 'Versi dua')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertFileExists($this->snapshotDir.'/'.$note->slug.'-v1.json');
        $this->assertFileExists($this->snapshotDir.'/'.$note->slug.'-v2.json');
        $this->assertSame(
            'Versi dua',
            json_decode(File::get($this->snapshotDir.'/'.$note->slug.'-v2.json'), true)['body'],
        );
    }

    public function test_switching_to_private_removes_the_snapshot_and_board_entry(): void
    {
        $owner = User::factory()->create();
        $note = Note::factory()->for($owner)->public()->create(['title' => 'Now Private', 'body' => 'Dulu publik']);
        app(NoteSnapshotWriter::class)->sync($note);

        Livewire::actingAs($owner)
            ->test('pages::notes.show', ['slug' => $note->slug])
            ->set('editVisibility', 'private')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame([], glob($this->snapshotDir.'/'.$note->slug.'-v*.json') ?: []);
        $this->assertSame([], app(NoteSnapshotWriter::class)->readManifest()['entries']);

        $this->get(route('home'))->assertDontSee('Now Private');
        $this->actingAs($owner)->get(route('notes.show', $note->slug))->assertOk();
    }

    public function test_deleting_a_note_cleans_up_snapshot_files(): void
    {
        $owner = User::factory()->create();
        $note = Note::factory()->for($owner)->public()->create(['title' => 'Disposable']);
        app(NoteSnapshotWriter::class)->sync($note);

        Livewire::actingAs($owner)
            ->test('pages::notes.wall')
            ->call('delete', $note->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
        $this->assertSame([], glob($this->snapshotDir.'/'.$note->slug.'-v*.json') ?: []);
        $this->assertSame([], app(NoteSnapshotWriter::class)->readManifest()['entries']);
    }

    public function test_owner_lands_in_edit_mode_on_the_detail_page(): void
    {
        $owner = User::factory()->create();
        $note = Note::factory()->for($owner)->create();

        Livewire::actingAs($owner)
            ->test('pages::notes.show', ['slug' => $note->slug])
            ->assertSet('isOwner', true)
            ->assertSet('editing', true);
    }

    public function test_invalid_slug_format_is_rejected(): void
    {
        $this->get(route('notes.show', 'Bad_Slug_Here'))->assertNotFound();
    }
}
