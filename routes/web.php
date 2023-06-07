<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'bank-webservice/api/v1', 'headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json'], 'middleware' => 'auth'], function ($router) {

    //beneficiary
    $router->get('beneficiary/all', 'BeneficiariesController@index');
    $router->put('beneficiary', 'BeneficiariesController@create');

    //Transaction
    $router->get('transactions/all', 'ProcessesController@index');
    $router->put('transactions', 'ProcessesController@create');
});

