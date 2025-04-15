<?php

namespace App\Livewire;

use App\Models\Collection;
use App\Models\InfoSession;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Mail\Markdown;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class InfoSessionTable extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    public Collection $collection;

    public function render()
    {
        return view('livewire.info-session-table');
    }

    public function mount(Collection $collection){
        $this->collection = $collection;
    }

    public function table(Table $table){

        $info_sessions = InfoSession::query()->whereHas('collections', function (Builder $query) {
            $query->where('collections.id', $this->collection->id);
        })->get();

        if(sizeof($info_sessions) > 0){
        return $table
            ->query(InfoSession::query()->whereHas('collections', function (Builder $query) {
                $query->where('collections.id', $this->collection->id);
            }))
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
                TextColumn::make('title')
                ->label('Titre')
                ->searchable(),
                TextColumn::make('session_datetime')
                    ->label('Date et heure'),
                TextColumn::make('organisation.title')
                    ->label('Organisation')
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Description')
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
            ->recordUrl(fn($record) => route('info_session.show', $record->id));
    }
        return;
    }
}
