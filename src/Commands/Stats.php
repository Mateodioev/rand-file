<?php

namespace App\Commands;

use App\Config\Files;
use Mateodioev\Bots\Telegram\Api;
use Mateodioev\TgHandler\Context;
use Mateodioev\TgHandler\Commands\MessageCommand;

class Stats extends MessageCommand
{
    protected string $name = 'stats';
    protected array $prefix = ['/', '!', '.'];

    public function handle(Api $bot, Context $context, array $args = [])
    {
        $message = "Estos son mis archivos:\n\n";

        foreach ($this->countFiles() as $name => $total) {
            $message .= '<b>' . $name . '</b> ~ <i>' . $total . "</i>\n";
        }

        $bot->replyTo(
            $context->getChatId(),
            $message,
            $context->getMessageId(),
        );
    }

    private function countFiles()
    {
        $stats = [];

        $files = \glob(Files::$dir . '*.txt');

        foreach ($files as $file) {
            $name = \basename($file, '.txt');

            if ($name === 'all_ids')
                continue;

            $stats[$name] = Files::Count($name);
        }

        return $stats;
    }
}