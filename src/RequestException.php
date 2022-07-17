<?php

/**
 * LztCombine - библиотека для программного использования ВСЕГО функционала форума lolzteam
 *
 * @package   destyk/lztcombine-php
 * @author    Nikita <nikita.karpov.1910@mail.ru>
 * @copyright 2022 (c) DestyK
 * @license   MIT https://raw.githubusercontent.com/destyk/lztcombine-php/master/LICENSE
 */

namespace DestyK\LztPHP;

use Curl\Curl;
use Exception;

/**
 * Обработка исключений для запросов
 *
 * @property Curl $request Библиотека для работы с cURL
 */
class RequestException extends Exception
{
    /**
     * Библиотека для работы с cURL
     *
     * @var Curl
     */
    protected $request;

    /**
     * Конструктор для обработчика исключений
     *
     * @param Curl|null      $curl     Библиотека для работы с cURL.
     * @param string         $message  Сообщение об ошибке.
     * @param int            $code     Код ошибки.
     * @param Throwable|null $previous Предыдущая ошибка.
     */
    public function __construct(Curl $curl = null, string $message = "", int $code = 0, \Throwable $previous = null)
    {
        $this->request = $curl;
        if (true === isset($curl)) {
            if (true === empty($message)) {
                $message = $curl->error_message;
            }

            if ($code === 0) {
                $code = $curl->error_code;
            }
        }

        parent::__construct($message, $code, $previous);
    }
}
