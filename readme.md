# The Pastor Bot

Create a file `www/config.php` with something like this in:

```
<?php
  define('CONSUMER_KEY', '');
  define('CONSUMER_SECRET', '');
  define('ACCESS_TOKEN','');
  define('ACCESS_TOKEN_SECRET', '');
?>
```

Make sure that `respond_id.txt` is writable, otherwise the bot will respond to the same tweet over and over and over and over...

Put a cronjob on `bot.php` for every 5 minutes or so
