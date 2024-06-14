<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Contract\UserRepositoryInterface;
use Livewire\WithPagination;
use Illuminate\Auth\Access\AuthorizationException;

class All extends Component
{
    use WithPagination;

    public $deleteButtonAccess = false;
    public $pagination = 5;
    public $usersToDelete;

    const ENTITY = 'user';

    private $userRepository;

    protected $listeners = [
        'delete-users'    => 'deleteUsers',
        'user-added'      => 'refetchUsers',
        'item-selection'  => 'processUserCheck',
        'user-edited'     => 'refetchUsers'
    ];

    public function render()
    {
        if (!access_control()->canAccess(auth()->user(), ['view_users', 'add_user', 'edit_user'])) {
            throw new AuthorizationException(trans('errors.unauthorized_action', ['action' => 'view user']));
        }

        return view('livewire.permissions.all', [
            'permissions' => $this->permissionRepository->getAllPaginated($this->pagination)
        ]);
    }

    public function paginationView()
    {
        return 'components.pagination-links';
    }

    public function boot(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
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
