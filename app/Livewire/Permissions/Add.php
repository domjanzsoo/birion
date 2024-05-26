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

    protected $rules = [
        'state.permission_name' => 'required|unique:permissions,name'
    ];

    protected $messages = [
        'state.permission_name.required' => 'Permission name is required.',
        'state.permission_name.unique' => 'Permission with the given name already exists.'
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
        $validatedData = $this->validate();

        $this->permissionRepository->create(['name' => $validatedData['state']['permission_name']]);

        $this->state['permission_name'] = null;

        $this->dispatch('toastr', ['type' => 'confirm', 'message' => 'Permission created successfully!']);
        $this->dispatch('permission-added');

        return;
    }
}
