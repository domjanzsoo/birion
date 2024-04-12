<?php

namespace App\Livewire\Permissions;

use Livewire\Component;
use App\Contract\PermissionRepositoryInterface;

class All extends Component
{
    public $permissions;
    public $permissionsToDelete = [];
    public $deleteButtonAccess = false;
    public $toastMessage = [
        'message'   => null,
        'type'      => null
    ];

    private $permissionRepository;

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
    }

    public function deletePermissions()
    {
        $permissions = array_keys($this->permissionsToDelete, true);

        if (!empty($permissions)) {
            $this->permissionRepository->delete($permissions);

            $this->permissions = $this->permissionRepository->getAll();
            $this->permissionsToDelete = [];

            $this->setToastMessage('Permissions deleted successfully!', 'confirm');

            return;
        }

        $this->setToastMessage('No permission is provided to delete!', 'error');
    }

    public function setToastMessage(string $msg, string $type): void
    {
        $this->toastMessage = [
            'message'   => $msg,
            'type'      => $type
        ];

        $this->dispatch('toastr');
    }

    public function clearToast()
    {
        $this->toastMessage = [
            'message'   => null,
            'type'      => null
        ];
    }
}
