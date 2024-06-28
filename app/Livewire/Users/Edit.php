<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Contract\UserRepositoryInterface;
use App\Contract\PermissionRepositoryInterface;
use App\Contract\RoleRepositoryInterface;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    private $userRepository;
    private $permissionRepository;
    private $roleRepository;

    public $user;

    const ENTITY = 'user';

    public array $state = [
        'id'                    => null,
        'full_name'             => null,
        'email'                 => null,
        'password'              => null,
        'password_confirmation' => null,
        'verified'              => false,
        'profile_picture'       => null,
        'permissions'           => [],
        'selected_permissions'  => [],
        'roles'                 => []
    ];

    protected function rules(): array
    {
        return [
            'state.full_name'               => 'required',
            'state.email'                   => 'required|email|unique:users,email,' . $this->state['id'],
            'state.password'                => 'confirmed|min:6|nullable',
            'state.password_confirmation'   => 'min:6|nullable',
            'state.profile_picture'         => 'image|max:2048|nullable',
            'state.permissions'             => 'array',
            'state.roles'                   => 'array'
        ];
    }

    public function messages(): array
    {
        return [
            'state.full_name.required' => trans('validation.required', ['attribute' => 'name']),
            'state.email.required' => trans('validation.required', ['attribute' => 'email']),
            'state.email.unique' => trans('validation.unique', ['attribute' => 'user email']),
            'state.email.email' => trans('validation.email', ['attribute' => 'user email']),
            'state.password.min' => trans('validation.min.string', ['attribute' => 'password', 'min' => 6]),
            'state.password.confirmed' => trans('validation.confirmed', ['attribute' => 'password']),
            'state.profile_picture.image' => trans('validation.image', ['attribute' => 'profile picture']),
            'state.profile_picture.max' => trans('validation.max.file', ['max' => '2048', 'attribute' => 'profile picture'])
        ];
    }

    protected $listeners = [
        'open-edit-modal'       => 'handleEditModalData',
        'save-modal-edit-user'  => 'save'
    ];

    public function render()
    {
        return view('livewire.users.edit', [
            'permissions' => $this->permissionRepository->getAll(),
            'roles' => $this->roleRepository->getAll()
        ]);
    }

    public function updatedStatePassword(): void
    {
        $this->validateOnly('state.password');
    }

    public function updatedStatePasswordConfirmation(): void
    {
        $this->validateOnly('state.password');
    }

    public function boot(
        UserRepositoryInterface $userRepository,
        PermissionRepositoryInterface $permissionRepository,
        RoleRepositoryInterface $roleRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->permissionRepository = $permissionRepository;
        $this->roleRepository = $roleRepository;
    }

    public function handleEditModalData($itemId, $entity): void
    {
        if ($entity === self::ENTITY) {
            $this->user = $this->userRepository->getById($itemId);

            $this->state['full_name'] = $this->user->name;
            $this->state['email'] = $this->user->email;
            $this->state['profile_picture'] = isset($this->user->profile_photo_path) ? asset($this->user->profile_photo_path) : asset('/storage/avatar/user.png');
            $this->state['selected_permissions'] = $this->user->permissions;
            $this->state['roles'] = $this->user->roles()->pluck('roles.id')->toArray();
            $this->state['id'] = $itemId;
        }
    }

    public function save(): void
    {
        if (!access_control()->canAccess(auth()->user(), 'edit_user')) {
            throw new AuthorizationException(trans('errors.unauthorized_action', ['action' => 'edit user']));
        }

        $validatedData = $this->validate();

        try {
            $this->userRepository->update(
                $this->user,
                [
                    'name' => $validatedData['state']['full_name'],
                    'email' => $validatedData['state']['email'],
                    'profile_photo_path' => $validatedData['state']['profile_picture']
                ]
            );

            $this->dispatch('toastr', ['type' => 'confirm', 'message' => trans('notifications.successfull_update', ['entity' => 'User'])]);
            $this->dispatch(self::ENTITY . '-edited', ['entity' => self::ENTITY]);
        } catch(Exception $exception) {
            $this->dispatch('toastr', ['type' => 'error', 'message' => $exception->getMessage()]);
        }


        return;
    }
}
