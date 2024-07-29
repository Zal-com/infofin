<?php

namespace App\Livewire;

use App\Models\Continent;
use App\Models\Countries;
use Livewire\Component;

class ProjectPreview extends Component
{
    public $data = [];
    public $geoZones = [];
    public $contactUlbs = [];
    public $contactExts = [];

    public function mount()
    {
        if (session()->has('previewData')) {
            $this->data = session('previewData');
            //todo : change organisation, on get l'id, il faut get les organisation, pareil pour scientific_domains
            $this->transformGeoZones();
            $this->transformContacts();
        }
    }

    private function transformGeoZones()
    {
        $geoZones = $this->data['Geo_zones'] ?? [];
        foreach ($geoZones as $zone) {
            if (strpos($zone, 'continent_') === 0) {
                $continent_id = str_replace('continent_', '', $zone);
                $this->geoZones[] = Continent::find($continent_id)->name ?? $zone;
            } elseif (strpos($zone, 'pays_') === 0) {
                $country_id = str_replace('pays_', '', $zone);
                $this->geoZones[] = Countries::find($country_id)->nomPays ?? $zone;
            }
        }
    }

    private function transformContacts()
    {
        $this->contactUlbs = [];
        if (!empty($this->data['contact_ulb'])) {
            foreach ($this->data['contact_ulb'] as $contact) {
                $name = trim(($contact['first_name'] ?? '') . ' ' . ($contact['last_name'] ?? ''));
                $this->contactUlbs[] = array_merge($contact, ['name' => $name]);
            }
        }

        $this->contactExts = [];
        if (!empty($this->data['contact_ext'])) {
            foreach ($this->data['contact_ext'] as $contact) {
                $name = trim(($contact['first_name'] ?? '') . ' ' . ($contact['last_name'] ?? ''));
                $this->contactExts[] = array_merge($contact, ['name' => $name]);
            }
        }
    }

    public function render()
    {
        return view('livewire.project-preview');
    }
}


