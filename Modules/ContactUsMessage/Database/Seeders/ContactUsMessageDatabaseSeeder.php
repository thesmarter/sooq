<?php

namespace Modules\ContactUsMessage\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\ContactUsMessage\Database\Seeders\ContactTableSeeder;

class ContactUsMessageDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // seeding data generate
        $this->call([
            ContactTableSeeder::class
        ]);
    }
}
