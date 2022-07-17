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
 * Класс для взаимодействия с методами авторизации ВНЕ API
 *
 * @see https://github.com/destyk/lztcombine-php#pushpin-метод-login
 *
 * @property Request $request Класс для совершения запросов
 */
class Login
{
    /**
     * Префикс URL для текущего класса
     *
     * @const string
     */
    const PREFIX_URI = 'login';

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
     * Добавление IP в доверенные (при включенном 2FA)
     *
     * @param int    $code      Одноразовый код
     * @param string $provider  Тип приложения для авторизации
     *                          Доступны следующие значения:
     *                            totp     - подтверждение через приложение (Например, Google Auth)
     *                            telegram - подтверждение через Telegram
     *                            email    - подтверждение через эл. почту
     *
     * @return array
     */
    public function verify2fa(int $code, string $provider)
    {
        return $this->request->requestBuilder(self::PREFIX_URI . '/two-step', Request::POST, [
            'code'       => $code,
            'provider'   => $provider,
            'trust'      => 1,
            '_xfConfirm' => 1,
            'remember'   => 1
        ]);
    }
}
