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
Route::post('/populations/{population}/elections','APIController@runElections')
    ->name('external.api.population.election.run');
Route::get('/populations/{population}/voters/{voter}','APIController@getVoterStats')
    ->name('external.api.population.get.voter');
Route::get('/populations/{population}/voters','APIController@getVotersStats')
    ->name('external.api.population.get.voters');
Route::get('/populations/{population}','APIController@getPopulation')
    ->name('external.api.population.get');

Route::get('/populations','APIController@getPopulations')
    ->name('external.api.population.index');

Route::get('/populations/{population}/timeline','APIController@getElectionsTimeline');
