<?php

namespace Tests\Livewire;

use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Permissions\Add as CreatePermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Permission;

class CreatePermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    /** @test */
    public function renders_sucessfully()
    {
        Livewire::test(CreatePermission::class)
            ->assertSee('Add Permission')
            ->assertSee('Add a new permission.')
            ->assertStatus(200);
    }

    /** @test */
    public function creates_permission()
    {
        $this->assertEquals(0, Permission::count());

        Livewire::test(CreatePermission::class)
            ->set('state', ['permission_name' => 'test_permissions'])
            ->call('addPermission')
            ->assertDispatched('toastr',
            [
                'type' => 'confirm',
                'message' => 'Permission created successfully!'
            ])
            ->assertDispatched('permission-added');

        $this->assertEquals(1, Permission::count());
    }

    /** @test */
    public function fails_with_missing_permission_name()
    {
        $this->assertEquals(0, Permission::count());

        Livewire::test(CreatePermission::class)
            ->set('state', ['permission_name' => ''])
            ->call('addPermission')
            ->assertHasErrors(['state.permission_name' => 'required']);

        $this->assertEquals(0, Permission::count());
    }

        /** @test */
        public function fails_with_permission_already_existing()
        {
            $existingPermission = new Permission(['name' => 'already_existing']);
            $existingPermission->save();

            $this->assertEquals(1, Permission::count());
    
            Livewire::test(CreatePermission::class)
                ->set('state', ['permission_name' => $existingPermission->name])
                ->call('addPermission')
                ->assertHasErrors(['state.permission_name' => 'unique']);
    
            $this->assertEquals(1, Permission::count());
        }
}
