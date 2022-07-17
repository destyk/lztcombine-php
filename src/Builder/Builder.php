<?php

/**
 * LztCombine - библиотека для программного использования ВСЕГО функционала форума lolzteam
 *
 * @package   destyk/lztcombine-php
 * @author    Nikita <nikita.karpov.1910@mail.ru>
 * @copyright 2022 (c) DestyK
 * @license   MIT https://raw.githubusercontent.com/destyk/lztcombine-php/master/LICENSE
 */

namespace DestyK\LztPHP\Builder;

use DestyK\LztPHP\Builder\Core\Request;
use DestyK\LztPHP\Builder\Core\Security;
use DestyK\LztPHP\Builder\Core\Types\User;

use DestyK\LztPHP\Builder\Section\Login;
use DestyK\LztPHP\Builder\Section\Market;
use DestyK\LztPHP\Builder\Section\Threads;

use DestyK\LztPHP\RequestException;
use DestyK\LztPHP\Exception;

/**
 * Класс для генерации запросов к форуму
 *
 * @see https://github.com/destyk/lztcombine-php#label-builder
 *
 * @property User     $user     Модель пользователя
 * @property Security $security Класс для обхода защиты
 */
class Init
{
    /**
     * Модель пользователя
     *
     * @var User
     */
    private $user;

    /**
     * Класс для обхода методов защиты
     *
     * @var Security
     */
    private $security;

    /**
     * Класс для совершения запросов
     *
     * @var Request
     */
    private $request;

    /**
     * Конструктор для билдера.
     *
     * @param string $_xfUser Идентификатор пользователя для запросов
     * @param string $path    Папка для хранения пользовательских данных
     * 
     * @throws Exception Выброс исключения при неожиданной ошибке в Curl запросе.
     */
    public function __construct(string $_xfUser = '', string $path = null)
    {
        /**
         * Разбиваем токен пользователя на части
         */
        $_xfUser         = urldecode($_xfUser);
        $exploded_xfUser = explode(',', $_xfUser);

        if (false === isset($exploded_xfUser[1])) {
            throw new Exception(
                'Wrong _xfUser passed'
            );
        }

        /**
         * Юзер хочет хранить данные в своем каталоге?...
         */
        if (true === empty($path)) {
            $path = $_SERVER['DOCUMENT_ROOT'] . '/assets';
        }

        $id   = trim($exploded_xfUser[0]);
        $hash = trim($exploded_xfUser[1]);

        $this->user     = new User($id, $hash, $path);
        $this->security = new Security();
        $this->request  = new Request($this->user, $this->security);
    }

    /**
     * Пробрасываем объект класса Login
     */
    public function login()
    {
        return new Login($this->request);
    }

    /**
     * Пробрасываем объект класса Login
     */
    public function market()
    {
        return new Market($this->request);
    }

    /**
     * Пробрасываем объект класса Login
     */
    public function threads()
    {
        return new Threads($this->request);
    }

    /**
     * Пробрасываем функцию requestBuilder.
     * Данная фича позволяет создавать свои собственные запросы,
     * которых нет "из-под коробки"
     *
     * @param string $uri
     * @param string $method
     * @param array  $body
     * @param array  $options
     * @param bool   $isJson
     * 
     * @return bool|array Ответ запроса.
     */
    public function createMethod(string $uri, string $method = Request::GET, array $body = [], array $options = [], bool $isJson = true)
    {
        return $this->request->requestBuilder($uri, $method, $body, $options, $isJson);
    }
}
