<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Url;

class AddUrl extends Component
{
    public function render()
    {
        return view('livewire.add-url');
    }

    public function addUrl()
    {
        // Validation
        $this->validate([
            'originalUrl' => 'required|url'
        ]);

        // Create URL record
        Url::create([
            'original_url' => $this->originalUrl,
            // Add other fields as needed
        ]);

        // Clear input
        $this->originalUrl = '';

        // Emit event for UI update
        $this->emit('urlAdded');

        // Optional: Redirect to a different page after adding the URL
        // return redirect()->route('home');
    }
}
