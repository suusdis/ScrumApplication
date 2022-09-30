<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RetrospectiveSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $sprints = array(1,5);
    $retrospectives = array(
      [1,'De technische vaardigheden moeten worden verhoogd!',0,0],
      [2,'Het werktempo moet worden versneld!',0,0],
      [3,'De samenwerking ging goed!',1,1],
      [3,'We moeten users stories duidelijker maken!',1,1],
      [4,'Er moet meer focus gelegd worden op de project definitie!',0,0],
      [5,'Mensen moet meer initiatief tonen!',0,0],
      [6,'Er moet gestopt worden met database credentials in git zetten.',0,1],
      [6,'Ik zie te weinig samenwerking!',0,0],
      [7,'Mensen houden zich netjes aan de style conventions',1,0],
      [7,'Er moet een hoger werktempo komen.',0,1],
      [8,'We hebben de expertise om dir te kunnen',0,0]
    );
    foreach ($sprints as $sprint) {
      foreach ($retrospectives as $retrospective) {
        DB::table('retrospectives')->insert([
          'user_id' => $retrospective[0],
          'sprint_id' => $sprint,
          'comment' => $retrospective[1],
          'is_good' => $retrospective[2],
          'anonymous' => $retrospective[3]
        ]);
      }
    }
  }
}
