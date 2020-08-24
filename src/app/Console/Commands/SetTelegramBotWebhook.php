<?php

namespace App\Console\Commands;

use App\TriggerWarningTelegramBot;
use Illuminate\Console\Command;

class SetTelegramBotWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set_telegram_webhook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return int
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle(): int
    {
        $k = new TriggerWarningTelegramBot();
        if ($k->setWebhook()) {
            $this->info("OK");
        } else {
            $this->error("Errore");
        }
        return 0;
    }
}
