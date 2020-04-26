<?php
/**
 * Created by PhpStorm.
 * Author: 查路
 * Date: 2020/4/23
 * Time: 14:54
 */

if (! function_exists('call')) {
    /**
     * Call a callback with the arguments.
     *
     * @param mixed $callback
     * @return null|mixed
     */
    function call($callback, array $args = [])
    {
        $result = null;
        if ($callback instanceof \Closure) {
            $result = $callback(...$args);
        } elseif (is_object($callback) || (is_string($callback) && function_exists($callback))) {
            $result = $callback(...$args);
        } elseif (is_array($callback)) {
            [$object, $method] = $callback;
            $result = is_object($object) ? $object->{$method}(...$args) : $object::$method(...$args);
        } else {
            $result = call_user_func_array($callback, $args);
        }
        return $result;
    }
}
if (!function_exists('Camel2words')) {
    /**
     * 将一个驼峰命名的名字转换成多个单词
     * Converts a CamelCase name into space-separated words.
     * For example, 'PostTag' will be converted to 'Post Tag'.
     * @param string $name the string to be converted
     * @param boolean $ucwords whether to capitalize the first letter in each word              是否单词首字母大写 true大写，false小写
     * @return string the resulting words
     */
    function Camel2words($name, $ucwords = false)
    {
        $label = trim(strtolower(str_replace([
            '-',
            '_',
            '.'
        ], ' ', preg_replace('/(?<![A-Z])[A-Z]/', ' \0', $name))));

        return $ucwords ? ucwords($label) : $label;
    }
}

if (!function_exists('wordsToUnderline')) {
    /**
     * 将一个驼峰命名的名字转换成多个单词
     * Converts a CamelCase name into underline words.
     * For example, 'PostTag' will be converted to 'pos_tag'.
     * @param string $name the string to be converted
     * @return string the resulting words
     */
    function wordsToUnderline($name)
    {
        return trim(strtolower(preg_replace('/(?<![A-Z])[A-Z]/', '_\0', $name)), '_');
    }
}