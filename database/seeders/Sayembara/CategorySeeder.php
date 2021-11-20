<?php

namespace Database\Seeders\Sayembara;

use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sayembaraCategories = [
            'Lost',
            'Challenge',
            'Help',
            'Government',
            'Creativity',
        ];

        foreach ($sayembaraCategories as $value){
            $dataInsert = [
                'name' => $value,
                'is_active' => true
            ];
            \App\Models\Sayembara\Category::query()->firstOrCreate($dataInsert);
        }

    }
}
