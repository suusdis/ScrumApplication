<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectUserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    for ($i=1; $i < 11; $i++) {
      DB::table('project_user')->insert([
        'project_id' => 1,
        'user_id' => $i
      ]);
    };
    for ($i=1; $i < 7; $i++) {
      DB::table('project_user')->insert([
        'project_id' => 2,
        'user_id' => $i
      ]);
    };
  }
}
