<div>
    <div class="relative overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 w-1/12">
                    Actif
                </th>
                <th scope="col" class="px-6 py-3 w-3/12">
                    Programme
                </th>
                <th scope="col" class="px-6 py-3 w-1/12">
                    Deadline
                </th>
                <th scope="col" class="px-6 py-3 w-1/12">
                    Seconde Deadline
                </th>
                <th scope="col" class="px-6 py-3 w-2/12">
                    Organisation
                </th>
                <th scope="col" class="px-6 py-3 w-3/12">
                    Description courte
                </th>
                <th scope="col" class="px-6 py-3 w-1/12">
                    Date d’introduction ou de dernière modification
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach ($projects as $project)
                <x-project
                    :id="$project->ProjectID"
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
x
        </div>
    </div>
</div>
