<?php

use Illuminate\Http\Request;


//Route::namespace('Api')->prefix('v1')->group(function () {
//    Route::get('/users', 'UserController@index')->name('users.index');
//    Route::get('/users/{user}', 'UserController@show')->where('user', '[0-9]+')->name('users.show');
//    Route::post('/users', 'UserController@store')->name('users.store');
//    Route::post('/login', 'UserController@login')->name('users.login');
//    //当前用户信息
//    Route::get('/users/info', 'UserController@info')->name('users.info');
//
//});

Route::namespace('Api')->prefix('v1')->middleware('cors')->group(function () {
    //用户注册
    Route::post('/users', 'UserController@store')->name('users.store');
    //用户登录
    Route::post('/login', 'UserController@login')->name('users.login');
    Route::middleware('api.refresh')->group(function () {
        //当前用户信息
        Route::get('/users/info', 'UserController@info')->name('users.info');
        //用户列表
        Route::get('/users', 'UserController@index')->name('users.index');
        //用户信息
        Route::get('/users/{user}', 'UserController@show')->name('users.show');
        //用户退出
        Route::get('/logout', 'UserController@logout')->name('users.logout');
    });
});
