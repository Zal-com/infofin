<?php

namespace App\Livewire;

use App\Models\Project;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\HtmlString;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ListProjects extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function render(): View
    {
        return view('livewire.list-projects');
    }

    public function table(Table $table): Table
    {
        return $table->query(Project::all()->toQuery())->columns([
            IconColumn::make('status')
                ->label('Est actif')
                ->boolean()
                ->getStateUsing(function ($record) {
                    $currentDate = Date::now();
                    return $record->Continuous === 1
                        || $record->Continuous2 === 1
                        || ($record->Deadline >= $currentDate && $record->Deadline != '0000-00-00')
                        || ($record->Deadline2 >= $currentDate && $record->Deadline2 != '0000-00-00');
                })
                ->trueIcon('heroicon-o-check-circle')
                ->trueColor('success')
                ->falseIcon('heroicon-o-x-circle')
                ->falseColor('danger')
                ->sortable(false),
            TextColumn::make('Name')
                ->label('Programme')
                ->wrap()
                ->lineClamp(2)
                ->weight(FontWeight::SemiBold)
                ->sortable()
                ->searchable(),
            TextColumn::make('Deadline')
                ->label('Deadline 1')
                ->sortable()
                ->searchable()
                ->formatStateUsing(function ($record) {
                    if ($record->Continuous) {
                        return 'Continue';
                    } elseif ($record->Deadline == '0000-00-00') {
                        return 'N/A';
                    } else {
                        return \Carbon\Carbon::parse($record->Deadline)->format('d/m/Y');
                    }
                }),
            TextColumn::make('Deadline2')
                ->label('Deadline 2')
                ->sortable()
                ->searchable()
                ->formatStateUsing(function ($record) {
                    if ($record->Continuous2) {
                        return 'Continue';
                    } elseif ($record->Deadline2 == '0000-00-00') {
                        return 'N/A';
                    } else {
                        return \Carbon\Carbon::parse($record->Deadline2)->format('d/m/Y');
                    }
                }),
            TextColumn::make('Organisation')
            ->label('Organisation')
                ->wrap()
            ->sortable()
            ->searchable(),
            TextColumn::make('ShortDescription')
                ->label('Description courte')
                ->formatStateUsing(fn (string $state) : HtmlString => new HtmlString($state))
                ->wrap()
                ->lineClamp(2)
            ->limit(100),
            TextColumn::make('TimeStamp')
            ->label('Date de dernière modif.')
            ->dateTime('d/m/Y')
            ->sortable()
        ])
            ->defaultPaginationPageOption(25)
            ->defaultSort('TimeStamp', 'desc')
            ->paginationPageOptions([5, 10, 25, 50, 100])
            ->recordUrl(fn ($record) => route('projects.show', $record->ProjectID));
    }
}
