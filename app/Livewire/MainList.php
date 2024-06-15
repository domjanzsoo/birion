<?php

namespace App\Livewire;

use Livewire\Component;
use Exception;
use Livewire\WithPagination;
use Illuminate\Auth\Access\AuthorizationException;

class MainList extends Component
{
    use WithPagination;

    public $deleteButtonAccess = false;
    protected $pagination = 5;

    private $entity;

    protected $listeners = [];
    
    private $itemsNaming;
    private $itemRepositoryNaming;
    private $itemDeletetionArrayNaming;

    public function preBoot()
    {
        $class = get_called_class();

        if (is_null($class::ENTITY)) {
            throw new Exception(trans('errors.missing_validation'));
        }

        $this->entity = $class::ENTITY;

        $this->itemsNaming = $class::ENTITY . 's';
        $this->itemRepositoryNaming = $class::ENTITY . 'Repository';
        $this->itemDeletetionArrayNaming = $class::ENTITY . 'sToDelete';
    }

    protected function authorizeRender()
    { 
        if (!access_control()->canAccess(auth()->user(), ['view_' . $this->entity . 's', 'add_' . $this->entity, 'edit_' . $this->entity])) {
            throw new AuthorizationException(trans('errors.unauthorized_action', ['action' => 'view ' . $this->entity]));
        }

        return;
    }

    protected function authorizeDelete()
    {
        if (!access_control()->canAccess(auth()->user(), 'delete_' . $this->entity)) {
            throw new AuthorizationException(trans('errors.unauthorized_action', ['action' => 'delete ' . $this->entity]));
        }

        return;
    }

    public function paginationView()
    {
        return 'components.pagination-links';
    }


    public function processItemCheck(string $entity, array $items)
    {
        if (!access_control()->canAccess(auth()->user(), 'delete_' . $this->entity)) {
            throw new AuthorizationException(trans('errors.unauthorized_action', ['action' => 'delete ' . $this->entity]));
        }

        if ($entity === $this->entity) {
            $buttonDisable = false;

            foreach ($items as $permission) {
                if ($permission) {
                    $buttonDisable = true;
                }
            }
    
            $this->deleteButtonAccess = $buttonDisable;
            $this->{$this->itemDeletetionArrayNaming} = $items;
        }

        return;
    }

    public function refetch()
    {
        $this->{$this->itemsNaming} = $this->{$this->itemRepositoryNaming}->getAllPaginated($this->pagination);
    }
}
