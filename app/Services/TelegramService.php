<?php

namespace App\Services;

use GuzzleHttp\Client;

class TelegramService
{
    protected $client;
    protected $token;
    protected $chatId;

    public function __construct()
    {
        $this->client = new Client();
        $this->token = env('TELEGRAM_BOT_TOKEN', '7396351340:AAG4KD0aEGK1MjaeMTUBkI3MiQHL2U0R8nY');
        $this->chatId = env('TELEGRAM_CHAT_ID', '-1002613115297');
    }

    public function sendMessage($message)
    {
        $url = "https://api.telegram.org/bot{$this->token}/sendMessage";

        $this->client->post($url, [
            'form_params' => [
                'chat_id' => $this->chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ],
        ]);
    }
}
