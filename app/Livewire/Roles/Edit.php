<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use App\Contract\RoleRepositoryInterface;
use App\Contract\PermissionRepositoryInterface;

class Edit extends Component
{
    private $roleRepository;
    private $permissionRepository;
    private $entity = 'role';

    protected $listeners = [
        'role-permissions'  => 'handlePermissions',
        'open-edit-modal'   => 'handleEditModalData',
        'save-modal-edit'   => 'save'

    ];
    
    public array $state = [
        'role_name'             => null,
        'permissions'           => [],
        'selected_permissions'  => [],
        'permission_update'     => [],
        'id'                    => null
    ];

    protected function rules()
    {
        return [
            'state.role_name'           => 'required|unique:roles,name,' . $this->state['id'],
            'state.permission_update'   => 'array',
            'state.id'                  => 'int'
        ];
    } 

    protected $messages = [
        'state.role_name.required' => 'The role name is required.',
        'state.role_name.unique' => 'A role with the given name already exists.'
    ];

    public function render()
    {
        return view('livewire.roles.edit', [
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

    public function handleEditModalData($itemId, $entity)
    {
        if ($entity === $this->entity) {
            $role = $this->roleRepository->getById($itemId);

            $this->state['role_name'] = $role->name;
            $this->state['selected_permissions'] = $role->permissions;
            $this->state['id'] = $itemId;
        }

        return;
    }

    public function handlePermissions(array $selections): void
    {
        $this->state['permission_update'] = [];

        foreach ($selections as $id => $selection) {
            if ($selection['selected'] && !in_array($id, $this->state['permissions'])) {
                array_push($this->state['permission_update'], $id);
            }
        }
    }

    public function save(): void
    {
        $validatedData = $this->validate();
        $role = $this->roleRepository->getById($validatedData['state']['id']);

        $this->roleRepository->update($role, ['name' => $validatedData['state']['role_name']]);

        if (!empty($validatedData['state']['permission_update'])) {
            $this->roleRepository->updatePermissions($role, $validatedData['state']['permission_update']);
        }

        $this->state['role_name'] = null;
        $this->state['permissions'] = [];
        $this->state['selected_permissions'] = [];
        $this->state['permission_update'] = [];
        $this->state['id'] = null;

        $this->dispatch('toastr', ['type' => 'confirm', 'message' => 'Role edited successfully!']);
        $this->dispatch($this->entity . '-edited', ['entity' => $this->entity]);

        return;
    }
}
