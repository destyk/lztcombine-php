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
 * Класс для взаимодействия с методами 'notifications'
 *
 * @see https://github.com/destyk/lztcombine-php#pushpin-метод-notificationsgetlist
 *
 */
class Notifications extends Init
{
    /**
     * Префикс URL для текущего класса
     *
     * @const string
     */
    const PREFIX_URI = 'notifications/';

    /**
     * Подгружаем список оповещений.
     *
     * @return array Возврат результата запроса.
     *
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    public function getList()
    {
        return $this->requestBuilder(self::PREFIX_URI . '&', parent::GET);
    }

    /**
     * Получает содержимое оповещения.
     *
     * @param number $notificationId Идентификатор оповещения
     *
     * @return array Возврат результата запроса.
     *
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    public function aboutOne(int $notificationId)
    {
        return $this->requestBuilder(self::PREFIX_URI . $notificationId . '/content&', parent::GET);
    }
}
