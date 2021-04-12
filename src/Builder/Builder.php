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

use Curl\Curl;
use DestyK\LztPHP\RequestException;

/**
 * Класс для генерации запросов к форуму
 *
 * @see https://github.com/destyk/lztcombine-php#label-builder
 *
 * @property string $_xfToken   Токен для запросов.
 * @property string $_xfSession Идентификатор сессии для запросов
 * @property Curl   $curl       Библиотека для работы с cURL
 */
class Init
{
    /**
     * URL для отправки запросов к форуму
     *
     * @const string
     */
    const FORUM_URI = 'https://lolz.guru/';

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
     * Токен для запросов
     *
     * @var string
     */
    protected $_xfToken;

    /**
     * Идентификатор сессии для запросов
     *
     * @var string
     */
    protected $_xfSession;

    /**
     * Либа для работы с запросами
     *
     * @var Curl
     */
    protected $internalCurl;

    /**
     * Дополнительные заголовки к запросу
     *
     * @var array
     */
    protected $options;

    /**
     * Конструктор для билдера.
     *
     * @param string $_xfToken   Токен для запросов
     * @param string $_xfSession Идентификатор сессии для запросов
     * @param array  $options    Дополнительные заголовки к запросу.
     *
     * @throws \ErrorException Выброс исключения при неожиданной ошибке в Curl запросе.
     */
    public function __construct($_xfToken = '', $_xfSession = '', array $options = [])
    {
        if (!extension_loaded('V8Js')) {
            throw new \ErrorException('The V8Js extensions is not loaded, make sure you have installed the V8Js extension');
        }

        $this->_xfToken     = (string) $_xfToken;
        $this->_xfSession   = (string) $_xfSession;
        $this->options      = $options;
        $this->internalCurl = new Curl();
    }

    /**
     * Предоставляет доступ к методам 'threads'
     *
     * @return \DestyK\LztPHP\Builder\Threads
     *
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    public function threads()
    {
        return new Threads($this->_xfToken, $this->_xfSession, $this->options);
    }

    /**
     * Алиас к protected функции requestBuilder
     *
     * @return bool|array Ответ запроса.
     *
     * @throws \Exception Выбрасывается при невалидном методе запроса.
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    public function createMethod($uri, $method = self::GET, array $body = [], string $userAgent = self::DEFAULT_USERAGENT)
    {
        return $this->requestBuilder($uri, $method, $body, $userAgent);
    }

    /**
     * Формирование запроса к форуму.
     *
     * @param string $uri       URL.
     * @param string $method    Метод.
     * @param array  $body      Параметры запроса.
     * @param string $userAgent Юзер-агент для запросов
     *
     * @return bool|array Ответ запроса.
     *
     * @throws \Exception Выбрасывается при неподдерживаемом $method запроса.
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    protected function requestBuilder($uri, $method = self::GET, array $body = [], string $userAgent = self::DEFAULT_USERAGENT)
    {
        $this->internalCurl->reset();
        foreach ($this->options as $option => $value) {+
            $this->internalCurl->setOpt($option, $value);
        }

        foreach($this->buildCookies() as $cookieKey => $cookieValue) {
            $this->internalCurl->setCookie($cookieKey, $cookieValue);
        }

        $url = self::FORUM_URI . $uri;
        $params = array_merge($body, [
            '_xfNoRedirect' => 1,
            '_xfToken' => $this->_xfToken,
            '_xfResponseType' => 'json'
        ]);

        $this->internalCurl->setUserAgent($userAgent);
        $this->internalCurl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $this->internalCurl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
        switch ($method) {
            case self::GET:
                $this->internalCurl->get($url, $params);
                break;
            case self::POST:
                $this->internalCurl->post($url, $params);
                break;
            default:
                throw new \Exception('Not supported method ' . $method . '.');
        }

        if (true === $this->internalCurl->error) {
            throw new RequestException(
                clone $this->internalCurl,
                $this->internalCurl->error_message . ' | ' . $this->internalCurl->response,
                $this->internalCurl->error_code
            );
        }

        if (false === empty($this->internalCurl->response)) {
            $json = json_decode($this->internalCurl->response, true);
            if (null === $json) {
                throw new RequestException(clone $this->internalCurl, json_last_error_msg(), json_last_error());
            }

            if (true === isset($json['error'])) {
                throw new RequestException(clone $this->internalCurl, $json['error'][0]);
            }

            return $json;
        }

        return true;
    }

    /**
     * Формирование куки для запроса.
     *
     * @return string Куки для установки.
     *
     * @throws Exception Выбрасывается, когда JS-файл не найден || ошибка при компиляции JS-файла.
     */
    protected function buildCookies()
    {
        $pathVerifyJSFile = __dir__ . '/JS/verify.js';
        if (!file_exists($pathVerifyJSFile)) {
            throw new \Exception('The ' . $pathVerifyJSFile . ' file was not found.');
        }

        $verifyJS = file_get_contents($pathVerifyJSFile);
        $v8 = new \V8Js();

        try {
            $compiled = $v8->executeString($verifyJS);
            $signRequest = json_decode($compiled);
        } catch (V8JsException $e) {
            throw new \Exception($e->getMessage());
        }

        return [
            $signRequest->name => $signRequest->value,
            'xf_session' => $this->_xfSession,
            'xf_logged_in' => 1
        ];
    }
}
