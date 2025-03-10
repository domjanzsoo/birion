<?php

namespace App\Livewire\Properties;

use App\Contract\PropertyRepositoryInterface;
use App\Contract\AddressRepositoryInterface;
use Livewire\Component;
use Illuminate\Auth\Access\AuthorizationException;
use App\Models\Enums\HeatingEnum;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;
use App\Models\Image;
use App\Services\TomtomService;
use Illuminate\Support\Facades\DB;

class Add extends Component
{
    use WithFileUploads;

    private $propertyRepository;
    private $addressRepository;
    private $tomTomService;
    public $roomNumberOptions = 6;
    public $addressOptions = [];
    public $selectedAddress;
    public $checkedImageIndex;
    public $imageSelectionEvent = 'check-main-property-image';

    public array $state = [
        'street_number' => '',
        'street'        => '',
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
            'state.street_number'   => 'required',
            'state.street'          => 'required',
            'state.heating'         => ['required', Rule::enum(HeatingEnum::class)],
            'state.room_number'     => 'required|numeric',
            'state.location'        => 'required',
            'state.country'         => 'required',
            'state.size'            => 'required|numeric',
            'state.description'     => 'nullable',
            'state.pictures'        => 'array',
            'state.picrures.*'      => 'image|max:2048|nullable'
        ];
    }

    public function messages(): array
    {
        return [
            'state.street_number.required'  => trans('validation.required', ['attribute' => 'street number/house']),
            'state.street.required'         => trans('validation.required', ['attribute' => 'street']),
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

    public $listeners = [
        'property-picture-empty'    => 'clearPropertyPictures',
        'address-selected'          => 'handleStreetSelection',
    ];


    public function updatedStateStreet(): void
    {
        $this->addressOptions = (strlen($this->state['street']) > 3) ? $this->tomTomService->search($this->state['street_number'] . ' ' . $this->state['street']) : [];
    }

    public function handleStreetSelection(int $addressOptionIndex): void
    {
        $option = $this->addressOptions[$addressOptionIndex];

        $this->state['street'] = $option->address->streetName;
        $this->state['location'] = $option->address->municipality ?? $option->address->municipalitySubdivision ?? $option->address->countrySecondarySubdivision ?? null;
        $this->state['country'] = $option->address->country;

        $this->selectedAddress = $option;
        $this->addressOptions = [];

        return;
    }

    public function handleImageCheck(?int $itemIndex): void
    {
        $this->checkedImageIndex = $itemIndex;
    }

    public function render()
    {
        return view('livewire.properties.add', [
            'roomNumberOptions'     => $this->roomNumberOptions,
            'heatingOptions'        => HeatingEnum::toArray(),
            'imageCheckEvent'       => $this->imageSelectionEvent
        ]);
    }

    public function boot(
        PropertyRepositoryInterface $propertyRepository,
        AddressRepositoryInterface $addressRepository,
        TomtomService $tomTomService
    )
    {
        $this->propertyRepository = $propertyRepository;
        $this->addressRepository = $addressRepository;
        $this->tomTomService = $tomTomService;
    }

    public function mount(): void
    {
        $this->listeners[$this->imageSelectionEvent] = 'handleImageCheck';
    }

    public function refreshFields()
    {
        $this->state['street_number'] = '';
        $this->state['street'] = '';
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

        try {
            DB::beginTransaction();

            $address = $this->addressRepository->create([
                'street' => $validatedData['state']['street'],
                'municipality' => $this->selectedAddress->address->municipality ?? $this->selectedAddress->address->municipalitySubdivision ?? null,
                'municipality_sub_division' => $this->selectedAddress->address->municipalitySubdivision ?? null,
                'municipality_secondary_sub_division' => $this->selectedAddress->address->municipalitySecondarySubdivision ?? null,
                'country' => $validatedData['state']['country'],
                'post_code' => $this->selectedAddress->address->postalCode ?? null,
                'lat' => $this->selectedAddress->position->lat,
                'lon' => $this->selectedAddress->position->lon,
                'house_number' => preg_match("/^\d+$/", $validatedData['state']['street_number']) ? $validatedData['state']['street_number'] : null,
                'house_name' => !preg_match("/^\d+$/", $validatedData['state']['street_number']) ? $validatedData['state']['street_number'] : null
            ]);

            $property = $this->propertyRepository->createProperty($validatedData['state'], $address);

            if (count($validatedData['state']['pictures']) > 0) {
                foreach ($validatedData['state']['pictures'] as $index => $picture) {
                    $profilePictureFileName = $property->id . '-property-' . md5($picture->getClientOriginalName());

                    $picture->storeAs(explode('/', config('filesystems.property_image_path'))[1], $profilePictureFileName , $disk = config('filesystems.default'));

                    $image = new Image([
                        'name' => $profilePictureFileName,
                        'file_route' => config('filesystems.property_image_path') . '/' . $profilePictureFileName
                    ]);

                    if ((!isset($this->checkedImageIndex) && $index === 0) || $this->checkedImageIndex === $index) {
                        $image->main_image = true;
                    }

                    $property->images()->save($image);
                }
            }

            $this->refreshFields();

            $this->dispatch('toastr', ['type' => 'confirm', 'message' => trans('notifications.successfull_creation', ['entity' => 'Property'])]);

            $this->dispatch('property-added');

            DB::commit();
            return;
        } catch(\Exception $exception) {
            DB::rollBack();
            $this->dispatch('toastr', ['type' => 'error', 'message' => $exception->getMessage()]);
        }
    }
}
