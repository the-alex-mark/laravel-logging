<?php

namespace ProgLib\Logging\Formatter;

use Monolog\Formatter\JsonFormatter as BaseJsonFormatter;

class JsonFormatter extends BaseJsonFormatter {

    #region Constants

    public const BATCH_MODE_JSON_EACH = 3;

    #endregion

    /**
     * {@inheritDoc}
     */
    public function formatBatch(array $records): string {
        switch ($this->batchMode) {

            case static::BATCH_MODE_JSON_EACH:
                return $this->formatBatchJsonEach($records);

            default:
                return parent::formatBatch($records);
        }
    }

    /**
     * Форматирование списка аналогичное методу "formatBatchJson", но для каждой записи.
     *
     * @phpstan-param Record[] $records
     */
    protected function formatBatchJsonEach(array $records): string {

        // Обработка списка записей
        array_walk($records, function (&$record) use ($records) {

            // Проверка пустых элементов
            array_walk($record, function (&$item, $key) use (&$record) {
                if (empty($item) && $this->ignoreEmptyContextAndExtra)
                    unset($record[$key]);

                elseif ($item === [])
                    $item = new \stdClass();
            });
        });

        return $this->toJson($this->normalize($records), true);
    }
}
