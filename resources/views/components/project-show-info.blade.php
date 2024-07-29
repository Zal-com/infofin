@props(['project'])

<div class="grid grid-cols-5 gap-4 mb-5"
     x-data="{ tab: localStorage.getItem('activeTab') || 'description' }"
     x-init="$watch('tab', value => localStorage.setItem('activeTab', value))">
    <x-filament::section class="col-span-4 row-span-2">
        <x-filament::tabs>
            <x-filament::tabs.item @click="tab = 'description'" :alpine-active="'tab === \'description\''">
                Description
            </x-filament::tabs.item>
            <x-filament::tabs.item @click="tab = 'dates'" :alpine-active="'tab === \'dates\''">
                Dates
            </x-filament::tabs.item>
            <x-filament::tabs.item @click="tab = 'infos'" :alpine-active="'tab === \'infos\''">
                Infos supplémentaires
            </x-filament::tabs.item>
            <x-filament::tabs.item @click="tab = 'documents'" :alpine-active="'tab === \'documents\''">
                Documents
            </x-filament::tabs.item>
        </x-filament::tabs>

        <div x-show="tab === 'description'" class="m-4">
            <h1>{{$project->title ?? ''}}</h1>
            <p>{{$project->organisations->first()->title ?? $project->Organisation}}</p>
            <div class="markdown">
                <x-filament::section.description class="my-3 text-justify">
                    {!! \Illuminate\Support\Str::of($project->long_description)->markdown()!!}
                </x-filament::section.description>
            </div>
        </div>

        <div x-show="tab === 'dates'" class="m-4">
            <h2>Première deadline</h2>
            <p>{{$project-> deadline ? \Carbon\Carbon::make($project->deadline)->format('d/m/Y') : ''}}</p>
            <p>{{$project->proof ?? ''}}</p>
            <h2>Seconde deadline</h2>
            <p>{{$project->deadline_2 ? \Carbon\Carbon::make($project->deadline_2)->format('d/m/Y') : ''}}</p>
            <p>{{$project->proof_2 ?? ''}}</p>
        </div>

        <div x-show="tab === 'infos'" class="m-4">
            @if(!empty($project->funding))
                <div class="markdown mb-5">
                    <x-filament::section.heading class="text-2xl">
                        Financement
                    </x-filament::section.heading>
                    <x-filament::section.description
                        class="mb-1 text-sm text-gray-500 dark:text-gray-400 text-justify list-inside">
                        <div class="text-sm text-gray-500 dark:text-gray-400 text-justify">
                            {!! Illuminate\Support\Str::of($project->funding)->markdown() !!}
                        </div>
                    </x-filament::section.description>
                </div>
            @endif
            <div class="markdown mb-5">
                <x-filament::section.heading class="text-2xl">
                    Pour postuler
                </x-filament::section.heading>
                <x-filament::section.description
                    class="mb-1 text-sm text-gray-500 dark:text-gray-400 text-justify list-inside">
                    <div class="text-sm text-gray-500 dark:text-gray-400 text-justify">
                        {!! \Illuminate\Support\Str::of($project->apply_instructions)->markdown() !!}
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
                        {!! \Illuminate\Support\Str::of($project->admission_requirements)->markdown() !!}
                    </div>
                </x-filament::section.description>
            </div>
        </div>
        <div x-show="tab === 'documents'" class="m-4">
            <x-filament-tables::empty-state icon="heroicon-o-archive-box-x-mark"
                                            heading="Pas de documents"></x-filament-tables::empty-state>
        </div>
    </x-filament::section>
    <div class="flex flex-col gap-4 sticky top-5">
        <x-filament::section>
            <x-filament::section.heading class="text-xl mb-4">
                Types de programme
            </x-filament::section.heading>
            <x-filament::section.description class="flex flex-wrap gap-1">
                @foreach($project->info_types as $info_type)
                    <x-filament::badge>{{$info_type->title ?? ''}}</x-filament::badge>
                @endforeach
            </x-filament::section.description>
        </x-filament::section>
        @if($project->contact_ulb)
            <x-filament::section class="col-span-1 row-span-1">
                <x-filament::section.heading class="text-xl mb-4">
                    Contacts ULB
                </x-filament::section.heading>
                @foreach($project->contact_ulb as $contact_ulb)
                    <div class="mb-3 last-of-type:mb-0">
                        <x-filament::section.heading>{{$contact_ulb['name']}}</x-filament::section.heading>
                        @if(!empty($contact_ulb['phone']))
                            <div class="flex items-center">
                                <x-filament::icon icon="heroicon-s-phone" class="h-5 w-5 mr-2"/>
                                {{$contact_ulb['phone']}}
                            </div>
                        @endif
                        @if(!empty($contact_ulb['email']))
                            <div class="flex items-center">
                                <x-filament::icon icon="heroicon-s-at-symbol" class="h-5 w-5 mr-2"/>
                                {{$contact_ulb['email']}}
                            </div>
                        @endif
                        @if(!empty($contact_ulb['address']))
                            <div class="flex items-center">
                                <x-filament::icon icon="heroicon-s-envelope" class="h-5 w-5 mr-2"/>
                                {{$contact_ulb['address']}}
                            </div>
                        @endif
                    </div>
                @endforeach
            </x-filament::section>
        @endif
        @if($project->contact_ext)
            <x-filament::section class="col-span-1 row-span-1 sticky top-5">
                <x-filament::section.heading class="text-xl mb-4">
                    Contacts externes
                </x-filament::section.heading>
                @foreach($project->contact_ext as $contact_ext)
                    <div class="mb-3 last-of-type:mb-0">
                        <x-filament::section.heading>{{$contact_ext['name']}}</x-filament::section.heading>
                        @if($contact_ext['phone'] != "")
                            <div class="flex items-center">
                                <x-filament::icon icon="heroicon-s-phone" size="md" class="mr-2"/>
                                {{$contact_ext['phone']}}
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
    </div>
</div>
