<?php

/**
 * LztCombine - библиотека для программного использования ВСЕГО функционала форума lolzteam
 *
 * @package   destyk/lztcombine-php
 * @author    Nikita <nikita.karpov.1910@mail.ru>
 * @copyright 2022 (c) DestyK
 * @license   MIT https://raw.githubusercontent.com/destyk/lztcombine-php/master/LICENSE
 */

namespace DestyK\LztPHP\Builder\Section;

use DestyK\LztPHP\Builder\Core\Request;

/**
 * Класс для взаимодействия с методами 'threads' ВНЕ API
 *
 * @see https://github.com/destyk/lztcombine-php#pushpin-метод-threadsbump
 *
 * @property Request $request Класс для совершения запросов
 */
class Threads
{
    /**
     * Класс для совершения запросов
     *
     * @var Request
     */
    private $request;

    /**
     * Конструктор для раздела Login
     *
     * @param Request $request Объект класса Request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

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
     */
    public function bump(int $threadId)
    {
        return $this->request->requestBuilder(self::PREFIX_URI . $threadId . '/bump', Request::GET);
    }

    /**
     * Позволяет участвовать в конкурсе
     *
     * @param int $threadId Идентификатор темы с конкурсом
     *
     * @return array Возврат результата запроса.
     */
    public function participate(int $threadId)
    {
        return $this->request->requestBuilder(self::PREFIX_URI . $threadId . '/participate', Request::POST);
    }
}
