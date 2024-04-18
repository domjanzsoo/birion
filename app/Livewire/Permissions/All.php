<?php

namespace App\Livewire\Permissions;

use Livewire\Component;
use App\Contract\PermissionRepositoryInterface;

class All extends Component
{
    public $permissions;
    public $permissionsToDelete = [];
    public $deleteButtonAccess = false;

    private $permissionRepository;

    protected $listeners = [
        'deletePermissions' => 'deletePermissions'
    ];

    public function render()
    {
        return view('livewire.permissions.all');
    }

    public function boot(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;

        $this->permissions = $this->permissionRepository->getAll();
    }

    public function processPermissionCheck()
    {
        $buttonDisable = false;

        foreach ($this->permissionsToDelete as $permission) {
            if ($permission) {
                $buttonDisable = true;
            }
        }

        $this->deleteButtonAccess = $buttonDisable;

        return;
    }

    public function deletePermissions()
    {
        $permissions = array_keys($this->permissionsToDelete, true);

        if (!empty($permissions)) {
            $this->permissionRepository->delete($permissions);

            $this->permissions = $this->permissionRepository->getAll();
            $this->permissionsToDelete = [];

            $this->dispatch('toastr', ['type' => 'confirm', 'message' => 'Permission deleted successfully!']);

            return;
        }

        $this->dispatch('toastr', ['type' => 'error', 'message' => 'No permission is provided to delete!']);

        return;
    }
}
