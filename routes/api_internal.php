<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Internal routes 'web' middleware
|
*/
Route::post('/populations','APIController@generatePopulation')
    ->name('internal.api.population.post');
Route::get('/populations','APIController@getPopulations')
    ->name('internal.api.population.index');
Route::get('/populations/{population}','APIController@getPopulation')
    ->name('internal.api.population.get');
Route::get('/populations/{population}/voters','APIController@getVotersStats')
    ->name('internal.api.population.get.voters');
Route::get('/populations/{population}/timeline','APIController@getElectionsTimeline')
    ->name('internal.api.population.get.elections.timeline');

Route::get('/populations/{population}/majority-distribution','APIController@getMajorityElectionsDistribution')
    ->name('internal.api.majority.distribution.get');

Route::post('/populations/{population}','APIController@runMajorityElections')
    ->name('internal.api.population.majority.run');

Route::post('/populations/{population}/elections','APIController@runElections')
    ->name('internal.api.elections.run');




