<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

/**
 * Category Seeder
 */
class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Lifestyle',
                'slug' => 'lifestyle',
                'description' => 'Thoughts on living a cozy, intentional life.',
                'color' => '#8B7355',
            ],
            [
                'name' => 'Recipes',
                'slug' => 'recipes',
                'description' => 'Warm, comforting recipes for the soul.',
                'color' => '#C9A961',
            ],
            [
                'name' => 'Wellness',
                'slug' => 'wellness',
                'description' => 'Mindful practices for body and mind.',
                'color' => '#A8B5A0',
            ],
            [
                'name' => 'Home',
                'slug' => 'home',
                'description' => 'Creating spaces that feel like home.',
                'color' => '#D4C4A8',
            ],
            [
                'name' => 'Reflections',
                'slug' => 'reflections',
                'description' => 'Quiet moments and gentle thoughts.',
                'color' => '#B8A082',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
