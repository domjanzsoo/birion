<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use App\Contract\UserRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Livewire\WithFileUploads;

class Add extends Component
{
    use WithFileUploads;

    private $userRepository;

    public $test;
    public array $state = [
        'full_name'             => '',
        'email'                 => '',
        'password'              => '',
        'password_confirmation' => '',
        'verified'              => false,
        'profile_picture'       => null
    ];

    protected $rules = [
        'state.full_name'               => 'required',
        'state.email'                   => 'required|email|unique:users,email',
        'state.password'                => 'required|confirmed|min:6|max:20',
        'state.password_confirmation'   => 'required'
    ];

    public function messages()
    {
        return [
            'state.permission_name.required' => trans('validation.required', ['attribute' => 'name']),
            'state.email.required' => trans('validation.required', ['attribute' => 'email']),
            'state.email.unique' => trans('validation.unique', ['attribute' => 'user email']),
            'state.email.email' => trans('validation.email', ['attribute' => 'user email']),
            'state.password.required' => trans('validation.required', ['attribute' => 'user email']),
            'state.password.confirmed' => trans('validation.confirmed', ['attribute' => 'password'])
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

        $this->userRepository->create(
            [
                'name'      => $validatedData['state']['full_name'],
                'email'     => $validatedData['state']['email'],
                'password'  => Hash::make($validatedData['password'])
            ]
        );

        $this->state['full_name'] = null;
        $this->state['email'] = null;
        $this->state['password'] = null;

        $this->dispatch('toastr', ['type' => 'confirm', 'message' => trans('notifications.successfull_creation', ['entity' => 'User'])]);
        $this->dispatch('user-added');

        return;
    }
}
