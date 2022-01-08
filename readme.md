Random file telegram bot 🤖
=======

Enviale un archivo y este te devolverá otro del mismo tipo

- [Archivos permitidos](#random-file-telegram-bot-support)
  -  Photos
  -  Stickers
  -  Audios
  -  Notas de voz
  -  Documentos
  -  Gifs


Obtener un token para el bot
--------
Valla a [@BotFather](https://t.me/BotFather) y escriba los siguientes comandos:
```
1. /newbot
2. Responda con un nombre para su bot
3. Escriba un username (@) para su bot, este debe terminar si o si en 'bot'
```
Telegram al final le devolver un token

Setwebhook
--------

```
    $endpoint = 'https://api.telegram.org/bot`BOT_TOKEN`/'
    $method = 'setwebhook'
    $params = url=dominio.donde.esta.hosteado.su.bot.com

    Con cURL
    $ curl 'https://api.telegram.org/botBOT_TOKEN/setwebhook?url=dominio.com/path_a_su_archivo'
```