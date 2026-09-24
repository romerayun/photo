<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin 
                            {--email= : Admin email address} 
                            {--name= : Admin full name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Safely create or update an administrator account without hardcoding credentials';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('=== Create Administrator Account ===');

        $email = $this->option('email') ?: $this->ask('Enter administrator email');
        $validator = Validator::make(['email' => $email], [
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        if ($validator->fails()) {
            $this->error($validator->errors()->first('email'));
            return Command::FAILURE;
        }

        $name = $this->option('name') ?: $this->ask('Enter administrator name', 'Роман Юн');

        $password = $this->secret('Enter password (min 8 characters)');
        if (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters.');
            return Command::FAILURE;
        }

        $passwordConfirmation = $this->secret('Confirm password');
        if ($password !== $passwordConfirmation) {
            $this->error('Passwords do not match.');
            return Command::FAILURE;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );

        $this->info("Admin user '{$user->name}' ({$user->email}) successfully registered/updated.");
        return Command::SUCCESS;
    }
}
