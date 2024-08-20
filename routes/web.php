<?php

use App\Http\Controllers\DraftController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UnsubscribeController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;


Route::get('/', function () {
    return redirect()->route('projects.index');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda');

Route::controller(ProjectController::class)->group(function () {
    Route::get('/projects/preview', "preview")->name('projects.preview');
    Route::get('/projects/archive', "archive")->name("projects.archive");
    Route::get('projects/create/', "create")->name('projects.create');
    Route::get('/projects', "index")->name('projects.index');
    Route::get('/projects/{id}', "show")->name('projects.show');
    Route::get('/projects/{id}/edit', "edit")->name('projects.edit');
});

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

Route::get('/agenda', function () {
    return view('calendar');
});

Route::get('/download', function (Request $request) {
    $filename = $request->query('file');
    $name = $request->query('name');

    if (Storage::exists($filename)) {
        return Storage::download($filename, $name);
    } else {
        return abort(404, 'File not found');
    }
})->name('download');

Route::get('/privacy-policy', function () {
    return view('privacy');
});


require __DIR__ . '/auth.php';
