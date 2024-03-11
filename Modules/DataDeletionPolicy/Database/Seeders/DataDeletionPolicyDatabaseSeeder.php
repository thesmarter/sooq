<?php

namespace Modules\DataDeletionPolicy\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DataDeletionPolicyDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('psx_core_data_deletions')->insert([
            'content' => '<p><img alt="" src="https://smart.sd/flutter-buysell/assets/ckeditor_fileupload/upload/jess-bailey-l3N9Q27zULw-unsplash (1).jpg" style="height:525px; width:700px" /></p>

<p>&nbsp;</p>

<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque venenatis luctus nunc, in ultrices orci suscipit sit amet. Proin finibus interdum elementum. Praesent et posuere augue, id consequat neque. Mauris maximus porta lacus eget fermentum. Nullam tristique justo sed sem ultrices condimentum. Nullam quis vulputate risus, sed pellentesque nibh. Ut gravida justo vel dolor elementum accumsan. Mauris fringilla magna leo. Pellentesque semper nibh ac ligula porta, sed maximus nisi aliquam. Etiam egestas turpis nibh, in finibus mauris mattis quis. Suspendisse potenti. Quisque ac mauris neque. Donec bibendum posuere quam, vitae venenatis urna viverra sit amet. Suspendisse nec diam eu turpis interdum placerat. Vivamus hendrerit, leo quis pellentesque feugiat, leo quam suscipit ipsum, quis porttitor purus est a lacus. Nulla in porta quam. Sed ac aliquam odio. Vestibulum in tincidunt augue. Quisque gravida velit quis orci blandit luctus et et est. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Aliquam erat volutpat. Cras vitae blandit sem. Fusce placerat sagittis mauris, a aliquet ligula varius vel. Proin ipsum elit, facilisis sed facilisis vitae, vestibulum vitae nunc. Aenean luctus consectetur vulputate. Cras laoreet rutrum urna, sit amet cursus arcu varius in. Suspendisse eu consequat erat. Nullam dapibus, enim ut pretium aliquet, dui ex lacinia nunc, ac porttitor massa nibh et augue. Curabitur ante felis, aliquam at rhoncus sit amet, tristique nec eros.</p>

<p>&nbsp;</p>

<p><img alt="" src="https://smart.sd/flutter-buysell/assets/ckeditor_fileupload/upload/jess-bailey-l3N9Q27zULw-unsplash (1).jpg" style="height:525px; width:700px" /></p>

<p>Morbi ultrices faucibus felis bibendum efficitur. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Pellentesque quis erat vel urna aliquet mollis eget a nisl. In fringilla efficitur urna, non aliquet nisi. Mauris et convallis orci. Pellentesque in tempor enim. Vestibulum nisl nunc, pretium ut congue auctor, eleifend vitae massa. Nunc venenatis mauris non suscipit rhoncus. Mauris vulputate tortor eget vehicula tempus. Quisque ullamcorper, dui vel pulvinar volutpat, nunc magna volutpat enim, quis imperdiet mauris nibh nec lorem. In et accumsan urna. Praesent vehicula lectus quam, id venenatis libero vestibulum ut. In bibendum mattis nisi nec mattis. Maecenas urna massa, tempor in arcu consectetur, pellentesque tincidunt elit. Mauris felis sem, cursus vitae fringilla maximus, tempor efficitur lectus. Nullam tincidunt metus tellus, eget convallis mi fringilla ut. Fusce hendrerit felis auctor sapien varius tempus. Cras molestie ornare est sed convallis. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Maecenas tristique risus ut tincidunt imperdiet. Vivamus quis lectus felis. Donec imperdiet nec libero non egestas. Cras sed nibh ut ligula maximus condimentum id in dolor. Praesent justo neque, fringilla vitae dui eget, dignissim fermentum sapien. Nunc auctor maximus ipsum in ornare. Phasellus tempus bibendum quam, et luctus arcu tristique a. Vivamus fringilla neque neque. Quisque eget laoreet mi. Curabitur elementum iaculis tristique. Curabitur convallis egestas tempor. Donec mattis tellus tellus, semper porta dui vehicula nec.</p>
',
            'added_user_id' => '1',
            'added_date' => Carbon::now(),
        ]);

        // $this->call("OthersTableSeeder");
    }
}
