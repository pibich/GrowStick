<?php

namespace App\Models;

use Database\Factories\NoteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'title', 'body', 'visibility', 'slug', 'color'])]
class Note extends Model
{
    /** @use HasFactory<NoteFactory> */
    use HasFactory;

    /**
     * Visibility levels a note can have.
     *
     * @var list<string>
     */
    public const VISIBILITIES = ['private', 'unlisted', 'public'];

    /**
     * Available sticky note colors.
     *
     * @var list<string>
     */
    public const COLORS = ['yellow', 'green', 'blue', 'pink', 'purple'];

    /**
     * Tailwind classes that paint a note surface in the given palette color.
     *
     * Literal class names (no interpolation) so Tailwind's scanner picks them up.
     */
    public static function colorClass(string $color): string
    {
        return match ($color) {
            'green' => 'bg-note-green border-note-green-edge',
            'blue' => 'bg-note-blue border-note-blue-edge',
            'pink' => 'bg-note-pink border-note-pink-edge',
            'purple' => 'bg-note-purple border-note-purple-edge',
            default => 'bg-note-yellow border-note-yellow-edge',
        };
    }

    /**
     * The user who owns the note.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Whether the given user may read this note.
     */
    public function accessibleBy(?User $user): bool
    {
        if ($this->visibility !== 'private') {
            return true;
        }

        return $user !== null && $this->user_id === $user->id;
    }
}
