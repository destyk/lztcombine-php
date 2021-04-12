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
 * Класс для взаимодействия с методами 'users'
 *
 * @see https://github.com/destyk/lztcombine-php#pushpin-метод-usersfind
 *
 */
class Users extends Init
{
    /**
     * Префикс URL для текущего класса
     *
     * @const string
     */
    const PREFIX_URI = 'users/';

    /**
     * Выгружает список пользователей.
     *
     * @param array $params Доступные опциональные параметры:
     *                      + username   {string} Выборка по логину пользователя;
     *                      + user_email {string} Выборка по email-адресу пользователя;
     *
     * @return array Возврат результата запроса.
     *
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    public function find(array $params = [])
    {
        return $this->requestBuilder(self::PREFIX_URI . 'find/', parent::GET, $params);
    }

    /**
     * Выгружает список постов пользователя.
     *
     * @param number $userId Идентификатор пользователя
     * @param array  $params Доступные опциональные параметры:
     *                      + page  {number} Получить указанную страницу с постами;
     *                      + limit {number} Получить указанное количество постов на странице;
     *
     * @return array Возврат результата запроса.
     *
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    public function getPosts(int $userId, array $params = [])
    {
        return $this->requestBuilder(self::PREFIX_URI . $userId . '/timeline/', parent::GET, $params);
    }

    /**
     * Подписываемся на указанного пользователя.
     *
     * @param number $userId Идентификатор пользователя
     *
     * @return array Возврат результата запроса.
     *
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    public function subscribe(int $userId)
    {
        return $this->requestBuilder(self::PREFIX_URI . $userId . '/followers&', parent::POST);
    }

    /**
     * Отписываемся от указанного пользователя.
     *
     * @param number $userId Идентификатор пользователя
     *
     * @return array Возврат результата запроса.
     *
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    public function unsubscribe(int $userId)
    {
        return $this->requestBuilder(self::PREFIX_URI . $userId . '/followers&', parent::DELETE);
    }

    /**
     * Получаем информацию "о себе".
     *
     *
     * @return array Возврат результата запроса.
     *
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    public function whoIAm()
    {
        return $this->requestBuilder(self::PREFIX_URI . '/me&', parent::GET);
    }
}
