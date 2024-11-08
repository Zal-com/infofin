<?php

namespace App\Livewire;

use App\Models\Activity;
use App\Models\Continent;
use App\Models\Country;
use App\Models\Expense;
use App\Models\InfoSession;
use App\Models\Organisation;
use App\Models\ScientificDomain;
use Filament\Notifications\Notification;
use Livewire\Component;

class ProjectPreview extends Component
{
    public $data = [];
    public $geoZones = [];
    public $contactUlbs = [];
    public $contactExts = [];
    public $organisation;
    public $scientificDomains = [];
    public $expenses = [];
    public $activities = [];
    public $info_sessions = [];

    public function mount()
    {
        if (session()->has('previewData')) {
            $this->data = session('previewData');
            $this->organisation = Organisation::find($this->data["organisation_id"]);
            $this->scientificDomains = ScientificDomain::find($this->data["scientific_domains"]);
            $this->activities = Activity::find($this->data["activities"]);
            $this->expenses = Expense::find($this->data["expenses"]);
            $this->info_sessions = InfoSession::find($this->data["infos_sessions"]);
            $this->transformGeoZones();
            $this->transformContacts();
        } else {
            Notification::make()
                ->title("Oops, fallait pas refresh, recommencez.")
                ->color('danger')
                ->seconds(5)
                ->send();
            return redirect()->to('/projects');
        }
    }

    private function transformGeoZones()
    {
        $geoZones = $this->data['geo_zones'] ?? [];
        foreach ($geoZones as $zone) {
            if (strpos($zone, 'continent_') === 0) {
                $continent_id = str_replace('continent_', '', $zone);
                $this->geoZones[] = Continent::find($continent_id)->name ?? $zone;
            } elseif (strpos($zone, 'pays_') === 0) {
                $country_id = str_replace('pays_', '', $zone);
                $this->geoZones[] = Country::find($country_id)->nomPays ?? $zone;
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

    public function return()
    {
        session()->forget('previewData');
        session()->flash('fromPreviewData', $this->data);
        return redirect()->route('projects.create');
    }

    public function render()
    {
        return view('livewire.project-preview');
    }
}
