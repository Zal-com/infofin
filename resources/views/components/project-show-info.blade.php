@props(['project'])
<div class="grid grid-cols-5 gap-4 mb-10" x-data="{ tab: 'description' }">
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
            <h1>{{$project->title}}</h1>
            <p>{{$project->organisation_id}}</p>
            <x-filament::section.description class="my-3 text-justify">
                {!! $project->long_description !!}
            </x-filament::section.description>
        </div>

        <div x-show="tab === 'dates'" class="m-4">
            <h2>Première deadline</h2>
            <p>{{\Carbon\Carbon::make($project->deadline)->format('d/m/Y')}}</p>
            <p>{{$project->proof}}</p>
            <h2>Seconde deadline</h2>
            <p>{{\Carbon\Carbon::make($project->deadline_2)->format('d/m/Y')}}</p>
            <p>{{$project->proof_2}}</p>
        </div>

        <div x-show="tab === 'infos'" class="m-4">
           <x-filament::section.heading>
               Type de programme
           </x-filament::section.heading>

                @foreach($project->infoType as $info_type)
                    <x-filament::section.description class="mb-3 text-justify">
                        {{$info_type['title']}}
                    </x-filament::section.description>
                @endforeach

            @if(!empty($project->financing))
                <x-filament::section.heading>
                    Financement
                </x-filament::section.heading>
                <x-filament::section.description class="mb-3 text-justify">
                    {{$project->financing}}
                </x-filament::section.description>

            @endif


            <x-filament::section.heading>
                Pour postuler
            </x-filament::section.heading>
            <x-filament::section.description class="mb-3 text-justify">
                {!! $project->apply_instructions !!}
            </x-filament::section.description>
        </div>
    </x-filament::section>
    <div class="flex flex-col gap-4 sticky top-5">
    <x-filament::section class="col-span-1 row-span-1">
        <x-filament::section.heading class="text-xl mb-4">
            Contacts ULB
        </x-filament::section.heading>
        @foreach(json_decode($project->contact_ulb, true) as $contact_ulb)
            <div class="mb-3 last-of-type:mb-0">
                <x-filament::section.heading>{{$contact_ulb['name']}}</x-filament::section.heading>
                @if($contact_ulb['phone'] != "")
                    <div class="flex items-center">
                        <x-filament::icon icon="heroicon-s-phone" class="h-5 w-5 mr-2"/>
                        {{$contact_ulb['phone']}}
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
    <x-filament::section class="col-span-1 row-span-1 sticky top-5">
        <x-filament::section.heading class="text-xl mb-4">
            Contacts externes
        </x-filament::section.heading>
        @foreach(json_decode($project->contact_ext, true) as $contact_ext)

            <div class="mb-3 last-of-type:mb-0">
                <x-filament::section.heading>{{$contact_ext['name']}}</x-filament::section.heading>
                @if($contact_ext['phone'] != "")
                    <div class="flex items-center">
                        <x-filament::icon icon="heroicon-s-phone" class="h-5 w-5 mr-2"/>
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
                        <x-filament::icon icon="heroicon-s-envelope" class="h-5 w-5 mr-2"/>
                        {{$contact_ext['address']}}
                    </div>
                @endif
            </div>
        @endforeach
    </x-filament::section>
    </div>
</div>
