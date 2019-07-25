<?php

use Illuminate\Database\Seeder;

class SelectionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $selections = factory(App\Selection::class, 150)->create();
    }
}
