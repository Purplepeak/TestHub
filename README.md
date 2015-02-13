Testhub
=======

Для полноценной работы сервиса, помимо скачанного кода, пользователю необходимо:

1) Используя Composer скачать или обновить зависимости.

2) Организовать MySQL базу данных используя файл test_me.sql. В репозитории последняя версия базы. Примененить миграции, если у    вас стоит предыдущая версия.

3) Внести изменения для доступа к вашей базе данных в конфиги
    https://github.com/Purplepeak/Testhub/blob/master/protected/config/main.php
    https://github.com/Purplepeak/TestHub/blob/master/protected/config/console.php

   Так же в основном конфиге измените параметр siteEmail.

4) Для работы Oauth авторизации в приватном конфиге      https://github.com/Purplepeak/TestHub/blob/master/protected/config/private.php необходимо прописать clientId и clientSecret для всех предложенных ресурсов.
Для подключения рекапчи указать privateKey и publicKey.

5) По умолчанию включен режим отладки, для его отключения необходимо присвоить false константам: 
       YII_DEBUG
       YII_DEBUG_SHOW_PROFILER
       YII_DEBUG_PROFILING 
   в https://github.com/Purplepeak/TestHub/blob/master/index.php.
