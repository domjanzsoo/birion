<?php

namespace App\Livewire\Permissions;

use Livewire\Component;
use App\Contract\PermissionRepositoryInterface;

class All extends Component
{
    public $permissions;
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
}
