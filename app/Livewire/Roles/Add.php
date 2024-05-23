<?php

namespace App\Livewire\Roles;

use App\Contract\RoleRepositoryInterface;
use App\Contract\PermissionRepositoryInterface;
use Livewire\Component;

class Add extends Component
{
    private $roleRepository;
    private $permissionRepository;

    protected $listeners = [
        'role-permissions' => 'handlePermissions'
    ];
    
    public array $state = [
        'role_name'     => '',
        'permissions'   => []
    ];

    public function render()
    {
        return view('livewire.roles.add', [
            'permissions' => $this->permissionRepository->getAll()
        ]);
    }

    public function boot(
        RoleRepositoryInterface $roleRepository,
        PermissionRepositoryInterface $permissionRepository
    )
    {
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
    }

    public function handlePermissions(array $selections): void
    {
        $this->state['permissions'] = [];

        foreach ($selections as $id => $selection) {
            if ($selection['selected'] && !in_array($id, $this->state['permissions'])) {
                array_push($this->state['permissions'], $id);
            }
        }
    }

    public function addRole(): void
    {
        $this->roleRepository->createRole($this->state['role_name'], $this->state['permissions']);

        $this->state['role_name'] = null;
        $this->state['permissions'] = [];

        $this->dispatch('toastr', ['type' => 'confirm', 'message' => 'Role created successfully!']);
        $this->dispatch('role-added');

        return;
    }
}
