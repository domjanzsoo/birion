<?php

namespace App\Livewire\Permissions;

use Livewire\Component;
use App\Contract\PermissionRepositoryInterface;
use Livewire\WithPagination;

class All extends Component
{
    use WithPagination;

    public $deleteButtonAccess = false;
    public $pagination = 5;

    public $entity = 'permission';
    public $permissionsToDelete;

    private $permissionRepository;

    protected $listeners = [
        'deletePermissions' => 'deletePermissions',
        'permissionAdded'   => 'refetchPermissions',
        'itemSelection'    => 'processPermissionCheck',
        'permission-edited'    => 'refetchPermissions'
    ];

    public function render()
    {
        return view('livewire.permissions.all', [
            'permissions' => $this->permissionRepository->getAllPaginated($this->pagination)
        ]);
    }

    public function paginationView()
    {
        return 'components.pagination-links';
    }

    public function boot(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function processPermissionCheck(string $entity, array $items)
    {
        if ($entity === $this->entity) {
            $buttonDisable = false;

            foreach ($items as $permission) {
                if ($permission) {
                    $buttonDisable = true;
                }
            }
    
            $this->deleteButtonAccess = $buttonDisable;
            $this->permissionsToDelete = $items;
        }

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
        $this->permissions = $this->permissionRepository->getAllPaginated($this->pagination);
    }
}
