<?php

namespace App\Livewire;

use App\Models\Continent;
use App\Models\Country;
use App\Models\Expense;
use App\Models\InfoSession;
use App\Models\InfoType;
use App\Models\Organisation;
use App\Models\Project;
use App\Models\ScientificDomain;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use League\HTMLToMarkdown\HtmlConverter;
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
            $this->activities = InfoType::find($this->data["activities"]);
            $this->expenses = Expense::find($this->data["expenses"]);
            $this->info_sessions = InfoSession::find($this->data["info_sessions"]);
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

    public function create()
    {
        $userId = Auth::id();

        $rules = [
            'title' => 'required|string|max:255',
            'is_big' => 'boolean',
            'organisation_id' => 'required|exists:organisations,id',
            "expenses" => 'array',
            'activities' => 'array',
            'documents' => 'array',
            'scientific_domains' => 'required|array|min:1',
            'scientific_domains.*' => 'integer|exists:scientific_domains,id',
            'geo_zones' => 'array',
            'deadlines' => 'array',
            'short_description' => 'nullable|string|max:500',
            'long_description' => 'array',
            'funding' => 'array|nullable',
            'admission_requirements' => 'array|nullable',
            'apply_instructions' => 'array|nullable',
            'contact_ulb' => 'array',
            'contact_ulb.*.first_name' => 'nullable|string',
            'contact_ulb.*.last_name' => 'nullable|string',
            'contact_ulb.*.email' => 'nullable|email',
            'contact_ext' => 'array',
            'contact_ext.*.first_name' => 'nullable|string|max:50',
            'contact_ext.*.last_name' => 'nullable|string|max:50',
            'contact_ext.*.email' => 'nullable|email|max:255',
            'status' => 'integer',
            'is_draft' => 'boolean',
            'info_sessions' => 'nullable|array'
        ];

        $validator = Validator::make($this->data, $rules, [], [
            'title' => 'Titre',
            'is_big' => 'Projet Majeur',
            'organisation_id' => 'Organisation',
            'activities' => 'Catégorie d\'activités',
            'expenses' => 'Catégorie de dépenses éligibles',
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

        $messages = [
            'title.required' => 'Le titre est requis.',
            'title.string' => 'Le titre doit être une chaîne de caractères.',
            'title.max' => 'Le titre ne peut pas dépasser :max caractères.',
            'is_big.boolean' => 'Le champ "Projet Majeur" doit être vrai ou faux.',
            'organisation_id.required' => 'Le champ Organisation est requis.',
            'organisation_id.exists' => 'L\'organisation sélectionnée n\'existe pas.',
            'activities.array' => 'Les catégories d\'activité doivent être remplis.',
            'expenses.array' => 'Les catégories de dépenses éligibles doivent être remplis.',
            'documents.array' => 'Les documents doivent être remplis.',
            'scientific_domains.array' => 'Les disciplines scientifiques doivent être remplies.',
            'scientific_domains.required' => 'Veuillez sélectionner au moins une discipline scientifique.',
            'scientific_domains.min' => 'Veuillez sélectionner au moins une discipline scientifique.',
            'scientific_domains.*.integer' => 'Chaque discipline scientifique sélectionnée doit être valide.',
            'scientific_domains.*.exists' => 'La discipline scientifique sélectionnée est invalide.',
            'geo_zones.array' => 'Les zones géographiques doivent être remplies.',
            'deadlines.array' => 'Les deadlines doivent être remplies.',
            'short_description.string' => 'La description courte doit être une chaîne de caractères.',
            'short_description.max' => 'La description courte ne peut pas dépasser :max caractères.',
            'long_description.array' => 'La description longue doit être remplie.',
            'funding.array' => 'Le champs "Budget & dépenses" doit être rempli.',
            'apply_instructions.array' => 'Les instructions pour postuler doivent être remplis.',
            'contact_ulb.*.first_name.string' => 'Le prénom du contact interne doit être une chaîne de caractères.',
            'contact_ulb.*.last_name.string' => 'Le nom du contact interne doit être une chaîne de caractères.',
            'contact_ulb.*.email.email' => 'L\'email du contact interne doit être une adresse email valide.',
            'contact_ext.*.first_name.string' => 'Le prénom du contact externe doit être une chaîne de caractères.',
            'contact_ext.*.first_name.max' => 'Le prénom du contact externe ne peut pas dépasser :max caractères.',
            'contact_ext.*.last_name.string' => 'Le nom du contact externe doit être une chaîne de caractères.',
            'contact_ext.*.last_name.max' => 'Le nom du contact externe ne peut pas dépasser :max caractères.',
            'contact_ext.*.email.email' => 'L\'email du contact externe doit être une adresse email valide.',
            'contact_ext.*.email.max' => 'L\'email du contact externe ne peut pas dépasser :max caractères.',
            'at_least_one_contact' => 'Veuillez fournir au moins un contact interne ou externe.',
            'status.integer' => 'Le statut doit être un nombre entier.',
            'is_draft.boolean' => 'Le champ "Brouillon" doit être vrai ou faux.',
            'info_sessions.array' => 'Les séances d\'informations doivent être remplies.'
        ];
        $validator = Validator::make($this->data, $rules, $messages, [
            'title' => 'Titre',
            'is_big' => 'Projet Majeur',
            'organisation_id' => 'Organisation',
            'activities' => 'Catégorie d\'activités',
            'expenses' => 'Catégorie de dépenses éligibles',
            'scientific_domains' => 'Disciplines scientifiques',
            'geo_zones' => 'Zones géographiques',
            'deadlines' => 'Deadlines',
            // 'date_lessor' => 'Date Bailleur',
            'short_description' => 'Description courte',
            'long_description' => 'Description longue',
            'funding' => 'Budget et dépenses',
            'admission_requirements' => 'Critères d\'admission',
            'apply_instructions' => 'Pour postuler',
            'contact_ulb.*.first_name' => 'Prénom',
            'contact_ulb.*.last_name' => 'Nom',
            'contact_ulb.*.email' => 'Email',
            'contact_ext.*.first_name' => 'Prénom',
            'contact_ext.*.last_name' => 'Nom',
            'contact_ext.*.email' => 'Email',
            'status' => 'Status',
            'is_draft' => 'Brouillon',
            'info_sessions' => 'Séance d\'informations'
        ]);
        $validator->after(function ($validator) {
            $contact_ulb = $this->data['contact_ulb'] ?? [];
            $contact_ext = $this->data['contact_ext'] ?? [];

            // Filtrer les contacts vides
            $contact_ulb = array_filter($contact_ulb, function ($contact) {
                return !empty($contact['first_name']) || !empty($contact['last_name']) || !empty($contact['email']);
            });

            $contact_ext = array_filter($contact_ext, function ($contact) {
                return !empty($contact['first_name']) || !empty($contact['last_name']) || !empty($contact['email']);
            });

            $ulb_has_contact = !empty($contact_ulb);
            $ext_has_contact = !empty($contact_ext);

            if (!$ulb_has_contact && !$ext_has_contact) {
                $validator->errors()->add('contact_ulb', 'Veuillez fournir au moins un contact interne ou externe.');
            }
        });

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                Notification::make()->title($error)->icon('heroicon-o-x-circle')->seconds(5)->color('danger')->send();
            }
        } else {
            $data = $validator->validated();
            $converter = new HtmlConverter();
            $markdown = $converter->convert($this->data["short_description"]);

            $data['short_description'] = $markdown;

            $data['poster_id'] = $userId;
            $data['last_update_user_id'] = $userId;

            $contactsUlB = [];
            if (isset($data['contact_ulb'])) {
                foreach ($data['contact_ulb'] as $contact) {
                    $name = trim(($contact['first_name'] ?? '') . ' ' . ($contact['last_name'] ?? ''));
                    $email = $contact['email'] ?? '';

                    if ($name !== '' || $email !== '') {
                        $contactsUlB[] = [
                            'name' => $name,
                            'email' => $email,
                        ];
                    }
                }
                $data['contact_ulb'] = !empty($contactsUlB) ? $contactsUlB : [];
            } else {
                $data['contact_ulb'] = [];
            }


            $contactsExt = [];
            if (isset($data["contact_ext"])) {
                foreach ($data['contact_ext'] as $contact) {
                    $name = trim(($contact['first_name'] ?? '') . ' ' . ($contact['last_name'] ?? ''));
                    $email = $contact['email'] ?? '';

                    if ($name !== '' || $email !== '') {
                        $contactsExt[] = [
                            'name' => $name,
                            'email' => $email,
                        ];
                    }
                }
                $data['contact_ext'] = !empty($contactsExt) ? $contactsExt : [];
            } else {
                $data['contact_ext'] = [];
            }

            if ($project = Project::create($data)) {
                if (!empty($data['expenses'])) {
                    $project->expenses()->sync($data['expenses']);
                }

                if (!empty($data['activities'])) {
                    $project->activities()->sync($data['activities']);
                }

                if (!empty($data['scientific_domains'])) {
                    $project->scientific_domains()->sync($data['scientific_domains']);
                }

                if (!empty($data['info_sessions'])) {
                    $project->info_sessions()->sync($data['info_sessions']);
                }

                if (isset($data['documents']) && count($data['documents']) > 0) {
                    $this->fileService->moveFiles($data['documents'], $project);
                }

                if (!empty($data['geo_zones'])) {
                    $continentIds = [];
                    $countryIds = [];

                    foreach ($data['geo_zones'] as $zone) {
                        if (strpos($zone, 'continent_') === 0) {
                            $continent_code = str_replace('continent_', '', $zone); // Extraire le code du continent
                            $continentIds[] = $continent_code; // Ajouter à la liste des continents
                        } elseif (strpos($zone, 'pays_') === 0) {
                            $country_id = str_replace('pays_', '', $zone); // Extraire l'ID du pays
                            $countryIds[] = $country_id; // Ajouter à la liste des pays
                        }
                    }

                    // Synchroniser les continents associés au projet (Many-to-Many)
                    if (!empty($continentIds)) {
                        $project->continents()->sync($continentIds); // Synchroniser les continents du projet
                    }

                    // Synchroniser les pays associés au projet (Many-to-Many)
                    if (!empty($countryIds)) {
                        $project->countries()->sync($countryIds); // Synchroniser les pays du projet
                    }
                }

                session()->forget('previewData');
                $project->save();
                Notification::make()->title('Votre appel a bien été ajouté.')->icon('heroicon-o-check-circle')->seconds(5)->color('success')->send();
                return redirect()->route('projects.index');
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
