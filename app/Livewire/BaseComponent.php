<?php

namespace App\Livewire;

use Livewire\Component;

class BaseComponent extends Component
{
    public $toastMessage = [
        'message'   => null,
        'type'      => null
    ];

    public function setToastMessage(string $msg, string $type): void
    {
        $this->toastMessage = [
            'message'   => $msg,
            'type'      => $type
        ];

        $this->dispatch('toastr', ['type' => 'confirm', 'message' => 'Permission deleted successfully!']);
    }

    public function clearToast(): void
    {
        $this->toastMessage = [
            'message'   => null,
            'type'      => null
        ];
    }
}