<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use Livewire\WithPagination;
use App\Contract\RoleRepositoryInterface;

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
        $roles = array_keys($this->rolesToDelete, true);

        if (!empty($roles)) {
            $this->roleRepository->delete($roles);

            $this->rolesToDelete = [];

            $this->dispatch('toastr', ['type' => 'confirm', 'message' => 'Roles deleted successfully!']);

            return;
        }

        $this->dispatch('toastr', ['type' => 'error', 'message' => 'No role is provided to delete!']);

        return;
    }

    public function refetchRoles()
    {
        $this->roles = $this->roleRepository->getAllPaginated($this->pagination);
    }
}


