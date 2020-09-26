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

    //User Roles Routes
    $router->get('userRole/index', 'Users_rolesController@index');
    $router->post('userRole/addRole', 'Users_rolesController@addRole');
    $router->get('userRole/getUserRole/{userID}', 'Users_rolesController@getUserRole');

    //Kin Routes
    $router->get('kins/index', 'KinsController@index');
    $router->post('kins/create/{userID}', 'KinsController@create');
    $router->get('kins/getKin/{userID}', 'KinsController@getKin');

    //Permission Routes
    $router->get('permission/index', 'Permissions@index');
    $router->post('permission/create', 'Permissions@create');
    $router->post('permission/update/{permID}', 'Permissions@update');
    $router->get('permission/roles', 'PermissionsController@roles');

    //User Routes
    $router->get('user/index', 'UsersController@index');
    $router->post('user/login', 'UsersController@login');
    $router->post('user/create', 'UsersController@create');
    $router->post('user/update/{userID}', 'UsersController@update');
    $router->get('user/getSpecificUser/{userID}', 'UsersController@getSpecificUser');
    $router->get('user/getSpecificUser_withID/{userID}', 'UsersController@getSpecificUser_withID');
    $router->get('user/verifyEmail/{userID}', 'UsersController@verifyEmail');
    $router->get('user/verifyAdmin/{userID}', 'UsersController@verifyAdmin');
    $router->get('user/me', 'UsersController@me');
    $router->get('user/fetchEmail/{user_email}', 'UsersController@fetchEmail');
    $router->get('user/userType/{usertype}', 'UsersController@getUserType');
    $router->post('user/resetPassword', 'UsersController@resetPassword');
    $router->post('user/forgotPassword/{userID}', 'UsersController@forgotPassword');
});
