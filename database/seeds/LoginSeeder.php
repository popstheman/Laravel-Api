<?php

use Illuminate\Database\Seeder;
use App\Login;
use Illuminate\Support\Facades\File;

class LoginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Login::truncate();
        $json = File::get("database/data/LoginData.json");
        $data = json_decode($json, true);
        foreach ($data as $obj) {
            $obj['password'] = bcrypt($obj['password']);
            Login::create($obj);
        }
    }
}
