<?php

namespace App\Livewire\Permissions;

use Livewire\Component;
use App\Contract\PermissionRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;

class Edit extends Component
{
    private $permissionRepository;
    public $permission;

    const ENTITY = 'permission';

    public $state = [
        'id'    => null,
        'permission_name'  => null
    ];

    protected function rules(): array
    {
        return [
            'state.permission_name' => 'required|unique:permissions,name,' . $this->state['id']
        ];
    }

    public function messages(): array
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

    public function handleEditModalData($itemId, $entity): void
    {
        if ($entity === self::ENTITY) {
            $this->permission = $this->permissionRepository->getById($itemId);

            $this->state['permission_name'] = $this->permission->name;
            $this->state['id'] = $itemId;
        }
    }

    public function save(): void
    {
        if (!access_control()->canAccess(auth()->user(), 'edit_permission')) {
            throw new AuthorizationException(trans('errors.unauthorized_action', ['action' => 'edit permission']));
        }

        $validatedData = $this->validate();

        $this->permissionRepository->update($this->permission, ['name' => $validatedData['state']['permission_name']]);

        $this->dispatch('toastr', ['type' => 'confirm', 'message' => trans('notifications.successfull_update', ['entity' => 'Permission'])]);
        $this->dispatch(self::ENTITY . '-edited', ['entity' => self::ENTITY]);

        return;
    }
}
