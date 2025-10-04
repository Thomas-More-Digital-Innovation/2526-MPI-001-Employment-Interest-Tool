<?php

namespace Tests\Concerns;

use App\Models\Role;
use App\Models\User;

/**
 * Helper trait for tests that need to work with user roles
 */
trait InteractsWithRoles
{
    /**
     * Create a user with the specified role
     */
    protected function createUserWithRole(string $roleName, array $attributes = []): User
    {
        $role = Role::firstOrCreate(
            ['role' => $roleName],
            ['receive_emails' => in_array($roleName, [Role::ADMIN, Role::SUPER_ADMIN])]
        );

        $user = User::factory()->create($attributes);
        
        // Remove any existing roles and attach the new one
        $user->roles()->sync([$role->role_id]);
        
        return $user->fresh();
    }

    /**
     * Create a mentor with clients
     */
    protected function createMentorWithClients(int $count = 1, array $mentorAttributes = [], array $clientAttributes = []): array
    {
        $mentor = $this->createUserWithRole(Role::MENTOR, $mentorAttributes);
        
        $clients = [];
        for ($i = 0; $i < $count; $i++) {
            $client = $this->createUserWithRole(Role::CLIENT, array_merge([
                'mentor_id' => $mentor->user_id,
                'active' => true,
            ], $clientAttributes));
            
            $clients[] = $client;
        }
        
        return [$mentor, $clients];
    }
}
