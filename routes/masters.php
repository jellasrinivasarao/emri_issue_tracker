<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\StateController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\OrganisationController;


Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    Route::resource('states',StateController::class)->except(['show']);

    Route::resource('projects',ProjectController::class)->except(['show']);
    
    Route::resource('organisations',OrganisationController::class)->except(['show']);
});