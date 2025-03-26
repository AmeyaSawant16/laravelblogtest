<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ES\ElasticSearchController;

Route::prefix('/elasticsearch')->group(function (){

    Route::post('/search', [ElasticSearchController::class, 'elasticSearch']);

    Route::delete('/index', [ElasticSearchController::class, 'deleteIndex']);
});
