@props(['active', 'program', 'deadline', 'deadline2', 'orga', 'desc', 'date'])

<tr class="bg-white border-b">
    <td class="px-6 py-4">
        @if ($active == 1)
            ✔️
        @else
            ❌
        @endif
    </td>
    <th scope="row" class="max-w-12 px-6 py-4 font-medium text-gray-900">
        {{$program}}
    </th>
    <td class="px-6 py-4">
        {{$deadline}}
    </td>
    <td class="px-6 py-4">
        {{$deadline2}}
    </td>
    <td class="px-6 py-4">
        {{$orga}}
    </td>
    <td class="px-6 py-4">
        {{$desc}}
    </td>
    <td class="px-6 py-4">
        {{$date}}
    </td>
</tr>
