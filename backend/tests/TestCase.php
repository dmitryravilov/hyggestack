<?php

declare(strict_types=1);

namespace Tests;

use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    /**
     * Seed roles after database is refreshed.
     * This method is called by RefreshDatabase trait after migrations complete.
     */
    protected function afterRefreshingDatabase()
    {
        parent::afterRefreshingDatabase();

        // Create roles for tests after migrations have run
        $this->seedRoles();
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Also ensure roles are seeded in setUp as a fallback
        // This handles cases where afterRefreshingDatabase might not be called
        $this->seedRoles();
    }

    /**
     * Seed roles if they don't already exist.
     */
    protected function seedRoles(): void
    {
        try {
            // Check if roles already exist to avoid duplicate key errors
            if (Role::where('name', 'admin')->where('guard_name', 'web')->doesntExist()) {
                $this->seed(RoleSeeder::class);
            }
        } catch (\Exception $e) {
            // If seeding fails (e.g., table doesn't exist yet), try again
            // This can happen in parallel testing where migrations might not be ready
            try {
                $this->seed(RoleSeeder::class);
            } catch (\Exception $e2) {
                // Ignore - migrations might not be ready yet
            }
        }
    }
}
