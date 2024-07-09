<?php /** @noinspection PhpIllegalArrayKeyTypeInspection */

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
use Illuminate\Support\HtmlString;
use Illuminate\View\View;
use Livewire\Component;

class ListProjects extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function render(): View
    {
        return view('livewire.list-projects');
    }

    /** @noinspection PhpIllegalArrayKeyTypeInspection */
    public function table(Table $table): Table
    {
        /** @noinspection PhpIllegalArrayKeyTypeInspection */
        return $table->query(Project::all()->toQuery())->columns([
            IconColumn::make('status')
                ->label('Est actif')
                ->boolean()
                ->trueIcon('heroicon-o-check-circle')
                ->trueColor('success')
                ->falseIcon('heroicon-o-x-circle')
                ->falseColor('danger')
                ->sortable()
                ->alignCenter(),
            TextColumn::make('title')
                ->label('Programme')
                ->wrap()
                ->lineClamp(2)
                ->weight(FontWeight::SemiBold)
                ->sortable()
                ->searchable(),
            TextColumn::make('deadline')
                ->label('Deadline 1')
                ->sortable()
                ->searchable()
                ->formatStateUsing(function ($record) {
                    if ($record->continuous) {
                        return 'Continue';
                    } elseif ($record->deadline == '0000-00-00 00:00:00') {
                        return 'N/A';
                    } else {
                        return \Carbon\Carbon::parse($record->deadline)->format('d/m/Y');
                    }
                }),
            TextColumn::make('deadline_2')
                ->label('Deadline 2')
                ->sortable()
                ->searchable()
                ->formatStateUsing(function ($record) {
                    if ($record->continuous_2) {
                        return 'Continue';
                    } elseif ($record->deadline_2 == '0000-00-00 00:00:00') {
                        return 'N/A';
                    } else {
                        return \Carbon\Carbon::parse($record->deadline_2)->format('d/m/Y');
                    }
                }),
            TextColumn::make('organisations')
                ->label('Organisation')
                ->wrap()
                ->sortable()
                ->searchable(),
            TextColumn::make('short_description')
                ->label('Description courte')
                ->formatStateUsing(fn (string $state) : HtmlString => new HtmlString($state))
                ->wrap()
                ->lineClamp(2)
                ->limit(100),
            TextColumn::make('updated_at')
                ->label('Date de dernière modif.')
                ->dateTime('d/m/Y')
                ->sortable()
                ->alignCenter()
        ])
            ->defaultPaginationPageOption(25)
            ->defaultSort('updated_at', 'desc')
            ->paginationPageOptions([5, 10, 25, 50, 100])
            ->recordUrl(fn ($record) => route('projects.show', $record->id));
    }
}
