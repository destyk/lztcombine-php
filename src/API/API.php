<?php
/**
 * LztCombine - библиотека для программного использования ВСЕГО функционала форума lolzteam
 *
 * @package   destyk/lztcombine-php
 * @author    Nikita <nikita.karpov.1910@mail.ru>
 * @copyright 2021 (c) DestyK
 * @license   MIT https://raw.githubusercontent.com/destyk/lztcombine-php/master/LICENSE
 */

namespace DestyK\LztPHP\API;

use Curl\Curl;
use DestyK\LztPHP\RequestException;

/**
 * Класс для взаимодействия с API
 *
 * @see https://github.com/destyk/lztcombine-php#label-официальное-api
 *
 * @property string $token Токен для работы с API.
 * @property Curl   $curl  Библиотека для работы с cURL
 */
class Init
{
    /**
     * URL для отправки запросов к API
     *
     * @const string
     */
    const API_URI = 'https://lolz.guru/api/index.php?';

    /**
     * GET метод для API
     *
     * @const string
     */
    const GET = 'GET';

    /**
     * POST метод для API
     *
     * @const string
     */
    const POST = 'POST';

    /**
     * PUT метод для API
     *
     * @const string
     */
    const PUT = 'PUT';

    /**
     * DELETE метод для API
     *
     * @const string
     */
    const DELETE = 'DELETE';

    /**
     * Токен для работы с API
     *
     * @var string
     */
    protected $token;

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
     * API конструктор.
     *
     * @param string $token   Токен для работы с API.
     * @param array  $options Дополнительные заголовки к запросу.
     *
     * @throws \ErrorException Выброс исключения при неожиданной ошибке в Curl запросе.
     */
    public function __construct($token = '', array $options = [])
    {
        $this->token        = (string) $token;
        $this->options      = $options;
        $this->internalCurl = new Curl();
    }

    /**
     * Предоставляет доступ к методам 'threads'
     *
     * @return \DestyK\LztPHP\API\Threads
     *
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    public function threads()
    {
        return new Threads($this->token, $this->options);
    }

    /**
     * Предоставляет доступ к методам 'users'
     *
     * @return \DestyK\LztPHP\API\Users
     *
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    public function users()
    {
        return new Users($this->token, $this->options);
    }

    /**
     * Предоставляет доступ к методам 'posts'
     *
     * @return \DestyK\LztPHP\API\Posts
     *
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    public function posts()
    {
        return new Posts($this->token, $this->options);
    }

    /**
     * Предоставляет доступ к методам 'conversations'
     *
     * @return \DestyK\LztPHP\API\Conversations
     *
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    public function conversations()
    {
        return new Conversations($this->token, $this->options);
    }

    /**
     * Предоставляет доступ к методам 'pages'
     *
     * @return \DestyK\LztPHP\API\Pages
     *
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    public function pages()
    {
        return new Pages($this->token, $this->options);
    }

    /**
     * Формирование запроса к API.
     *
     * @param string $uri    URL.
     * @param string $method Метод.
     * @param array  $body   Тело запроса.
     *
     * @return bool|array Ответ запроса.
     *
     * @throws \Exception Выбрасывается при неподдерживаемом $method запроса.
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    protected function requestBuilder($uri, $method = self::GET, array $body = [])
    {
        $this->internalCurl->reset();
        foreach ($this->options as $option => $value) {+
            $this->internalCurl->setOpt($option, $value);
        }

        $url = self::API_URI . $uri;
        $this->internalCurl->setHeader('Accept', 'application/json');
        $this->internalCurl->setHeader('Authorization', 'Bearer '. $this->token);
        $this->internalCurl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $this->internalCurl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
        switch ($method) {
            case self::GET:
                $this->internalCurl->get($url, $body);
                break;
            case self::DELETE:
                $this->internalCurl->delete($url, $body);
                break;
            case self::POST:
                $this->internalCurl->post($url, $body);
                break;
            case self::PUT:
                $this->internalCurl->put($url, $body);
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

            if (true === isset($json['errors'])) {
                throw new RequestException(clone $this->internalCurl, $json['errors'][0]);
            }

            return $json;
        }

        return true;
    }
}
