<?php

namespace ProgLib\Logging\Via;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Log\ParsesLogConfiguration;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger as Monolog;

class CustomizeLineLogger {

    use ParsesLogConfiguration;

    #region Methods

    /**
     * {@inheritDoc}
     */
    protected function getFallbackChannelName() {
        return app()->bound('env') ? app()->environment() : 'production';
    }

    /**
     * Объединяет два массива, так что параметры первого массива (передаваемые) заменяют при совпадении параметры второго массива (по умолчанию).
     * <br>
     * Параметры можно указать строкой.
     *
     * @param  array|string $args
     * @param  array        $defaults
     * @return array
     */
    protected function parseConfig($args, $defaults = array()) {
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

    #endregion

    /**
     * Customize the given logger instance.
     *
     * @param  array $config
     * @return Monolog
     * @throws BindingResolutionException
     */
    public function __invoke($config) {

        // Установка параметров по умолчанию
        $args = $this->parseConfig($config, [
            'path'       => storage_path('logs/json/laravel.json'),
            'days'       => 21,
            'bubble'     => true,
            'permission' => null,
            'locking'    => true,
            'level'      => 'debug',
            'processors' => []
        ]);

        $channel = $this->parseChannel($config);
        $handler = new RotatingFileHandler($args['path'], $args['days'], $this->level($config), $args['bubble'], $args['permission'], $args['locking']);

        // Инициализация процессора
        if (!empty($args['processors'])) {
            array_walk($args['processors'], function ($item) use ($handler) {
                if (!empty($item))
                    $handler->pushProcessor(is_callable($processor = $item) ? $processor : app()->make($processor));
            });
        }

        // Инициализация обработчика
        return new Monolog($channel, [ $handler ]);
    }
}
