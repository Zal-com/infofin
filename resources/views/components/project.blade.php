@php use Illuminate\Support\Facades\Date; @endphp
@props(['program', 'deadline', 'deadline2', 'orga', 'desc', 'date', 'continuous', 'continuous2'])
@php
    $currentDate = Date::now();
    $isActive = $continuous === 1 || $continuous2 === 1 || ($deadline >= $currentDate && $deadline != '0000-00-00') || ($deadline2 >= $currentDate && $deadline2 != '0000-00-00');
@endphp


<tr class="bg-white border-b">
    <td class="px-6 py-4">
        {{$isActive == 1 ? '✔️' : '❌' }}
    </td>
    <th scope="row" class="max-w-12 px-6 py-4 font-medium text-gray-900">
        {{$program}}
    </th>
    <td class="px-6 py-4">
        {{$continuous === 1 ? 'Continue' : date('d/m/Y', strtotime($deadline))}}
    </td>
    <td class="px-6 py-4">
        {{$continuous2 === 1 ? 'Continue' : ($deadline2 === '0000-00-00' ? 'N/A' :  date('d/m/Y', strtotime($deadline2)))}}
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
