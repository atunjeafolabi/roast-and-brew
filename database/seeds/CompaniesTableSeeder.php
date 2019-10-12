<?php

use Illuminate\Database\Seeder;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Generates Companies and related Cafes
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\Company::class, 10)->create()->each(function ($company) {
            $cafes = factory(App\Models\Cafe::class, 5)->make();

            $cafes->each(function ($cafe) use ($company){
                $company->cafes()->save($cafe);
            });
        });
    }
}
