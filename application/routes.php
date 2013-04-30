<?php

Route::controller('home');
Route::controller('admin');
Route::controller('faculty');

Route::any('admin/file_upload', 'admin@file_upload');
Route::any('admin/scan', 'admin@scan');
Route::any('admin/scheduler', 'admin@scheduler');
Route::any('admin/fill_prefs', 'admin@fill_prefs');
Route::any('admin/display_output', 'admin@display_output');
Route::any('admin/delete_version', 'admin@delete_version');
Route::any('admin/edit_course', 'admin@edit_course');
Route::any('admin/update_container', 'admin@update_container');
Route::any("admin/publish_schedule", 'admin@publish_schedule');
Route::any("admin/unlock_user", 'admin@unlock_user');

Route::any("display_published_output", 'home@display_published_output');

Route::any('faculty/submit_prefs', 'faculty@submit_prefs');
Route::any('faculty/retrieve_prefs', 'faculty@retrieve_prefs');

Route::filter('pattern: admin/*', 'check_user');
Route::filter('pattern: faculty/*', 'check_user');

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Simply tell Laravel the HTTP verbs and URIs it should respond to. It is a
| breeze to setup your application using Laravel's RESTful routing and it
| is perfectly suited for building large applications and simple APIs.
|
| Let's respond to a simple GET request to http://example.com/hello:
|
|		Route::get('hello', function()
|		{
|			return 'Hello World!';
|		});
|
| You can even respond to more than one URI:
|
|		Route::post(array('hello', 'world'), function()
|		{
|			return 'Hello World!';
|		});
|
| It's easy to allow URI wildcards using (:num) or (:any):
|
|		Route::put('hello/(:any)', function($name)
|		{
|			return "Welcome, $name.";
|		});
|
*/

Route::get('/', function()
{
	$published_schedules = Schedule::where_is_published(1)->get();
  return View::make('home.index')->with("schedules", $published_schedules);
});

/*
|--------------------------------------------------------------------------
| Application 404 & 500 Error Handlers
|--------------------------------------------------------------------------
|
| To centralize and simplify 404 handling, Laravel uses an awesome event
| system to retrieve the response. Feel free to modify this function to
| your tastes and the needs of your application.
|
| Similarly, we use an event to handle the display of 500 level errors
| within the application. These errors are fired when there is an
| uncaught exception thrown in the application.
|
*/

Event::listen('404', function()
{
	return Response::error('404');
});

Event::listen('500', function()
{
	return Response::error('500');
});

/*
|--------------------------------------------------------------------------
| Route Filters
|--------------------------------------------------------------------------
|
| Filters provide a convenient method for attaching functionality to your
| routes. The built-in before and after filters are called before and
| after every request to your application, and you may even create
| other filters that can be attached to individual routes.
|
| Let's walk through an example...
|
| First, define a filter:
|
|		Route::filter('filter', function()
|		{
|			return 'Filtered!';
|		});
|
| Next, attach the filter to a route:
|
|		Route::get('/', array('before' => 'filter', function()
|		{
|			return 'Hello World!';
|		}));
|
*/

Route::filter('before', function()
{


});

Route::filter('after', function($response)
{
	// Do stuff after every request to your application...
});

Route::filter('csrf', function()
{
	if (Request::forged()) return Response::error('500');
});

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::to('login');
});

Route::filter('check_user', function(){

  if(! Session::get('user_id'))
  {
    return view::make('home.login');
  }

});

