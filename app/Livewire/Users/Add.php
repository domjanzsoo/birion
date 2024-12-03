<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use App\Contract\UserRepositoryInterface;
use App\Contract\PermissionRepositoryInterface;
use App\Contract\RoleRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rules\Password;

class Add extends Component
{
    use WithFileUploads;

    private $userRepository;
    private $permissionRepository;
    private $roleRepository;

    const ENTITY = 'user';

    public array $state = [
        'full_name'             => null,
        'email'                 => null,
        'password'              => null,
        'password_confirmation' => null,
        'verified'              => false,
        'profile_picture'       => null,
        'permissions'           => [],
        'roles'                 => []
    ];

    public function rules(): array
    {
        return [
            'state.full_name'               => 'required',
            'state.email'                   => 'required|email|unique:users,email',
            'state.password'                => ['required', 'confirmed', Password::min(6)->mixedCase()->symbols()],
            'state.password_confirmation'   => 'required',
            'state.profile_picture'         => 'image|max:2048|nullable',
            'state.permissions'             => 'array',
            'state.roles'                   => 'array'
        ];
    }

    protected $listeners = [
        'user-permissions'  => 'handlePermissions',
        'user-roles'        => 'handleRoles'
    ];

    public function messages(): array
    {
        return [
            'state.full_name.required' => trans('validation.required', ['attribute' => 'name']),
            'state.email.required' => trans('validation.required', ['attribute' => 'email']),
            'state.email.unique' => trans('validation.unique', ['attribute' => 'user email']),
            'state.email.email' => trans('validation.email', ['attribute' => 'user email']),
            'state.password.required' => trans('validation.required', ['attribute' => 'password']),
            'state.password.min' => trans('validation.min.string', ['attribute' => 'password', 'min' => 6]),
            'state.password.confirmed' => trans('validation.confirmed', ['attribute' => 'password']),
            'state.password_confirmation.required' => trans('validation.required', ['attribute' => 'password confirmation']),
            'state.profile_picture.image' => trans('validation.image', ['attribute' => 'profile picture']),
            'state.profile_picture.max' => trans('validation.max.file', ['max' => '2048', 'attribute' => 'profile picture'])
        ];
    }

    public function updatedStatePassword(): void
    {
        $this->validateOnly('state.password');
    }

    public function updatedStatePasswordConfirmation(): void
    {
        $this->validateOnly('state.password');
    }

    public function handlePermissions(array $selections): void
    {
        $this->state['permissions'] = [];

        foreach ($selections as $id => $selection) {
            if ($selection['selected'] && !in_array($id, $this->state['permissions'])) {
                array_push($this->state['permissions'], $id);
            }
        }
    }

    public function handleRoles(array $selections): void
    {
        $this->state['roles'] = [];

        foreach ($selections as $id => $selection) {
            if ($selection['selected'] && !in_array($id, $this->state['roles'])) {
                array_push($this->state['roles'], $id);
            }
        }
    }

    public function render()
    {
        return view('livewire.users.add', [
            'permissions'   => $this->permissionRepository->getAll('name'),
            'roles'         => $this->roleRepository->getAll('name')
        ]);
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

    public function addUser(): void
    {
        if (!access_control()->canAccess(auth()->user(), 'add_user')) {
            throw new AuthorizationException(trans('errors.unauthorized_action', ['action' => 'add user']));
        }

        $validatedData = $this->validate();

        try {
            $user = $this->userRepository->createUser(
                [
                    'name'      => $validatedData['state']['full_name'],
                    'email'     => $validatedData['state']['email'],
                    'password'  => Hash::make($validatedData['state']['password'])
                ],
                $validatedData['state']['permissions'],
                $validatedData['state']['roles']
            );

            if ($validatedData['state']['profile_picture']) {
                $profilePictureFileName = md5($user->id) . '.' . $validatedData['state']['profile_picture']->extension();

                $validatedData['state']['profile_picture']->storeAs(explode('/', config('filesystems.user_profile_image_path'))[1], $profilePictureFileName, $disk = config('filesystems.default'));

                $user->profile_photo_path = config('filesystems.user_profile_image_path') . '/' . $profilePictureFileName;
                $user->save();
            }
        } catch(\Exception $exception) {
            $this->dispatch('toastr', ['type' => 'error', 'message' => $exception->getMessage()]);
        }

        $this->resetFields();

        $this->dispatch('toastr', ['type' => 'confirm', 'message' => trans('notifications.successfull_creation', ['entity' => 'User'])]);
        $this->dispatch(self::ENTITY . '-added');
        $this->dispatch('user-roles-submitted');

        return;
    }

    public function resetFields()
    {
        $this->state['full_name'] = null;
        $this->state['email'] = null;
        $this->state['password'] = null;
        $this->state['password_confirmation'] = null;
        $this->state['profile_picture'] = null;
        $this->state['permissions'] = [];
        $this->state['roles'] = [];

        $this->dispatch('user-permissions-cleared');
    }
}
