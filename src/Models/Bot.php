<?php

namespace App\Models;

use App\Config\Request;

class Bot
{

    private static string $token = '';
    private static string $website = '';
    private static $result;
    public static $bot;

    public static bool $decode = true;
    public static $content;
    public static $update;

    /**
     * Añadir un token de bot
     *
     * @param string $token Tg bot token
     */
    public function __construct(string $token)
    {
        self::$token = $token;
        self::$website = 'https://api.telegram.org/bot' . self::$token . '/';
    }

    /**
     * Interactuar con la api de telegram
     * @return array|string
     */
    private static function request($method, $datas = [])
    {
        $url = self::$website . $method;

        self::$result = Request::Post($url, null, $datas)['response'];

        return (self::$decode) ? json_decode(self::$result, true) : self::$result;
    }

    public static function __callStatic($name, $arguments)
    {
        return self::request($name, @$arguments[0]);
    }

    /**
     * Obtener el contenido del webhook
     *
     * @return array|string
     */
    public static function GetContent()
    {
        self::$content = file_get_contents('php://input') or die('No se pudo obtener el contenido');

        if (empty(self::$content)) die('No body');

        self::$update = (self::$decode) ? json_decode(self::$content, true) : self::$content;
        return self::$update;
    }

    /**
     * Validar un comando del usuario
     */
    public static function Cmd(string $cmd_name, ?string $txt = null, array $separators = ['!', '¡', '.', ',', '/', '#', '\\', '@']) 
    {
        $texto = $txt ?? self::$update['message']['text'];

        if (in_array($texto[0], $separators)) {

            $text = explode(' ', substr($texto, 1))[0];
            $cmd = explode('|', $cmd_name);

            if (in_array($text, $cmd)) return true;
        }
        return false;
    }

    /**
     * Enviar una action a un chat
     * @link https://core.telegram.org/bots/api#sendchataction
     */
    public static function SendAction(string $chat_id, string $action)
    {
        self::request('sendChatAction', [
            'chat_id' => $chat_id,
            'action' => $action,
        ]);
    }

    /**
     * Enviar mensajes
     *
     * @link https://core.telegram.org/bots/api#sendmessage
     * @return array
     */
    final public static function SendMsg(string $chat, string $content, $reply = null, $button = null, $parse_mode = 'HTML', $web_page_preview = false)
    {
        $payload = [
            'chat_id' => $chat,
            'text' => $content,
            'reply_to_message_id' => $reply,
            'parse_mode' => $parse_mode,
            'reply_markup' => json_encode($button),
            'disable_web_page_preview' => $web_page_preview,
        ];

        if ($reply == null) unset($payload['reply_to_message_id']);
        if ($button == null) unset($payload['reply_markup']);

        self::SendAction($chat, 'typing');
        return self::request('SendMessage', $payload);
    }

    /**
     * Editar un mensaje enviado por el bot
     *
     * @link https://core.telegram.org/bots/api#editmessagetext
     * @return array
     */
    final public static function EditMsgTxt(string $chat, string $msg_id, string $txt, $button = null, $parse_mode = 'HTML') {
        $payload = [
            'chat_id' => $chat,
            'message_id' => $msg_id,
            'parse_mode' => $parse_mode,
            'text' => $txt,
            'reply_markup' => json_encode($button)
        ];
        if (empty($button)) unset($payload['reply_markup']);

        return self::request('editMessageText', $payload);
    }

    /**
     * Responder a callback_querys from inline keyboard
     *
     * @link https://core.telegram.org/bots/api#answercallbackquery
     * @return array
     */
    final public static function AnswerQuery(string $callback_query_id, string $text = '', bool $show_alert = true)
    {
        $payload = [
            'callback_query_id' => $callback_query_id,
            'text' => $text,
            'show_alert' => $show_alert,
        ];

        return self::request('answerCallbackQuery', $payload);
    }

    /**
     * Eliminar un mensaje de un chat
     * @return array
     */
    final public static function DelMsg(string $chat_id, string $msg_id): array
    {
        return self::request('deleteMessage', [
            'chat_id' => $chat_id,
            'message_id' => $msg_id,
        ]);
    }

    /**
     * Enviar un audio
     * 
     * @link https://core.telegram.org/bots/api#sendaudio
     */
    final public static function Audio(string $chat_id, string|\CURLFile $audio, ?string $caption = null, ?string $reply = null, $button = null, string $parse_mode = 'HTML'): array
    {
        if (file_exists($audio)) {
            $audio = new \CURLFile(realpath($audio));
        }

        $payload = [
            'chat_id' => $chat_id,
            'audio' => $audio,
            'caption' => $caption,
            'reply_to_message_id' => $reply,
            'parse_mode' => $parse_mode,
            'allow_sending_without_reply' => true,
            'reply_markup' => $button,
        ];

        if ($reply == null) unset($payload['reply_to_message_id']);
        if ($caption == null) unset($payload['caption']);
        if ($button == null) unset($payload['reply_markup']);
        self::SendAction($chat_id, 'upload_voice');
        return self::request('sendAudio', $payload);
    }

    /**
     * Enviar una nota de voz (archivos .OGG)
     * 
     * @link https://core.telegram.org/bots/api#sendvoice
     * @return array
     */
    final public static function Voice(string $chat_id, string|\CURLFile $voice, ?string $caption = null, ?string $reply = null, $button = null, string $parse_mode = 'HTML') : array
    {
        if (file_exists($voice)) {
            $voice = new \CURLFile(realpath($voice));
        }

        $payload = [
            'chat_id' => $chat_id,
            'voice' => $voice,
            'caption' => $caption,
            'reply_to_message_id' => $reply,
            'parse_mode' => $parse_mode,
            'allow_sending_without_reply' => true,
            'reply_markup' => $button,
        ];

        if ($reply == null) unset($payload['reply_to_message_id']);
        if ($caption == null) unset($payload['caption']);
        if ($button == null) unset($payload['reply_markup']);

        self::SendAction($chat_id, 'upload_voice');
        return self::request('sendVoice', $payload);
    }

    /**
     * Enviar fotos
     *
     * @link https://core.telegram.org/bots/api#sendphoto
     * @return array
     */
    final public static function Photo(string $chat_id, string|\CURLFile $photo, ?string $caption = null, ?string $reply = null, $button = null, string $parse_mode = 'HTML') : array
    {
        if (file_exists($photo)) {
            $photo = new \CURLFile(realpath($photo));
        }

        $payload = [
            'chat_id' => $chat_id,
            'photo' => $photo,
            'caption' => $caption,
            'reply_to_message_id' => $reply,
            'parse_mode' => $parse_mode,
            'allow_sending_without_reply' => true,
            'reply_markup' => $button,
        ];

        if ($reply == null) unset($payload['reply_to_message_id']);
        if ($caption == null) unset($payload['caption']);
        if ($button == null) unset($payload['reply_markup']);

        self::SendAction($chat_id, 'upload_photo');
        return self::request('sendPhoto', $payload);
    }

    /**
     * Enviar una animación (GIF or H.264/MPEG-4 AVC video without sound)
     * 
     * @link https://core.telegram.org/bots/api#sendanimation
     */
    final public static function Gif(string $chat_id, string|\CURLFile $gif, ?string $caption = null, ?string $reply = null, $button = null, string $parse_mode = 'HTML') : array 
    {
        if (file_exists($gif)) {
            $gif = new \CURLFile(realpath($gif));
        }

        $payload = [
            'chat_id' => $chat_id,
            'animation' => $gif,
            'caption' => $caption,
            'reply_to_message_id' => $reply,
            'parse_mode' => $parse_mode,
            'allow_sending_without_reply' => true,
            'reply_markup' => $button,
        ];

        if ($reply == null) unset($payload['reply_to_message_id']);
        if ($caption == null) unset($payload['caption']);
        if ($button == null) unset($payload['reply_markup']);

        self::SendAction($chat_id, 'upload_document');
        return self::request('sendAnimation', $payload);
    }

    /**
     * Send static .WEBP or animated .TGS stickers
     * 
     * @link https://core.telegram.org/bots/api#sendsticker
     * @return array
     */
    final public static function Sticker(string $chat_id, string|\CURLFile $sticker, ?string $reply = null) : array 
    {
        if (file_exists($sticker)) {
            $sticker = new \CURLFile(realpath($sticker));
        }

        $payload = [
            'chat_id' => $chat_id,
            'sticker' => $sticker,
            'disable_notification' => true,
            'reply_to_message_id' => $reply,
            'allow_sending_without_reply' => true,
        ];
        if ($reply == null) unset($payload['reply_to_message_id']);

        self::SendAction($chat_id, 'choose_sticker');
        return self::request('sendSticker', $payload);
    }
}