<?php

/**
 * LztCombine - библиотека для программного использования ВСЕГО функционала форума lolzteam
 *
 * @package   destyk/lztcombine-php
 * @author    Nikita <nikita.karpov.1910@mail.ru>
 * @copyright 2022 (c) DestyK
 * @license   MIT https://raw.githubusercontent.com/destyk/lztcombine-php/master/LICENSE
 */

namespace DestyK\LztPHP\Builder\Core;

use DestyK\LztPHP\Builder\Core\Request;
use DestyK\LztPHP\RequestException;
use DestyK\LztPHP\Exception;

use V8Js;
use V8JsException;

/**
 * Класс для обхода методов защиты
 */
class Security
{
    /**
     * Путь до библиотеки AES
     *
     * @const string
     */
    const AES_LIBRARY = __dir__ . '/js/aes.js';

    /**
     * Путь до библиотеки с формированием хэша
     *
     * @const string
     */
    const HASH_LIBRARY = __dir__ . '/js/hash.js';

    /**
     * Конструктор для класса обхода защиты
     * 
     * @throws Exception
     */
    public function __construct()
    {
        if (false === extension_loaded('V8Js')) {
            throw new Exception(
                'The V8Js extension is not loaded, make sure you have installed the V8Js extension'
            );
        }
    }

    /**
     * Генерация AES-хэша
     * 
     * @param string $encryUserHash Входящий хэш, на основе которого генерируется AES-хэш
     * 
     * @return string
     * @throws V8JsException
     */
    public function generateHash(string $entryUserHash)
    {
        try {
            /**
             * Получаем все необходимые библиотеки
             */
            $aesLibrary  = file_get_contents(self::AES_LIBRARY);
            $hashLibrary = file_get_contents(self::HASH_LIBRARY);
            $hashLibrary = str_replace('*user_hash*', $entryUserHash, $hashLibrary);

            /**
             * Подключаем библиотеки к интерпретатору
             */
            $snapshot = V8Js::createSnapshot($aesLibrary);
            $v8 = new V8Js('PHP', [], [], true, $snapshot);

            $result = $v8->executeString($hashLibrary);
        } catch (V8JsException $e) {
            throw new Exception(
                'Error generating user aes-hash: ' . $e->getMessage()
            );
        }

        return $result;
    }

    /**
     * Парсинг CSRF-токена со страницы
     * 
     * @param Request $request Функция запроса
     * 
     * @return string
     * @throws Exception
     */
    public function parseCsrfToken(Request $request)
    {
        try {
            $html = $request->requestBuilder('login/csrf-token-refresh', Request::POST, ['_xfResponseType' => 'html'], [], false);
        } catch (Exception | RequestException $e) {
            throw new Exception(
                'Cannot parse csrf-token: ' . $e->getMessage()
            );
        }

        preg_match('/_csrfToken: "(.*?)"/', $html, $csrfToken);

        if (!isset($csrfToken[1]) || empty($csrfToken[1])) {
            throw new Exception(
                'Сsrf token is empty. Most likely, 2FA authentication is required'
            );
        }

        return trim($csrfToken[1]);
    }
}
