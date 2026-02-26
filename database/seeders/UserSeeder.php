<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment() !== 'local') {
            logger()->warning('Cannot run user seeder in production');

            return;
        }

        User::create([
            'name' => 'admin',
            'email' => 'admin@example.org',
            'password' => bcrypt('admin'),
        ]);
    }
}
