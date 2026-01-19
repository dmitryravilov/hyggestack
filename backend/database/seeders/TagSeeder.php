<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

/**
 * Tag Seeder
 */
class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'cozy',
            'minimalism',
            'mindfulness',
            'winter',
            'coffee',
            'candles',
            'reading',
            'baking',
            'self-care',
            'gratitude',
            'simplicity',
            'comfort',
            'warmth',
            'peace',
            'slow-living',
        ];

        foreach ($tags as $tag) {
            Tag::create([
                'name' => ucfirst($tag),
                'slug' => $tag,
            ]);
        }
    }
}

