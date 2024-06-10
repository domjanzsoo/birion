<?php

namespace App\Livewire\Permissions;

use Livewire\Component;
use App\Contract\PermissionRepositoryInterface;
use App\Services\AccessControlService;
use Illuminate\Auth\Access\AuthorizationException;

class Add extends Component
{
    private $permissionRepository;
    private $accessControlService;

    public array $state = [
        'permission_name' => ''
    ];

    protected $rules = [
        'state.permission_name' => 'required|unique:permissions,name'
    ];

    public function messages()
    {
        return [
            'state.permission_name.required' => trans('validation.required', ['attribute' => 'name']),
            'state.permission_name.unique' => trans('validation.unique', ['attribute' => 'permission name'])
        ];
    } 

    public function render()
    {
        return view('livewire.permissions.add');
    }

    public function boot(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
        $this->accessControlService = AccessControlService::getInstance();
    }

    public function addPermission()
    {
        if (!$this->accessControlService->canAccess(auth()->user(), 'add_permission')) {
            throw new AuthorizationException(trans('errors.unauthorized_action', ['action' => 'add permission']));

            return false;
        }

        $validatedData = $this->validate();

        $this->permissionRepository->create(['name' => $validatedData['state']['permission_name']]);

        $this->state['permission_name'] = null;

        $this->dispatch('toastr', ['type' => 'confirm', 'message' => trans('notifications.successfull_creation', ['entity' => 'Permission'])]);
        $this->dispatch('permission-added');

        return;
    }
}
