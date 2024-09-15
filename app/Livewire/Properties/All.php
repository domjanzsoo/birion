<?php

namespace App\Livewire\Properties;

use App\Livewire\MainList;
use App\Contract\PropertyRepositoryInterface;
use App\Models\Enums\HeatingEnum;

class All extends MainList
{
    public $propertiesToDelete;

    protected $pagination = 6;
    protected $maxRoomNumbers = 6;
    protected $entityRelations = ['images', 'address'];

    const ENTITY = 'property';

    protected $propertyRepository;
    public $filters = [
        'room_number' => [
            'value' => null,
            'condition' => '=',
            'property' => 'room_number',
            'label' => '',
            'options' => []
        ],
        'heating' => [
            'value' => null,
            'condition' => '=',
            'property' => 'heating',
            'label' => '',
            'options' => []
        ]
    ];
    protected $searchFields = ['description', 'address.street', 'address.municipality', 'address.municipality_sub_division', 'address.municipality_secondary_sub_division', 'address.house_name'];
    protected $search;

    protected $listeners = [
        'delete-properties'     => 'deleteProperties',
        'property-added'        => 'refetch',
        'item-selection'        => 'processItemCheck',
        'property-search'       => 'updateSearch'
    ];

    public function updateSearch(string $search = null): void
    {
        $this->search = $search;

        return;
    }

    public function filter($filter, $value)
    {
        $this->filters[$filter]['value'] = $value;

        return;
    }

    public function render()
    {
        $this->authorizeRender();

        return view('livewire.properties.all', [
            'properties' => $this->propertyRepository->getAllPaginated(
                $this->pagination,
                $this->entityRelations,
                ['value' => $this->search, 'searchFields' => $this->searchFields],
                $this->filters
            ),
            'filters' => $this->filters
        ]);
    }

    public function boot(PropertyRepositoryInterface $propertyRepository)
    {
        $this->propertyRepository = $propertyRepository;

        $this->filters['room_number']['options'] = array_combine(range(1, $this->maxRoomNumbers), range(1, $this->maxRoomNumbers));
        $this->filters['room_number']['label'] = trans('properties.rooms');

        $this->filters['heating']['options'] = array_combine(HeatingEnum::toArray(), HeatingEnum::toArray());
        $this->filters['heating']['label'] = trans('properties.heating_type');

        parent::preBoot();
    }

    public function deleteProperties()
    {
        $this->authorizeDelete();

        $properties = array_keys($this->propertiesToDelete, true);

        if (!empty($properties)) {
            $this->propertyRepository->delete($properties);

            $this->propertiesToDelete = [];

            $this->dispatch('toastr', ['type' => 'confirm', 'message' => trans('notifications.successfull_deletion', ['entity' => 'Property'])]);

            return;
        }

        $this->dispatch('toastr', ['type' => 'error', 'message' => trans('notifications.nothing_provided_to_action', ['entity' => 'Property', 'action' => 'delete'])]);

        return;
    }
}
