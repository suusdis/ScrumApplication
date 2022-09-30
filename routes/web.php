<?php

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


// Redirects
Route::redirect('/', '/projects');

Auth::routes();

Route::resource(
  'projects',
  App\Http\Controllers\ProjectController::class
)->scoped([
  'project' => 'name'
]);

Route::resource(
  'projects.stories',
  App\Http\Controllers\ProjectStoryController::class
)->scoped([
  'project' => 'name'
]);

Route::put(
  '/projects/{project:name}/sprints/{sprint}/review',
  [App\Http\Controllers\ProjectSprintController::class, 'review']
);
Route::get(
  '/projects/{project:name}/taskboard',
  [App\Http\Controllers\ProjectSprintController::class, 'redirectToTaskboard']
);
Route::get(
  '/projects/{project:name}/sprints/{sprint}/taskboard',
  [App\Http\Controllers\ProjectSprintController::class, 'taskboard']
);
Route::put(
  '/projects/{project:name}/sprints/{sprint}/taskboard/take',
  [App\Http\Controllers\ProjectSprintController::class, 'take']
);
Route::put(
  '/projects/{project:name}/sprints/{sprint}/taskboard/state',
  [App\Http\Controllers\ProjectSprintController::class, 'state']
);
Route::resource(
  'projects.sprints',
  App\Http\Controllers\ProjectSprintController::class
)->scoped([
  'project' => 'name'
]);
