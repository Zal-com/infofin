<?php

namespace App\Livewire;

use App\Models\Organisation;
use App\Models\Project;
use App\Models\User;
use App\Models\UserFavorite;
use Awcodes\FilamentBadgeableColumn\Components\Badge;
use Awcodes\FilamentBadgeableColumn\Components\BadgeableColumn;
use Doctrine\DBAL\Schema\Column;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Tiptap\Nodes\Text;

class UserFavorites extends Component implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function render()
    {
        return view('livewire.user-favorites');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                UserFavorite::where('user_id', Auth::id())
                    ->with(['project', 'user'])
            )
            ->columns([
                BadgeableColumn::make('project.title')
                    ->label('Programme')
                    ->wrap()
                    ->lineClamp(3)
                    ->weight(FontWeight::SemiBold)
                    ->sortable()
                    ->searchable()
                    ->width(300)
                    ->suffixBadges(function (UserFavorite $record) {
                        if ($record->project->is_big) {
                            return [
                                Badge::make('is_big')
                                    ->label('Projet majeur')
                                    ->color('primary')
                            ];
                        }
                        return [];
                    })->separator(false),
                TextColumn::make('project.short_description')
                    ->label('Description')
                    ->formatStateUsing(fn(string $state): HtmlString => new HtmlString($state))
                    ->wrap()
                    ->lineClamp(2)
                    ->limit(100)
                    ->width(300)
                    ->searchable(),
                TextColumn::make('project.organisations.title')
                    ->label('Organisation')
                    ->wrap()
                    ->width(200)
                    ->searchable()
                    ->sortable(),
            ])
            ->actions([
                Action::make('remove')->button()->icon('heroicon-o-x-mark')->label('Enlever')->color('danger')
                    ->action(fn($record) => Auth::user()->removeFromFavorites($record->project->id))
                    ->extraAttributes(['class' => 'btn btn-danger text-red-400'])

            ])
            ->recordUrl(fn($record) => route('projects.show', $record->project->id));
    }
}
