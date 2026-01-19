<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Database Seeder
 *
 * Seeds the database with demo hygge content.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            TagSeeder::class,
            PostSeeder::class,
        ]);
    }
}
