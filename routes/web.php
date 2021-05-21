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
    $router->post('user/create', 'UsersController@create');
    $router->post('user/authenticate-token', 'UsersController@authenticateToken');
    $router->post('user/resetPassword', 'UsersController@resetPassword');
    $router->post('user/forgotPassword/{userID}', 'UsersController@forgotPassword');
    $router->get('user/verifyEmail/{userID}', 'UsersController@verifyEmail');
});


$router->group(['prefix' => 'api/v1', 'middleware' => 'auth'], function ($router) {
    //$router->group(['prefix' => 'api/v1'], function ($router) {
    //Role/Category Routes
    $router->get('roles/index', 'RolesController@index');
    $router->post('roles/create', 'RolesController@create');
    $router->post('roles/update/{roleID}', 'RolesController@update');

    //User Roles Routes
    $router->get('userRole/index', 'Users_rolesController@index');
    $router->post('userRole/addRole', 'Users_rolesController@addRole');
    $router->get('userRole/getUserRole/{userID}', 'Users_rolesController@getUserRole');
    $router->get('userRole/userInformation_userID', 'Users_rolesController@userInformation_userID');

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
    $router->post('user/update/{userID}', 'UsersController@update');
    $router->get('user/getSpecificUser/{userID}', 'UsersController@getSpecificUser');
    $router->get('user/getSpecificUser_withID/{userID}', 'UsersController@getSpecificUser_withID');
    
    $router->get('user/verifyAdmin/{userID}', 'UsersController@verifyAdmin');
    $router->get('user/me', 'UsersController@me');
    $router->get('user/fetchEmail/{user_email}', 'UsersController@fetchEmail');
    $router->get('user/userType/{usertype}', 'UsersController@getUserType');
    $router->post('user/discard/{userID}', 'UsersController@discard_user');

    //Notification  Routes
    $router->post('notifications', 'NotificationController@create');
    $router->get('notifications/unread/{userID}','NotificationController@getUnreadNotifications');
    $router->get('notifications/read/{userID}','NotificationController@getReadNotifications');
    $router->get('notifications/all/{userID}','NotificationController@getAllNotifications');
    $router->post('notifications/markread','NotificationController@markAsRead');
    $router->post('notifications/notifications_byrole','NotificationController@getAllNotification_by_role');
    $router->post('notifications/unreadnotifications','NotificationController@getAllUnreadNotifications_by_role');
   

//});

});
