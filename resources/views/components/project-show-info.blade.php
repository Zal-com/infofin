@props(['project'])
<div class="grid grid-cols-5 gap-4 mb-5" x-data="{
    tab: ['description', 'infos', 'documents'].includes(localStorage.getItem('projectActiveTab')) ? localStorage.getItem('projectActiveTab') : 'description'
}"
     x-init="$watch('tab', value => localStorage.setItem('projectActiveTab', value))">
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
            <x-filament::section.description class="flex flex-wrap gap-1">
                @foreach($project->info_types as $info_type)
                    <x-filament::badge>{{$info_type->title ?? ''}}</x-filament::badge>
                @endforeach
            </x-filament::section.description>
            <h1 class="font-bold text-4xl my-1">{{$project->title ?? ''}}</h1>
            <p class="text-md italic">{{$project->organisation->title ?? $project->Organisation}}</p>
            <div class="markdown">
                <x-filament::section.description class="my-3 text-justify">
                    @php
                        $long_description = $project->long_description ?? '';

                        if (!empty($long_description)) {
                            try {
                                echo tiptap_converter()->asHTML($long_description);
                            } catch (Exception $e) {
                                echo nl2br(e($long_description));
                            }
                        } else {
                            echo '<p>No description provided.</p>';
                        }
                    @endphp
                </x-filament::section.description>
            </div>
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
                            @php
                                $funding = $project->funding ?? '';

                                if (!empty($funding)) {
                                    try {
                                        echo tiptap_converter()->asHTML($funding);
                                    } catch (Exception $e) {
                                        echo \Illuminate\Support\Str::of($funding)->markdown();
                                    }
                                } else {
                                    echo '<p>No funding information provided.</p>';
                                }
                            @endphp
                        </div>
                    </x-filament::section.description>
                </div>
            @endif
            @if(!empty($project->apply_instructions))
                <hr>
                <div class="markdown mt-5 mb-5">
                    <x-filament::section.heading class="text-2xl">
                        Pour postuler
                    </x-filament::section.heading>
                    <x-filament::section.description
                        class="mb-1 text-sm text-gray-500 dark:text-gray-400 text-justify list-inside">
                        <div class="text-sm text-gray-500 dark:text-gray-400 text-justify">
                            @php
                                $apply_instructions = $project->apply_instructions ?? '';

                                if (!empty($apply_instructions)) {
                                    try {
                                        echo tiptap_converter()->asHTML($apply_instructions);
                                    } catch (Exception $e) {
                                        echo \Illuminate\Support\Str::of($apply_instructions)->markdown();
                                    }
                                } else {
                                    echo '<p>No application instructions provided.</p>';
                                }
                            @endphp
                        </div>
                    </x-filament::section.description>
                </div>
            @endif
            @if(!empty($project->admission_requirements))
                <hr>
                <div class="markdown mt-5">
                    <x-filament::section.heading class="text-2xl">
                        Requis d'admission
                    </x-filament::section.heading>
                    <x-filament::section.description
                        class="mb-1 text-sm text-gray-500 dark:text-gray-400 text-justify list-inside">
                        <div class="text-sm text-gray-500 dark:text-gray-400 text-justify">
                            @php
                                $admission_requirements = $project->admission_requirements ?? '';

                                if (!empty($admission_requirements)) {
                                    try {
                                        echo tiptap_converter()->asHTML($admission_requirements);
                                    } catch (Exception $e) {
                                        echo \Illuminate\Support\Str::of($admission_requirements)->markdown();
                                    }
                                } else {
                                    echo '<p>No admission requirements provided.</p>';
                                }
                            @endphp
                        </div>
                    </x-filament::section.description>
                </div>
            @endif
        </div>
        <div x-show="tab === 'documents'" class="m-4">
            @if($project->documents && $project->documents->isNotEmpty())
                <x-filament::section.heading class="text-2xl mb-4">
                    Documents disponibles
                </x-filament::section.heading>
                <ul>
                    @foreach($project->documents as $document)
                        <x-filament::section class="w-1/2">
                            <li>
                                <div class="flex justify-between">
                                    <div class="flex items-center">
                                        <x-filament::icon icon="heroicon-o-document" class="h-[24px] w-[24px] mr-2"/>
                                        <a href="{{ route('download', ['name'=> $document->filename ,'file' => $document->path]) }}"
                                           class="text-blue-600 hover:underline">
                                            {{ $document->filename }}
                                        </a>
                                    </div>
                                    <div class="flex items-center">
                                        <a href="{{ route('download', ['name'=> $document->filename ,'file' => $document->path]) }}"
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
                <x-filament-tables::empty-state icon="heroicon-o-archive-box-x-mark"
                                                heading="Pas de documents disponibles">
                </x-filament-tables::empty-state>
            @endif
        </div>
    </x-filament::section>
    <div class="flex flex-col gap-4 sticky top-5">
        @if($project->hasUpcomingDeadline())
            <x-zeus-accordion::accordion>
                <x-zeus-accordion::accordion.item
                    icon="heroicon-o-calendar-days"
                    label="{{ $project->firstDeadline === 'Continu' ? 'Continu' : (explode('|', $project->firstDeadline)[1]}} : {{explode('|',$project->firstDeadline)[0])}}">
                    <div class="bg-white p-4">
                        @foreach($project->allDeadlinesSorted as $sortedDeadline)
                            @if(!$sortedDeadline['continuous'])
                                <p @if(\Carbon\Carbon::make($sortedDeadline['date'])->format("d/m/Y") === explode('|',$project->firstDeadline)[0]) style="font-weight: bold" @endif>
                                    {{$sortedDeadline['proof'] != '' ? $sortedDeadline['proof'] . ' :'  : ''}}
                                    {{\Carbon\Carbon::make($sortedDeadline['date'])->format("d/m/Y")}}</p>
                            @endif
                        @endforeach
                    </div>
                </x-zeus-accordion::accordion.item>
            </x-zeus-accordion::accordion>
        @else
            <x-zeus-accordion::accordion>
                <x-zeus-accordion::accordion.item
                    icon="heroicon-o-calendar-days"
                    label="Projet terminé"
                >
                    <div class="bg-white p-4">
                        @foreach($project->allDeadlinesSorted as $sortedDeadline)
                            <p>
                                {{$sortedDeadline['proof']}}
                                : {{\Carbon\Carbon::make($sortedDeadline['date'])->format("d/m/Y - H:i")}}</p>
                        @endforeach
                    </div>
                </x-zeus-accordion::accordion.item>
            </x-zeus-accordion::accordion>
        @endif
        @if(!empty($project->contact_ulb))
            <x-filament::section class="col-span-1 row-span-1">
                <x-filament::section.heading class="text-xl mb-4">
                    Contacts ULB
                </x-filament::section.heading>
                @foreach($project->contact_ulb as $contact_ulb)
                    <div class="mb-3 last-of-type:mb-0">
                        <x-filament::section.heading>
                            <p class="flex-1 flex-wrap overflow-ellipsis line-clamp-1">
                                {{$contact_ulb['name']}}
                            </p>
                        </x-filament::section.heading>
                        @if(!empty($contact_ulb['phone']))
                            <div class="flex items-center">
                                <x-filament::icon icon="heroicon-s-phone" class="h-[24px] w-[24px] mr-2"/>
                                <p class="flex-1 flex-wrap overflow-ellipsis line-clamp-1">
                                    {{$contact_ulb['phone']}}
                                </p>
                            </div>
                        @endif
                        @if(!empty($contact_ulb['email']))
                            <div class="flex items-center">
                                <x-filament::icon icon="heroicon-s-at-symbol" class="h-[24px] w-[24px] mr-2"/>
                                <p class="flex-1 flex-wrap overflow-ellipsis line-clamp-1">
                                    <a href="mailto:{{trim($contact_ulb['email'])}}">
                                        {{$contact_ulb['email']}}
                                    </a>
                                </p>
                            </div>
                        @endif
                        @if(!empty($contact_ulb['address']))
                            <div class="flex items-center">
                                <x-filament::icon icon="heroicon-s-envelope" class="h-[24px] w-[24px] mr-2"/>
                                {{$contact_ulb['address']}}
                            </div>
                        @endif
                    </div>
                @endforeach
            </x-filament::section>
        @endif
        @if(!empty($project->contact_ext))
            <x-filament::section class="col-span-1 row-span-1 sticky top-5">
                <x-filament::section.heading class="text-xl mb-4">
                    Contacts externes
                </x-filament::section.heading>
                @foreach($project->contact_ext as $contact_ext)
                    <div class="mb-3 last-of-type:mb-0">
                        <x-filament::section.heading>
                            <p class="flex-1 flex-wrap overflow-ellipsis line-clamp-1">
                                {{$contact_ext['name']}}
                            </p>
                        </x-filament::section.heading>
                        @if($contact_ext['phone'] != "")
                            <div class="flex items-center">
                                <x-filament::icon icon="heroicon-s-phone" class="h-[24px] w-[24px] mr-2"/>
                                <p class="flex-1 flex-wrap overflow-ellipsis line-clamp-1">
                                    {{$contact_ext['phone']}}
                                </p>
                            </div>
                        @endif
                        @if($contact_ext['email'] != "")
                            <div class="flex items-center">
                                <x-filament::icon icon="heroicon-s-at-symbol" class="h-[24px] w-[24px] mr-2"/>
                                <p class="flex-1 flex-wrap overflow-ellipsis line-clamp-1">
                                    <a href="mailto:{{trim($contact_ext['email'])}}">
                                        {{$contact_ext['email']}}
                                    </a>
                                </p>
                            </div>
                        @endif
                        @if($contact_ext['address'] != "")
                            <div class="flex items-center">
                                <x-filament::icon icon="heroicon-s-envelope"
                                                  class="h-[24px] w-[24px] mr-2"/>
                                <p class="flex-1 flex-wrap overflow-ellipsis line-clamp-1">
                                    {{$contact_ext['address']}}
                                </p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </x-filament::section>
        @endif
    </div>
</div>
