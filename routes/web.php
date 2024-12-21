<?php

use App\Http\Controllers\CollectionController;
use App\Http\Controllers\DraftController;
use App\Http\Controllers\InfoSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UnsubscribeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;


Route::fallback(function () {
    redirect()->route('projects.index');
});

Route::get('/', function () {
    return redirect()->route('projects.index');
})->name('home');

Route::prefix('projects')
    ->controller(ProjectController::class)
    ->name('projects.')
    ->group(function () {
        // Routes protégées par auth
        Route::middleware('contributor')->group(function () {
            Route::get('/preview', "preview")->name('preview');
            Route::get('/archive', "archive")->name("archive");
            Route::get('/create/', "create")->name('create');
            Route::get('/{id}/edit', "edit")->name('edit');
        });
        // Routes publiques
        Route::get('', "index")->name('index');
        Route::get('/{id}', "show")->name('show');
    });


Route::prefix('collection')
    ->controller(CollectionController::class)
    ->name('collection.')
    ->group(function () {
        Route::get('/{id}', 'show')->name('show');
        Route::get('/{id}/edit', 'edit')->name('edit')->middleware('auth');
    });

Route::prefix('profile')
    ->controller(ProfileController::class)
    ->name('profile.')
    ->middleware('auth')
    ->group(function () {
        Route::get('', 'show')->name('show');
        Route::patch('', 'update')->name('update');
        Route::delete('', 'destroy')->name('destroy');
    });

Route::get('/unsubscribe', [UnsubscribeController::class, 'unsubscribe']);

Route::prefix('info_session')
    ->controller(InfoSessionController::class)
    ->name('info_session.')
    ->group(function () {
        //Routes protégées par auth
        Route::middleware('contributor')->group(function () {
            Route::get('/create', 'create')->name('create');
            Route::get('/{id}/edit', 'edit')->name('edit');
        });
        //Routes publiques
        Route::get('/{id}', 'show')->name('show');
        Route::get('', "index")->name('index');


    });


Route::get('/agenda', function () {
    return view('calendar');
})->name('agenda');

Route::get('/download', function (Request $request) {
    $filename = $request->query('file');
    $name = $request->query('name');

    if (Storage::disk('public')->exists($filename)) {
        return Storage::disk('public')->download($filename);
    } else {
        return abort(404, 'File not found');
    }
})->name('download');

Route::get('/privacy-policy', function () {
    return view('privacy');
})->name('privacy-policy');

Route::get('/faq', function () {
    return view('faq');
})->name('faq');

require __DIR__ . '/auth.php';
require __DIR__ . '/api.php';
