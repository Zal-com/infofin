@extends('layout')

@section('content')
    <div class="relative overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3">
                    Actif
                </th>
                <th scope="col" class="px-6 py-3">
                    Programme
                </th>
                <th scope="col" class="px-6 py-3">
                    Deadline
                </th>
                <th scope="col" class="px-6 py-3">
                    Deadline2
                </th>
                <th scope="col" class="px-6 py-3">
                    Organisation
                </th>
                <th scope="col" class="px-6 py-3">
                    Description courte
                </th>
                <th scope="col" class="px-6 py-3">
                    Date d’introduction ou de dernière modification
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach ($projects as $project)
                <x-project
                    :active="$project->Active"
                    :program="$project->Name"
                    :deadline="$project->Deadline"
                    :deadline2="$project->Deadline2"
                    :orga="$project->Organisation"
                    :desc="$project->ShortDescription"
                    :date="$project->TimeStamp"
                    :continuous="$project->Continuous"
                    :continuous2="$project->Continuous2"
                />
            @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $projects->links('components.pagination') }}
        </div>
    </div>
@endsection
