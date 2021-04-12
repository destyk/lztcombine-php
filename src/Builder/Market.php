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
 * Класс для взаимодействия с методами МАРКЕТА ВНЕ API
 *
 * @see https://github.com/destyk/lztcombine-php#pushpin-метод-market
 *
 */
class Market extends Init
{
    /**
     * Попытка купить позицию на маркете
     *
     * @param int   $itemId Идентификатор позиции
     * @param float $price  Цена, по которой Вы готовы купить позицию
     *
     * @return array Возврат результата запроса.
     *
     * @throws RequestException Выбрасывается при невалидном ответе.
     */
    public function purchase(int $itemId, float $price)
    {
        return $this->requestBuilder('market/' . $itemId . '/balance/check', parent::GET, [
            'price' => $price
        ]);
    }

    /**
     * Проверка купленной позиции на валидность
     *
     * @param int $itemId Идентификатор позиции
     *
     * @return array Возврат результата запроса.
     *
     * @throws RequestException Выбрасывается при невалидном ответе.
     */
    public function purchaseCheck(int $itemId)
    {
        return $this->requestBuilder('market/' . $itemId . '/check-account', parent::POST, [
            'hide_info' => 1
        ]);
    }

    /**
     * Подтверждение покупки позиции
     *
     * @param int $itemId Идентификатор позиции
     *
     * @return array Возврат результата запроса.
     *
     * @throws RequestException Выбрасывается при невалидном ответе.
     */
    public function purchaseConfirm(int $itemId)
    {
        return $this->requestBuilder('market/' . $itemId . '/confirm-buy', parent::POST, [
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
     *
     * @throws RequestException Выбрасывается при невалидном ответе.
     */
    public function paymentCreate(string $currency, float $amount, string $method)
    {
        return $this->requestBuilder('payment/method', parent::POST, [
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
     *
     * @throws RequestException Выбрасывается при невалидном ответе.
     */
    public function paymentCheck(int $paymentId)
    {
        return $this->requestBuilder('payment/check-payment', parent::POST, [
            'payment_id' => $paymentId
        ]);
    }
}
