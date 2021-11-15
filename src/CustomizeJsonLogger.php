<?php

namespace ProgLib\Logging;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Log\ParsesLogConfiguration;
use Monolog\Logger as Monolog;
use ProgLib\Logging\Handler\RotatingJsonHandler;

class CustomizeJsonLogger {

    use ParsesLogConfiguration;

    /**
     *
     *
     * @param  array $config
     * @return Monolog
     * @throws BindingResolutionException
     */
    public function __invoke(array $config) {

        // Установка параметров по умолчанию
        $args = parse_args($config, [
            'path'       => storage_path('logs/json/laravel.json'),
            'days'       => 7,
            'bubble'     => true,
            'permission' => null,
            'locking'    => false,
            'processors' => []
        ]);

        $channel = $this->parseChannel($config);
        $handler = new RotatingJsonHandler($args['path'], $args['days'], $this->level($config), $args['bubble'], $args['permission'], $args['locking']);

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

    #region Methods

    /**
     * {@inheritDoc}
     */
    protected function getFallbackChannelName() {
        return app()->bound('env') ? app()->environment() : 'production';
    }

    #endregion
}
