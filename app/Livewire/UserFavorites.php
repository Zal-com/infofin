<?php

namespace App\Livewire;

use App\Models\UserFavorite;
use Awcodes\FilamentBadgeableColumn\Components\Badge;
use Awcodes\FilamentBadgeableColumn\Components\BadgeableColumn;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Livewire\Component;

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
                UserFavorite::query()
                    ->select(
                        'users_favorite_projects.*',
                        'projects.title as project_title',
                        'projects.short_description as project_short_description',
                        'projects.is_big as project_is_big',
                        'organisations.title as organisation_title'
                    )
                    ->join('users', 'users_favorite_projects.user_id', '=', 'users.id')
                    ->join('projects', 'users_favorite_projects.project_id', '=', 'projects.id')
                    ->leftJoin('organisations', 'projects.organisation_id', '=', 'organisations.id')
                    ->where('users_favorite_projects.user_id', Auth::id())
            )
            ->columns([
                BadgeableColumn::make('project_title')
                    ->label('Programme')
                    ->wrap()
                    ->lineClamp(3)
                    ->weight(FontWeight::SemiBold)
                    ->sortable()
                    ->searchable()
                    ->width(300)
                    ->suffixBadges(function (UserFavorite $record) {
                        $user = Auth::user();
                        if (($user->hasRole('contributor') || $user->hasRole('admin')) && $record->project_is_big) {
                            return [
                                Badge::make('is_big')
                                    ->label('Projet majeur')
                                    ->color('primary')
                            ];
                        }
                        return [];
                    })->separator(false),
                TextColumn::make('project_short_description')
                    ->label('Description')
                    ->formatStateUsing(fn(string $state): HtmlString => new HtmlString($state))
                    ->wrap()
                    ->lineClamp(2)
                    ->limit(100)
                    ->width(300)
                    ->searchable(),
                TextColumn::make('organisation_title')
                    ->label('Organisation')
                    ->wrap()
                    ->width(200)
                    ->searchable()
                    ->sortable(),
            ])
            ->actions([
                Action::make('remove')->button()->icon('heroicon-o-x-mark')->label('Enlever')->color('danger')
                    ->action(fn($record) => Auth::user()->removeFromFavorites($record->project_id))
                    ->extraAttributes(['class' => 'btn btn-danger text-red-400'])

            ])
            ->recordUrl(fn($record) => route('projects.show', $record->project_id));
    }
}
