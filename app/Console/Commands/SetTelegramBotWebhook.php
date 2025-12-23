<?php

namespace App\Console\Commands;

use App\TriggerWarningTelegramBot;
use Illuminate\Console\Command;
use Telegram\Bot\Exceptions\TelegramSDKException;

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
    protected $description = 'Sets telegram webhook';

    /**
     * @return int
     * @throws TelegramSDKException
     */
    public function handle(): int
    {
        $k = new TriggerWarningTelegramBot();
        $this->info("Webhook was " . $k->getWebhook());
        if ($k->setWebhook()) {
            $this->info("OK");
            $this->info("Webhook is now " . $k->getWebhook());
        } else {
            $this->error("Errore");
        }
        return 0;
    }
}
