@php
    $deadlines = $data['deadlines'] ?? [];
@endphp
<div class="flex flex-col">
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
                <h1>{{ $data['title'] ?? 'Aucun titre entré' }}</h1>
                <p>{{ $organisations['title'] ?? 'Aucune organisation entrée' }}</p>
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
                <x-filament::section.heading class="text-2xl">
                    Type de programme
                </x-filament::section.heading>
                @if(!empty($info_types))
                    @foreach($info_types as $info_type)
                        <x-filament::section.description class="mb-4 text-justify">
                            {{ $info_type['title'] ?? 'No Title' }}
                        </x-filament::section.description>
                    @endforeach
                @else
                    <p>Pas de type de programme entré</p>
                @endif
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
                                {!! tiptap_converter()->asHTML($data['apply_instructions']) !!}}
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
                                            <x-filament::icon icon="heroicon-o-document" class="h-[24px] w-[24px] mr-2"/>
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
                            @if($contact_ulb['tel'] != "")
                                <div class="flex items-center">
                                    <x-filament::icon icon="heroicon-s-phone" class="h-5 w-5 mr-2"/>
                                    {{$contact_ulb['tel']}}
                                </div>
                            @endif
                            @if($contact_ulb['email'] != "")
                                <div class="flex items-center">
                                    <x-filament::icon icon="heroicon-s-at-symbol" class="h-5 w-5 mr-2"/>
                                    {{$contact_ulb['email']}}
                                </div>
                            @endif
                            @if($contact_ulb['address'] != "")
                                <div class="flex items-center">
                                    <x-filament::icon icon="heroicon-s-envelope" class="h-5 w-5 mr-2"/>
                                    {{$contact_ulb['address']}}
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
                            <x-filament::section.heading>{{$contact_ext['name']}}</x-filament::section.heading>
                            @if($contact_ext['tel'] != "")
                                <div class="flex items-center">
                                    <x-filament::icon icon="heroicon-s-phone" class="h-5 w-5 mr-2"/>
                                    {{$contact_ext['tel']}}
                                </div>
                            @endif
                            @if($contact_ext['email'] != "")
                                <div class="flex items-center">
                                    <x-filament::icon icon="heroicon-s-at-symbol" class="h-5 w-5 mr-2"/>
                                    {{$contact_ext['email']}}
                                </div>
                            @endif
                            @if($contact_ext['address'] != "")
                                <div class="flex items-center">
                                    <x-filament::icon icon="heroicon-s-envelope" class="h-5 w-5 mr-2 overflow-auto"/>
                                    {{$contact_ext['address']}}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </x-filament::section>
            @endif
            @if(!empty($project->deadlines))
                <x-filament::section class="col-span-1 row-span-1 sticky top-5">
                    <x-filament::section.heading class="text-xl mb-4">
                        Dates
                    </x-filament::section.heading>
                    @foreach($project->deadlines as $deadline)
                        <div class="mb-3 last-of-type:mb-0">
                            <x-filament::section>
                                <div>{{$deadline['continuous'] == 1 ? "Continue" : \Carbon\Carbon::make($deadline['date'])->format('d/m/Y')}}</div>
                                {{$deadline['proof'] ?? ""}}
                            </x-filament::section>
                        </div>
                    @endforeach
                </x-filament::section>
            @endif
        </div>
    </div>

    <div class="mt-4 grid grid-cols-5">
        <div class="col-span-4 flex justify-end">
            <x-filament::button wire:click="return" color="secondary" icon="heroicon-o-arrow-uturn-left" class="mx-2">
                Retour
            </x-filament::button>
            <x-filament::button wire:click="create" color="primary" icon="heroicon-o-plus">Créer</x-filament::button>
        </div>
    </div>
</div>
