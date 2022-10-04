<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['namespace' => 'App\Http\Controllers'], function ($api) {
    $api->group(['middleware' => 'api'], function ($api) {
        $api->get('ping', 'ApiController@ping')->name('ping');

        $api->group(['prefix' => 'logs'], function ($api) {
            $api->group(['prefix' => 'batch'], function ($api) {
                $api->delete('/', 'LogController@destroyBatch')->name('logs.destroyBatch');
            });

            $api->group(['prefix' => 'query'], function ($api) {
                $api->delete('/', 'LogController@destroyQuery')->name('logs.destroyQuery');
            });

            $api->get('/', 'LogController@index')->name('logs.index');
            $api->get('{log}', 'LogController@show')->whereNumber('log')->name('logs.show');
            $api->delete('{log}', 'LogController@destroy')->whereNumber('log')->name('logs.destroy');
        });

        $api->group(['prefix' => 'auth', 'namespace' => 'Auth'], function ($api) {
            $api->post('register', 'RegisterController@register')->name('auth.register');
            $api->post('verification', 'VerificationController@sendVerificationEmail')->name('auth.sendVerificationEmail');
            $api->get('verify', 'VerificationController@verify')->name('auth.verify');

            $api->group(['prefix' => 'password'], function ($api) {
                $api->post('/', 'ForgotPasswordController@sendResetPasswordEmail')->name('auth.sendResetPasswordEmail');
                $api->patch('/', 'ResetPasswordController@updatePassword')->name('auth.updatePassword');
            });

            $api->group(['prefix' => 'login'], function ($api) {
                $api->post('/', 'LoginController@login')->name('auth.login');
                $api->post('{user}', 'LoginController@loginAs')->whereUuid('user')->name('auth.loginAs');
            });

            $api->post('logout', 'LoginController@logout')->name('auth.logout');
        });

        $api->group(['prefix' => 'me'], function ($api) {
            $api->get('/', 'MeController@show')->name('me.show');
            $api->match(['put', 'patch'], '/', 'MeController@update')->name('me.update');
            $api->delete('/', 'MeController@destroy')->name('me.destroy');
        });

        $api->group(['prefix' => 'users'], function ($api) {
            $api->group(['prefix' => 'batch'], function ($api) {
                $api->post('/', 'UserController@storeBatch')->name('users.storeBatch');
                $api->match(['put', 'patch'], '/', 'UserController@updateBatch')->name('users.updateBatch');
                $api->delete('/', 'UserController@destroyBatch')->name('users.destroyBatch');
            });

            $api->group(['prefix' => 'query'], function ($api) {
                $api->match(['put', 'patch'], '/', 'UserController@updateQuery')->name('users.updateQuery');
                $api->delete('/', 'UserController@destroyQuery')->name('users.destroyQuery');
            });

            $api->get('/', 'UserController@index')->name('users.index');
            $api->post('/', 'UserController@store')->name('users.store');
            $api->get('{user}', 'UserController@show')->whereUuid('user')->name('users.show');
            $api->match(['put', 'patch'], '{user}', 'UserController@update')->whereUuid('user')->name('users.update');
            $api->delete('{user}', 'UserController@destroy')->whereUuid('user')->name('users.destroy');
        });

        $api->group(['prefix' => 'roles'], function ($api) {
            $slug = '^[a-z0-9]+(?:[_-][a-z0-9]+)*$';

            $api->group(['prefix' => 'batch'], function ($api) {
                $api->post('/', 'RoleController@storeBatch')->name('roles.storeBatch');
                $api->match(['put', 'patch'], '/', 'RoleController@updateBatch')->name('roles.updateBatch');
                $api->delete('/', 'RoleController@destroyBatch')->name('roles.destroyBatch');
            });

            $api->group(['prefix' => 'query'], function ($api) {
                $api->match(['put', 'patch'], '/', 'RoleController@updateQuery')->name('roles.updateQuery');
                $api->delete('/', 'RoleController@destroyQuery')->name('roles.destroyQuery');
            });

            $api->get('/', 'RoleController@index')->name('roles.index');
            $api->post('/', 'RoleController@store')->name('roles.store');
            $api->get('{role}', 'RoleController@show')->where('role', $slug)->name('roles.show');
            $api->match(['put', 'patch'], '{role}', 'RoleController@update')->where('role', $slug)->name('roles.update');
            $api->delete('{role}', 'RoleController@destroy')->where('role', $slug)->name('roles.destroy');
        });

        $api->group(['prefix' => 'permissions'], function ($api) {
            $slug = '^[a-z0-9]+(?:[_-][a-z0-9]+)*$';

            $api->group(['prefix' => 'batch'], function ($api) {
                $api->post('/', 'PermissionController@storeBatch')->name('permissions.storeBatch');
                $api->match(['put', 'patch'], '/', 'PermissionController@updateBatch')->name('permissions.updateBatch');
                $api->delete('/', 'PermissionController@destroyBatch')->name('permissions.destroyBatch');
            });

            $api->group(['prefix' => 'query'], function ($api) {
                $api->match(['put', 'patch'], '/', 'PermissionController@updateQuery')->name('permissions.updateQuery');
                $api->delete('/', 'PermissionController@destroyQuery')->name('permissions.destroyQuery');
            });

            $api->get('/', 'PermissionController@index')->name('permissions.index');
            $api->post('/', 'PermissionController@store')->name('permissions.store');
            $api->get('{permission}', 'PermissionController@show')->where('permission', $slug)->name('permissions.show');
            $api->match(['put', 'patch'], '{permission}', 'PermissionController@update')->where('permission', $slug)->name('permissions.update');
            $api->delete('{permission}', 'PermissionController@destroy')->where('permission', $slug)->name('permissions.destroy');
        });
    });
});
