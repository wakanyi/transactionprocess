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

$router->group(['prefix' => 'api/v1'], function ($router) {
    $router->post('user/login', 'UsersController@login');  
});
//$router->group(['prefix' => 'api/v1', 'middleware' => 'auth'], function ($router) {
    $router->group(['prefix' => 'api/v1'], function ($router) {
    //Role/Category Routes
    $router->get('roles/index', 'RolesController@index');
    $router->post('roles/create', 'RolesController@create');
    $router->post('roles/update/{roleID}', 'RolesController@update');

    //Permission Routes
    $router->get('permission/index', 'Permissions@index');
    $router->post('permission/create', 'Permissions@create');
    $router->post('permission/update/{permID}', 'Permissions@update');

    //User Routes
    $router->get('user/index', 'UsersController@index');
    $router->post('user/login', 'UsersController@login');
    $router->post('user/create', 'UsersController@create');
    $router->get('user/verifyEmail/{userID}', 'UsersController@verifyEmail');
    $router->get('user/verifyAdmin/{userID}', 'UsersController@verifyAdmin');
    $router->get('user/me', 'UsersController@me');
    $router->get('user/fetchEmail/{user_email}', 'UsersController@fetchEmail');
    $router->get('user/userType/{usertype}', 'UsersController@getUserType');
    $router->post('user/resetPassword', 'UsersController@resetPassword');
    $router->get('user/forgotPassword/{userID}', 'UsersController@forgotPassword');
});