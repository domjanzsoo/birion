<?php

namespace App\Livewire\Properties;

use App\Contract\PropertyRepositoryInterface;
use Livewire\Component;
use Illuminate\Auth\Access\AuthorizationException;
use App\Models\Enums\HeatingEnum;
use Illuminate\Validation\Rule;

class Add extends Component
{
    private $propertyRepository;
    public $roomNumberOptions = 6;
    
    public array $state = [
        'address'       => '',
        'description'   => '',
        'heating'       => null,
        'room_number'   => 1,
        'location'      => '',
        'country'       => '',
        'size'          => null,
        'photos'        => []
    ];

    public function rules(): array
    {
        return [
            'state.address'     => 'required',
            'state.heating'     => ['required', Rule::enum(HeatingEnum::class)],
            'state.room_number' => 'required|numeric',
            'state.location'    => 'required',
            'state.country'     => 'required',
            'state.size'        => 'requried|numeric',
            'state.description' => 'nullable',
            'state.photos'      => 'array'
        ];
    } 

    public function messages(): array
    {
        return [
            'state.address.required'        => trans('validation.required', ['attribute' => 'address']),
            'state.heating.required'        => trans('validation.required', ['attribute' => 'heating']),
            'state.room_number.required'    => trans('validation.required', ['attribute' => 'room number']),
            'state.room_number.numeric'     => trans('validation.numeric', ['attribute' => 'room number']),
            'state.location.required'       => trans('validation.required', ['attribute' => 'location']),
            'state.size.required'           => trans('validation.required', ['attribute' => 'size']),
            'state.size.numeric'            => trans('validation.numeric', ['attribute' => 'size']),
            'state.country.required'        => trans('validation.required', ['attribute' => 'country']),
            'state.photos.array'            => trans('validation.array', ['attribute' => 'photos']),
        ];
    }

    public function render()
    {
        return view('livewire.properties.add', [
            'roomNumberOptions' => $this->roomNumberOptions,
            'heatingOptions'    => HeatingEnum::toArray()
        ]);
    }

    public function boot(
        PropertyRepositoryInterface $propertyRepository
    )
    {
        $this->propertyRepository = $propertyRepository;
    }

    public function refreshFields()
    {
        $this->state['address'] = '';
        $this->state['location'] = '';
        $this->state['country'] = '';
        $this->state['description'] = '';
        $this->state['heating'] = null;
        $this->state['room_number'] = null;
        $this->state['size'] = null;
        $this->state['photos'] = [];
    }


    public function addProperty(): void
    {
        if (!access_control()->canAccess(auth()->user(), 'add_property')) {
            throw new AuthorizationException(trans('errors.unauthorized_action', ['action' => 'add property']));
        }

        $validatedData = $this->validate();

        $this->propertyRepository->create($validatedData['state']);

        $this->refreshFields();

        $this->dispatch('toastr', ['type' => 'confirm', 'message' => trans('notifications.successfull_creation', ['entity' => 'Property'])]);
        
        $this->dispatch('property-added');

        return;
    }
}
