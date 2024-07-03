<?php

namespace App\Livewire\Users;

use App\Livewire\MainList;
use App\Contract\UserRepositoryInterface;

class All extends MainList
{
    public $usersToDelete;

    const ENTITY = 'user';

    public $extraInformation = [
        'component' => 'data-grid',
        'dataProperty' => [
          'email', 
          'verified',
          'user_permission_list' => [
            'componentName'     => 'info-tooltip',
            'attributes'        => [
              'information'     => 'user_permissions_list',
              'label'           => '[all_user_permissions_count] permissions',
              'refreshEvent'    => 'user-edited'
            ]
          ],
          'user_roles_list' => [
            'componentName'     => 'info-tooltip',
            'attributes'        => [
              'information'     => 'user_roles_list',
              'label'           => '[all_user_roles_count] roles',
              'refreshEvent'    => 'user-edited'
            ]
          ]
        ],
      ];

    protected $userRepository;

    protected $listeners = [
        'delete-users'    => 'deleteUsers',
        'user-added'      => 'refetch',
        'user-edited'     => 'refetchUsers',
        'item-selection'  => 'processItemCheck',
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
        $this->extraInformation['enityType'] = self::ENTITY;

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

    public function refetchUsers()
    {
        $this->users = $this->userRepository->getAllPaginated($this->pagination);
    }
}
