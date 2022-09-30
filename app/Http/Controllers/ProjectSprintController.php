<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Sprint;
use App\Models\Story;
use App\Models\Review;
use App\Models\State;
use App\Models\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Types\False_;

class ProjectSprintController extends Controller
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
   * @param Project $project
   * @return Response
   */
  public function index(Project $project)
  {
    $now = Carbon::now();
    $sprints = $project->sprints()->get();

    foreach ($sprints as $sprint) {
      // convert date timestamp to Carbon Object
      $sprint->start = Carbon::parse(strtotime($sprint->start));
      $sprint->end = Carbon::parse(strtotime($sprint->end));

      if ($now->greaterThan($sprint->end)) {
        $sprint->status = array('Done', 'success');
      } elseif ($now->lessThan($sprint->start)) {
        if ($sprint->stories()->get()->isEmpty()) {
          $sprint->status = array('Accepted', 'danger');
        } else {
          $sprint->status = array('Planned', 'warning');
        }
      } else {
        $sprint->status = array('In-Progress', 'primary');
      }
    }
    return view('project.sprint.index', [
      'title' => 'Planning',
      'project' => $project->name,
      'sprints' => $sprints,
    ]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param \Illuminate\Http\Request $request
   * @param \App\Models\Project $project
   * @param \App\Models\Sprint $sprint
   * @return \Illuminate\Http\Response
   */
  public function review(Request $request, Project $project, Sprint $sprint)
  {
    $validator = Validator::make($request->all(), [
      'feedback' => 'required|string|min:8|max:16384'
    ]);

    if ($validator->fails()) {
      return back()
        ->withErrors($validator)
        ->withInput();
    }

    $review = Review::firstWhere('sprint_id', $sprint->id);
    if ($review->get()->isEmpty()) {
      $review = new Review;
    }

    $review->feedback = $request->feedback;
    $review->sprint_id = $sprint->id;
    $review->save();

    return back()
      ->with('success', 'Review has been updated!');
  }

  /**
   * Show the form for creating a new resource.
   *
   * @param Project $project
   * @return Response
   */
  public function create(Project $project)
  {
    $sprints = $project->sprints()->get();
    foreach ($sprints as $sprint) {
      $sprint->start = date('d-m-Y', strtotime($sprint->start));
      $sprint->end = date('d-m-Y', strtotime($sprint->end));
    }

    $return = redirect()
      ->action(
        [ProjectSprintController::class, 'index'],
        ['project' => $project->name])
      ->getTargetUrl();

    return view('project.sprint.create', [
      'title' => 'Create Sprint',
      'project' => $project->name,
      'sprints' => $sprints,
      'num' => 0,
      'return' => $return
    ]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param \Illuminate\Http\Request $request
   * @param Project $project
   * @return Response
   */
  public function store(Request $request, Project $project)
  {
    $now = date('Y-m-d', strtotime('yesterday'));
    $lastSprintEndDate = $project->sprints()
      ->orderByDesc('id')
      ->limit(1)
      ->get('end');
    if ($lastSprintEndDate->isEmpty()) {
        $validated = $request->validate([
          'sprint' => 'required|max:100',
          'goal' => 'required|max:255',
          'start' => "required|date|after:$now",
          'end' => "required|date"
        ]);
    } else {
        $lastSprintEndDate = $lastSprintEndDate[0]->end;
        $validated = $request->validate([
          'sprint' => 'required|max:100',
          'goal' => 'required|max:255',
          'start' => "required|date|after:$now|after:$lastSprintEndDate",
          'end' => "required|date"
        ]);
    }
    $sprint = new Sprint;
    $sprint->name = $validated['sprint'];
    $sprint->goal = $validated['goal'];
    $sprint->start = $validated['start'];
    $sprint->end = $validated['end'];
    $sprint->project_id = $project->id;
    $sprint->save();

    return redirect()
      ->action(
        [ProjectSprintController::class, 'index'],
        ['project' => $project->name])
      ->with('success', "Successfully created $sprint->name!");
  }

  /**
   * Display the specified resource.
   *
   * @param Project $project
   * @param Sprint $sprint
   * @return Response
   */
  public function show(Project $project, Sprint $sprint)
  {
    $review = $sprint->review()->get();
    if ($review->isEmpty()) {
      $review = False;
    } else {
      $review = $review[0];
    }

    $metrics = ['total' => 0, 'velocity' => 0];
    $stories = $sprint->stories()->get();
    $done = State::firstWhere('name', 'Done')->id;
    $doneStories = $sprint->stories()->where('state_id', $done)->get();

    foreach ($stories as $story) {
      $metrics['total'] = $metrics['total'] + $story->points;
    }
    foreach ($doneStories as $doneStory) {
      $metrics['velocity'] = $metrics['velocity'] + $doneStory->points;
    }

    if ($metrics['velocity'] <= self::getPercentOfNumber($metrics['total'], 33)) {
      $metrics['color'] = 'danger';
    } elseif ($metrics['velocity'] <= self::getPercentOfNumber($metrics['total'], 66)) {
      $metrics['color'] = 'warning';
    } elseif ($metrics['velocity'] != $metrics['total']) {
      $metrics['color'] = 'primary';
    } else {
      $metrics['color'] = 'success';
    }

    $return = redirect()
      ->action(
        [ProjectSprintController::class, 'index'],
        ['project' => $project->name])
      ->getTargetUrl();

    return view('project.sprint.show', [
      'title' => 'Show Sprint',
      'project' => $project->name,
      'sprint' => $sprint,
      'stories' => $stories,
      'review' => $review,
      'metrics' => $metrics,
      'return' => $return
    ]);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param Project $project
   * @param Sprint $sprint
   * @return Response
   */
  public function edit(Project $project, Sprint $sprint)
  {
    $now = Carbon::now();
    $details = $project->sprints()->get();
    foreach ($details as $detail) {
      $detail->start = Carbon::parse(strtotime($detail->start));
      $detail->end = Carbon::parse(strtotime($detail->end));
    }

    $return = redirect()
      ->action(
        [ProjectSprintController::class, 'index'],
        ['project' => $project->name])
      ->getTargetUrl();

    return view('project.sprint.edit', [
      'title' => 'Edit Sprint',
      'project' => $project->name,
      'now' => $now,
      'sprint' => $sprint,
      'details' => $details,
      'num' => 0,
      'return' => $return
    ]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param \Illuminate\Http\Request $request
   * @param Project $project
   * @param Sprint $sprint
   * @return Response
   */
  public function update(Request $request, Project $project, Sprint $sprint)
  {
    $validator = Validator::make($request->all(), [
      'sprint' => 'required|max:100',
      'goal' => 'required|max:255'
    ]);

    if ($validator->fails()) {
      return redirect()
        ->action(
          [ProjectSprintController::class, 'edit'],
          ['project' => $project->name, 'sprint' => $sprint->id])
        ->withErrors($validator)
        ->withInput();
    }

    $sprint->name = $request->sprint;
    $sprint->goal = $request->goal;
    $sprint->save();
    return redirect()
      ->action(
        [ProjectSprintController::class, 'edit'],
        ['project' => $project->name, 'sprint' => $sprint->id])
      ->with('success', "$sprint->name has been updated!");
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param Project $project
   * @param Sprint $sprint
   * @return Response
   * @throws \Exception
   */
  public function destroy(Project $project, Sprint $sprint)
  {
    $sprint->stories()->delete();
    $sprint->delete();
    return redirect()
      ->action(
        [ProjectSprintController::class, 'index'],
        ['project' => $project->name])
      ->with('success', "Sprint '$sprint->name' is successfully deleted!");
  }

  function getPercentOfNumber($number, $percent) {
    return ($percent / 100) * $number;
  }

  /**
   * Show the page moving stories to done.
   *
   * @param \App\Models\Project $project
   * @param \App\Models\Sprint $sprint
   * @return \Illuminate\Http\Response
   */
  public function taskboard(Project $project, Sprint $sprint) {
    $users = array();
    $userObjs = User::all();
    foreach ($userObjs as $userObj) {
      $users[$userObj->id] = $userObj->name;
    }
    $allSprints = $project->sprints()->get(['id', 'name']);
    $states = State::where('id', '>', 2)->get('name')->toArray();
    $stories = [];
    foreach ($states as $state) {
      $stories[$state['name']] = Story::join(
        'states',
        'state_id',
        '=',
        'states.id'
      )->where(
        'sprint_id',
        $sprint->id
      )->where(
        'states.name',
        $state['name']
      )->get([
        'backlog_items.id AS id',
        'title',
        'description',
        'points',
        'sprint_id',
        'user_id',
        'states.name AS state',
        'bootstrap_color'
      ]);
    }
    $now = Carbon::now();
    $sprint->start = Carbon::parse(strtotime($sprint->start));
    $sprint->end = Carbon::parse(strtotime($sprint->end));
    if ($now->greaterThan($sprint->end)) {
      $sprint->status = array('Done', 'success');
    } elseif ($now->lessThan($sprint->start)) {
      return back()->with('error', 'This sprint is in the furture, and is not yet active!');
    } else {
      $sprint->status = array('In-Progress', 'primary');
    }

    return view('project.sprint.taskboard', [
      'title' => 'Taskboard',
      'project' => $project->name,
      'sprint' => $sprint,
      'stories' => $stories,
      'allSprints' => $allSprints,
      'users' => $users
    ]);
  }

  /**
   * Show the page moving stories to done.
   *
   * @param \Illuminate\Http\Request $request
   * @param \App\Models\Project $project
   * @param \App\Models\Sprint $sprint
   * @return \Illuminate\Http\Response
   */
  public function take(Request $request, Project $project, Sprint $sprint) {
    $validator = Validator::make($request->all(), [
      'story' => [
        'required',
        'int',
        Rule::exists('backlog_items', 'id')->where('sprint_id', $sprint->id)
      ]
    ]);

    if ($validator->fails()) {
      return back()
        ->withErrors($validator)
        ->withInput();
    }

    $story = Story::find($request->story);
    $story->user_id = Auth::id();
    $story->save();
    return back();
  }

  public function state(Request $request, Project $project, Sprint $sprint) {
    $validator = Validator::make($request->all(), [
      'state' => [
        'required',
        'string',
        Rule::exists('states', 'name')
      ],
      'story' => [
        'required',
        'int',
        Rule::exists('backlog_items', 'id')->where('sprint_id', $sprint->id)
      ]
    ]);

    if ($validator->fails()) {
      return back()
        ->withErrors($validator)
        ->withInput();
    }

    $states = [];
    $stateObjs = State::all();
    foreach ($stateObjs as $stateObj) {
      $states[$stateObj->name] = $stateObj->id;
    }

    $story = Story::find($request->story);
    $story->state_id = $states[$request->state];
    $story->save();
    return back();
  }

  public function redirectToTaskboard(Project $project) {
    $sprintId = 0;
    $now = Carbon::now();
    $sprints = $project->sprints()->get();
    foreach ($sprints as $sprint) {
      $sprint->start = Carbon::parse(strtotime($sprint->start));
      $sprint->end = Carbon::parse(strtotime($sprint->end));
      if ($now->isBetween($sprint->start, $sprint->end)) {
        $sprintId = $sprint->id;
        break;
      }
    }
    if ($sprintId === 0) {
      return back();
    }

    return redirect()
      ->action(
        [ProjectSprintController::class, 'taskboard'],
        ['project' => $project->name, 'sprint' => $sprintId]);
  }
}
