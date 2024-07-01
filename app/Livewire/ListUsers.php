<?php /** @noinspection PhpIllegalArrayKeyTypeInspection */
namespace App\Livewire;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\View\View;
use Livewire\Component;

class ListUsers extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function render(): View
    {
        return view('livewire.list-users');
    }

    public function table(Table $table): Table
    {
        return $table->query(User::query())->columns([
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
            SelectColumn::make('role_id')
                ->label('Rôles')
                ->options(Role::all()->pluck('name', 'id')->toArray())
                ->updateStateUsing(function ($state, User $record) {
                    $record->syncRoles(Role::find($state));
                    return $state;
                })
                ->getStateUsing(function ($record) {
                    return $record->roles->pluck('id')->first();
                })
                ->sortable(false)
                ->searchable(false)
        ])
            ->defaultPaginationPageOption(25)
            ->defaultSort('updated_at', 'desc')
            ->paginationPageOptions([5, 10, 25, 50, 100]);
            //->recordUrl(fn ($record) => route('us.show', $record->id));
    }
}
