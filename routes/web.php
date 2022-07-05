<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Auth::routes(['register' => false]);

Route::get('/', 'HomeController@index')->name('home');

Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    // Route::view('register', 'auth.register')->name('register');
});

// Route::view('/password/reset', 'auth.passwords.email')->name('password.request');
// Route::get('/password/reset/{token}', 'Auth\PasswordResetController')->name('password.reset');

Route::middleware('auth')->group(function () {
    Route::get('/changePassword', 'PasswordController@showChangePasswordForm');
    Route::post('/changePassword', 'PasswordController@changePassword')->name('changePassword');

    // Route::view('email/verify', 'auth.verify')->middleware('throttle:6,1')->name('verification.notice');
    // Route::get('email/verify/{id}/{hash}', 'Auth\EmailVerificationController')->middleware('signed')->name('verification.verify');

    //     Route::view('password/confirm', 'auth.passwords.confirm')->name('password.confirm');

    Route::group(['middleware' => ['role:sysadmin']], function () {
        Route::get('/user/impersonate', 'UserController@impersonation');
        Route::get('/user/impersonate/start/{user}', 'UserController@user_impersonate_start');
    });
    Route::get('/user/impersonate/stop', 'UserController@user_impersonate_stop');

    Route::get('/project/select', 'ProjectController@selectList');
    Route::get('/project/{project}/select', 'ProjectController@select');

    Route::group(['middleware' => ['permission:manage-projects']], function () {
        Route::get('/project/stypes', 'ProjectController@storageSampleTypes');
        Route::get('/project/{project}/reset', 'ProjectController@reset');
        Route::resource('/project', 'ProjectController');

        Route::get('/redcapproject/new', 'RedcapController@create');
        Route::post('/redcapproject', 'RedcapController@store');
        Route::get('/redcapproject/{project}/edit', 'RedcapController@edit');
        Route::patch('/redcapproject/{project}', 'RedcapController@update');
    });

    Route::group(['middleware' => ['permission:manage-users']], function () {
        Route::resource('/user', 'UserController', ['except' => ['show']]);
        Route::get('/user/{user}/roles', 'UserController@editroles');
        Route::post('/user/{user}/roles', 'UserController@updateroles');

        Route::resource('/role', 'RoleController');
        Route::post('/role/{role}/permissions', 'RoleController@updatepermissions');

        Route::resource('/permission', 'PermissionController');
    });

    Route::group(['middleware' => ['permission:manage-freezers']], function () {
        Route::resource('/unitDefinition', 'UnitDefinitionController');
        Route::resource('/section', 'SectionController', ['except' => ['index', 'show', 'edit', 'update']]);
    });

    Route::resource('/physicalUnit', 'PhysicalUnitController', ['except' => ['show']]);
    Route::group(['middleware' => ['permission:manage-storage']], function () {
        Route::get('/physicalUnit/{physicalUnit}/toggleActive', 'PhysicalUnitController@toggleActive');
        Route::get('/virtualUnit/{virtualUnit}/toggleActive', 'VirtualUnitController@toggleActive');
        Route::resource('/virtualUnit', 'VirtualUnitController');
    });

    Route::group(['middleware' => ['project.auth:manage-subjects,manage-teams']], function () {
        Route::get('/substitute', 'UserSubstituteController@index');
        Route::get('/substitute/{user}', 'UserSubstituteController@show');
        Route::post('/substitute', 'UserSubstituteController@store');
        Route::delete('/substitute', 'UserSubstituteController@destroy');
    });

    // Route::group(['middleware' => ['permission:manage-teams,another_study']], function () {
    Route::middleware('project.auth:manage-teams')->group(function () {
        Route::get('/team', 'TeamController@index');
        Route::get('/team/addmember', 'TeamController@addmember');
        Route::post('/team', 'TeamController@storemember');
        Route::get('/team/{user}', 'TeamController@showmember');
        Route::get('/team/{user}/edit', 'TeamController@editmember');
        Route::patch('/team/{user}/update', 'TeamController@updatemember');
        Route::get('/team/{user}/permissions', 'TeamController@editpermissions');
        Route::patch('/team/{user}/permissions', 'TeamController@updatepermissions');
        Route::delete('/teammember/{user}', 'TeamController@destroymember');
    });

    Route::middleware('project.auth:administer-project')->group(function () {
        Route::resource('/sites', 'SiteController')->except('show');
        Route::resource('/arms', 'ArmController')->except('show');
        Route::resource('/events', 'EventController')->except('show');
        Route::resource('/sampletypes', 'SampleTypesController')->except('show');
        Route::resource('/tubelabeltype', 'TubeLabelTypeController')->except('show');
        Route::get('/tubelabeltype/{tubelabeltype}/override', 'TubeLabelTypeController@override');
    });

    Route::middleware('project.auth:manage-subjects')->group(function () {
        Route::resource('/subjects', 'SubjectController');
        // Route::get('/subjects/enrol', 'SubjectController@enrol');
        Route::post('/subjects/{subject}/enrol', 'SubjectController@enrol');
        Route::post('/subjects/{subject}/switch', 'SubjectController@switch');
        Route::post('/subjects/{subject}/reverseSwitch', 'SubjectController@reverseSwitch');
        Route::post('/subjects/{subject}/drop', 'SubjectController@drop');
        Route::post('/subjects/{subject}/restore', 'SubjectController@restore');
        Route::post('/subjects/{subject}/addEvent', 'SubjectController@addEvent');

        Route::get('/schedule/{week}', 'ScheduleController@generate');

        Route::get('/labels', 'LabelController@labelqueue');
        Route::post('/labels', 'LabelController@clear');
        Route::get('/labels/queue', 'LabelController@addEventsToLabelQueue');
        Route::get('/labels/{event_subject}/queue', 'LabelController@addEventToLabelQueue');
        Route::get('/labels/print', 'LabelController@printLabels');

        Route::get('/subjectsearch/{searchterm}', 'SubjectController@search');

        Route::get('/event_subject', 'EventSubjectController@index');
        Route::get('/event_subject/retrieve', 'EventSubjectController@show');
        Route::post('/event_subject/{event_subject}', 'EventSubjectController@update');
    });

    Route::middleware('project.auth:register-samples')->group(function () {
        Route::view('/primary', 'primarysamples.register');
        Route::get('/primary/retrieve', 'PrimarySampleController@primary');
        Route::post('/primary', 'PrimarySampleController@registerprimary');
    });

    Route::middleware('project.auth:log-samples')->group(function () {
        Route::view('/primary.log', 'primarysamples.log');
        Route::get('/primary.log/retrieve', 'PrimarySampleController@primarylogging');
        Route::post('/primary.log', 'PrimarySampleController@log');
    });

    Route::middleware('project.auth:log-samples')->group(function () {
        Route::view('/derivative/parent', 'derivativesamples.parent');
        Route::view('/derivative/pse', 'derivativesamples.pse');
        Route::post('/derivative/pse', 'DerivativeSampleController@primaries');
        Route::post('/derivative/parent', 'DerivativeSampleController@parent');
        Route::get('/derivative/{event_sample}', 'DerivativeSampleController@retrieve');
        Route::post('/derivative', 'DerivativeSampleController@log');

        Route::get('/samples', 'EventSampleController@index');
        Route::get('/samples/search', 'EventSampleController@search');
        Route::post('/samples/search', 'EventSampleController@retrieve');
        Route::get('/samples/{event_sample}', 'EventSampleController@show');
        Route::get('/samples/{event_sample}/unlog', 'EventSampleController@unlog');
        Route::post('/samples/{event_sample}/volume', 'EventSampleController@volumeUpdate');
    });

    Route::middleware('project.auth:manage-samples')->group(function () {
        Route::view('/sample/logout', 'samples.logout');
        Route::patch('/sample/logout', 'EventSampleController@logout');
        Route::view('/sample/logreturn', 'samples.logreturn');
        Route::patch('/sample/logreturn', 'EventSampleController@logreturn');
        Route::view('/sample/logused', 'samples.logused');
        Route::patch('/sample/logused', 'EventSampleController@logused');
        Route::view('/sample/loglost', 'samples.loglost');
        Route::patch('/sample/loglost', 'EventSampleController@loglost');
    });

    Route::middleware('project.auth:manage-samples')->group(function () {
        // Route::resource('/manifest', 'ManifestController');
        Route::get('/manifest', 'ManifestController@index');
        Route::get('/manifest/{manifest}/samplelist', 'ManifestController@samplelist');
        Route::get('/manifest/{manifest}/itemlist', 'ManifestController@itemlist');
        Route::get('/manifest/receive', 'ManifestController@index_received');
        Route::get('/manifest/receive/{manifest}', 'ManifestController@show_received');
        Route::get('/manifest/{manifest}', 'ManifestController@show');
        Route::post('/manifest', 'ManifestController@store');
        Route::delete('/manifest/{manifest}', 'ManifestController@destroy');
        Route::post('/manifest/{manifest}/ship', 'ManifestController@ship');
        Route::post('/manifest/{manifest}/receiveall', 'ManifestController@receiveall');
        Route::post('/manifest/{manifest}/receive', 'ManifestController@receive');
        Route::post('/manifest/{manifest}/shipperLogReceived', 'ManifestController@shipperLogReceived');
        Route::post('/manifestitem', 'ManifestItemController@store');
        Route::patch('/manifestitem', 'ManifestItemController@update');
        Route::delete('/manifestitem/{manifestItem}', 'ManifestItemController@destroy');
    });

    Route::middleware('project.auth:store-samples')->group(function () {
        Route::get('/samplestore', 'SamplestoreController@listSamples');
        Route::post('/samplestore', 'SamplestoreController@allocateStorage');
        Route::get('/samplestore/report', 'SamplestoreController@reportList');
        Route::get('/samplestore/{storageReport}/report', 'SamplestoreController@report');
        Route::get('/samplestore/status', 'SamplestoreController@storageStatusReport');
        Route::get('/samplestore/nexus', 'SamplestoreController@nexusReport');
        Route::get('/physicalUnit/{physicalUnit}', 'PhysicalUnitController@show');
    });

    Route::middleware('project.auth:monitor-progress')->group(function () {
        Route::get('/progress', 'ProgressController@index');
    });

    Route::get('/datafiles', 'DatafileController@index');
    Route::middleware('project.auth:manage-datafiles')->group(function () {
        Route::resource('/datafiles', 'DatafileController')->except('index', 'show');
    });
    Route::get('/datafiles/{datafile}', 'DatafileController@show');
    Route::get('/datafiles/{datafile}/download', 'DatafileController@download');

    Route::middleware('project.auth:administer-project')->group(function () {
        // Route::group(['middleware' => ['role:admin|sysadmin']], function () {
        Route::get('/redcap/arms', 'RedcapController@arms');
        Route::get('/redcap/events', 'RedcapController@events');
        Route::get('/redcap/user/', 'RedcapController@users');
        Route::get('/redcap/user/direct', 'RedcapController@usersdirect');
        Route::get('/redcap/project', 'RedcapController@project');
        Route::get('/redcap/projects', 'RedcapController@projectlist');
    });

    Route::post('/storagebox/search', 'StorageboxController@search');
    Route::resource('/storagebox', 'StorageboxController');
});

// URL::forceScheme('https');
