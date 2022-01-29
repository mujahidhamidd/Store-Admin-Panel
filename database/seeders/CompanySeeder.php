<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $data = [
            ['name' => 'John'],
            ['name' => 'Maria'],
            ['name' => 'Julia'],
        ];

        Company::insert($data);
    }
}
