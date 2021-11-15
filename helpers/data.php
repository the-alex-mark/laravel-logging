<?php

if (!function_exists('parse_args')) {

    /**
     * Объединяет два массива, так что параметры первого массива (передаваемые) заменяют при совпадении параметры второго массива (по умолчанию).
     * <br>
     * Параметры можно указать строкой.
     *
     * @param  array|string $args
     * @param  array        $defaults
     * @return array
     */
    function parse_args($args, $defaults = array()) {
        if (is_object($args)) {
            $parsed_args = get_object_vars($args);
        } elseif (is_array($args)) {
            $parsed_args =& $args;
        } else {
            parse_str($args, $parsed_args);
        }

        if (is_array($defaults) && $defaults)
            return array_merge($defaults, $parsed_args);

        return $parsed_args;
    }
}
