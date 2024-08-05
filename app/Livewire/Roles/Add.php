<?php

namespace App\Livewire\Roles;

use App\Contract\RoleRepositoryInterface;
use App\Contract\PermissionRepositoryInterface;
use Livewire\Component;
use Illuminate\Auth\Access\AuthorizationException;

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

    protected $rules = [
        'state.role_name' => 'required|unique:roles,name',
        'state.permissions' => 'array'
    ];

    public function  messages(): array
    {
        return [
            'state.role_name.required' => trans('validation.required', ['attribute' => 'name']),
            'state.role_name.unique' => trans('validation.unique', ['attribute' => 'role name'])
        ];
    }

    public function render()
    {
        return view('livewire.roles.add', [
            'permissions' => $this->permissionRepository->getAll('name')
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
        if (!access_control()->canAccess(auth()->user(), 'add_role')) {
            throw new AuthorizationException(trans('errors.unauthorized_action', ['action' => 'add role']));
        }

        $validatedData = $this->validate();

        $this->roleRepository->createRole($validatedData['state']['role_name'], $validatedData['state']['permissions']);

        $this->state['role_name'] = null;
        $this->state['permissions'] = [];

        $this->dispatch('toastr', ['type' => 'confirm', 'message' => 'Role created successfully!']);
        $this->dispatch('role-permissions-submitted');
        $this->dispatch('role-added');

        return;
    }
}
