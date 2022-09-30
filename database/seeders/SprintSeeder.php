<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SprintSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $projects = array(1, 2);
    $sprints = array(
      [
        'Sprint Null',
        'Set up basic framework to build up on.',
        '2020-12-14',
        '2020-12-25',
        28,
      ],
      [
        'Sprint 1',
        'Complete the frontend. And start get basic functionality working',
        '2020-12-28',
        '2021-01-08',
        31,
      ],
      [
        'Sprint 2',
        'Complete requirements in MVP.',
        '2021-01-11',
        '2021-01-22',
        34,
      ],
      [
        'Hardening Sprint',
        'Fix any problems: Bugs, Weird behavior etc. And hand product in.',
        '2021-01-25',
        '2021-02-05',
        14,
      ]
    );
    foreach ($projects as $project) {
      foreach ($sprints as $sprint) {
        DB::table('sprints')->insert([
          'name' => $sprint[0],
          'goal' => $sprint[1],
          'start' => $sprint[2],
          'end' => $sprint[3],
          'velocity' => $sprint[4],
          'project_id' => $project
        ]);
      }
    }
  }
}
