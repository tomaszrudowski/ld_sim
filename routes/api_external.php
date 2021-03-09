<?php
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| External routes no auth middleware, access check in controllers
|
*/

/* todo: TMP !! */
Route::post('/populations','APIController@generatePopulation')
    ->name('external.api.population.post');
Route::get('/populations','APIController@getPopulations')
    ->name('external.api.population.index');
Route::get('/populations/{population}','APIController@getPopulation')
    ->name('external.api.population.get');
Route::get('/populations/{population}/majority-distribution','APIController@getMajorityElectionsDistribution')
    ->name('external.api.majority.distribution.get');
Route::get('/populations/{population}/voters','APIController@getVoters')
    ->name('external.api.population.get.voters');
Route::post('/populations/{population}','APIController@runMajorityElections')
    ->name('external.api.population.majority.run');

