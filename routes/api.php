<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::controller(ApiController::class)->group(function () {
    Route::prefix('api')->name('api.')->middleware('enforce.limit')->group(function () {
        // API information endpoint
        Route::get('', 'index')->name('api.index');

        // Projects endpoint
        Route::get('/projects', 'projects_index')->name('projects');

        // Continents endpoint
        Route::get('/continents', 'continents_index')->name('continents');

        // Countries endpoint
        Route::get('/countries', 'countries_index')->name('countries');

        // Info Types endpoint
        Route::get('/info-types', 'info_types_index')->name('info_types');

        // Organisations endpoint
        Route::get('/organisations', 'organisation_index')->name('organisations');

        // Scientific Domains endpoint
        Route::get('/scientific-domains', 'scientific_domains_index')->name('scientific_domains');

        Route::get('/projects/{id}', 'show_project')->name('show_project');
        Route::get('/continents/{id}', 'show_continent')->name('show_continent');
        Route::get('/countries/{id}', 'show_country')->name('show_country');
        Route::get('/info-types/{id}', 'show_info_type')->name('show_info_type');
        Route::get('/organisations/{id}', 'show_organisation')->name('show_organisation');
        Route::get('/scientific-domains/{id}', 'show_scientific_domain')->name('show_scientific_domain');
    });
});

