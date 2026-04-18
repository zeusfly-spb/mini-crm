<?php

use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\WidgetTicketController;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('widget')
    ->name('widget.')
    ->group(function () {
        Route::get('/', [WidgetTicketController::class, 'create'])->name('index');
        Route::get('tickets/create', [WidgetTicketController::class, 'create'])->name('tickets.create');
    });

Route::get('feedback-widget', [WidgetTicketController::class, 'create'])->name('feedback-widget');

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::apiResource('tickets', TicketController::class)->only(['index', 'show', 'update']);
    });

Route::get('media/{media}/download', function (Media $media) {
        return $media->toResponse(request());
    })->name('media.download');
