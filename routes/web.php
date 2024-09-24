<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\DraftController;
use App\Http\Controllers\InfoSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UnsubscribeController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;


Route::controller(ApiController::class)->group(function () {
    Route::prefix('api')->middleware('enforce.limit')->group(function () {
        // API information endpoint
        Route::get('', 'index')->name('api.index');

        // Projects endpoint
        Route::get('/projects', 'projects_index')->name('api.projects');

        // Continents endpoint
        Route::get('/continents', 'continents_index')->name('api.continents');

        // Countries endpoint
        Route::get('/countries', 'countries_index')->name('api.countries');

        // Info Types endpoint
        Route::get('/info-types', 'info_types_index')->name('api.info_types');

        // Organisations endpoint
        Route::get('/organisations', 'organisation_index')->name('api.organisations');

        // Scientific Domains endpoint
        Route::get('/scientific-domains', 'scientific_domains_index')->name('api.scientific_domains');

        Route::get('/projects/{id}', 'show_project')->name('api.show_project');
        Route::get('/continents/{id}', 'show_continent')->name('api.show_continent');
        Route::get('/countries/{id}', 'show_country')->name('api.show_country');
        Route::get('/info-types/{id}', 'show_info_type')->name('api.show_info_type');
        Route::get('/organisations/{id}', 'show_organisation')->name('api.show_organisation');
        Route::get('/scientific-domains/{id}', 'show_scientific_domain')->name('api.show_scientific_domain');
    });
});

Route::get('/', function () {
    return redirect()->route('projects.index');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::controller(ProjectController::class)->group(function () {
    Route::get('/projects/preview', "preview")->name('projects.preview');
    Route::get('/projects/archive', "archive")->name("projects.archive");
    Route::get('projects/create/', "create")->name('projects.create');
    Route::get('/projects', "index")->name('projects.index');
    Route::get('/projects/{id}', "show")->name('projects.show');
    Route::get('/projects/{id}/edit', "edit")->name('projects.edit');
});

Route::get('/collection/create', [CollectionController::class, 'create'])->name('collection.create');

Route::controller(UserController::class)->group(function () {
    Route::get('users', 'index')->name('users.index');
    Route::get('users/{id}', 'show')->name('users.show');
});

Route::controller(DraftController::class)->group(function () {
    Route::get('drafts', 'index')->name('drafts.index');
    Route::get('drafts/{id}', 'show')->name('drafts.show');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/unsubscribe', [UnsubscribeController::class, 'unsubscribe']);

Route::controller(InfoSessionController::class)->group(function () {
    Route::get('/info_session/create', 'create')->name('info_session.create');
    Route::get('/info_session', "index")->name('info_session.index');
    Route::get('/info_session/{id}', 'show')->name('info_session.show');
    Route::get('/info_session/{id}/edit', 'edit')->name('info_session.edit');
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
