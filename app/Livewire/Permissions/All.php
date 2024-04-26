<?php

namespace App\Livewire\Permissions;

use Livewire\Component;
use App\Contract\PermissionRepositoryInterface;
use Livewire\WithPagination;

class All extends Component
{
    use WithPagination;

    public $permissionsToDelete = [];
    public $deleteButtonAccess = false;

    private $permissionRepository;

    protected $listeners = [
        'deletePermissions' => 'deletePermissions',
        'permissionAdded'   => 'refetchPermissions'
    ];

    public function render()
    {
        return view('livewire.permissions.all', [
            'permissions' => $this->permissionRepository->getAllPaginated()
        ]);
    }

    public function boot(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
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

            $this->permissionsToDelete = [];

            $this->dispatch('toastr', ['type' => 'confirm', 'message' => 'Permission deleted successfully!']);

            return;
        }

        $this->dispatch('toastr', ['type' => 'error', 'message' => 'No permission is provided to delete!']);

        return;
    }

    public function refetchPermissions()
    {
        $this->permissions = $this->permissionRepository->getAll();
    }
}
