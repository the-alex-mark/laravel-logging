<?php

namespace ProgLib\Logging\Handler;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\RotatingFileHandler;
use ProgLib\Logging\Formatter\JsonFormatter as CustomJsonFormatter;

/**
 * Хранит журналы к файлам, которые вращаются каждый день, и содержатся ограниченное количество файлов.
 * <br>
 * Работает так же как и обработчик <b>RotatingFileHandler</b>.
 *
 * @see RotatingFileHandler
 */
class RotatingJsonHandler extends RotatingFileHandler {

    /**
     * Сохраняет новую запись журнала в кеш и возвращает весь список.
     *
     * @param  array $record Новая запись журнала.
     * @param  int   $ttl    Время хранения записей (в секундах).
     * @return array
     */
    private function cache($record, $ttl = 10) {

        // Получение списка актуальных записей
        if (!Cache::has($this->url) && file_exists($this->url)) {
            $records = file_get_contents($this->url);
            $records = json_decode($records, true) ?? [];

            Cache::put($this->url, $records, $ttl);
        }

        // Добавление новой записи
        $records   = Cache::get($this->url, []);
        $records[] = $record;

        // Кеширование всех записей
        Cache::put($this->url, $records, $ttl);

        return $records;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(array $record): bool {
        if (!$this->isHandling($record))
            return false;

        if ($this->processors)
            $record = $this->processRecord($record);

        // Получение актуального списка записей
        $records = $this->cache($record);

        // Создание директории при её отсутствии
        if (!file_exists(dirname($this->url)))
            @mkdir(dirname($this->url), $filePermission ?? 0755, true);

        // Инициализация потока работы с файлом
        $this->stream = fopen($this->url, 'w');

        // Форматирование и сохранение полного списка записей журнала
        $this->write([
            'formatted' => $this->getFormatter()->formatBatch($records),
            'datetime'  => Carbon::now()
        ]);

        return false === $this->bubble;
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultFormatter(): FormatterInterface {
        return (new CustomJsonFormatter(CustomJsonFormatter::BATCH_MODE_JSON_EACH, false, true))
            ->setJsonPrettyPrint(true)
            ->setDateFormat('Y-m-d H:i:s');
    }
}
