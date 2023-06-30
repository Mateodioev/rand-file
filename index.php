<?php

use App\Commands\{Start, Stats};
use App\Events\EvMessage;
use Mateodioev\TgHandler\Bot;
use Mateodioev\TgHandler\Log\{Logger, TerminalStream};

require './vendor/autoload.php';

const BOT_TOKEN = ''; // Pon tu bot token aqui

$bot = new Bot(BOT_TOKEN);

$bot->setLogger(new Logger(new TerminalStream));

$bot->onEvent(Start::get())
    ->onEvent(Stats::get())
    ->onEvent(new EvMessage);

$bot->longPolling(60, true, true);