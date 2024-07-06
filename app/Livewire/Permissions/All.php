<?php

namespace App\Livewire\Permissions;

use App\Livewire\MainList;
use App\Contract\PermissionRepositoryInterface;

class All extends MainList
{
    public $permissionsToDelete;
    const ENTITY = 'permission';

    protected $permissionRepository;

    protected $listeners = [
        'delete-permissions'    => 'deletePermissions',
        'permission-added'      => 'refetch',
        'item-selection'        => 'processItemCheck',
        'permission-edited'     => 'refetch'
    ];

    public function render()
    {
        $this->authorizeRender();

        return view('livewire.permissions.all', [
            'permissions' => $this->permissionRepository->getAllPaginated($this->pagination)
        ]);
    }

    public function boot(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;

        parent::preBoot();
    }

    public function deletePermissions()
    {
        $this->authorizeDelete();

        $permissions = array_keys($this->permissionsToDelete, true);

        if (!empty($permissions)) {
            $this->permissionRepository->delete($permissions);

            $this->permissionsToDelete = [];

            $this->dispatch('toastr', ['type' => 'confirm', 'message' => trans('notifications.successfull_deletion', ['entity' => 'Permission'])]);

            return;
        }

        $this->dispatch('toastr', ['type' => 'error', 'message' => trans('notifications.nothing_provided_to_action', ['entity' => 'Permission', 'action' => 'delete'])]);

        return;
    }
}
