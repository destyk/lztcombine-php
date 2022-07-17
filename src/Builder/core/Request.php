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

use Curl\Curl;
use DestyK\LztPHP\Builder\Core\Security;
use DestyK\LztPHP\Builder\Core\Types\User;
use DestyK\LztPHP\RequestException;
use DestyK\LztPHP\Exception;

/**
 * Класс для генерации запросов к форуму
 *
 * @see https://github.com/destyk/lztcombine-php#label-builder
 *
 * @property User $user Модель пользователя
 * @property Curl $curl Библиотека для работы с cURL
 */
class Request
{
    /**
     * URL для отправки запросов к форуму
     *
     * @const string
     */
    const URL = 'https://lolz.guru/';

    /**
     * GET метод для запросов
     *
     * @const string
     */
    const GET = 'GET';

    /**
     * POST метод для запросов
     *
     * @const string
     */
    const POST = 'POST';

    /**
     * Стандартный UserAgent для запросов
     *
     * @const string
     */
    const DEFAULT_USERAGENT = 'Mozilla/5.0 Chrome Whale Edg';

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
     * Библиотека для работы с cURL
     *
     * @var Curl
     */
    private $request;

    /**
     * Конструктор для билдера.
     *
     * @param User     $user     Объект класса User
     * @param Security $security Объект класса Security
     */
    public function __construct(User $user, Security $security)
    {
        $this->user     = $user;
        $this->security = $security;
        $this->request  = new Curl();
    }

    /**
     * Формирование запроса к форуму.
     *
     * @param string $uri     URL.
     * @param string $method  Метод.
     * @param array  $body    Параметры запроса.
     * @param array  $options Дополнительные заголовки
     * @param bool   $isJson  JSON or not?
     *
     * @return bool|array Ответ запроса.
     *
     * @throws Exception       Выбрасывается при неподдерживаемом $method запроса
     * @throws RequestException Выбрасывается при невалидном ответе от API
     */
    public function requestBuilder(string $uri, string $method, array $body = [], array $options = [], bool $isJson = true)
    {
        $this->request->reset();
        $this->request->setUserAgent(self::DEFAULT_USERAGENT);

        /**
         * Если необходимо, добавляем дополнительные заголовки к запросу
         */
        foreach ($options as $option => $value) {
            $this->request->setHeader($option, $value);
        }

        /**
         * Устанавливаем необходимые cookies для запроса
         */
        $cookies = [
            'sfwefwe'      => $this->user->getAesHash(),
            'xf_user'      => $this->user->getXfUser(),
            'xf_logged_in' => 1
        ];

        foreach ($cookies as $cookieKey => $cookieValue) {
            $this->request->setCookie($cookieKey, $cookieValue);
        }

        /**
         * Устанавливаем необходимые параметры для запроса
         */
        $url = self::URL . $uri;
        $params = array_merge([
            '_xfNoRedirect'   => 1,
            '_xfToken'        => $this->user->getCsrf(),
            '_xfResponseType' => 'json'
        ], $body);

        /**
         * Устанавливаем необходимые headers для запроса
         */
        $this->request->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $this->request->setOpt(CURLOPT_SSL_VERIFYHOST, false);
        $this->request->setOpt(CURLOPT_COOKIEJAR, $this->user->getCookiesPath());
        $this->request->setOpt(CURLOPT_COOKIEFILE, $this->user->getCookiesPath());

        switch ($method) {
            case self::GET:
                $this->request->get($url, $params);
                break;
            case self::POST:
                $this->request->post($url, $params);
                break;
            default:
                throw new Exception(
                    'Not supported method ' . $method
                );
        }

        $this->request->close();

        if (false === empty($this->request->response)) {
            /**
             * Ещё не получали AES-хэш?...
             */
            if (false !== stripos($this->request->response, 'Oops! Please enable JavaScript and Cookies in your browser.')) {
                preg_match('/slowAES\.decrypt\(toNumbers\("(.*)"\)/', $this->request->response, $hash);
                if (false === $hash[1]) {
                    throw new Exception(
                        'Failed to get AES token'
                    );
                }

                /**
                 * Обрабатываем и устанавливаем...
                 */
                $hash    = trim($hash[1]);
                $aesHash = $this->security->generateHash($hash);
                $this->user->setAesHash($aesHash);

                /**
                 * Повторяем запрос
                 */
                return $this->requestBuilder($uri, $method, $body, $options, $isJson);
            }

            /**
             * Ожидаем ответ не в формате JSON?...
             */
            if (false === $isJson) {
                return $this->request->response;
            }

            /**
             * Декодируем тело ответа
             */
            $json = json_decode($this->request->response, true);
            if (null === $json) {
                throw new RequestException(
                    clone $this->request,
                    json_last_error_msg(),
                    json_last_error()
                );
            }

            if (true === isset($json['error'])) {

                /**
                 * Обновляем CSRF-токен...
                 */
                if (false !== mb_stripos($json['error'][0], 'Обнаружено нарушение безопасности', 0, 'UTF-8')) {
                    $token = $this->security->parseCsrfToken(clone $this);
                    $this->user->setCsrf($token);

                    /**
                     * Повторяем запрос
                     */
                    return $this->requestBuilder($uri, $method, $body, $options, $isJson);
                }

                throw new RequestException(
                    clone $this->request,
                    $json['error'][0]
                );
            }

            // Нужно пройти проверку системой 2FA
            if (true === isset($json['_redirectTarget']) && false !== stripos($json['_redirectTarget'], 'login/two-step')) {
                if ($uri == 'login/two-step') {
                    return $this->requestBuilder($uri, $method, $body, $options, $isJson);
                }

                throw new Exception(
                    'You need to call the method: $builder->login()->verify2fa(code, provider). If already called, call again'
                );
            }

            return $json;
        }

        if (true === $this->request->error) {
            $this->user->clearAssets();
            throw new RequestException(
                clone $this->request,
                $this->request->error_message,
                $this->request->error_code
            );
        }

        return true;
    }
}
