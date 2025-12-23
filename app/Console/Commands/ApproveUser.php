<?php

namespace App\Console\Commands;

use App\TriggerWarningTelegramBot;
use Illuminate\Console\Command;
use Telegram\Bot\Exceptions\TelegramSDKException;

class ApproveUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'approve_user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Approve email';

    /**
     * @return int
     */
    public function handle(): int
    {
        $email = $this->ask('Enter the email to approve:');

        $user = \App\User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }

        $user->approved = true;
        $user->save();

        $this->info("User with email {$email} has been approved.");
        return 0;
    }
}
