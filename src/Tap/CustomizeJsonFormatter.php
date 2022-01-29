<?php

namespace ProgLib\Logging\Tap;

use Monolog\Formatter\JsonFormatter as BaseJsonFormatter;
use Monolog\Logger;

class CustomizeJsonFormatter {

    /**
     * Customize the given logger instance.
     *
     * @param  Logger $logger
     * @return void
     */
    public function __invoke($logger) {

        // Инициализация базового экземпляра
        $formatter = (new BaseJsonFormatter(BaseJsonFormatter::BATCH_MODE_JSON, false, true))
            ->setJsonPrettyPrint(true)
            ->setDateFormat('Y-m-d H:i:s');

        // Установка необходимых параметров
        $formatter->addJsonEncodeOption(JSON_UNESCAPED_UNICODE);
        $formatter->addJsonEncodeOption(JSON_UNESCAPED_SLASHES);

        foreach ($logger->getHandlers() as $handler) {
            if (method_exists($handler, 'setFormatter'))
                $handler->setFormatter($formatter);
        }
    }
}
