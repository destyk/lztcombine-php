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

use DestyK\LztPHP\Exception;

/**
 * Базовая модель
 */
class BaseType
{
    /**
     * Сохранение данных в файл
     *
     * @param string $path Путь к файлу
     * @param string $data Данные для сохранения
     * 
     * @return void
     *
     * @throws \Exception Выбрасывается при возникновении ошибки
     */
    protected function writeInFile(string $path, string $data)
    {
        if (false === file_put_contents($path, $data)) {
            throw new Exception(
                'Failed to write in file ' . $path
            );
        }
    }

    /**
     * Получение данных из файла
     *
     * @param string $path Путь к файлу
     * 
     * @return void
     *
     * @throws \Exception Выбрасывается при возникновении ошибки
     */
    protected function getFromFile(string $path)
    {
        /**
         * Файл не существует? Создадим заглушку
         */
        if (false === file_exists($path)) {
            file_put_contents($path, '');
        }

        $result = file_get_contents($path);
        if (false === $result) {
            throw new Exception(
                'Getting error from file ' . $path
            );
        }

        return $result;
    }
}
