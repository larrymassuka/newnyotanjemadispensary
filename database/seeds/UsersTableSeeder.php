<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'email' => 'larrymassuka@gmail.com',
                'name' => 'larry',
                'password' => '$2y$10$T/AO49e7BmIC9aUG/33mAOdy9yDm/SUGUZC5zU.3Gtj4Lvvf.27My', //12345678
                'education' => 'computer science',
                'location' => 'sfavasdvasd',
                'skills' => 'asdasd',
                'notes' => '4a56sd4avas',
                'contactnumber' => '0778700405',
                'user_type' => 'admin'
            ],

            [
                'email' => 'lawrencemassuka@hotmail.com',
                'name' => "lawrence",
                'password' => '$2y$10$T/AO49e7BmIC9aUG/33mAOdy9yDm/SUGUZC5zU.3Gtj4Lvvf.27My', //12345678
                'education' => 'acccccccccccccccccadc',
                'location' => 'sfavasdvasd',
                'skills' => 'asdasd',
                'notes' => '4a56sd4avas',
                'contactnumber' => '0778700405',
                'user_type' => 'doctor'
            ],

            [
                'email' => 'thedebuglaw@gmail.com',
                'name' => "thedebuglaw",
                'password' => '$2y$10$T/AO49e7BmIC9aUG/33mAOdy9yDm/SUGUZC5zU.3Gtj4Lvvf.27My', //12345678
                'education' => 'acccccccccccccccccadc',
                'location' => 'sfavasdvasd',
                'skills' => 'asdasd',
                'notes' => '4a56sd4avas',
                'contactnumber' => '714193432',
                'user_type' => 'pharmacist'
            ],

            [
                'email' => 'larrymassuka@outlook.com',
                'name' => "programmer",
                'password' => '$2y$10$T/AO49e7BmIC9aUG/33mAOdy9yDm/SUGUZC5zU.3Gtj4Lvvf.27My', //12345678
                'education' => 'acccccccccccccccccadc',
                'location' => 'sfavasdvasd',
                'skills' => 'asdasd',
                'notes' => '4a56sd4avas',
                'contactnumber' => '0778700405',
                'user_type' => 'laboratorian'
            ],

            [
                'email' => 'lawrencemassuka@gmail.com',
                'name' => "developer",
                'password' => '$2y$10$T/AO49e7BmIC9aUG/33mAOdy9yDm/SUGUZC5zU.3Gtj4Lvvf.27My', //12345678
                'education' => 'acccccccccccccccccadc',
                'location' => 'sfavasdvasd',
                'skills' => 'asdasd',
                'notes' => '4a56sd4avas',
                'contactnumber' => '703169302',
                'user_type' => 'receptionist'
            ]
        ]);
    }
}
