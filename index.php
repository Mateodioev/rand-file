<?php require './vendor/autoload.php';

use App\Config\Files;
use App\Models\Bot;
use App\Config\StringUtils;

const BOT_TOKEN = 'YOUR BOT TOKEN';
const GITHUB    = 'https://github.com/Mateodioev/';
const REPO      = 'rand-file';

$bot = new Bot(BOT_TOKEN);

$up = $bot::GetContent();

if (isset($up['message'])) {
    $msg = $up['message'];
    $chat_id = $msg['chat']['id'];
    $msg_id = $msg['message_id'];
}

// Messages text
if (isset($msg['text'])) {
    
    $message = $msg['text'];
    $name = StringUtils::QuitMarkdown(@$msg['from']['first_name'] . ' ' . @$msg['from']['last_name']);

    if ($bot::Cmd('start')) {
        $hi = "Hola " . $name . ", enviame un sticker, gif, foto, o un audio y yo te enviare otro archivo del mismo tipo\n\nPd: No me hago responsable de los archivos enviados por el bot, ya que estos proviene de los usuarios";
        $bot::SendMsg($chat_id, $hi, $msg_id, ['inline_keyboard' => [[['text' => 'Gihub', 'url' => GITHUB], ['text' => 'Repository', 'url' => GITHUB.REPO]]]], 'markdown');
    }
    exit;
}

$all = Files::Open('all_ids');

// Stickers
if (isset($msg['sticker'])) {
    $sticker_id = $msg['sticker']['file_id'];
    $unique = $msg['sticker']['file_unique_id'];

    Bot::Sticker($chat_id, SaveAndGet('sticker', $sticker_id, $unique, $all), $msg_id);
    exit;
}

// Animations (Gifs)
if (isset($msg['animation'])) {
    $gif_id = $msg['animation']['file_id'];
    $unique = $msg['animation']['file_unique_id'];

    Bot::Gif($chat_id, SaveAndGet('animation', $gif_id, $unique, $all), null, $msg_id);
    exit;
}

// Photos
if (isset($msg['photo'])) {
    $photo = $msg['photo'][count($msg['photo']) - 1];
    $photo_id = $photo['file_id'];
    $unique = $photo['file_unique_id'];

    Bot::Photo($chat_id, SaveAndGet('photo', $photo_id, $unique, $all), null, $msg_id);
    exit;
}

// Audio
if (isset($msg['audio'])) {
    $audio_id = $msg['audio']['file_id'];
    $unique = $msg['audio']['file_unique_id'];

    Bot::Audio($chat_id, SaveAndGet('audio', $audio_id, $unique, $all), null, $msg_id);
    exit;
}

// Voice
if (isset($msg['voice'])) {
    $voice_id = $msg['voice']['file_id'];
    $unique = $msg['voice']['file_unique_id'];

    Bot::Voice($chat_id, SaveAndGet('voice', $voice_id, $unique, $all), null, $msg_id);
    exit;
}

// Documents
if (isset($msg['document'])) {
    $doc_id = $msg['document']['file_id'];
    $unique = $msg['document']['file_unique_id'];

    Bot::sendDocument(['chat_id' => $chat_id, 'document' => SaveAndGet('document', $doc_id, $unique, $all), 'reply_to_message_id' => $msg_id, 'allow_sending_without_reply' => true]);
    exit;
}



/**
 * Guardar el id según su tipo y obtener otro id random del mismo tipo
 */
function SaveAndGet(string $type, string $id, string $unique, array $all): string
{
    SaveId($type, $id, $unique, $all);
    return Files::OpenUnique($type);
}

/**
 * Guardar un id solo si no es repetido el unique_id
 */
function SaveId(string $type, string $id, string $unique_id, array $all)
{
    if (StringUtils::Compare($all, $unique_id)) return;

    Files::Save($type, $id);
    Files::Save('all_ids', $unique_id);
}