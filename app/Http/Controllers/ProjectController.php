<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Sprint;
use App\Models\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
      $this->middleware('auth');
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $user = User::find(Auth::id());
    $projects = $user->projects()->get();

    return view('project.index', [
      'project' => 'Scrumapp',
      'details' => $projects
    ]);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $users = User::where('id', '!=', Auth::id())->get(['id', 'name']);
    return view('project.create', [
      'title' => 'Create Project',
      'project' => 'Scrumapp',
      'users' => $users
    ]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $now = date('Y-m-d');
    $validator = Validator::make($request->all(), [
      'projectName' => [
        'required',
        'max:100',
        'unique:App\Models\Project,name',
        Rule::notIn(['Scrumapp'])
      ],
      'projectDescription' => 'required|max:255',
      'projectEndDate' => "required|date|after:$now",
      'users' => 'required'
    ]);

    if ($validator->fails())
    {
      return back()
        ->withErrors($validator)
        ->withInput();
    }

    // Save name, description and end date.
    $project = New Project;
    $project->name = $request->projectName;
    $project->description = $request->projectDescription;
    $project->project_end_date = $request->projectEndDate;
    $project->save();

    $project->users()->attach(Auth::id());
    foreach ($request->users as $rowId => $userId) {
      $project->users()->attach($userId);
    }
    $project->save();
    return redirect()
      ->action([ProjectController::class, 'index'])
      ->with('success', "Project $project->name has been created!");
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Http\Response
   */
  public function show(Project $project)
  {
    return redirect()
      ->action(
        [ProjectStoryController::class, 'index'],
        ['project' => $project->name]);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Http\Response
   */
  public function edit(Project $project)
  {
    $users = $project->users()->get();
    $project->project_end_date = Carbon::parse($project->project_end_date);
    return view('project.edit', [
      'title' => 'Edit Project',
      'project' => $project->name,
      'details' => $project,
      'users' => $users,
      'return' => "/projects/$project->name"
    ]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Project $project)
  {
    $now = date('Y-m-d');
    $allowedForms = ['details', 'addUser', 'dismissUser', 'archive'];
    $validator = Validator::make($request->all(), [
      '_form' => ['required', 'string', Rule::in($allowedForms)]
    ]);

    if ($validator->fails()) {
      return redirect()
        ->action(
          [ProjectController::class, 'index'],
          ['project' => $project->name])
        ->withErrors($validator)
        ->withInput();
    }

    if ($request->_form == $allowedForms[0]) {
      $validator = Validator::make($request->all(), [
        'name' => 'required|max:100',
        'end' => "required|date|after:$now",
        'description' => 'required|max:255'
      ]);
      if ($validator->fails()) {
        return back()
          ->withErrors($validator)
          ->withInput();
      }
      $project->name = $request->name;
      $project->project_end_date = $request->end;
      $project->description = $request->description;
    } elseif ($request->_form == $allowedForms[1]) {
      $validator = Validator::make($request->all(), [
        'addUser' => 'required|exists:users,name'
      ]);
      if ($validator->fails()) {
        return redirect()
          ->withErrors($validator)
          ->withInput();
      }
      $user = User::where('name', $request->addUser)->get()->toArray();
      $project->users()->attach($user[0]['id']);
    } elseif ($request->_form == $allowedForms[2]) {
      $validator = Validator::make($request->all(), [
        'dismissUser' => 'required|integer|exists:users,id'
      ]);
      if ($validator->fails()) {
        return redirect()
          ->withErrors($validator)
          ->withInput();
      }
      $user = User::find($request->dismissUser);
      $project->users()->detach($user->id);
    }
    $project->save();
    return redirect()
      ->action(
        [ProjectController::class, 'edit'],
        ['project' => $request->name])
      ->with('success', "Project settings have been successfully updated!");
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Http\Response
   */
  public function destroy(Project $project)
  {
      //
  }
}
