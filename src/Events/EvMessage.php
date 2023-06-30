<?php

namespace App\Events;

use App\Config\StringUtils;
use Mateodioev\Bots\Telegram\Api;
use Mateodioev\Bots\Telegram\Types\sendInputFile;
use Mateodioev\TgHandler\Context;
use Mateodioev\Bots\Telegram\Types\Message;
use Mateodioev\TgHandler\Events\Types\MessageEvent;
use App\Config\Files;

class EvMessage extends MessageEvent
{
    public function execute(Api $bot, Context $context, array $args = [])
    {
        $msg = $context->message;

        $this->getFile($msg, $bot, $context);
    }

    private function getFile(Message $message, Api $bot, Context $ctx)
    {
        $bot->addOpt([
            'reply_to_message_id' => $ctx->getMessageId(),
            'disable_notification' => true,
            'allow_sending_without_reply' => true,
        ]);

        if ($message->sticker) {
            [$fileId, $uniqueId] = $this->getId('sticker', $message);
            $bot->sendSticker($ctx->getChatId(), $this->saveAndGet('sticker', $fileId, $uniqueId));

        } elseif ($message->animation) {
            [$fileId, $uniqueId] = $this->getId('animation', $message);
            $bot->sendAnimation($ctx->getChatId(), $this->saveAndGet('animation', $fileId, $uniqueId));

        } elseif ($message->photo) {
            [$fileId, $uniqueId] = $this->getId('photo', $message);
            $bot->sendPhoto($ctx->getChatId(), $this->saveAndGet('photo', $fileId, $uniqueId));

        } elseif ($message->audio) {
            [$fileId, $uniqueId] = $this->getId('audio', $message);
            $bot->sendAudio($ctx->getChatId(), $this->saveAndGet('audio', $fileId, $uniqueId));

        } elseif ($message->voice) {
            [$fileId, $uniqueId] = $this->getId('voice', $message);
            $bot->sendVoice($ctx->getChatId(), $this->saveAndGet('voice', $fileId, $uniqueId));

        } elseif ($message->document) {
            [$fileId, $uniqueId] = $this->getId('document', $message);
            $bot->sendDocument($ctx->getChatId(), $this->saveAndGet('document', $fileId, $uniqueId));

        } else {
            return;
        }
    }

    private function getId(string $type, Message $message): array
    {
        $this->logger()->debug('Getting {type}', compact('type'));

        if ($type === 'photo') { // Get the largest file
            $file = $message->photo[count($message->photo) - 1];
            return [$file->file_id, $file->file_unique_id];
        }

        $file = $message->{$type};
        return [$file->file_id, $file->file_unique_id];
    }

    private function saveAndGet(string $type, string $id, string $uniqueId): sendInputFile
    {
        $allIds = Files::Open('all_ids');

        $this->saveId($type, $id, $uniqueId, $allIds); // Guarda el ID si es que aun no existe
        return sendInputFile::fromId(Files::OpenUnique($type)); // Obtiene un ID random de la DB
    }

    private function saveId(string $type, string $id, string $uniqueId, array $allIds)
    {
        // Compara que aun no exista
        if (StringUtils::Compare($allIds, $uniqueId))
            return;

        Files::Save($type, $id); // Guarda el ID del archivo
        Files::Save('all_ids', $uniqueId); // Guarda el ID unico en la DB
    }
}