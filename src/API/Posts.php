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

use DestyK\LztPHP\RequestException;

/**
 * Класс для взаимодействия с методами 'posts'
 *
 * @see https://github.com/destyk/lztcombine-php#pushpin-метод-postsgetlist
 *
 */
class Posts extends Init
{
    /**
     * Префикс URL для текущего класса
     *
     * @const string
     */
    const PREFIX_URI = 'posts/';

    /**
     * Подгружаем посты темы.
     *
     * @param array $params Доступные опциональные параметры:
     *                      + thread_id       {number} ID темы (если передан page_of_post_id, то можно пропустить);
     *                      + page_of_post_id {number} Будут возвращены все сообщения, которые находятся на одной странице с указанным;
     *                      + post_ids        {string} ID сообщений, которые нужно вернуть (через запятую);
     *                      + page            {number} Страница сообщений
     *                      + limit           {number} Количество сообщений на одной странице
     *                      + order           {string} Сортировка сообщений
     *                                                      Доступны значения: natural, natural_reverse, post_create_date, post_create_date_reverse
     *
     * @return array Возврат результата запроса.
     *
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    public function getList(array $params = [])
    {
        return $this->requestBuilder(self::PREFIX_URI, parent::GET, $params);
    }

    /**
     * Создаёт новый пост в указанной теме.
     *
     * @param number $threadId Идентификатор темы
     * @param string $postBody Содержание поста
     * @param array $params Доступные опциональные параметры:
     *                      + quote_post_id {number} ID цитируемого сообщения;
     *
     * @return array Возврат результата запроса.
     *
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    public function create(int $threadId, string $postBody, array $params = [])
    {
        $params = array_merge($params, [
            'thread_id' => $threadId,
            'post_body' => $postBody
        ]);
        return $this->requestBuilder(self::PREFIX_URI . '&', parent::POST, $params);
    }

    /**
     * Удаляет указанный пост.
     *
     * @param number $postId Идентификатор поста
     * @param array $params  Доступные опциональные параметры:
     *                       + reason {string} Причина удаления поста;
     *
     * @return array Возврат результата запроса.
     *
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    public function delete(int $postId, array $params = [])
    {
        $query = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        return $this->requestBuilder(self::PREFIX_URI . $postId . '/', parent::DELETE, $params);
    }

    /**
     * Ставит лайк на указанный пост.
     *
     * @param number $postId Идентификатор поста
     *
     * @return array Возврат результата запроса.
     *
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    public function like(int $postId)
    {
        return $this->requestBuilder(self::PREFIX_URI . $postId . '/likes&', parent::POST);
    }

    /**
     * Удаляет лайк с указанного поста.
     *
     * @param number $postId Идентификатор поста
     *
     * @return array Возврат результата запроса.
     *
     * @throws RequestException Выбрасывается при невалидном ответе от API.
     */
    public function unlike(int $postId)
    {
        return $this->requestBuilder(self::PREFIX_URI . $postId . '/likes&', parent::DELETE);
    }
}
