<?php

namespace App\Livewire\Permissions;

use Livewire\Component;
use App\Contract\PermissionRepositoryInterface;

class Edit extends Component
{
    private $permissionRepository;
    private $entity = 'permission';
    public $permission;
    public $name = '';

    protected $listeners = [
        'open-edit-modal' => 'handleEditModalData'
    ];

    public function render()
    {
        return view('livewire.permissions.edit');
    }

    public function boot(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function handleEditModalData($itemId, $entity)
    {
        if ($entity === $this->entity) {
            $this->permission = $this->permissionRepository->getById($itemId);

            $this->name = $this->permission->name;
        }
    }

    public function save()
    {
        $this->permissionRepository->update($this->permission, ['name' => $this->name]);

        $this->dispatch('toastr', ['type' => 'confirm', 'message' => 'Permission updated successfully!']);
        $this->dispatch($this->entity . '-edited', ['entity' => $this->entity]);

        return;
    }
}
