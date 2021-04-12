<?php
/**
 * LztCombine - библиотека для программного использования ВСЕГО функционала форума lolzteam
 *
 * @package   destyk/lztcombine-php
 * @author    Nikita <nikita.karpov.1910@mail.ru>
 * @copyright 2021 (c) DestyK
 * @license   MIT https://raw.githubusercontent.com/destyk/lztcombine-php/master/LICENSE
 */

namespace DestyK\LztPHP\Builder;

use DestyK\LztPHP\RequestException;

/**
 * Класс для взаимодействия с методами 'threads' ВНЕ API
 *
 * @see https://github.com/destyk/lztcombine-php#pushpin-метод-threadsbump
 *
 */
class Threads extends Init
{
    /**
     * Префикс URL для текущего класса
     *
     * @const string
     */
    const PREFIX_URI = 'threads/';

    /**
     * Поднимает указанную тему
     *
     * @param int $threadId Идентификатор темы
     *
     * @return array Возврат результата запроса.
     *
     * @throws RequestException Выбрасывается при невалидном ответе.
     */
    public function bump(int $threadId)
    {
        return $this->requestBuilder(self::PREFIX_URI . $threadId . '/bump/', parent::GET);
    }
}
