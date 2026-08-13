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
use Illuminate\Support\Facades\Storage;
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

        $this->createCategoryImages($categories->all());

        $articles = Article::factory()
            ->count(500)
            ->state(new Sequence(function (Sequence $sequence) use ($authors, $categories): array {
                $category = $categories[$sequence->index % $categories->count()];
                $author = $authors[$sequence->index % $authors->count()];

                return [
                    'author_id' => $author->id,
                    'category_id' => $category->id,
                    'image_path' => 'demo/'.$category->slug.'.svg',
                    'image_alt' => $category->name.' product review illustration',
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

    /** @param array<int, Category> $categories */
    private function createCategoryImages(array $categories): void
    {
        $disk = Storage::disk('public');
        $disk->deleteDirectory('demo');

        $accentColors = [
            '#2f65f5', '#5b7cfa', '#14a3a8', '#7c5ce7', '#e05d80',
            '#22a06b', '#e17b32', '#4a70c9', '#be5dd8', '#1687c9',
        ];

        foreach ($categories as $index => $category) {
            $name = e($category->name);
            $accent = $accentColors[$index % count($accentColors)];
            $svg = <<<SVG
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 800" role="img" aria-label="{$name}">
                    <defs>
                        <linearGradient id="background" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0" stop-color="#f4f8ff"/>
                            <stop offset="1" stop-color="#dce8ff"/>
                        </linearGradient>
                    </defs>
                    <rect width="1200" height="800" fill="url(#background)"/>
                    <circle cx="990" cy="110" r="290" fill="{$accent}" opacity=".12"/>
                    <circle cx="150" cy="730" r="250" fill="#0b2a5b" opacity=".08"/>
                    <path d="M130 210h170l215 350-108 170L130 210Zm420 0h185L515 560 407 390l143-180Z" fill="#0b2a5b" opacity=".94"/>
                    <path d="M348 335h155l108 170L907 82 575 730 348 335Z" fill="{$accent}"/>
                    <text x="760" y="580" fill="#0b2a5b" font-family="Arial, sans-serif" font-size="56" font-weight="700" text-anchor="middle">{$name}</text>
                    <text x="760" y="640" fill="#61718a" font-family="Arial, sans-serif" font-size="24" letter-spacing="6" text-anchor="middle">VIRAL VERDICT</text>
                </svg>
                SVG;

            $disk->put('demo/'.$category->slug.'.svg', $svg);
        }
    }
}
