<?php

namespace App\Livewire\Permissions;

use Livewire\Component;
use App\Contract\PermissionRepositoryInterface;

class Edit extends Component
{
    private $permissionRepository;
    private $entity = 'permission';
    public $permission;

    public $state = [
        'id'    => null,
        'name'  => null
    ];

    protected function rules()
    {
        return [
            'state.name' => 'required|unique:permissions,name,' . $this->state['id']
        ];
    } 

    protected $messages = [
        'state.name.required' => 'Permission name is required.',
        'state.name.unique' => 'The permission name given is already used.'
    ];

    protected $listeners = [
        'open-edit-modal'   => 'handleEditModalData'
    ];

    public function render()
    {
        return view('livewire.permissions.edit');
    }

    public function boot(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function handleEditModalData($itemId, $entity)
    {
        if ($entity === $this->entity) {
            $this->permission = $this->permissionRepository->getById($itemId);

            $this->state['name'] = $this->permission->name;
            $this->state['id'] = $itemId;
        }
    }

    public function save()
    {
        $validatedData = $this->validate();

        $this->permissionRepository->update($this->permission, ['name' => $validatedData['state']['name']]);

        $this->dispatch('toastr', ['type' => 'confirm', 'message' => 'Permission updated successfully!']);
        $this->dispatch($this->entity . '-edited', ['entity' => $this->entity]);

        return;
    }
}
