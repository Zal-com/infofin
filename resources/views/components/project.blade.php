@php use Illuminate\Support\Facades\Date; @endphp
@props(['active', 'program', 'deadline', 'deadline2', 'orga', 'desc', 'date'])

<tr class="bg-white border-b">
    <td class="px-6 py-4">
        {{$active == 1 ? '✔️' : '❌' }}
    </td>
    <th scope="row" class="max-w-12 px-6 py-4 font-medium text-gray-900">
        {{$program}}
    </th>
    <td class="px-6 py-4">
        {{date('d/m/Y', strtotime($deadline))}}
    </td>
    <td class="px-6 py-4">
        {{$deadline2 === '0000-00-00' ? 'N/A' : date('d/m/Y', strtotime($deadline2))}}
    </td>
    <td class="px-6 py-4">
        {{$orga}}
    </td>
    <td class="px-6 py-4">
        {{$desc}}
    </td>
    <td class="px-6 py-4">
        {{date('d/m/Y', strtotime($date))}}
    </td>
</tr>
