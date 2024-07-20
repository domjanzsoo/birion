<?php

namespace App\Livewire\Properties;

use App\Contract\PropertyRepositoryInterface;
use Livewire\Component;
use Illuminate\Auth\Access\AuthorizationException;
use App\Models\Enums\HeatingEnum;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;
use App\Models\Image;

class Add extends Component
{
    use WithFileUploads;
    
    private $propertyRepository;
    public $roomNumberOptions = 6;
    
    public array $state = [
        'address'       => '',
        'description'   => '',
        'heating'       => null,
        'room_number'   => null,
        'location'      => '',
        'country'       => '',
        'size'          => null,
        'pictures'      => []
    ];

    public function rules(): array
    {
        return [
            'state.address'     => 'required',
            'state.heating'     => ['required', Rule::enum(HeatingEnum::class)],
            'state.room_number' => 'required|numeric',
            'state.location'    => 'required',
            'state.country'     => 'required',
            'state.size'        => 'required|numeric',
            'state.description' => 'nullable',
            'state.pictures'    => 'array',
            'state.picrures.*'  => 'image|max:2048|nullable'
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
            'state.pictures.array'          => trans('validation.array', ['attribute' => 'photos']),
            'state.pictures.*.image'        => trans('validation.image', ['attribute' => 'profile picture']),
            'state.pictures.*.max'          => trans('validation.max.file', ['max' => '2048', 'attribute' => 'profile picture']),
        ];
    }

    public function render()
    {
        return view('livewire.properties.add', [
            'roomNumberOptions' => $this->roomNumberOptions,
            'heatingOptions'    => HeatingEnum::toArray()
        ]);
    }

    protected $listeners = [
        'property-picture-empty' => 'clearPropertyPictures'
    ];

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
        $this->state['pictures'] = [];
    }

    public function clearPropertyPictures()
    {
        $this->state['pictures'] = [];
    }


    public function addProperty(): void
    {
        if (!access_control()->canAccess(auth()->user(), 'add_property')) {
            throw new AuthorizationException(trans('errors.unauthorized_action', ['action' => 'add property']));
        }

        $validatedData = $this->validate();

        $fileDirectories = explode('/', config('filesystems.image_path'));

        // dd($validatedData);
        try {
            $property = $this->propertyRepository->create($validatedData['state']);

            if (count($validatedData['state']['pictures']) > 0) {
                foreach ($validatedData['state']['pictures'] as $picture) {
                    $profilePictureFileName = md5($property->id . '-property');
    
                    $picture->storeAs($fileDirectories[count($fileDirectories) - 1], $profilePictureFileName . '.' . $picture->extension(), $disk = config('filesystems.default'));
        
                    $image = new Image([
                        'name' => $profilePictureFileName,
                        'file_route' => config('filesystems.user_profile_image_path') . '/' . $profilePictureFileName . '.' . $picture->extension() 
                    ]);

                    $property->images()->save($image);
                }
            }

            $this->refreshFields();

            $this->dispatch('toastr', ['type' => 'confirm', 'message' => trans('notifications.successfull_creation', ['entity' => 'Property'])]);
            
            $this->dispatch('property-added');

            return;
        } catch(\Exception $exception) {
            dd($exception->getMessage());
            $this->dispatch('toastr', ['type' => 'error', 'message' => $exception->getMessage()]);
        }
    }
}
