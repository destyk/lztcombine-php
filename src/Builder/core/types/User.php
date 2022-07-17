<?php

/**
 * LztCombine - библиотека для программного использования ВСЕГО функционала форума lolzteam
 *
 * @package   destyk/lztcombine-php
 * @author    Nikita <nikita.karpov.1910@mail.ru>
 * @copyright 2022 (c) DestyK
 * @license   MIT https://raw.githubusercontent.com/destyk/lztcombine-php/master/LICENSE
 */

namespace DestyK\LztPHP\Builder\Core\Types;

use DestyK\LztPHP\Builder\Core\BaseType;
use DestyK\LztPHP\Exception;

/**
 * Модуль для хранения данных пользователя
 */
class User extends BaseType
{
    /**
     * ID пользователя на форуме
     */
    private $id;

    /**
     * Хэш из токена _xfUser
     */
    private $hash;

    /**
     * AES хэш
     */
    private $aesHash;

    /**
     * _xfToken
     */
    private $csrf;

    /**
     * Общий путь к пользовательским файлам
     */
    private $assets;

    /**
     * Путь к файлу с AES-хэшем
     */
    private $aesPath;

    /**
     * Путь к файлу с CSRF-токеном
     */
    private $csrfPath;

    /**
     * Путь к файлу с cookies
     */
    private $cookiesPath;

    /**
     * Конструктор класса
     * 
     * @param int    $id   ID пользователя форума
     * @param string $hash Хэш из параметра _xfUser
     * @param string $path Путь для сохранения пользовательских данных
     */
    public function __construct(int $id, string $hash, string $path)
    {
        $this->setId($id);
        $this->setHash($hash);

        /**
         * Установка путей для пользовательских файлов
         */
        $this->assets      = $path . '/' . $id;
        $this->aesPath     = $this->assets . '/aes.txt';
        $this->csrfPath    = $this->assets . '/csrf.txt';
        $this->cookiesPath = $this->assets . '/cookies.txt';

        /**
         * Создаем пользовательскую директорию
         */
        if (false === is_dir($this->assets)) {
            mkdir($this->assets, 0777, true);
        }
    }

    /**
     * Получение ID пользователя
     * 
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Получение хэша из _xfUser
     * 
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Получение полного хэша (_xfUser)
     * 
     * @return string
     */
    public function getXfUser()
    {
        return $this->id . ',' . $this->hash;
    }

    /**
     * Получение AES-хэша
     * 
     * @return string
     * 
     * @throws Exception
     */
    public function getAesHash()
    {
        if (true === empty($this->aesHash)) {
            $this->aesHash = $this->getFromFile($this->aesPath);
        }

        return $this->aesHash;
    }

    /**
     * Получение CSRF-токена
     * 
     * @return string
     * 
     * @throws Exception
     */
    public function getCsrf()
    {
        if (true === empty($this->csrf)) {
            $this->csrf = $this->getFromFile($this->csrfPath);
        }

        return $this->csrf;
    }

    /**
     * Получение директории хранения cookies
     * 
     * @return string
     */
    public function getCookiesPath()
    {
        return $this->cookiesPath;
    }

    /**
     * Устанавка ID пользователя
     * 
     * @return void
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * Установка хэша из _xfUser
     * 
     * @return void
     */
    public function setHash(string $hash)
    {
        $this->hash = $hash;
    }

    /**
     * Установка AES-хэша
     * 
     * @return void
     * 
     * @throws Exception
     */
    public function setAesHash(string $aesHash)
    {
        $this->aesHash = $aesHash;
        $this->writeInFile($this->aesPath, $this->aesHash);
    }

    /**
     * Установка CSRF-токена
     * 
     * @return void
     * 
     * @throws Exception
     */
    public function setCsrf(string $csrf)
    {
        $this->csrf = $csrf;
        $this->writeInFile($this->csrfPath, $this->csrf);
    }

    /**
     * Очистка директории пользователя
     * 
     * @return void
     */
    public function clearAssets()
    {
        array_map('unlink', array_filter((array) glob($this->assets)));
    }
}
