<?php
/**
 * LztCombine - библиотека для программного использования ВСЕГО функционала форума lolzteam
 *
 * @package   destyk/lztcombine-php
 * @author    Nikita <nikita.karpov.1910@mail.ru>
 * @copyright 2021 (c) DestyK
 * @license   MIT https://raw.githubusercontent.com/destyk/lztcombine-php/master/LICENSE
 */

namespace DestyK\LztPHP\API;

use DestyK\LztPHP\RequestException;

/**
 * Класс для взаимодействия с методами 'conversations'
 *
 * @see https://github.com/destyk/lztcombine-php#pushpin-метод-conversationsgetlist
 *
 */
class Conversations extends Init
{
    /**
     * Префикс URL для текущего класса
     *
     * @const string
     */
    const PREFIX_URI = 'conversations/';

    /**
     * Подгружаем личные сообщения пользователя.
     *
     * @param array $params Доступные опциональные параметры:
     *                      + page            {number} Страница личных сообщений
     *                      + limit           {number} Количество личных сообщений на одной странице
     *
     * @return array Возврат результата запроса.
     *
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    public function getList(array $params = [])
    {
        return $this->requestBuilder(self::PREFIX_URI, parent::GET, $params);
    }

    /**
     * Получает подробную информацию о личном сообщении.
     *
     * @param number conversationId Идентификатор личного сообщения
     *
     * @return array Возврат результата запроса.
     *
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    public function aboutOne(int $conversationId)
    {
        return $this->requestBuilder(self::PREFIX_URI . $conversationId . '/&', parent::GET);
    }

    /**
     * Создаёт новое личное сообщение.
     *
     * @param string $conversationTitle Заголовок личного сообщения;
     * @param string $recipients        Список username (через запятую);
     * @param string $messageBody       Тело личного сообщения;
     *
     * @return array Возврат результата запроса.
     *
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    public function create(string $conversationTitle, string $recipients, array $messageBody)
    {
        return $this->requestBuilder(self::PREFIX_URI . '&', parent::POST, [
            'conversation_title' => $conversationTitle,
            'recipients' => $recipients,
            'message_body' => $messageBody
        ]);
    }

    /**
     * Удаляет личное сообщение.
     *
     * @param number conversationId Идентификатор личного сообщения
     *
     * @return array Возврат результата запроса.
     *
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    public function delete(int $conversationId)
    {
        return $this->requestBuilder(self::PREFIX_URI . $conversationId . '/&', parent::DELETE);
    }
}
