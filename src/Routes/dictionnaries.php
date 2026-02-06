<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use MetaFramework\Dictionnaries\Controllers\DictionnaryController;
use MetaFramework\Dictionnaries\Controllers\DictionnaryEntryController;

Route::get('dictionnaryentry/subentry/{dictionnaryentry}', [DictionnaryEntryController::class, 'subentry'])
    ->name('dictionnaryentry.subentry');

// Backward-compatibility for the typo used in older app copies.
Route::get('dictionnaryentry/subentrty/{dictionnaryentry}', [DictionnaryEntryController::class, 'subentry'])
    ->name('dictionnaryentry.subentrty');

Route::resource('dictionnary', DictionnaryController::class);
Route::resource('dictionnary.entries', DictionnaryEntryController::class)->shallow();
Route::resource('dictionnaryentry', DictionnaryEntryController::class)->except(['create']);

Route::delete('dictionnary/mass-delete', [DictionnaryController::class, 'massDelete'])
    ->name('dictionnary.mass-delete');
