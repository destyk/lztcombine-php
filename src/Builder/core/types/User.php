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

/**
 * Модуль для хранения данных пользователя
 * 
 * @property int    $id          ID пользователя на форуме
 * @property string $hash        Хэш из токена _xfUser
 * @property string $aesHash     AES хэш
 * @property string $csrf        _xfToken
 * @property string $assets      Общий путь к пользовательским файлам
 * @property string $aesPath     Путь к файлу с AES-хэшем
 * @property string $csrfPath    Путь к файлу с CSRF-токеном
 * @property string $cookiesPath Путь к файлу с cookies
 */
class User extends BaseType
{
    /**
     * ID пользователя на форуме
     * 
     * @var int
     */
    private $id;

    /**
     * Хэш из токена _xfUser
     * 
     * @var string
     */
    private $hash;

    /**
     * AES хэш
     * 
     * @var string
     */
    private $aesHash;

    /**
     * _xfToken
     * 
     * @var string
     */
    private $csrf;

    /**
     * Общий путь к пользовательским файлам
     * 
     * @var string
     */
    private $assets;

    /**
     * Путь к файлу с AES-хэшем
     * 
     * @var string
     */
    private $aesPath;

    /**
     * Путь к файлу с CSRF-токеном
     * 
     * @var string
     */
    private $csrfPath;

    /**
     * Путь к файлу с cookies
     * 
     * @var string
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
     * @param int $id ID пользователя
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
     * @param string $hash Хэш из _xfUser
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
     * @param string $aesHash AES-хэш
     * 
     * @return void
     */
    public function setAesHash(string $aesHash)
    {
        $this->aesHash = $aesHash;
        $this->writeInFile($this->aesPath, $this->aesHash);
    }

    /**
     * Установка CSRF-токена
     * 
     * @param string $csrf CSRF-токен
     * 
     * @return void
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
