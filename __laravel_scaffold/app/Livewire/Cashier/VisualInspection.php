<?php

namespace App\Livewire\Cashier;

use App\Services\PythonApiService;
use Livewire\Component;

class VisualInspection extends Component
{
    public ?string $frame = null;

    /**
     * @var array<string, mixed>
     */
    public array $result = [];

    public function inspect(): void
    {
        $this->validate([
            'frame' => ['required', 'string'],
        ]);

        $this->result = app(PythonApiService::class)->inspectPackage($this->frame);
    }

    public function clearResult(): void
    {
        $this->result = [];
        $this->frame = null;
    }

    public function render()
    {
        return view('livewire.cashier.visual-inspection');
    }
}
