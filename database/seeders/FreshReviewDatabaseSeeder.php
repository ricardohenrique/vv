<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FreshReviewDatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $authors = collect([
            ['name' => 'Alex Morgan', 'email' => 'test@example.com'],
            ['name' => 'Maya Chen', 'email' => 'maya@example.com'],
            ['name' => 'Noah Williams', 'email' => 'noah@example.com'],
            ['name' => 'Sofia Rossi', 'email' => 'sofia@example.com'],
            ['name' => 'Liam Okafor', 'email' => 'liam@example.com'],
        ])->map(fn (array $author): User => User::factory()->create([
            ...$author,
            'is_admin' => true,
        ]))->values();

        $categoryNames = [
            'Audio',
            'Computing',
            'Home & Kitchen',
            'Mobile',
            'Gaming',
            'Health & Fitness',
            'Photography',
            'Smart Home',
            'Travel',
            'Wearables',
        ];

        $categories = collect($categoryNames)
            ->map(fn (string $name): Category => Category::query()->create([
                'name' => $name,
                'slug' => Str::slug($name),
            ]))
            ->values();

        $tags = collect([
            'Affordable', 'Android', 'Apple', 'Battery Life', 'Beginner Friendly',
            'Best Buy', 'Bluetooth', 'Camera', 'Compact', 'Design', 'Durable',
            'Eco Friendly', 'Editors Choice', 'Everyday Use', 'Family', 'Fast Charging',
            'Gaming', 'Gift Idea', 'Home Office', 'Innovation', 'Lightweight',
            'Long Term Test', 'Luxury', 'Noise Cancelling', 'Outdoor', 'Performance',
            'Portable', 'Premium', 'Productivity', 'Smart Features', 'Software',
            'Sound Quality', 'Travel Friendly', 'Value', 'Versatile', 'Water Resistant',
            'Wellness', 'Wireless', 'Work From Home', 'Worth The Hype',
        ])->map(fn (string $name): Tag => Tag::query()->create([
            'name' => $name,
            'slug' => Str::slug($name),
        ]))->values();

        $articles = Article::factory()
            ->count(500)
            ->state(new Sequence(function (Sequence $sequence) use ($authors, $categories): array {
                $category = $categories[$sequence->index % $categories->count()];
                $author = $authors[$sequence->index % $authors->count()];

                return [
                    'author_id' => $author->id,
                    'category_id' => $category->id,
                    'image_path' => 'assets/demo-articles/'.$category->slug.'.jpg',
                    'image_alt' => $category->name.' product review photograph',
                    'affiliate_url' => 'https://example.com/products/'.($sequence->index + 1),
                    'published_at' => now()
                        ->subDays(($sequence->index % 365) + 1)
                        ->subMinutes($sequence->index),
                ];
            }))
            ->create();

        $tagIds = $tags->pluck('id')->all();
        $pivotRows = [];

        foreach ($articles as $article) {
            $articleTagIds = collect($tagIds)
                ->shuffle()
                ->take(random_int(3, 10));

            foreach ($articleTagIds as $tagId) {
                $pivotRows[] = [
                    'article_id' => $article->id,
                    'tag_id' => $tagId,
                ];
            }
        }

        $pivotTable = (new Article)->tags()->getTable();

        foreach (array_chunk($pivotRows, 500) as $pivotChunk) {
            DB::table($pivotTable)->insert($pivotChunk);
        }
    }
}
