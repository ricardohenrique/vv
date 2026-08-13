<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(5);

        return [
            'author_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'image_path' => 'articles/example.jpg',
            'image_alt' => 'Product shown from the front',
            'summary' => fake()->paragraph(),
            'body' => fake()->paragraphs(5, true),
            'rating' => fake()->randomFloat(1, 0, 10),
            'affiliate_url' => fake()->optional()->url(),
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => now()->subDay(),
        ]);
    }
}
