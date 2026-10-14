<?php

namespace App\Services\Notes;

use App\Models\Note;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class NoteSnapshotWriter
{
    private string $directory;

    public function __construct(?string $directory = null)
    {
        $this->directory = $directory ?: config('notes.snapshots_path', public_path('snapshots'));
    }

    /**
     * Sync the on-disk snapshot with the note's current state.
     *
     * Private notes never touch disk; public notes are listed in the board
     * manifest; unlisted notes only get a per-slug file.
     */
    public function sync(Note $note): void
    {
        if ($note->visibility === 'private') {
            $this->forget($note->slug);

            return;
        }

        $note->loadMissing('user');

        $version = $this->latestVersion($note->slug) + 1;
        $updatedAt = $note->updated_at ?? now();

        $snapshot = [
            'schema' => 1,
            'slug' => $note->slug,
            'version' => $version,
            'title' => $note->title,
            'body' => $note->body,
            'color' => $note->color,
            'visibility' => $note->visibility,
            'user_name' => $note->user?->name,
            'updated_at' => $updatedAt->toIso8601String(),
            'excerpt' => Str::limit(
                preg_replace('/\s+/', ' ', trim($note->body)) ?? '',
                140,
            ),
        ];

        File::ensureDirectoryExists($this->directory);
        File::put(
            $this->directory.'/'.$note->slug.'-v'.$version.'.json',
            json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
        );

        $this->syncManifest($note, $snapshot);
    }

    /**
     * Remove every snapshot file and manifest entry for a slug.
     */
    public function forget(string $slug): void
    {
        foreach ($this->snapshotPaths($slug) as $path) {
            File::delete($path);
        }

        $manifest = $this->readManifest();
        $entries = collect($manifest['entries'])
            ->reject(fn (array $entry): bool => ($entry['slug'] ?? null) === $slug)
            ->values()
            ->all();

        if (count($entries) !== count($manifest['entries'])) {
            $this->writeManifest($entries);
        }
    }

    /**
     * Read the public board manifest (public notes only, newest first).
     *
     * @return array{updated_at: ?string, entries: list<array<string, mixed>>}
     */
    public function readManifest(): array
    {
        $path = $this->directory.'/manifest-index.json';

        if (! File::exists($path)) {
            return ['updated_at' => null, 'entries' => []];
        }

        $data = json_decode(File::get($path), true);

        if (! is_array($data) || ! isset($data['entries']) || ! is_array($data['entries'])) {
            return ['updated_at' => null, 'entries' => []];
        }

        return [
            'updated_at' => $data['updated_at'] ?? null,
            'entries' => array_values($data['entries']),
        ];
    }

    /**
     * Read the latest snapshot of a slug, if one exists on disk.
     *
     * @return array<string, mixed>|null
     */
    public function latestSnapshot(string $slug): ?array
    {
        $version = $this->latestVersion($slug);

        if ($version === 0) {
            return null;
        }

        $path = $this->directory.'/'.$slug.'-v'.$version.'.json';

        if (! File::exists($path)) {
            return null;
        }

        $data = json_decode(File::get($path), true);

        return is_array($data) ? $data : null;
    }

    /**
     * @param  array<string, mixed>  $snapshot
     */
    private function syncManifest(Note $note, array $snapshot): void
    {
        $manifest = $this->readManifest();
        $entries = collect($manifest['entries'])
            ->reject(fn (array $entry): bool => ($entry['slug'] ?? null) === $note->slug);

        if ($note->visibility === 'public') {
            $entries = $entries->prepend([
                'slug' => $note->slug,
                'title' => $note->title,
                'color' => $note->color,
                'user_name' => $note->user?->name,
                'excerpt' => $snapshot['excerpt'],
                'version' => $snapshot['version'],
                'updated_at' => $snapshot['updated_at'],
            ]);
        }

        $this->writeManifest($entries->values()->all());
    }

    /**
     * @return list<string>
     */
    private function snapshotPaths(string $slug): array
    {
        $paths = glob($this->directory.'/'.$slug.'-v*.json');

        return is_array($paths) ? $paths : [];
    }

    private function latestVersion(string $slug): int
    {
        $max = 0;

        foreach ($this->snapshotPaths($slug) as $path) {
            if (preg_match('/-v(\d+)\.json$/', $path, $matches) === 1) {
                $max = max($max, (int) $matches[1]);
            }
        }

        return $max;
    }

    /**
     * @param  list<array<string, mixed>>  $entries
     */
    private function writeManifest(array $entries): void
    {
        File::ensureDirectoryExists($this->directory);
        File::put(
            $this->directory.'/manifest-index.json',
            json_encode([
                'updated_at' => now()->toIso8601String(),
                'entries' => $entries,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
        );
    }
}
