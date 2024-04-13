<?php

namespace App\Livewire\Permissions;

use Livewire\Component;
use App\Contract\PermissionRepositoryInterface;

class Add extends Component
{
    private $permissionRepository;
    public array $state = [
        'permission_name' => ''
    ];

    public function render()
    {
        return view('livewire.permissions.add');
    }

    public function boot(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function addPermission()
    {
        $this->permissionRepository->create(['name' => $this->state['permission_name']]);

        $this->state['permission_name'] = null;

        $this->dispatch('toastr', ['type' => 'confirm', 'message' => 'Permission created successfully!']);

        $this->redirect('/permissions');
    }
}
