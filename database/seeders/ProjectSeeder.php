<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProjectSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $projects = array(
      'WFFLix' => 'Grootste aanbieder voor videomateriaal op gebied van programmeren.',
      'Flevosap' => 'Aanbieder van heerlijke vruchten/groente sappen.'
    );
    foreach ($projects as $key => $value) {
      DB::table('projects')->insert([
        'name' => $key,
        'description' => $value,
          'project_end_date'=> Carbon::create('2022', '01', '01'),

      ]);
    }
  }
}
