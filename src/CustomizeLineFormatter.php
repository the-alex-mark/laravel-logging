<?php

namespace ProgLib\Logging;

use Illuminate\Log\Logger;
use Monolog\Formatter\LineFormatter as BaseLineFormatter;

class CustomizeLineFormatter {

    /**
     * Customize the given logger instance.
     *
     * @param  Logger $logger
     * @return void
     */
    public function __invoke($logger) {
        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter(
                (new BaseLineFormatter(BaseLineFormatter::SIMPLE_FORMAT, 'Y-m-d H:i:s', true, true))
                    ->setJsonPrettyPrint(true)
            );
        }
    }
}
