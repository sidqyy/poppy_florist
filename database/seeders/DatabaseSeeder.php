<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Admin User', 'username' => 'admin', 'role' => 'admin', 'password' => 'admin123'],
            ['name' => 'Marketing 1', 'username' => 'marketing1', 'role' => 'marketing', 'password' => 'marketing123'],
            ['name' => 'Marketing 2', 'username' => 'marketing2', 'role' => 'marketing', 'password' => 'marketing123'],
            ['name' => 'Asisten Manajer', 'username' => 'asmen', 'role' => 'admin', 'password' => 'asmen123'],
            ['name' => 'IT Support', 'username' => 'itsupport', 'role' => 'admin', 'password' => 'password'],
            ['name' => 'Florist 1', 'username' => 'florist1', 'role' => 'florist', 'password' => 'florist1'],
            ['name' => 'Florist 2', 'username' => 'florist2', 'role' => 'florist', 'password' => 'florist2'],
            ['name' => 'Florist 3', 'username' => 'florist3', 'role' => 'florist', 'password' => 'florist3'],
            ['name' => 'Florist 4', 'username' => 'florist4', 'role' => 'florist', 'password' => 'florist4'],
            ['name' => 'Owner', 'username' => 'owner', 'role' => 'owner', 'password' => 'owner123'],
        ];

        foreach ($users as $user) {
            User::factory()->create([
                'name' => $user['name'],
                'username' => $user['username'],
                'email' => $user['username'] . '@poppyflorist.com',
                'role' => $user['role'],
                'password' => bcrypt($user['password']),
            ]);
        }

        $this->call([
            MasterDataSeeder::class,
        ]);
    }
}
