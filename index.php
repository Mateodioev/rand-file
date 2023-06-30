<?php

use App\Events\EvMessage;
use Mateodioev\TgHandler\Bot;

require './vendor/autoload.php';

const BOT_TOKEN = '';

$bot = new Bot('');

$bot->onEvent(Start::get())
    ->onEvent(new EvMessage);

$bot->longPolling(60, true, true);