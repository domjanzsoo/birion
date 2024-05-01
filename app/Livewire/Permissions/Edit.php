<?php

namespace App\Livewire\Permissions;

use Livewire\Component;
use App\Contract\PermissionRepositoryInterface;

class Edit extends Component
{
    private $permissionRepository;
    public $permission;
    public $name = '';

    protected $listeners = [
        'openeditmodal' => 'handleEditModalData'
    ];

    public function render()
    {
        return view('livewire.permissions.edit');
    }

    public function boot(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function handleEditModalData($itemId)
    {
        $this->permission = $this->permissionRepository->getById($itemId);

        $this->name = $this->permission->name;
    }

    public function save()
    {
       $this->permissionRepository->update($this->permission, ['name' => $this->name]);

       $this->dispatch('toastr', ['type' => 'confirm', 'message' => 'Permission updated successfully!']);

        return $this->redirect('/permissions');
    }
}
