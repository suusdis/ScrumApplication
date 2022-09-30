<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $sprintIds = [
      1 => 'Zorg ervoor dat jullie de project definitie in orde hebben!',
      2 => 'De voorkant ziet er netjes uit, maar hij werkt niet! Zorg voor de backend!',
      3 => 'De minimum eisen zijn er, misschien nog mooi om wat extra\'s er in te zetten',
      4 => 'Zorg er voor dat volgende keer geen nederlands in de code staat.',
      5 => 'De usecase diagrammen kloppen niet, kijk goed naar "Alternative Scenario" deze bereikt het doel niet.',
      6 => 'De voorkant ziet er netjes uit, maar hij werkt niet! Zorg voor de backend!',
      7 => 'De minimum eisen zijn er, misschien nog mooi om wat extra\'s er in te zetten',
      8 => 'Zorg er voor dat volgende keer geen nederlands in de code staat.'
    ];
    foreach ($sprintIds as $sprintId => $feedback) {
      DB::table('reviews')->insert([
        'feedback' => $feedback,
        'sprint_id' => $sprintId
      ]);
    }
  }
}
