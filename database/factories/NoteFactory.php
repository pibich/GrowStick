<?php

namespace Database\Factories;

use App\Models\Note;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Note>
 */
class NoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->optional()->sentence(3),
            'body' => fake()->paragraph(),
            'visibility' => 'private',
            'slug' => Str::lower(Str::random(12)),
            'color' => fake()->randomElement(Note::COLORS),
        ];
    }

    /**
     * Indicate that the note is visible through its link only.
     */
    public function unlisted(): static
    {
        return $this->state(fn () => ['visibility' => 'unlisted']);
    }

    /**
     * Indicate that the note is listed on the public board.
     */
    public function public(): static
    {
        return $this->state(fn () => ['visibility' => 'public']);
    }
}
