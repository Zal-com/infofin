<?php

namespace App\Livewire;

use App\Models\Collection;
use App\Models\Project;
use Awcodes\FilamentBadgeableColumn\Components\Badge;
use Awcodes\FilamentBadgeableColumn\Components\BadgeableColumn;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class ProjectTable extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    public Collection $collection;

    public function render()
    {
        return view('livewire.project-table');
    }

    public function mount(Collection $collection){
        $this->collection = $collection;
    }

    public function table(Table $table) : Table | null{

        return $table
            ->query(
                Project::query()
                    ->select('projects.*', 'organisations.title as organisation_title')
                    ->leftJoin('organisations', 'projects.organisation_id', '=', 'organisations.id')
                    ->whereHas('collections', function (Builder $query) {
                        $query->where('collections.id', $this->collection->id);
                    })
            )
            ->columns([
                IconColumn::make('status')
                    ->label(false)
                    ->boolean()
                    ->trueIcon('heroicon-s-check-circle')
                    ->trueColor('success')
                    ->falseIcon('heroicon-s-x-circle')
                    ->falseColor('danger')
                    ->sortable()
                    ->alignCenter(),
                BadgeableColumn::make('title')
                    ->label('Programme')
                    ->wrap()
                    ->lineClamp(3)
                    ->weight(FontWeight::SemiBold)
                    ->sortable()
                    ->suffixBadges(function (Project $record) {
                        if ($record->is_big && Auth::check() && Auth::user()->hasRole('contributor')) {
                            return [
                                Badge::make('is_big')
                                    ->label('Projet majeur')
                                    ->color('primary')
                            ];
                        }
                        return [];
                    })
                    ->separator(false)
                    ->searchable(),
                TextColumn::make('firstDeadline')
                    ->label('Prochaine deadline')
                    ->formatStateUsing(function ($record) {
                        $deadline = explode('|', $record->firstDeadline);
                        return new HtmlString("
    <div>
        <p class='my-0'>{$deadline[0]}</p>
        <p class='text-gray-500 text-xs'>" . ($deadline[1] ?? '') . "</p>
    </div>
");
                    }),
                TextColumn::make('organisation_title')
                    ->label('Organisation')
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('short_description')
                    ->label('Description courte')
                    ->formatStateUsing(fn(string $state): HtmlString => new HtmlString(Markdown::parse($state)))
                    ->wrap()
                    ->lineClamp(2)
                    ->limit(100),
                TextColumn::make('updated_at')
                    ->label('Date de dernière modif.')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->alignCenter()
            ])
            ->recordUrl(fn($record) => route('projects.show', $record->id));
    }
}
