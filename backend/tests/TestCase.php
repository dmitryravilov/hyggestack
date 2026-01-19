<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Schema;
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
     * Uses firstOrCreate to safely create roles even in parallel test environments.
     */
    protected function seedRoles(): void
    {
        // Check if the roles table exists (migrations might not be ready yet)
        if (!Schema::hasTable('roles')) {
            return;
        }

        // Clear permissions cache to ensure Spatie picks up new roles
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)
            ->forgetCachedPermissions();

        // Use firstOrCreate which handles race conditions in parallel tests
        // It will create the role if it doesn't exist, or return the existing one
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'writer', 'guard_name' => 'web']);
    }
}
