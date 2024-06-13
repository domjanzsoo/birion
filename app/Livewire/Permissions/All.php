<?php

namespace App\Livewire\Permissions;

use Livewire\Component;
use App\Contract\PermissionRepositoryInterface;
use Livewire\WithPagination;
use Illuminate\Auth\Access\AuthorizationException;

class All extends Component
{
    use WithPagination;

    public $deleteButtonAccess = false;
    public $pagination = 5;
    public $permissionsToDelete;

    const ENTITY = 'permission';

    private $permissionRepository;

    protected $listeners = [
        'delete-permissions'    => 'deletePermissions',
        'permission-added'      => 'refetchPermissions',
        'item-selection'        => 'processPermissionCheck',
        'permission-edited'     => 'refetchPermissions'
    ];

    public function render()
    {
        if (!access_control()->canAccess(auth()->user(), ['view_permissions', 'add_permission', 'edit_permission'])) {
            throw new AuthorizationException(trans('errors.unauthorized_action', ['action' => 'view permission']));
        }

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
        if (!access_control()->canAccess(auth()->user(), 'delete_permission')) {
            throw new AuthorizationException(trans('errors.unauthorized_action', ['action' => 'delete permission']));
        }

        if ($entity === self::ENTITY) {
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
        if (!access_control()->canAccess(auth()->user(), 'delete_permission')) {
            throw new AuthorizationException(trans('errors.unauthorized_action', ['action' => 'delete permission']));
        }

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

    public function refetchPermissions()
    {
        $this->permissions = $this->permissionRepository->getAllPaginated($this->pagination);
    }
}
