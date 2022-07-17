<?php

/**
 * LztCombine - библиотека для программного использования ВСЕГО функционала форума lolzteam
 *
 * @package   destyk/lztcombine-php
 * @author    Nikita <nikita.karpov.1910@mail.ru>
 * @copyright 2022 (c) DestyK
 * @license   MIT https://raw.githubusercontent.com/destyk/lztcombine-php/master/LICENSE
 */

namespace DestyK\LztPHP\Builder\Section;

use DestyK\LztPHP\Builder\Core\Request;

/**
 * Класс для взаимодействия с методами МАРКЕТА ВНЕ API
 *
 * @see https://github.com/destyk/lztcombine-php#pushpin-метод-market
 *
 */
class Market
{
    /**
     * Класс для совершения запросов
     *
     * @var Request
     */
    private $request;

    /**
     * Конструктор для раздела Login
     *
     * @param Request $request Объект класса Request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Попытка купить позицию на маркете
     *
     * @param int   $itemId Идентификатор позиции
     * @param float $price  Цена, по которой Вы готовы купить позицию
     *
     * @return array Возврат результата запроса.
     */
    public function purchase(int $itemId, float $price)
    {
        return $this->request->requestBuilder('market/' . $itemId . '/balance/check', Request::GET, [
            'price' => $price
        ]);
    }

    /**
     * Проверка купленной позиции на валидность
     *
     * @param int $itemId Идентификатор позиции
     *
     * @return array Возврат результата запроса.
     */
    public function purchaseCheck(int $itemId)
    {
        return $this->request->requestBuilder('market/' . $itemId . '/check-account', Request::POST, [
            'hide_info' => 1
        ]);
    }

    /**
     * Подтверждение покупки позиции
     *
     * @param int $itemId Идентификатор позиции
     *
     * @return array Возврат результата запроса.
     */
    public function purchaseConfirm(int $itemId)
    {
        return $this->request->requestBuilder('market/' . $itemId . '/confirm-buy', Request::POST, [
            '_xfConfirm' => 1
        ]);
    }

    /**
     * Создание заявки на пополнение счёта
     *
     * @param string $currency Валюта
     * @param float  $amount   Сумма пополнения
     * @param string $method   Метод оплаты
     *
     * @return array Возврат результата запроса.
     */
    public function paymentCreate(string $currency, float $amount, string $method)
    {
        return $this->request->requestBuilder('payment/method', Request::POST, [
            'currency'     => $currency,
            'amount'       => $amount,
            'method'       => $method,
            'service_type' => 'refill-balance',
            'service_id'   => 577817,
            '_xfConfirm'   => 1
        ]);
    }

    /**
     * Проверка платежа по заявке
     *
     * @param int $paymentId Идентификатор заявки
     *
     * @return array Возврат результата запроса.
     */
    public function paymentCheck(int $paymentId)
    {
        return $this->request->requestBuilder('payment/check-payment', Request::POST, [
            'payment_id' => $paymentId
        ]);
    }
}
