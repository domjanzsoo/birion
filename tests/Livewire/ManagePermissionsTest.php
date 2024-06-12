<?php

namespace Tests\Livewire;

use App\Models\User;
use App\Models\Permission;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Permissions\ManagePermissions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManagePermissionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_renders_successfully(): void
    {
        $user = User::factory()->create();
        $user ->save();

        $viewPermission = new Permission(['name' => 'view_permissions']);
        $viewPermission->save();

        $user->permissions()->attach($viewPermission);

        $this->actingAs($user);

        Livewire::test(ManagePermissions::class)
            ->assertSeeHtml('<h2 id="permissions-header" class="font-semibold text-xl text-gray-800 leading-tight">')
            ->assertStatus(200);
    }
}
