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
        'permission_name'  => null
    ];

    protected function rules()
    {
        return [
            'state.permission_name' => 'required|unique:permissions,name,' . $this->state['id']
        ];
    }

    public function messages()
    {
        return [
            'state.permission_name.required' => trans('validation.required', ['attribute' => 'name']),
            'state.permission_name.unique' => trans('validation.unique', ['attribute' => 'permission name'])
        ];
    }

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

            $this->state['permission_name'] = $this->permission->name;
            $this->state['id'] = $itemId;
        }
    }

    public function save()
    {
        $validatedData = $this->validate();

        $this->permissionRepository->update($this->permission, ['name' => $validatedData['state']['permission_name']]);

        $this->dispatch('toastr', ['type' => 'confirm', 'message' => trans('notifications.successfull_update', ['entity' => 'Permission'])]);
        $this->dispatch($this->entity . '-edited', ['entity' => $this->entity]);

        return;
    }
}
