<?php

namespace ProgLib\Logging\Tap;

use Monolog\Formatter\LineFormatter as BaseLineFormatter;
use Monolog\Logger;

class CustomizeLineFormatter {

    /**
     * Customize the given logger instance.
     *
     * @param  Logger $logger
     * @return void
     */
    public function __invoke($logger) {

        // Инициализация базового экземпляра
        $formatter = (new BaseLineFormatter(BaseLineFormatter::SIMPLE_FORMAT, 'Y-m-d H:i:s', true, true))
            ->setJsonPrettyPrint(true);

        // Установка необходимых параметров
        $formatter->addJsonEncodeOption(JSON_UNESCAPED_UNICODE);
        $formatter->addJsonEncodeOption(JSON_UNESCAPED_SLASHES);

        foreach ($logger->getHandlers() as $handler) {
            if (method_exists($handler, 'setFormatter'))
                $handler->setFormatter($formatter);
        }
    }
}
