<?php

namespace App\Commands;

use Mateodioev\Bots\Telegram\Api;
use Mateodioev\TgHandler\Context;

use App\Config\StringUtils;
use Mateodioev\Bots\Telegram\Buttons;
use Mateodioev\TgHandler\Commands\MessageCommand;

class Start extends MessageCommand
{
    private const GITHUB = 'https://github.com/Mateodioev/';
    private const REPO = 'rand-file';

    protected string $name = 'start';
    protected array $prefix = ['/', '!', '.'];

    public function handle(Api $bot, Context $context, array $args = [])
    {
        $name = StringUtils::QuitMarkdown($context->getFullName());
        $message = 'Hola ' . $name . ", enviame un sticker, gif, foto, o un audio y yo te enviare otro archivo del mismo tipo\n\nPd: No me hago responsable de los archivos enviados por el bot, ya que estos proviene de los usuarios";

        $bot->replyTo(
            $context->getChatId(),
            $message,
            $context->getMessageId(),
            params: [
                'inline_keyboard' => (string) Buttons::create()
                    ->addCeil(['text' => 'Gihub', 'url' => self::GITHUB])
                    ->addCeil(['text' => 'Repository', 'url' => self::GITHUB . self::REPO])
            ]
        );
    }
}