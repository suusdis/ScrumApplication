<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $users = [
      [
        'Suzanne Distelbrink',
        'suzannedistelbrink@hotmail.com',
        'suzannedistelbrink'
      ],
      [
        'Robin Doorenbosch',
        'doorenboschrs@gmail.com',
        'doorenboschrs'
      ],
      [
        'Wybren Terpstra',
        'wybren@apertures.nl',
        'password'
      ],
      [
        'Ghislaine El Fardaoussi',
        's1153521@student.windesheim.nl',
        'password'
      ],
      [
        'Willem König',
        's1153847@student.windesheim.nl',
        'password'
      ],
      [
        'Jeffrey Goijaerts',
        'jeffreygoijaerts@icloud.com',
        'password'
      ],
      [
        'Aman Ahmed',
        'aman16.aa17@gmail.com',
        'password'
      ],
      [
        'Ayoub El Kaoui',
        'ayoub.el.kaoui@windesheim.nl',
        'password'
      ],
      [
        'Farsan Houshiar',
        'farsan.houshiar@windesheim.nl',
        'password'
      ],
      [
        'John Doe',
        'johndoe@mail.nl',
        'password'
      ]
    ];
    foreach ($users as $user) {
      DB::table('users')->insert([
        'name' => $user[0],
        'email' => $user[1],
        'password' => Hash::make($user[2]),
      ]);
    }
  }
}
