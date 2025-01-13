@php
    $deadlines = $data['deadlines'] ?? [];
@endphp
<div
    class="flex flex-col border-alternating-wrapper">
    <div class="border-alternating-content">
        <div class="flex-grow grid grid-cols-5 gap-4 mb-5" x-data="{ tab: 'description' }">
            <x-filament::section class="col-span-4 row-span-2">
                <x-filament::tabs>
                    <x-filament::tabs.item @click="tab = 'description'" :alpine-active="'tab === \'description\''">
                        Description
                    </x-filament::tabs.item>
                    <x-filament::tabs.item @click="tab = 'infos'" :alpine-active="'tab === \'infos\''">
                        Infos supplémentaires
                    </x-filament::tabs.item>
                    <x-filament::tabs.item @click="tab = 'documents'" :alpine-active="'tab === \'documents\''">
                        Documents
                    </x-filament::tabs.item>
                </x-filament::tabs>
                <div x-show="tab === 'description'" class="m-4">
                    @if(empty($data['contact_ulb']))
                        <x-filament::section class="bg-primary-100 mt-1 custom-section-wrapper mb-2"
                                             style="background: #2A9D8F60">
                            <div class="flex flex-row items-start text-xs gap-5">
                                <div class="w-fit">
                                    <x-filament::icon icon="heroicon-o-information-circle" class="h-5"/>
                                </div>
                                <p>
                                    Cet appel ne nécessite pas de suivi particulier par le Département
                                    Recherche. Merci de suivre directement la procédure indiquée. Si toutefois vous
                                    souhaitez
                                    contacter
                                    le
                                    Département Recherche, écrivez à ulbkto@ulb.be (propriété intellectuelle) ou
                                    fonds.recherche@ulb.be
                                    (pour
                                    toute autre question).
                                </p></div>
                        </x-filament::section>
                    @endif
                    <x-filament::section.description class="flex flex-wrap gap-1">
                        @if($expenses->isNotEmpty())
                            @foreach($expenses as $expense)
                                <x-filament::badge>{{$expense->title ?? ''}}</x-filament::badge>
                            @endforeach
                        @endif
                        @if($activities->isNotEmpty())
                            @foreach($activities as $activity)
                                <x-filament::badge>{{$activity->title ?? ''}}</x-filament::badge>
                            @endforeach
                        @endif
                    </x-filament::section.description>
                    <h1 class="font-bold text-4xl my-1">{{$data['title'] ?? ''}}</h1>
                    <div class="inline-flex justify-between gap-2 mt-0 w-full">
                        <div>
                            <p class="text-md italic">{{ $organisation['title'] ?? 'Aucune organisation entrée' }}</p>
                        </div>
                        @if($scientificDomains->isNotEmpty())
                            <div class="inline-flex gap-2">
                                <!-- Ajout des badges pour les disciplines scientifiques -->

                                @php
                                    // Récupérer tous les domaines associés au projet
                                    $linkedDomains = $scientificDomains;

                                    // Grouper tous les domaines disponibles par catégorie
                                    $domainsByCategory = \App\Models\ScientificDomain::all()->groupBy('category.name');
                                @endphp

                                @foreach($domainsByCategory as $categoryName => $domains)
                                    @php
                                        // Filtrer les domaines qui sont liés au projet dans cette catégorie
                                        $linkedDomainsInCategory = $linkedDomains->whereIn('id', $domains->pluck('id'));

                                        $totalDomainsLinked = $linkedDomainsInCategory->count(); // Nombre de domaines liés dans la catégorie
                                    @endphp

                                        <!-- Condition pour n'afficher la catégorie que si au moins un domaine est sélectionné -->
                                    @if($totalDomainsLinked > 0)
                                        <div class="relative group" x-data="{ showTooltip: false }"
                                             @mouseenter="showTooltip = true"
                                             @mouseleave="showTooltip = false">
                                            @if($totalDomainsLinked === $domains->count())
                                                <!-- Si tous les domaines sont cochés, afficher uniquement le nom de la catégorie -->
                                                <x-filament::badge color="success"
                                                                   class="w-fit">{{ $categoryName }}</x-filament::badge>
                                            @else
                                                <!-- Sinon, afficher la catégorie et le nombre de domaines cochés -->
                                                <x-filament::badge color="success" class="w-fit">{{ $categoryName }}
                                                    ({{ $totalDomainsLinked }})
                                                </x-filament::badge>
                                            @endif

                                            <!-- Tooltip personnalisé qui s'affiche au survol avec uniquement les domaines sélectionnés -->
                                            @php
                                                // Créer la liste des noms des domaines sélectionnés pour le tooltip sous forme de HTML
                                                $selectedDomainsListHtml = '<ul>' . $linkedDomainsInCategory->map(fn($domain) => '<li>' . e($domain->name) . '</li>')->implode('') . '</ul>';
                                            @endphp
                                            <div x-show="showTooltip"
                                                 class="absolute left-0 bg-gray-800 text-white text-sm rounded-lg p-2 z-10 w-max mt-2 shadow-lg"
                                                 style="display: none;" x-cloak>
                                                {!! $selectedDomainsListHtml !!}
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <p>Aucun domaine scientifique.</p>
                        @endif
                    </div>
                    <div class="tiptap">
                        @empty($data['long_description'])
                            Pas de description fournie.
                        @else
                            {!! tiptap_converter()->asHTML($data['long_description']) !!}
                            {{--!! \Illuminate\Support\Str::of($data['long_description'] ?? 'Aucune description entrée')->markdown() !!--}}
                        @endempty
                    </div>
                </div>

                <div x-show="tab === 'infos'" class="m-4">
                    <div class="markdown mb-5">
                        <x-filament::section.heading class="text-2xl">
                            Financement
                        </x-filament::section.heading>
                        <x-filament::section.description
                            class="mb-1 text-sm text-gray-500 dark:text-gray-400 text-justify list-inside">
                            <div class="text-sm text-gray-500 dark:text-gray-400 text-justify">
                                @empty($data['funding'])
                                    Pas de financement fourni.
                                @else
                                    {!! tiptap_converter()->asHTML($data['funding']) !!}
                                @endempty
                            </div>
                        </x-filament::section.description>
                    </div>

                    <div class="markdown mb-5">
                        <x-filament::section.heading class="text-2xl">
                            Pour postuler
                        </x-filament::section.heading>
                        <x-filament::section.description
                            class="mb-1 text-sm text-gray-500 dark:text-gray-400 text-justify list-inside">
                            <div class="text-sm text-gray-500 dark:text-gray-400 text-justify">
                                @empty($data['apply_instructions'])
                                    Pas d'instructions d'application fournies.
                                @else
                                    {!! tiptap_converter()->asHTML($data['apply_instructions']) !!}
                                @endempty
                            </div>
                        </x-filament::section.description>
                    </div>

                    <div class="markdown mb-5">
                        <x-filament::section.heading class="text-2xl">
                            Requis d'admission
                        </x-filament::section.heading>
                        <x-filament::section.description
                            class="mb-1 text-sm text-gray-500 dark:text-gray-400 text-justify list-inside">
                            <div class="text-sm text-gray-500 dark:text-gray-400 text-justify">
                                @empty($data['admission_requirements'])
                                    Pas de requis d'admission fournis.
                                @else
                                    {!! tiptap_converter()->asHTML($data['admission_requirements']) !!}
                                @endempty
                            </div>
                        </x-filament::section.description>
                    </div>
                    <div class="markdown mb-5">
                        <x-filament::section.heading class="text-2xl">
                            Zones géographiques
                        </x-filament::section.heading>
                        <x-filament::section.description
                            class="mb-1 text-sm text-gray-500 dark:text-gray-400 text-justify list-inside">
                            <div class="text-sm text-gray-500 dark:text-gray-400 text-justify">
                                @empty($data['geo_zones'])
                                    Pas de zones géographiques fournies.
                                @else
                                    @php
                                        $geo_zones = [];
                                            //Si geo_zone contient 'pays' chercher pays, si 'continent' chercher continent
                                            foreach($data['geo_zones'] as $zone){
                                               $zone = explode('_', $zone);
                                               switch ($zone[0]){
                                                   case 'continent' : $geo_zones[] = \App\Models\Continent::where('code', $zone[1])->pluck('name')->first();
                                                    break;
                                                    case 'pays' : $geo_zones[] = \App\Models\Country::where('id', $zone[1])->pluck('name')->first();
                                                        break;
                                               }
                                            }
                                    @endphp
                                    {{implode(', ',$geo_zones)}}
                                @endempty
                            </div>
                        </x-filament::section.description>
                    </div>
                </div>

                <div x-show="tab === 'documents'" class="m-4">
                    <x-filament::section.heading class="text-2xl">
                        Documents
                    </x-filament::section.heading>
                    @if(isset($data['documents']) && is_array($data['documents']) && !empty($data['documents']))
                        <ul>
                            @foreach($data['documents'] as $document)
                                <x-filament::section class="w-1/2">
                                    <li>
                                        <div class="flex justify-between">
                                            <div class="flex items-center">
                                                <x-filament::icon icon="heroicon-o-document"
                                                                  class="h-[24px] w-[24px] mr-2"/>
                                                <a href="{{ route('download', ['name'=> $document['filename'] ,'file' => $document['path']]) }}"
                                                   class="text-blue-600 hover:underline">
                                                    {{ $document['filename'] }}
                                                </a>
                                            </div>
                                            <div class="flex items-center">
                                                <a href="{{ route('download', ['name'=> $document['filename'] ,'file' => $document['path']]) }}"
                                                   class="inline-block">
                                                    <x-filament::icon icon="heroicon-o-arrow-down-tray"
                                                                      class="min-h-[28px] min-w-[28px] text-gray-900 hover:text-gray-600"/>
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                </x-filament::section>
                            @endforeach
                        </ul>
                    @else
                        <p>Aucun document</p>
                    @endif
                </div>
            </x-filament::section>


            <div class="flex flex-col gap-4 sticky top-5">
                @if($deadlines)
                    <x-zeus-accordion::accordion>
                        <x-zeus-accordion::accordion.item
                            icon="heroicon-o-calendar-days"
                            label="{{ $deadlines[array_key_first($deadlines)]['continuous'] ? 'Continu' : $deadlines[array_key_first($deadlines)]['proof']}} : {{\Carbon\Carbon::make($deadlines[array_key_first($deadlines)]['date'])->format('d/m/Y')}}"
                        >
                            <div class="bg-white p-4">
                                @foreach($deadlines as $deadline)
                                    @if(! $deadline['continuous'])
                                        <p>
                                            {{$deadline['proof'] != '' ? $deadline['proof'] . ' :'  : ''}}
                                            {{\Carbon\Carbon::make($deadline['date'])->format("d/m/Y")}}</p>
                                    @endif
                                @endforeach
                            </div>
                        </x-zeus-accordion::accordion.item>
                    </x-zeus-accordion::accordion>
                @else
                    <x-zeus-accordion::accordion>
                        <x-zeus-accordion::accordion.item
                            icon="heroicon-o-calendar-days"
                            label="Pas de deadlines fournies"
                        >
                        </x-zeus-accordion::accordion.item>
                    </x-zeus-accordion::accordion>
                @endif
                @if($contactUlbs)
                    <x-filament::section class="col-span-1 row-span-1">
                        <x-filament::section.heading class="text-xl mb-4">
                            Contacts ULB
                        </x-filament::section.heading>
                        @foreach($contactUlbs as $contact_ulb)
                            <div class="mb-3 last-of-type:mb-0">
                                <x-filament::section.heading>{{$contact_ulb['name']}}</x-filament::section.heading>
                                @if($contact_ulb['email'] != "")
                                    <div class="flex items-center">
                                        <x-filament::icon icon="heroicon-s-at-symbol" class="h-5 w-5 mr-2"/>
                                        {{$contact_ulb['email']}}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </x-filament::section>
                @endif
                @if($contactExts)
                    <x-filament::section class="col-span-1 row-span-1 sticky top-5">
                        <x-filament::section.heading class="text-xl mb-4">
                            Contacts externes
                        </x-filament::section.heading>
                        @foreach($contactExts as $contact_ext)
                            <div class="mb-3 last-of-type:mb-0">
                                @if($contact_ext['name'] != "")
                                    <x-filament::section.heading>{{$contact_ext['name']}}</x-filament::section.heading>
                                @endif
                                @if($contact_ext['email'] != "")
                                    <div class="flex items-center">
                                        <x-filament::icon icon="heroicon-s-at-symbol" class="h-5 w-5 mr-2"/>
                                        {{$contact_ext['email']}}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </x-filament::section>
                @endif
                @if($info_sessions && $info_sessions->count() > 0)
                    <x-filament::section class="col-span-1 row-span-1">
                        <x-filament::section.heading>
                            Prochaine séance d'information
                        </x-filament::section.heading>
                        <div class="mt-3">
                            <p class="flex flex-row gap-2 items-center my-1">
                                <x-filament::icon
                                    icon="heroicon-o-map-pin"
                                    class="max-h-6 max-w-6"/> {{$info_sessions[0]->sessionTypeString}}
                            </p>
                            <p class="flex flex-row gap-2 items-center my-1">
                                <x-filament::icon
                                    icon="heroicon-o-calendar-days"
                                    class="max-h-6 max-w-6"/> {{\Carbon\Carbon::make($info_sessions[0]->session_datetime)->format('d/m/Y')}}
                            </p>
                            <p class="flex flex-row gap-2 items-center my-1">
                                <x-filament::icon
                                    icon="heroicon-o-clock"
                                    class="max-h-6 max-w-6"/> {{\Carbon\Carbon::make($info_sessions[0]->session_datetime)->format('H:i')}}
                            </p>
                            <div class="flex justify-end">
                                <x-filament::button color="secondary" tag="a"
                                                    href="{{route('info_session.show', $info_sessions[0]->id)}}"
                                                    icon="heroicon-o-arrow-right" iconPosition="after"
                                                    class="mt-2 justify-end">Plus d'infos
                                </x-filament::button>
                            </div>
                        </div>
                    </x-filament::section>
                @endif
            </div>
        </div>

        <div class="mt-4 grid grid-cols-5">
            <div class="col-span-4 flex justify-end">
                <x-filament::button wire:click="return" color="secondary" icon="heroicon-o-arrow-uturn-left"
                                    class="mx-2">
                    Retour
                </x-filament::button>
            </div>
        </div>
    </div>
</div>
