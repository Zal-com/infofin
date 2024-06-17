<?php /** @noinspection PhpIllegalArrayKeyTypeInspection */

namespace App\Livewire;

use App\Models\User;
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

class ListUsers extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function render(): View
    {
        return view('livewire.list-users');
    }

    /** @noinspection PhpIllegalArrayKeyTypeInspection */
    public function table(Table $table): Table
    {
        /** @noinspection PhpIllegalArrayKeyTypeInspection */
        return $table->query(User::all()->toQuery())->columns([
            TextColumn::make('email')
                ->label('Email')
                ->wrap()
                ->lineClamp(2)
                ->weight(FontWeight::SemiBold)
                ->sortable()
                ->searchable(),
            TextColumn::make('type')
                ->label('Type'),
            TextColumn::make('updated_at')
                ->label('Date de dernière modif.')
                ->dateTime('d/m/Y')
                ->sortable()
                ->alignCenter(),
            TextColumn::make('roles.name')
                ->label('Rôle')
                ->sortable()
                ->alignCenter()
        ])
            ->defaultPaginationPageOption(25)
            ->defaultSort('updated_at', 'desc')
            ->paginationPageOptions([5, 10, 25, 50, 100]);
           //->recordUrl(fn ($record) => route('us.show', $record->id));
    }
}
