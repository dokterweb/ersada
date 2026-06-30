<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superadminRole     = Role::create(['name'=> 'superadmin']);

        $komisarisRole      = Role::create(['name'=> 'komisaris']);
        $direkturRole       = Role::create(['name'=> 'direktur']);
        $kacabRole          = Role::create(['name'=> 'kacab']);
        $spvmarketingRole   = Role::create(['name'=> 'spvmarketing']);
        $marketingRole      = Role::create(['name'=> 'marketing']);
        $spvsurveyorRole    = Role::create(['name'=> 'spvsurveyor']);
        $surveyorRole       = Role::create(['name'=> 'surveyor']);

        $userOwner =  User::create([
            'name'      => 'Superadmin',
            'avatar'    => 'images/default-avatar.png',
            'email'     => 'superadmin@email.com',
            'password'  => bcrypt('123123123'),
        ]);

        $userOwner->assignRole($superadminRole);
    }
}
