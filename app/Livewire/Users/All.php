<?php

namespace App\Livewire\Users;

use App\Livewire\MainList;
use App\Contract\UserRepositoryInterface;

class All extends MainList
{
    public $usersToDelete;

    const ENTITY = 'user';

    protected $userRepository;

    protected $listeners = [
        'delete-users'    => 'deleteUsers',
        'user-added'      => 'refetch',
        'item-selection'  => 'processItemCheck',
        'user-edited'     => 'refetchUsers'
    ];

    public function render()
    {
        $this->authorizeRender();

        return view('livewire.users.all', [
            'users' => $this->userRepository->getAllPaginated($this->pagination)
        ]);
    }

    public function boot(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;

        parent::preBoot();
    }

    public function deleteUsers()
    {
       $this->authorizeDelete();

        $users = array_keys($this->usersToDelete, true);

        if (!empty($users)) {
            $this->userRepository->delete($users);

            $this->usersToDelete = [];

            $this->dispatch('toastr', ['type' => 'confirm', 'message' => trans('notifications.successfull_deletion', ['entity' => 'User'])]);

            return;
        }

        $this->dispatch('toastr', ['type' => 'error', 'message' => trans('notifications.nothing_provided_to_action', ['entity' => 'User', 'action' => 'delete'])]);

        return;
    }
}
