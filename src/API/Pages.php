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
 * Класс для взаимодействия с методами 'pages'
 *
 * @see https://github.com/destyk/lztcombine-php#pushpin-метод-pagesgetlist
 *
 */
class Pages extends Init
{
    /**
     * Префикс URL для текущего класса
     *
     * @const string
     */
    const PREFIX_URI = 'pages/';

    /**
     * Подгружаем список разделов системы.
     *
     * @param array $params Доступные опциональные параметры:
     *                      + parent_page_id {number} ID родительского раздела;
     *                      + order          {string} Сортировка разделов
     *                                                Доступны значения: natural, list
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
     * Получает информацию о разделе.
     *
     * @param number $pageId Идентификатор раздела
     *
     * @return array Возврат результата запроса.
     *
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    public function aboutOne(int $pageId)
    {
        return $this->requestBuilder(self::PREFIX_URI . $pageId . '/&', parent::GET);
    }
}
