<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StateSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $states = array(
      'Suggested' => 'secondary',
      'Accepted' => 'danger',
      'Planned' => 'warning',
      'In-Progress' => 'primary',
      'In-Review' => 'info',
      'Done' => 'success'
    );
    foreach ($states as $state => $color) {
      DB::table('states')->insert([
        'name' => $state,
        'bootstrap_color' => $color
      ]);
    }
  }
}
