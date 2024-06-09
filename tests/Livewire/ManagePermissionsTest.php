<?php

namespace Tests\Livewire;

use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Permissions\ManagePermissions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManagePermissionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_renders_successfully()
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(ManagePermissions::class)
            ->assertSeeHtml('<h2 id="permissions-header" class="font-semibold text-xl text-gray-800 leading-tight">')
            ->assertStatus(200);
    }
}
