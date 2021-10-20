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
Route::post('/templates','APIController@generatePopulationTemplate')
    ->name('internal.api.templates.post');

Route::get('/templates','APIController@getPopulationTemplates')
    ->name('internal.api.templates.index');

Route::get('/templates/{template}','APIController@getPopulationTemplate')
    ->name('internal.api.template.get');

Route::post('/templates/{template}/populations','APIController@getChildPopulations')
    ->name('internal.api.populations.index');

Route::post('/templates/{template}/populations','APIController@generateChildPopulation')
    ->name('internal.api.populations.post');

Route::get('/templates/{template}/populations/{population}','APIController@getPopulation')
    ->name('internal.api.population.get');

Route::get('/templates/{template}/populations/{population}/voters','APIController@getVotersStats')
    ->name('internal.api.population.get.voters');

Route::get('/templates/{template}/populations/{population}/timeline','APIController@getElectionsTimeline')
    ->name('internal.api.population.get.elections.timeline');


Route::get('/templates/{template}/populations/{population}/weights','APIController@getWeightsTimeline')
    ->name('internal.api.population.get.weights.timeline');

Route::get('/templates/{template}/populations/{population}/majority-distribution','APIController@getMajorityElectionsDistribution')
    ->name('internal.api.majority.distribution.get');

Route::post('/templates/{template}/populations/{population}','APIController@runMajorityElections')
    ->name('internal.api.population.majority.run');

Route::post('/templates/{template}/populations/{population}/stage','APIController@changeToPerformanceStage')
    ->name('internal.api.population.stage.update');

Route::post('/templates/{template}/populations/{population}/elections','APIController@runElections')
    ->name('internal.api.elections.run');




