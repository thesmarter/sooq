<?php

namespace Modules\ContactUsMessage\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ContactTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('psx_contacts')->insert([
            'contact_name' => 'User 1',
            'contact_phone' => '09123456789',
            'contact_email' => 'user1@gmail.com',
            'contact_message' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus consequat orci nec consectetur efficitur. Nam porta vitae nunc eget facilisis. Aliquam scelerisque tempus eros, eu volutpat erat porta porta. ',
            'added_user_id' => '1',
            'added_date'=>Carbon::now(),
        ]);

        DB::table('psx_contacts')->insert([
            'contact_name' => 'User 2',
            'contact_phone' => '09123456789',
            'contact_email' => 'user2@gmail.com',
            'contact_message' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus consequat orci nec consectetur efficitur. Nam porta vitae nunc eget facilisis. Aliquam scelerisque tempus eros, eu volutpat erat porta porta. ',
            'added_user_id' => '1',
            'added_date'=>Carbon::now(),
        ]);

        DB::table('psx_contacts')->insert([
            'contact_name' => 'User 3',
            'contact_phone' => '09123456789',
            'contact_email' => 'user3@gmail.com',
            'contact_message' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus consequat orci nec consectetur efficitur. Nam porta vitae nunc eget facilisis. Aliquam scelerisque tempus eros, eu volutpat erat porta porta. ',
            'added_user_id' => '1',
            'added_date'=>Carbon::now(),
        ]);

        DB::table('psx_contacts')->insert([
            'contact_name' => 'User 4',
            'contact_phone' => '09123456789',
            'contact_email' => 'user4@gmail.com',
            'contact_message' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus consequat orci nec consectetur efficitur. Nam porta vitae nunc eget facilisis. Aliquam scelerisque tempus eros, eu volutpat erat porta porta. ',
            'added_user_id' => '1',
            'added_date'=>Carbon::now(),
        ]);

        DB::table('psx_contacts')->insert([
            'contact_name' => 'User 5',
            'contact_phone' => '09123456789',
            'contact_email' => 'user5@gmail.com',
            'contact_message' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus consequat orci nec consectetur efficitur. Nam porta vitae nunc eget facilisis. Aliquam scelerisque tempus eros, eu volutpat erat porta porta. ',
            'added_user_id' => '1',
            'added_date'=>Carbon::now(),
        ]);

        DB::table('psx_contacts')->insert([
            'contact_name' => 'User 6',
            'contact_phone' => '09123456789',
            'contact_email' => 'user6@gmail.com',
            'contact_message' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus consequat orci nec consectetur efficitur. Nam porta vitae nunc eget facilisis. Aliquam scelerisque tempus eros, eu volutpat erat porta porta. ',
            'added_user_id' => '1',
            'added_date'=>Carbon::now(),
        ]);
    }
}
