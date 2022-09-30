<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Story;
use App\Models\Sprint;
use App\Models\State;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProjectStoryController extends Controller
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
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Http\Response
   */
  public function index(Project $project)
  {
    return view('project.story.index', [
      'title' => 'Backlog',
      'project' => $project->name,
      'projectId' => $project->id,
      'states' => State::all(),
      'active' => True
    ]);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Http\Response
   */
  public function create(Project $project)
  {
    $return = redirect()
      ->action(
        [ProjectStoryController::class, 'index'],
        ['project' => $project->name])
      ->getTargetUrl();

    return view('project.story.create', [
      'title' => 'Create Story',
      'project' => $project->name,
      'return' => $return
    ]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Project  $project
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request, Project $project)
  {
    $points = ['?', '1', '2', '3', '5', '8', '13', '21'];
    $states = [];
    $allStates = State::all();
    foreach ($allStates as $state) {
      $states[$state->name] = $state->id;
    }

    $validator = Validator::make($request->all(), [
      'title' => 'required|string|min:8|max:100',
      'description' => 'required|string|min:8|max:255',
      'points' => [
        'required',
        Rule::in($points)
      ]
    ]);

    if ($validator->fails()) {
      return back()
        ->withErrors($validator)
        ->withInput();
    }

    $story = new Story;
    $story->title = $request->title;
    $story->description = $request->description;
    $story->project_id = $project->id;
    if ($request->points == '?') {
      $story->points = null;
      $story->state_id = $states['Suggested'];
    } else {
      $story->points = $request->points;
      $story->state_id = $states['Accepted'];
    }
    $story->save();
    return redirect()
      ->action(
        [ProjectStoryController::class, 'edit'],
        ['project' => $project->name, 'story' => $story->id])
      ->with('success', 'Successfully created User Story');
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Project  $project
   * @param  \App\Models\Story  $story
   * @return \Illuminate\Http\Response
   */
  public function show(Project $project, Story $story)
  {
      //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Project  $project
   * @param  \App\Models\Story  $story
   * @return \Illuminate\Http\Response
   */
  public function edit(Project $project, Story $story)
  {
    $sprints = $project
      ->sprints()
      ->get(['sprints.id', 'sprints.name']);
    $state = State::find($story->state_id)->name;

    $return = redirect()
      ->action(
        [ProjectStoryController::class, 'index'],
        ['project' => $project->name])
      ->getTargetUrl();

    return view('project.story.edit', [
      'title' => 'Edit Story',
      'project' => $project->name,
      'sprints' => $sprints,
      'story' => $story,
      'state' => $state,
      'return' => $return
    ]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Project  $project
   * @param  \App\Models\Story  $story
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Project $project, Story $story)
  {
    $states = [];
    $allStates = State::all();
    foreach ($allStates as $state) {
      $states[$state->name] = $state->id;
    }
    $points = ['?', '1', '2', '3', '5', '8', '13', '21'];
    $backlogs = [];
    $sprints = $project->sprints()->get();
    foreach ($sprints as $sprint) {
      $sprint->start = Carbon::parse(strtotime($sprint->start));
      $sprint->end = Carbon::parse(strtotime($sprint->end));
      array_push($backlogs, $sprint->id);
    }
    array_push($backlogs, 'backlog');

    $validator = Validator::make($request->all(), [
      'title' => 'required|string|min:8|max:100',
      'description' => 'required|string|min:8|max:255',
      'points' => [
        'required',
        Rule::in($points)
      ],
      'sprint' => [
        'required',
        Rule::in($backlogs)
      ]
    ]);

    if ($validator->fails()) {
      return back()
        ->withErrors($validator)
        ->withInput();
    }

    $story->title = $request->title;
    $story->description = $request->description;
    if ($request->points == '?') {
      $story->state_id = $states['Suggested'];
      $story->sprint_id = null;
      $story->points = null;
    } elseif ($request->sprint == 'backlog') {
      $story->state_id = $states['Accepted'];
      $story->sprint_id = null;
      $story->points = $request->points;
    } else {
      if ($story->state_id == $states['Suggested'] ||
        $story->state_id == $states['Accepted'])
      {
        $story->state_id = $states['Planned'];
      }
      $story->sprint_id = $project->sprints()
        ->find($request->sprint)
        ->id;
      $story->points = $request->points;
    }
    $story->save();
    return back()
      ->with('success', 'Story is successfully updated!');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Project  $project
   * @param  \App\Models\Story  $story
   * @return \Illuminate\Http\Response
   */
  public function destroy(Project $project, Story $story)
  {
    $story->delete();
    return redirect()
      ->action(
        [ProjectStoryController::class, 'index'],
        ['project' => $project->name])
      ->with('success', "Story '$story->title' is successfully deleted!");
  }
}
