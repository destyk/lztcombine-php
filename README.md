<h1 align="center">:trophy: LztCombine PHP v2.0</h1>
<h3 align="center">Библиотека для программного использования ВСЕГО функционала форума <a href="https://lolz.guru" target="_blank">lolz.guru</a></h3>
<h4 align="center">:boom: Наконец, руки дошли обновить библиотеку :) :boom:</h4><br>
<p align="center">
    <img alt="Made with PHP" src="https://img.shields.io/badge/Made%20with-PHP-%23FFD242?logo=php&logoColor=white">
    <img alt="Repo size" src="https://img.shields.io/github/repo-size/destyk/lztcombine-php">
    <img alt="issues" src="https://img.shields.io/github/issues/destyk/lztcombine-php">
    <img alt="Downloads" src="https://img.shields.io/packagist/dt/destyk/lztcombine-php?label=downloads&logo=Packagist&logoColor=white">
    <img alt="Downloads" src="https://img.shields.io/github/downloads/destyk/lztcombine-php/total?color=orange&label=downloads&logo=GitHub">
</p>

## Полезная информация

- [:key: Установка библиотеки](#key-установка-библиотеки)
- [:label: Builder](#label-builder)
  - [:memo: Как использовать?](#memo-использование-buildera)
  - [:open_file_folder: Доступные методы](#open_file_folder-доступные-методы-buildera)
    - [:pushpin: Метод createMethod](#pushpin-метод-createmethod)
    - [:pushpin: Метод login/verify2fa](#pushpin-метод-loginverify2fa)
    - [:pushpin: Метод threads/bump](#pushpin-метод-threadsbump)
    - [:pushpin: :boom: Метод threads/participate](#pushpin-boom-метод-threadsparticipate)
    - [:pushpin: Метод market/purchase](#pushpin-метод-marketpurchase)
    - [:pushpin: Метод market/purchaseCheck](#pushpin-метод-marketpurchasecheck)
    - [:pushpin: Метод market/purchaseConfirm](#pushpin-метод-marketpurchaseconfirm)
    - [:pushpin: Метод market/paymentCreate](#pushpin-метод-marketpaymentcreate)
    - [:pushpin: Метод market/paymentCheck](#pushpin-метод-marketpaymentcheck)
- [:label: Официальное API](#label-официальное-api)
  - [:memo: Как использовать?](#memo-использование-официального-api)
  - [:open_file_folder: Доступные методы](#open_file_folder-доступные-методы-оф-api)
    - [:pushpin: Метод threads/getList](#pushpin-метод-threadsgetlist)
    - [:pushpin: Метод threads/aboutOne](#pushpin-метод-threadsaboutone)
    - [:pushpin: Метод posts/getList](#pushpin-метод-postsgetlist)
    - [:pushpin: Метод posts/create](#pushpin-метод-postscreate)
    - [:pushpin: Метод posts/delete](#pushpin-метод-postsdelete)
    - [:pushpin: Метод posts/like](#pushpin-метод-postslike)
    - [:pushpin: Метод posts/unlike](#pushpin-метод-postsunlike)
    - [:pushpin: Метод users/find](#pushpin-метод-usersfind)
    - [:pushpin: Метод users/getPosts](#pushpin-метод-usersgetposts)
    - [:pushpin: Метод users/subscribe](#pushpin-метод-userssubscribe)
    - [:pushpin: Метод users/unsubscribe](#pushpin-метод-usersunsubscribe)
    - [:pushpin: Метод users/whoIAm](#pushpin-метод-userswhoiam)
    - [:pushpin: Метод pages/getList](#pushpin-метод-pagesgetlist)
    - [:pushpin: Метод pages/aboutOne](#pushpin-метод-pagesaboutone)
    - [:pushpin: Метод notifications/getList](#pushpin-метод-notificationsgetlist)
    - [:pushpin: Метод notifications/aboutOne](#pushpin-метод-notificationsaboutone)
    - [:pushpin: Метод conversations/getList](#pushpin-метод-conversationsgetlist)
    - [:pushpin: Метод conversations/create](#pushpin-метод-conversationscreate)
    - [:pushpin: Метод conversations/delete](#pushpin-метод-conversationsdelete)
    - [:pushpin: Метод conversations/aboutOne](#pushpin-метод-conversationsaboutone)

## :key: Установка библиотеки

Установить данную библиотеку можно с помощью composer:

```sh
composer require destyk/lztcombine-php
```

<h1 align="center">:label: Builder</h1>
<h3 align="center">С помощью builder'a Вы сможете выполнить абсолютно любой запрос к форуму словно так, будто Вы его совершили в своём браузере, минуя официальный API.</h3>

## :memo: Использование builder'a

:warning: Важно! Для корректной работы builder'a, требуется установленное php-расширение `V8Js`.<br>
Инструкция по установке расширения на Ubuntu <a href="https://github.com/destyk/lztcombine-php/tree/main/v8js">находится здесь</a>.<br><br>
Чтобы начать работу, Вам необходим параметр `xf_user`.<br>
Узнать как и где его получить можно <a href="https://disk.yandex.ru/i/RatjHwrb-yN3VA">здесь</a>.

```php
require('vendor/autoload.php');

use DestyK\LztPHP\Builder\Core\Request;
use DestyK\LztPHP\Builder\Init;
use DestyK\LztPHP\Exception;

try {
    $builder = new Init('*xf_user*');

    // Если на Вашем аккаунте включена двуфакторная система авторизации, то изначально стоит вызвать метод верификации
    // Подробнее об этом методе чуть ниже
    $builder->login()->verify2fa('*Одноразовый код*', '*Тип приложения*');

    // Можно создать свой собственный метод. Например, добавить человека в список игнор-листа.
    $builder->createMethod('account/ignore', Request::POST, [
        'users' => 'BotFather,'
    ]);

    // Также есть возможность использовать методы, реализованные из "коробки".
    // Например, участвовать в конкурсе
    $threadId = 4120785; // Id темы с розыгрышем
    $builder->threads()->participate($threadId);
} catch(\Exception $e) {
    echo $e->getMessage();
}
```

## :open_file_folder: Доступные методы builder'a

#### :pushpin: Метод `login/verify2fa`

Позволяет пройти проверку системой 2FA.<br>
:warning: Важно! Этот метод стоит использовать единоразово, если появилось следующее сообщение при включенной системе двуфакторной авторизации: `You need to call the method: $builder->login()->verify2fa(code, provider)`.

```php
...

// Проходим авторизацию с помощью системы 2FA
$code = '2444332'; // Одноразовый код из приложения, Telegram или же с эл. почты
$provider = 'totp'; // Тип приложения. Доступны значения: totp (приложение), telegram (Telegram) и email (эл. почта)
$builder->login()->verify2fa($code, $provider);
```

После успешного прохождения авторизации, вызов этого метода лучше убрать, т.к. будет совершаться дополнительный лишний запрос к форуму.

#### :pushpin: Метод `createMethod`

Позволяет создать абсолютно любой запрос к форуму lolzteam, минуя официальный API.

```php
...

// Для наглядности можем создать вручную метод threads/bump
$threadId = 2444332; // ID Вашей темы, которую нужно поднять
$builder->createMethod('threads/' . $threadId . '/bump', $builder::GET);
```

#### :pushpin: :boom: Метод `threads/participate`

Позволяет участвовать в конкурсе.

```php
...

$threadId = 2444332; // ID темы с конкурсом
$builder->threads()->participate($threadId);
```

#### :pushpin: Метод `threads/bump`

Позволяет поднять указанную тему (если она является Вашей).

```php
...

$threadId = 2444332; // ID Вашей темы, которую нужно поднять
$builder->threads()->bump($threadId);
```

#### :pushpin: Метод `market/purchase`

Попытка купить указанный аккаунт.

```php
...

$itemId = 2444332; // ID аккаунта, который нужно купить
$price = 15; // Стоимость, за которую готовы совершить покупку
$builder->market()->purchase($itemId, $price);
```

#### :pushpin: Метод `market/purchaseCheck`

Проверяем купленный аккаунт на валид/невалид.<br>
:warning: Важно! Этот метод необходимо вызывать после `market/purchase`

```php
...

$itemId = 2444332; // ID аккаунта, который нужно проверить
$builder->market()->purchaseCheck($itemId);
```

#### :pushpin: Метод `market/purchaseConfirm`

Подтверждаем покупку и получаем купленный товар.<br>
:warning: Важно! Этот метод необходимо вызывать после `market/purchaseCheck`

```php
...

$itemId = 2444332; // ID аккаунта, покупку которого нужно подтвердить
$builder->market()->purchaseConfirm($itemId);
```

#### :pushpin: Метод `market/paymentCreate`

Создаёт новую заявку на пополнение счёта

```php
...

$amount = 50; // Сумма для пополнения
$currency = 'rub'; // Валюта пополнения
$method = 'P2PQiwi'; // Метод оплаты
$builder->market()->paymentCreate($currency, $amount, $method);
```

#### :pushpin: Метод `market/paymentCheck`

Проверяет поступление платежа по ранее созданной заявке.

```php
...

$orderId = 125531; // ID ранее созданной Вами заявки
$builder->market()->paymentCheck($orderId);
```

<h1 align="center">:label: Официальное API</h1>
<h3 align="center">В отличие от builder'a, официальное API полностью одобрено администрацией проекта.</h3>

## :memo: Использование официального API

Чтобы начать работу, Вам необходимо получить `access_token`.<br>
Узнать как и где его получить можно <a href="https://lolz.guru/account/api">здесь</a>.

```php
require('vendor/autoload.php');

use DestyK\LztPHP\API\Init;

try {
    $api = new Init('*Ваш token*');
    $result = $api->users()->whoIAm();
} catch(\Exception $e) {
    echo $e->getMessage();
}
```

## :open_file_folder: Доступные методы оф. API

#### :pushpin: Метод `threads/getList`

Парсит темы с форума, исходя из указанных параметров.

```php
...

$threads = $api->threads()->getList([
    'page' => 2,
    'limit' => 5
]);
```

**[Подробнее о входящих/выходящих параметрах метода](https://github.com/xfrocks/bdApi/blob/master/docs/api.markdown#get-threads)**

#### :pushpin: Метод `threads/aboutOne`

Парсит информацию об указанной теме.

```php
...

$threadId = 5000; // ID темы
$thread = $api->threads()->aboutOne($threadId);
```

**[Подробнее о входящих/выходящих параметрах метода](https://github.com/xfrocks/bdApi/blob/master/docs/api.markdown#get-threadsthreadid)**

---

#### :pushpin: Метод `posts/getList`

Парсит посты из определённой темы, исходя из указанных параметров.

```php
...

$threads = $api->posts()->getList([
    'thread_id' => 26412, // ID темы
    'page' => 1,
    'limit' => 10
]);
```

**[Подробнее о входящих/выходящих параметрах метода](https://github.com/xfrocks/bdApi/blob/master/docs/api.markdown#get-posts)**

#### :pushpin: Метод `posts/create`

Создаёт новый пост в указанной теме.

```php
...

$threadId = 5000; // ID темы
$postBody = 'Hello World'; // Содержимое поста
$post = $api->posts()->create($threadId, $postBody, [
    'quote_post_id' => 12050 // Если передается, то threadId не обязателен
]);
```

**[Подробнее о входящих/выходящих параметрах метода](https://github.com/xfrocks/bdApi/blob/master/docs/api.markdown#post-posts)**

#### :pushpin: Метод `posts/delete`

Удаляет созданный пост.

```php
...

$postId = 5000; // ID созданного поста
$api->posts()->delete($postId, [
    'reason' => 'Так звёзды сошлись...' // Причина удаления
]);
```

**[Подробнее о входящих/выходящих параметрах метода](https://github.com/xfrocks/bdApi/blob/master/docs/api.markdown#delete-postspostid)**

#### :pushpin: Метод `posts/like`

Поставить лайк на указанный пост.

```php
...

$postId = 5000; // ID созданного поста
$api->posts()->like($postId);
```

**[Подробнее о входящих/выходящих параметрах метода](https://github.com/xfrocks/bdApi/blob/master/docs/api.markdown#post-postspostidlikes)**

#### :pushpin: Метод `posts/unlike`

Убрать лайк с указанного поста.

```php
...

$postId = 5000; // ID созданного поста
$api->posts()->unlike($postId);
```

**[Подробнее о входящих/выходящих параметрах метода](https://github.com/xfrocks/bdApi/blob/master/docs/api.markdown#delete-postspostidlikes)**

---

#### :pushpin: Метод `users/find`

Парсит пользователей форума, исходя из указанных параметров.

```php
...

$users = $api->users()->getList([
    'username' => 'DestyK', // юзернейм
    'user_email' => 'admin@mail.ru' // почта юзера
]);
```

**[Подробнее о входящих/выходящих параметрах метода](https://github.com/xfrocks/bdApi/blob/master/docs/api.markdown#get-usersfind)**

#### :pushpin: Метод `users/getPosts`

Спрсить список постов пользователя.

```php
...

$userId = 14647; // ID пользователя
$posts = $api->users()->getPosts($userId, [
    'page' => 1,
    'limit' => 5
]);
```

**[Подробнее о входящих/выходящих параметрах метода](https://github.com/xfrocks/bdApi/blob/master/docs/api.markdown#get-usersuseridtimeline)**

#### :pushpin: Метод `users/subscribe`

Оформляет подписку на указанного пользователя.

```php
...

$userId = 14647; // ID пользователя
$api->users()->subscribe($userId);
```

**[Подробнее о входящих/выходящих параметрах метода](https://github.com/xfrocks/bdApi/blob/master/docs/api.markdown#post-usersuseridfollowers)**

#### :pushpin: Метод `users/unsubscribe`

Отменяет подписку на указанного пользователя.

```php
...

$userId = 14647; // ID пользователя
$api->users()->unsubscribe($userId);
```

**[Подробнее о входящих/выходящих параметрах метода](https://github.com/xfrocks/bdApi/blob/master/docs/api.markdown#delete-usersuseridfollowers)**

#### :pushpin: Метод `users/whoIAm`

Получить информацию о текущем токене.

```php
...

$info = $api->users()->whoIAm();
```

**[Подробнее о входящих/выходящих параметрах метода](https://github.com/xfrocks/bdApi/blob/master/docs/api.markdown#get-usersme)**

---

#### :pushpin: Метод `pages/getList`

Парсит разделы с форума, исходя из указанных параметров.

```php
...

$pages = $api->pages()->getList([
    'parent_page_id' => 2, // ID родительского раздела
    'order' => 'natural' // Доступны значения: natural, list
]);
```

**[Подробнее о входящих/выходящих параметрах метода](https://github.com/xfrocks/bdApi/blob/master/docs/api.markdown#get-pages)**

#### :pushpin: Метод `pages/aboutOne`

Парсит информацию об указанном разделе.

```php
...

$pageId = 2000; // ID раздела
$pageInfo = $api->pages()->aboutOne($pageId);
```

**[Подробнее о входящих/выходящих параметрах метода](https://github.com/xfrocks/bdApi/blob/master/docs/api.markdown#get-pagespageid)**

---

#### :pushpin: Метод `notifications/getList`

Парсит оповещения пользователя.

```php
...

$notifications = $api->notifications()->getList();
```

**[Подробнее о входящих/выходящих параметрах метода](https://github.com/xfrocks/bdApi/blob/master/docs/api.markdown#get-notifications)**

#### :pushpin: Метод `notifications/aboutOne`

Получает содержимое оповещения.

```php
...

$notificationId = 2000; // ID оповещения
$notificationInfo = $api->notifications()->aboutOne($notificationId);
```

**[Подробнее о входящих/выходящих параметрах метода](https://github.com/xfrocks/bdApi/blob/master/docs/api.markdown#get-notificationsnotificationidcontent)**

---

#### :pushpin: Метод `conversations/getList`

Парсит личные сообщения, исходя из указанных параметров.

```php
...

$conversations = $api->conversations()->getList([
    'page' => 2,
    'limit' => 5
]);
```

**[Подробнее о входящих/выходящих параметрах метода](https://github.com/xfrocks/bdApi/blob/master/docs/api.markdown#get-conversations)**

#### :pushpin: Метод `conversations/create`

Создаёт новое личное сообщение.

```php
...

$conversationTitle = 'Привет, как дела?'; // Заголовок личного сообщения
$recipients = '1252,3556,4361'; // ID пользователей через запятую
$messageBody = 'Содержимое сообщения';
$conversation = $api->conversations()->create($conversationTitle, $recipients, $messageBody);
```

**[Подробнее о входящих/выходящих параметрах метода](https://github.com/xfrocks/bdApi/blob/master/docs/api.markdown#post-conversations)**

#### :pushpin: Метод `conversations/delete`

Удаляет личное сообщение.

```php
...

$conversationId = 7000; // ID личного сообщения
$api->conversations()->delete($conversationId);
```

**[Подробнее о входящих/выходящих параметрах метода](https://github.com/xfrocks/bdApi/blob/master/docs/api.markdown#delete-conversationsconversationid)**

#### :pushpin: Метод `conversations/aboutOne`

Получить подробное содержимое личного сообщения.

```php
...

$conversationId = 7000; // ID личного сообщения
$conversation = $api->conversations()->aboutOne($conversationId);
```

**[Подробнее о входящих/выходящих параметрах метода](https://github.com/xfrocks/bdApi/blob/master/docs/api.markdown#get-conversationsconversationid)**
