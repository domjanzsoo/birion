<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Contract\UserRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;

class Edit extends Component
{
    private $userRepository;
    public $user;

    const ENTITY = 'user';

    public $state = [
        'id'    => null,
        'user_name'  => null
    ];

    protected function rules(): array
    {
        return [
            'state.user_name' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'state.user_name.required' => trans('validation.required', ['attribute' => 'name'])
        ];
    }

    protected $listeners = [
        'open-edit-modal'   => 'handleEditModalData'
    ];

    public function render()
    {
        return view('livewire.users.edit');
    }

    public function boot(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handleEditModalData($itemId, $entity): void
    {
        if ($entity === self::ENTITY) {
            $this->user = $this->userRepository->getById($itemId);

            $this->state['user_name'] = $this->user->name;
            $this->state['id'] = $itemId;
        }
    }

    public function save(): void
    {
        if (!access_control()->canAccess(auth()->user(), 'edit_user')) {
            throw new AuthorizationException(trans('errors.unauthorized_action', ['action' => 'edit user']));
        }

        $validatedData = $this->validate();

        $this->userRepository->update($this->user, ['name' => $validatedData['state']['user_name']]);

        $this->dispatch('toastr', ['type' => 'confirm', 'message' => trans('notifications.successfull_update', ['entity' => 'User'])]);
        $this->dispatch(self::ENTITY . '-edited', ['entity' => self::ENTITY]);

        return;
    }
}
