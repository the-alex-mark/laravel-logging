<?php

namespace ProgLib\Logging;

use Illuminate\Log\Logger;
use Monolog\Formatter\JsonFormatter as BaseJsonFormatter;

class CustomizeJsonFormatter {

    /**
     * Customize the given logger instance.
     *
     * @param  Logger $logger
     * @return void
     */
    public function __invoke($logger) {
        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter(
                (new BaseJsonFormatter(BaseJsonFormatter::BATCH_MODE_JSON, false, true))
                    ->setJsonPrettyPrint(true)
                    ->setDateFormat('Y-m-d H:i:s')
            );
        }
    }
}
