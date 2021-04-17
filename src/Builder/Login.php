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
 * Класс для взаимодействия с методами авторизации ВНЕ API
 *
 * @see https://github.com/destyk/lztcombine-php#pushpin-метод-login
 *
 */
class Login extends Init
{
    /**
     * Префикс URL для текущего класса
     *
     * @const string
     */
    const PREFIX_URI = 'login/';

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
     * @return array Возврат результата запроса.
     *
     * @throws RequestException Выбрасывается при невалидном ответе.
     */
    public function verify2fa(int $code, string $provider)
    {
        return $this->requestBuilder(self::PREFIX_URI . 'two-step', parent::POST, [
            'code' => $code,
            'provider' => $provider,
            'trust' => 1,
            '_xfConfirm' => 1,
            'remember' => 1
        ]);
    }
}
