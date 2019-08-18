<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect(route('login'));
});

Route::get('admin', function(){
    return view('layouts.admin_template');
});

#superadmin login
Route::get('secure-login','SuperAdmin\SpLoginController@login_form');

Route::group(['middleware' => ['auth','role:super admin']],function(){
    Route::get('/super-admin/dashboard','SuperAdmin\SuperAdminController@dashboard');

    Route::get('/employee','SuperAdmin\SuperAdminController@employee');


    Route::get('super-admin/roles','SuperAdmin\SuperAdminController@roles')->name('role');
    Route::post('super-admin/roles','SuperAdmin\SuperAdminController@roleFormValidation');
    Route::post('/roles','SuperAdmin\SuperAdminController@deleteRole')->name('delete.role');
    Route::post('/roles-name','SuperAdmin\SuperAdminController@getRoleName');
    Route::post('/get-role-details','SuperAdmin\SuperAdminController@getRoleDetails');
    Route::post('/update-role-details','SuperAdmin\SuperAdminController@updateRoleDetails');

    Route::get('super-admin/permissions','SuperAdmin\SuperAdminController@permissions')->name('permission');
    Route::post('super-admin/permissions','SuperAdmin\SuperAdminController@permissionFormValidation');
    Route::post('/get-permission-details','SuperAdmin\PermissionController@getPermissionDetails');
    Route::post('/update-permission-details','SuperAdmin\PermissionController@updatePermission');
    Route::post('/delete-permission','SuperAdmin\PermissionController@deletePermission');

    Route::post('/add-new-call-center','CallCenter\CallCenterController@addNewCallCenter');
    Route::post('/get-call-center-value','CallCenter\CallCenterController@getCallCenterDetails');
    Route::post('/update-call-center-details','CallCenter\CallCenterController@updateCallCenterDetails');
    Route::post('/get-call-center-delete-value','CallCenter\CallCenterController@getCallCenterDetails');
    Route::post('/delete-call-center-details','CallCenter\CallCenterController@deleteCallCenter');
    Route::get('/call-center-profile/{id}','SuperAdmin\SuperAdminController@callCenterProfile')->name('callcenter.profile');

    Route::get('super-admin/callCenter','SuperAdmin\SuperAdminController@callCenter');
    Route::get('super-admin/lgu','SuperAdmin\SuperAdminController@lgu');
});

Route::post('/add-employee','Employee\EmployeeController@addEmployee')->middleware(['auth','role:super admin|admin']);


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/test','Auth\LoginController@test');
