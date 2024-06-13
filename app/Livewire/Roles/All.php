<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use Livewire\WithPagination;
use App\Contract\RoleRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;

class All extends Component
{
    use WithPagination;

    public $deleteButtonAccess = false;
    public $pagination = 5;

    public $entity = 'role';
    public $rolesToDelete;

    private $roleRepository;

    protected $listeners = [
        'delete-roles'          => 'deleteRoles',
        'role-added'            => 'refetchRoles    ',
        'item-selection'        => 'processRoleCheck',
        'role-edited'           => 'refetchPermissions'
    ];

    public function render()
    {
        if (!access_control()->canAccess(auth()->user(), ['view_roles', 'add_role', 'edit_role'])) {
            throw new AuthorizationException(trans('errors.unauthorized_action', ['action' => 'view role']));
        }

        return view('livewire.roles.all', [
            'roles' => $this->roleRepository->getAllPaginated($this->pagination)
        ]);
    }

    public function paginationView()
    {
        return 'components.pagination-links';
    }

    public function boot(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function processRoleCheck(string $entity, array $items)
    {
        if (!access_control()->canAccess(auth()->user(), 'delete_role')) {
            throw new AuthorizationException(trans('errors.unauthorized_action', ['action' => 'select role']));
        }

        if ($entity === $this->entity) {
            $buttonDisable = false;

            foreach ($items as $role) {
                if ($role) {
                    $buttonDisable = true;
                }
            }
    
            $this->deleteButtonAccess = $buttonDisable;
            $this->rolesToDelete = $items;
        }

        return;
    }

    public function deleteRoles()
    {
        if (!access_control()->canAccess(auth()->user(), 'delete_role')) {
            throw new AuthorizationException(trans('errors.unauthorized_action', ['action' => 'delete role']));
        }

        $roles = array_keys($this->rolesToDelete, true);

        if (!empty($roles)) {
            $this->roleRepository->delete($roles);

            $this->rolesToDelete = [];

            $this->dispatch('toastr', ['type' => 'confirm', 'message' => trans('notifications.successfull_deletion', ['entity' => 'Role'])]);

            return;
        }

        $this->dispatch('toastr', ['type' => 'error', 'message' => trans('notifications.nothing_provided_to_action', ['entity' => 'Role', 'action' => 'delete'])]);

        return;
    }

    public function refetchRoles()
    {
        $this->roles = $this->roleRepository->getAllPaginated($this->pagination);
    }
}


