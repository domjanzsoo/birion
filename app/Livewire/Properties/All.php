<?php

namespace App\Livewire\Properties;

use App\Livewire\MainList;
use App\Contract\PropertyRepositoryInterface;

class All extends MainList
{
    public $propertiesToDelete;

    protected $pagination = 6;
    protected $entityRelations = ['images', 'address'];

    const ENTITY = 'property';

    protected $propertyRepository;
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

    public function getPropertiesProperty()
    {
        return $this->propertyRepository->getAllPaginated($this->pagination, $this->entityRelations, $this->search);
    }

    public function render()
    {
        $this->authorizeRender();

        return view('livewire.properties.all', ['properties' => $this->propertyRepository->getAllPaginated($this->pagination, $this->entityRelations, ['value' => $this->search, 'searchFields' => $this->searchFields])]);
    }

    public function boot(PropertyRepositoryInterface $propertyRepository)
    {
        $this->propertyRepository = $propertyRepository;

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
