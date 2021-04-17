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
 * @property string $_xfUser    Идентификатор пользователя для запросов
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
     * URI для загрузки идентификационной библиотеки
     *
     * @const string
     */
    const JS_LIB_URI = 'process-qv9ypsgmv9.js';

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
     * Директория для сохранения cookies
     *
     * @const string
     */
    const PATH_TO_COOKIES = __dir__ . '/cookies/';

    /**
     * Директория для сохранения csrf-токенов
     *
     * @const string
     */
    const PATH_TO_CSRF = __dir__ . '/csrf/';

    /**
     * Идентификатор пользователя для запросов
     *
     * @var string
     */
    protected $_xfUser;

    /**
     * Либа для работы с запросами
     *
     * @var Curl
     */
    protected $internalCurl;

    /**
     * Скомипилированная либа JS
     * На основе которой строится параметр df_id
     *
     * @var string
     */
    protected $compiledLib;

    /**
     * Дополнительные заголовки к запросу
     *
     * @var array
     */
    protected $options;

    /**
     * Конструктор для билдера.
     *
     * @param string $_xfUser    Идентификатор пользователя для запросов
     * @param array  $options    Дополнительные заголовки к запросу.
     *
     * @throws \ErrorException Выброс исключения при неожиданной ошибке в Curl запросе.
     */
    public function __construct($_xfUser = '', array $options = [])
    {
        if (!extension_loaded('V8Js')) {
            throw new \ErrorException('The V8Js extensions is not loaded, make sure you have installed the V8Js extension');
        }

        // Разбиваем токен пользователя на части
        $_xfUser = urldecode($_xfUser);
        $exploded_xfUser = explode(',', $_xfUser);
        if (!isset($exploded_xfUser[1])) {
            throw new \ErrorException('Wrong _xfUser passed');
        }

        $this->_xfUser      = (array) [
            'id' => trim($exploded_xfUser[0]),
            'hash' => trim($exploded_xfUser[1]),
            'full' => $_xfUser
        ];
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
        return new Threads($this->_xfUser['full'], $this->options);
    }

    /**
     * Предоставляет доступ к методам маркета
     *
     * @return \DestyK\LztPHP\Builder\Market
     *
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    public function market()
    {
        return new Market($this->_xfUser['full'], $this->options);
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
    protected function requestBuilder($uri, $method = self::GET, array $body = [], string $userAgent = self::DEFAULT_USERAGENT, $isJson = true)
    {
        $this->internalCurl->reset();
        foreach ($this->options as $option => $value) {
            $this->internalCurl->setOpt($option, $value);
        }

        foreach($this->buildCookies() as $cookieKey => $cookieValue) {
            $this->internalCurl->setCookie($cookieKey, $cookieValue);
        }

        $url = self::FORUM_URI . $uri;
        $params = array_merge([
            '_xfNoRedirect' => 1,
            '_xfToken' => $this->getCsrfToken(),
            '_xfResponseType' => 'json'
        ], $body);

        $this->internalCurl->setUserAgent($userAgent);
        $this->internalCurl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $this->internalCurl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
        $this->internalCurl->setOpt(CURLOPT_COOKIEJAR, self::PATH_TO_COOKIES . 'usr_' . $this->_xfUser['id'] . '.txt');
        $this->internalCurl->setOpt(CURLOPT_COOKIEFILE, self::PATH_TO_COOKIES . 'usr_' . $this->_xfUser['id'] . '.txt');
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

        if (false === empty($this->internalCurl->response)) {
            if ($isJson) {
                $json = json_decode($this->internalCurl->response, true);
                if (null === $json) {
                    throw new RequestException(clone $this->internalCurl, json_last_error_msg(), json_last_error());
                }

                if (true === isset($json['error'])) {
                    if (mb_stripos($json['error'][0], 'Обнаружено нарушение безопасности', 0, 'UTF-8') !== false) {
                        $this->loadNewCsrfToken();
                        return $this->requestBuilder($uri, $method, $body, $userAgent);
                    }

                    throw new RequestException(clone $this->internalCurl, $json['error'][0]);
                }

                return $json;
            }

            return $this->internalCurl->response;
        }

        if (true === $this->internalCurl->error) {
            throw new RequestException(
                clone $this->internalCurl,
                $this->internalCurl->error_message,
                $this->internalCurl->error_code
            );
        }

        return true;
    }

    /**
     * Формирование куки для запроса.
     *
     * @return string Куки для установки.
     *
     * @throws Exception Выбрасывается, когда ошибка при компиляции JS-файла.
     */
    protected function buildCookies()
    {
        $v8 = new \V8Js();

        try {
            $compiled = $v8->executeString($this->getCompiledLib());
            $signRequest = json_decode($compiled);
        } catch (V8JsException $e) {
            throw new \Exception($e->getMessage());
        }

        return [
            $signRequest->name => $signRequest->value,
            'xf_user' => $this->_xfUser['full'],
            'xf_logged_in' => 1
        ];
    }

    /**
     * Формирование JS-файла для создания параметра df_id.
     *
     * @return string Код готовой библиотеки.
     *
     * @throws Exception Выбрасывается, когда файл не был загружен.
     */
    protected function getCompiledLib()
    {
        if (empty($this->compiledLib)) {
            try {
                $loadJSFromUrl = $this->loadFileFromUrl(self::FORUM_URI . self::JS_LIB_URI);
            } catch(Exception $e) {
                throw new \Exception('Cannot load process.js from forum');
            }

            $this->compiledLib = str_replace("document[_0x2da6('0x3')]=_0x474ac8+'='+_0x5894d4+_0x2da6('0x4')+_0x52e8c0+_0x2da6('0x5');setTimeout(function(){location[_0x2da6('0x6')]();},0xc8);}", "return JSON.stringify({name:_0x474ac8,value:_0x5894d4});}process();", $loadJSFromUrl);
        }

        return $this->compiledLib;
    }

    /**
     * Загрузка файла с форума.
     *
     * @return string Содержимое загруженного файла.
     *
     * @throws RequestException Выбрасывается, когда файл не был загружен.
     */
    protected function loadFileFromUrl(string $url)
    {
        $this->internalCurl->reset();
        $this->internalCurl->setUserAgent(self::DEFAULT_USERAGENT);
        $this->internalCurl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $this->internalCurl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
        $this->internalCurl->get($url);

        if (true === $this->internalCurl->error) {
            throw new RequestException(
                clone $this->internalCurl,
                $this->internalCurl->error_message . ' | ' . $this->internalCurl->response,
                $this->internalCurl->error_code
            );
        }

        return $this->internalCurl->response;
    }

    /**
     * Загрузка нового CSRF-токена
     *
     * @return string Новый CSRF-токен
     *
     * @throws RequestException Выбрасывается, когда возникла ошибка при обновлении csrf-токена.
     */
    protected function loadNewCsrfToken()
    {
        try {
            $refreshHtml = $this->requestBuilder('login/csrf-token-refresh', self::POST, [
                '_xfResponseType' => 'html'
            ], self::DEFAULT_USERAGENT, false);
        } catch(Exception $e) {
            throw new \Exception('Cannot refresh csrf-token: ' . $e->getMessage());
        }

        preg_match('/_csrfToken: "(.*?)"/', $refreshHtml, $csrfToken);

        if (!isset($csrfToken[1]) || empty($csrfToken[1])) {
            throw new \Exception('Wrong response when refreshing csrf-token');
        }

        $this->putCsrfTokenToFile($csrfToken[1]);
        return $csrfToken[1];
    }

    /**
     * Сохранение CSRF-токена в файл
     *
     * @param string $csrfToken Новый CSRF-токен
     *
     * @return boolean Успешное/неуспешное сохранение CSRF-токена
     *
     * @throws RequestException Выбрасывается при возникновении ошибки записи csrf-токена в файл.
     */
    protected function putCsrfTokenToFile(string $csrfToken)
    {
        $file = self::PATH_TO_CSRF . 'usr_' . $this->_xfUser['id'] . '.txt';
        if (!file_put_contents($file, $csrfToken)) {
            throw new \Exception('Failed to write CSRF-token to file');
        }

        return true;
    }

    /**
     * Получение CSRF-токена из файла
     *
     * @return string Полученный CSRF-токен
     */
    protected function getCsrfToken()
    {
        $file = self::PATH_TO_CSRF . 'usr_' . $this->_xfUser['id'] . '.txt';
        return file_exists($file) ? file_get_contents($file) : '';
    }
}
