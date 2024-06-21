<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use App\Contract\UserRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Add extends Component
{
    use WithFileUploads;

    private $userRepository;

    public $test;
    public array $state = [
        'full_name'             => null,
        'email'                 => null,
        'password'              => null,
        'password_confirmation' => null,
        'verified'              => false,
        'profile_picture'       => null
    ];

    protected $rules = [
        'state.full_name'               => 'required',
        'state.email'                   => 'required|email|unique:users,email',
        'state.password'                => 'required|confirmed|min:6|max:2048',
        'state.password_confirmation'   => 'required',
        'state.profile_picture'         => 'image|max:2048'
    ];
    
    public function messages()
    {
        return [
            'state.permission_name.required' => trans('validation.required', ['attribute' => 'name']),
            'state.email.required' => trans('validation.required', ['attribute' => 'email']),
            'state.email.unique' => trans('validation.unique', ['attribute' => 'user email']),
            'state.email.email' => trans('validation.email', ['attribute' => 'user email']),
            'state.password.required' => trans('validation.required', ['attribute' => 'user email']),
            'state.password.confirmed' => trans('validation.confirmed', ['attribute' => 'password']),
            'state.profile_picture.image' => trans('validation.image', ['attribute' => 'profile picture']),
            'state.profile_picture.max' => trans('validation.max.file', ['max' => '1024', 'attribute' => 'profile picture'])
        ];
    }

    public function updatedStatePassword($value)
    {
        $this->validateOnly('state.password');
    }

    public function updatedStatePasswordConfirmation($value)
    {
        $this->validateOnly('state.password');
    }

    public function render()
    {
        return view('livewire.users.add');
    }

    public function boot(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function addUser()
    {
        if (!access_control()->canAccess(auth()->user(), 'add_user')) {
            throw new AuthorizationException(trans('errors.unauthorized_action', ['action' => 'add user']));
        }

        $validatedData = $this->validate();

        $user = $this->userRepository->create(
            [
                'name'      => $validatedData['state']['full_name'],
                'email'     => $validatedData['state']['email'],
                'password'  => Hash::make($validatedData['state']['password'])
            ]
        );

        if ($this->state['profile_picture']) {
            $profilePictureFileName = md5($user->id) . '.' . $this->state['profile_picture']->extension();

            $this->state['profile_picture']->storeAs('/avatar', $profilePictureFileName, $disk = config('filesystems.default'));

            $user->profile_photo_path = 'storage/avatar/' . $profilePictureFileName;
            $user->save();
        }

        $this->state['full_name'] = null;
        $this->state['email'] = null;
        $this->state['password'] = null;
        $this->state['password_confirmation'] = null;
        $this->state['profile_picture'] = null;

        $this->dispatch('toastr', ['type' => 'confirm', 'message' => trans('notifications.successfull_creation', ['entity' => 'User'])]);
        $this->dispatch('user-added');

        return;
    }
}
