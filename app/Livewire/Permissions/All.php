<?php

namespace App\Livewire\Permissions;

use Livewire\Component;
use App\Contract\PermissionRepositoryInterface;

class All extends Component
{
    public $permissions;
    public $permissionsToDelete = [];
    public $toastMessage = [
        'message'   => null,
        'type'      => null
    ];

    private $permissionRepository;

    public function render()
    {
        return view('livewire.permissions.all');
    }

    public function mount(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;

        $this->permissions = $this->permissionRepository->getAll();
    }

    public function deletePermissions()
    {
        if (!empty($this->permissionsToDelete)) {
            $this->permissionRepository->delete($this->permissionsToDelete);

            $this->permissions = $this->permissionRepository->getAll();

            return;
        }

        $this->setToastMessage('No permission is provided to delete!');
    }

    public function setToastMessage($msg)
    {
        $this->toastMessage = [
            'message'   => $msg,
            'type'      => 'error'
        ];

        $this->dispatch('toastr.error');
    }

    public function clearToast()
    {
        $this->toastMessage = [
            'message'   => null,
            'type'      => null
        ];
    }
}
