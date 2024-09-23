<?php

namespace App\Livewire;

use App\Models\Continent;
use App\Models\Countries;
use App\Models\InfoType;
use App\Models\Organisation;
use App\Models\Project;
use App\Models\ScientificDomain;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class ProjectPreview extends Component
{
    public $data = [];
    public $geoZones = [];
    public $contactUlbs = [];
    public $contactExts = [];
    public $organisation;
    public $scientificDomains = [];
    public $info_types = [];

    public function mount()
    {
        if (session()->has('previewData')) {
            $this->data = session('previewData');
            $this->organisation = Organisation::find($this->data["organisation_id"]);
            $this->scientificDomains = ScientificDomain::find($this->data["scientific_domains"]);
            $this->info_types = InfoType::find($this->data["info_types"]);
            $this->transformGeoZones();
            $this->transformContacts();
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

    public function create()
    {
        $userId = Auth::id();

        $rules = [
            'title' => 'required|string|max:255',
            'is_big' => 'boolean',
            'organisation_id' => 'required|exists:organisations,id',
            'info_types' => 'array',
            'docs' => 'array',
            'scientific_domains' => 'array',
            'geo_zones' => 'array',
            'deadline' => 'nullable|date|required_if:continuous,false',
            'proof' => 'nullable|string|max:50',
            'continuous' => 'boolean',
            'deadline_2' => 'nullable|date|required_if:continuous_2,false',
            'proof_2' => 'nullable|string|max:50',
            'continuous_2' => 'boolean',
            'date_lessor' => 'nullable|date',
            'short_description' => 'nullable|string|max:500',
            'long_description' => 'nullable|string',
            'funding' => 'nullable|string',
            'admission_requirements' => 'nullable|string',
            'apply_instructions' => 'nullable|string',
            'contact_ulb.*.first_name' => 'nullable|string',
            'contact_ulb.*.last_name' => 'nullable|string',
            'contact_ulb.*.email' => 'nullable|email',
            'contact_ulb.*.tel' => 'nullable|phone:BE',
            'contact_ulb.*.address' => 'nullable|string',
            'contact_ext.*.first_name' => 'nullable|string|max:50',
            'contact_ext.*.last_name' => 'nullable|string|max:50',
            'contact_ext.*.email' => 'nullable|email|max:255',
            'contact_ext.*.tel' => 'nullable|phone:BE',
            'status' => 'integer',
            'is_draft' => 'boolean',
        ];

        $validator = Validator::make($this->data, $rules, [], [
            'title' => 'Titre',
            'is_big' => 'Projet Majeur',
            'organisation_id' => 'Organisation',
            'info_types' => 'Types de programme',
            'scientific_domains' => 'Disciplines scientifiques',
            'geo_zones' => 'Zones géographiques',
            'deadline' => 'Deadline 1',
            'proof' => 'Justificatif de la 1ere deadline',
            'continuous' => 'Continu',
            'deadline_2' => 'Deadline 2',
            'proof_2' => 'Justificatif de la 2nde deadline',
            'continuous_2' => 'Continu',
            'date_lessor' => 'Date Bailleur',
            'short_description' => 'Description courte',
            'long_description' => 'Description longue',
            'funding' => 'Financement',
            'admission_requirements' => 'Requis d\'admission',
            'apply_instructions' => 'Instructions d\'application',
            'contact_ulb.*.first_name' => 'Prénom',
            'contact_ulb.*.last_name' => 'Nom',
            'contact_ulb.*.email' => 'Email',
            'contact_ulb.*.tel' => 'Téléphone',
            'contact_ulb.*.address' => 'Addresse',
            'contact_ext.*.first_name' => 'Prénom',
            'contact_ext.*.last_name' => 'Nom',
            'contact_ext.*.email' => 'Email',
            'contact_ext.*.tel' => 'Téléphone',
            'status' => 'Status',
            'is_draft' => 'Brouillon',
        ]);

        if ($validator->fails()) {
            Notification::make()->title($validator->errors()->all())->icon('heroicon-o-x-circle')->seconds(5)->color('danger')->send();
            return redirect()->back();
        } else {
            $data = $validator->validated();
        }

        $data['poster_id'] = $userId;
        $data['last_update_user_id'] = $userId;

        $contactsUlB = [];
        if (isset($data['contact_ulb'])) {
            foreach ($data['contact_ulb'] as $contact) {
                $name = trim(($contact['first_name'] ?? '') . ' ' . ($contact['last_name'] ?? ''));
                $email = $contact['email'] ?? '';
                $phone = $contact['tel'] ?? '';
                $address = $contact['address'] ?? '';

                if ($name !== '' || $email !== '' || $phone !== '' || $address !== '') {
                    $contactsUlB[] = [
                        'name' => $name,
                        'email' => $email,
                        'phone' => $phone,
                        'address' => $address,
                    ];
                }
            }
            $data['contact_ulb'] = !empty($contactsUlB) ? json_encode($contactsUlB) : '[]';
        } else {
            $data['contact_ulb'] = '[]';
        }

        $contactsExt = [];
        if (isset($data["contact_ext"])) {
            foreach ($data['contact_ext'] as $contact) {
                $name = trim(($contact['first_name'] ?? '') . ' ' . ($contact['last_name'] ?? ''));
                $email = $contact['email'] ?? '';
                $phone = $contact['tel'] ?? '';
                $address = $contact['address'] ?? '';

                if ($name !== '' || $email !== '' || $phone !== '' || $address !== '') {
                    $contactsExt[] = [
                        'name' => $name,
                        'email' => $email,
                        'phone' => $phone,
                        'address' => $address,
                    ];
                }
            }
            $data['contact_ext'] = !empty($contactsExt) ? json_encode($contactsExt) : '[]';
        } else {
            $data['contact_ext'] = '[]';
        }

        if ($project = Project::create($data)) {
            if (!empty($data['info_types'])) {
                $project->info_types()->sync($data['info_types']);
            }

            if (!empty($data['scientific_domains'])) {
                $project->scientific_domains()->sync($data['scientific_domains']);
            }

            if (isset($data['docs']) && count($data['docs']) > 0) {
                $data['docs'] = $this->moveFiles($data['docs']);
            }

            if (!empty($data['geo_zones'])) {
                foreach ($data['geo_zones'] as $zone) {
                    if (strpos($zone, 'continent_') === 0) {
                        $continent_id = str_replace('continent_', '', $zone);
                        $project->continent()->associate($continent_id);
                    } elseif (strpos($zone, 'pays_') === 0) {
                        $country_id = str_replace('pays_', '', $zone);
                        $project->country()->associate($country_id);
                    }
                }

                $project->save();
            }

            Notification::make()->title('Votre appel a bien été ajouté')->icon('heroicon-o-check-circle')->seconds(5)->color('success')->send();

            return redirect()->route('projects.index');
        }
    }

    public function return()
    {
        session()->flash('fromPreviewData', $this->data);
        return redirect()->route('projects.create');
    }

    public function render()
    {
        return view('livewire.project-preview');
    }
}
