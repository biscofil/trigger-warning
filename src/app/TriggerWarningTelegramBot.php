<?php


namespace App;

use App\Http\Requests\NewCardRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Telegram\Bot\Api;

class TriggerWarningTelegramBot
{

    private $telegram = null;

    public function __construct()
    {

        $token = config('telegram.bot_token');

        if (is_null($token)) {
            throw new \Exception("Telegram bot token not set");
        }

        $this->telegram = new Api($token);

    }

    /**
     * @return bool
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function setWebhook(): bool
    {

        $url = route('telegram_webhook');

        Log::debug("Setting telegram webhook to $url");

        $response = $this->telegram->setWebhook(['url' => $url]);

        return $response->getRawResponse()[0];

    }

    private static function get_code_hash($code)
    {
        return md5($code);
    }

    /**
     * @param User $user
     * @return string
     */
    public static function get_login_url(User $user): string
    {

        $original = Str::random(45);

        $hash = self::get_code_hash($original);

        $user->update([
            'telegram_auth_code' => $hash,
            'telegram_id' => null // TODO check, reset
        ]);

        return 'https://telegram.me/' . config('telegram.bot_name') . '?start=' . $original;
    }

    /**
     * @param $code
     * @return User|null
     */
    private static function findUserFromAuthCode($code): ?User
    {
        $u = User::query()->where('telegram_auth_code', '=', self::get_code_hash($code))->get();
        if ($u->count()) {
            return $u->first();
        }
        return null;
    }

    /**
     * @param $chatID
     * @param $messageID
     * @param $code
     * @param $userID
     */
    public function do_login($chatID, $messageID, $code, $userID): bool
    {

        if ($user = self::findUserFromAuthCode($code)) {

            /** @var User $user */
            Log::debug("user found :  " . $user->name);

            $user->update([
                'telegram_auth_code' => null,
                'telegram_id' => $userID
            ]);

            $this->telegram->sendMessage([
                'chat_id' => $chatID,
                'text' => 'Bea toso! Fatto.',
                'reply_to_message_id' => $messageID
            ]);

            return true;

        }

        return false;
    }

    /**
     * @param $chatID
     * @param $messageID
     * @param $msg
     */
    public function sendMessage($chatID, $messageID, $msg)
    {
        $this->telegram->sendMessage([
            'chat_id' => $chatID,
            'text' => $msg,
            'reply_to_message_id' => $messageID,
            'parse_mode' => 'markdown'
        ]);
    }

    /**
     *
     */
    public function getMe()
    {

        $response = $this->telegram->getMe();

        $botId = $response->getId();
        $firstName = $response->getFirstName();
        $username = $response->getUsername();
        dd([
            'id' => $botId,
            'fn' => $firstName,
            '$username' => $username
        ]);
    }

    /**
     * @param array $request
     */
    public function parse_webhook(array $request): void
    {

        // array (
        //  'update_id' => 543070879,
        //  'message' =>
        //  array (
        //    'message_id' => 17,
        //    'from' =>
        //    array (
        //      'id' => 22488611,
        //      'is_bot' => false,
        //      'first_name' => 'Bisco',
        //      'language_code' => 'en',
        //    ),
        //    'chat' =>
        //    array (
        //      'id' => 22488611,
        //      'first_name' => 'Bisco',
        //      'type' => 'private',
        //    ),
        //    'date' => 1598283814,
        //    'text' => '/start test',
        //    'entities' =>
        //    array (
        //      0 =>
        //      array (
        //        'offset' => 0,
        //        'length' => 6,
        //        'type' => 'bot_command',
        //      ),
        //    ),
        //  ),
        //)

        $messageID = null;
        $userID = null;
        $chatID = null;
        $messageText = null;

        if (!array_key_exists('message', $request)) {
            return;
        }

        //Log::debug($request);

        try {
            $messageID = $request['message']['message_id'];
            $userID = $request['message']['from']['id'];
            $chatID = $request['message']['chat']['id'];
            $messageText = $request['message']['text'];
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        /*Log::debug("telegram_webhook from " . $userID);
        Log::debug($messageText);*/

        if (str_starts_with($messageText, '/')) {

            $parts = explode(' ', $messageText, 2); // max 2

            switch ($parts[0]) {

                case '/start':

                    if (count($parts) == 2) {

                        $code = $parts[1];

                        Log::debug("login with code " . $code);

                        if (self::do_login($chatID, $messageID, $code, $userID)) {
                            break;
                        }

                    }
                    break;

                case '/riempitiva':

                    if (count($parts) == 2) {

                        $user = $this->getServerUser($userID);

                        if (is_null($user)) {

                            Log::debug("no user with id " . $userID);
                            $this->send_auth_message($chatID, $messageID);
                            break;
                        }

                        $cardContent = $parts[1];

                        $requestData = [
                            'content' => $cardContent,
                            'type' => Card::$TypeFillingCart
                        ];

                        $newCardRequest = new NewCardRequest();

                        $validator = Validator::make($requestData, $newCardRequest->rules());

                        $newCardRequest->setValidator($validator);
                        $newCardRequest->request->replace($requestData);
                        try {
                            $newCardRequest->store($user);
                        } catch (\Exception $e) {
                            $this->sendMessage($chatID, $messageID, "Errore nella creazione della carta: " . $e->getMessage());
                            break;
                        }

                        $this->sendMessage($chatID, $messageID, "Nuova carta riempitiva: " . $cardContent);
                        break;

                    }


                case '/riempibile':

                    if (count($parts) == 2) {

                        $user = $this->getServerUser($userID);

                        if (is_null($user)) {

                            Log::debug("no user with id " . $userID);
                            $this->send_auth_message($chatID, $messageID);
                            break;
                        }

                        $cardContent = $parts[1];

                        $requestData = [
                            'content' => $cardContent,
                            'type' => Card::$TypeCartToFill
                        ];

                        $newCardRequest = new NewCardRequest();

                        $validator = Validator::make($requestData, $newCardRequest->rules());

                        $newCardRequest->setValidator($validator);
                        $newCardRequest->request->replace($requestData);
                        try {
                            $newCardRequest->store($user);
                        } catch (\Exception $e) {
                            $this->sendMessage($chatID, $messageID, "Errore nella creazione della carta: " . $e->getMessage());
                            break;
                        }

                        $this->sendMessage($chatID, $messageID, "Nuova carta da riempire: " . $cardContent);
                        break;

                    }

                case '/help':
                    $this->sendMessage($chatID, $messageID, "Usa " . PHP_EOL .
                        "`/riempitiva TESTO DELLA CARTA` o " . PHP_EOL .
                        "`/riempibile TESTO DELLA @`" . PHP_EOL .
                        " per aggiungere carte");
                    break;


                default:

                    $msgs = [
                        'Ferma a sagra, son ndà in merda co tutto',
                        "Che casso situ drio far?!?!",
                        'No so drio capir na tega.... CIANOOO, VIEN QUA SPIEGARME COME FUNSIONA STA MERDA'
                    ];

                    $this->sendMessage($chatID, $messageID, $msgs[array_rand($msgs)]);

            }

        } else {

            $this->sendMessage($chatID, $messageID, "Cossa xe sta roba?");

        }

    }

    /**
     * @param int $userID
     * @return User|null
     */
    private function getServerUser(int $userID): ?User
    {
        return User::query()->where('telegram_id', '=', $userID)->get()->first();
    }

    /**
     * @param $chatID
     * @param $messageID
     */
    private function send_auth_message($chatID, $messageID)
    {
        $this->sendMessage($chatID, $messageID, "Chi cazzo sei??? Prova a ricollegare Telegram: \n" .
            "1) vai su ```" . route('play.trigger_warning') ."```\n" .
            "2) premi il pulsante *Accedi con Telegram* in basso\n" .
            "3) nella chat Telegram che si apre premi 'start' in basso!");
    }

}
