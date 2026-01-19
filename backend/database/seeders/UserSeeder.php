<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * User Seeder
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        $admin = User::create([
            'name' => 'Hygge Admin',
            'email' => 'admin@hyggestack.local',
            'password' => Hash::make('password'),
            'bio' => 'Curator of cozy moments and warm experiences.',
        ]);
        $admin->assignRole('admin');

        // Writer
        $writer = User::create([
            'name' => 'Emma',
            'email' => 'emma@hyggestack.local',
            'password' => Hash::make('password'),
            'bio' => 'Writer of cozy stories and hygge moments.',
        ]);
        $writer->assignRole('writer');
    }
}
