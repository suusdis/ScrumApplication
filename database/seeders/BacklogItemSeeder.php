<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BacklogItemSeeder extends Seeder
{

  function suggested($table, $state, $projectId) {
    $items = [
      'As a user I want a Navbar to better navigate the page',
      'As a user I want a Footer to get more to get contact info'
    ];
    foreach ($items as $item) {
      $now = Carbon::now();
      DB::table($table)->insert([
        'title' => $item,
        'state_id' => $state,
        'project_id' => $projectId,
        'created_at' => $now,
        'updated_at' => $now,
      ]);
    }
  }

  function accepted($table, $state, $projectId) {
    $items = [
      ['As a user I want a Home page in order to get an introduction to the site',5],
      ['As a user I want a Contact page in order to send: questions, get more info, etc.',8]
    ];
    foreach ($items as $item) {
      $now = Carbon::now();
      DB::table($table)->insert([
        'title' => $item[0],
        'points' => $item[1],
        'state_id' => $state,
        'project_id' => $projectId,
        'created_at' => $now,
        'updated_at' => $now,
      ]);
    }
  }

  function sprintBacklog($table, $state, $activeSprint, $projectId) {
    $items = [
      [
        'As a user I want a Register page in order to create an accout',
        'Step 1: Create a design. Step 2: Implement design. Step 3: Create database structure. Step 4: Create backend code',
        21,
        3
      ],
      [
        'As a user I want a Login page in order to login',
        'Step 1: Create a design. Step 2: Implement design. Step 3: Create backend code',
        13,
        3
      ],
      [
        'As a user I want a Courses page in order to choose a Programming course',
        'Step 1: Create a design. Step 2: Implement design. Step 3: Create backend code',
        8,
        4
      ],
      [
        'As a user I want a Shopping cart in order to view and buy added items',
        'Step 1: Create a design. Step 2: Implement design. Step 3: Create backend code',
        21,
        4
      ],
      [
        'As a user I want a Products page in order to get a list of buyable products',
        'Step 1: Create a design. Step 2: Implement design. Step 3: Create database structure Step 4: Create backend code',
        13,
        5
      ],
      [
        'As a user I want a Comment section in order to post constructive critism',
        'Step 1: Create a design. Step 2: Implement design. Step 3: Create database structure Step 4: Create backend code',
        21,
        6
      ]
    ];
    if ($state) {
      foreach ($items as $item) {
        $now = Carbon::now();
        DB::table($table)->insert([
          'title' => $item[0],
          'description' => $item[1],
          'points' => $item[2],
          'state_id' => $state,
          'sprint_id' => $activeSprint,
          'project_id' => $projectId,
          'created_at' => $now,
          'updated_at' => $now,
        ]);
      }
      return;
    }
    foreach ($items as $item) {
      $now = Carbon::now();
      DB::table($table)->insert([
        'title' => $item[0],
        'description' => $item[1],
        'points' => $item[2],
        'state_id' => $item[3],
        'sprint_id' => $activeSprint,
        'project_id' => $projectId,
        'created_at' => $now,
        'updated_at' => $now,
      ]);
    }
  }

  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $table = 'backlog_items';
    $doneSprints = array(
      1 => 1,
      2 => 5
    );
    $activeSprints = array(
      1 => 2,
      2 => 6
    );
    $plannedSprints = array(
      1 => 3,
      2 => 7
    );
    foreach (array(1,2) as $projectId) {
      self::suggested($table, 1, $projectId);
      self::accepted($table, 2, $projectId);
    }
    foreach ($doneSprints as $projectId => $doneSprint) {
      self::sprintBacklog($table, 6, $doneSprint, $projectId);
    }
    foreach ($activeSprints as $projectId => $activeSprint) {
      self::sprintBacklog($table, False, $activeSprint, $projectId);
    }
    foreach ($plannedSprints as $projectId => $plannedSprint) {
      self::sprintBacklog($table, 3, $plannedSprint, $projectId);
    }

  }
}
