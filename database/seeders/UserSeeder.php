<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment() !== 'local') {
            throw new RuntimeException('Cannot run user seeder in production');
        }

        $email = 'admin@example.org';

        if (User::where('email', $email)->exists()) {
            $this->command->warn('User already exists: ' . $email);

            return;
        }

        User::Create([
            'name' => 'admin',
            'email' => $email,
            'password' => bcrypt('admin'),
        ]);
    }
}
