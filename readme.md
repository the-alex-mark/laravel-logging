# Laravel Logging

Дополнительные реализации форматов журнала для проектов «**Laravel**».

<br>

## Установка

```bash
composer require the_alex_mark/laravel-logging
```

<br>

## Использование

### Логирование в формате JSON

Класс «**CustomizeJsonLogger**» построен на базе драйвера «**daily**» и поддерживает все его параметры. Дополнительно позволяет указать список процессоров для включения в записи журнала дополнительной информации.

```php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Monolog\Processor\HostnameProcessor;
use Monolog\Processor\WebProcessor;
use ProgLib\Logging\Via\CustomizeJsonLogger;

class AppServiceProvider extends ServiceProvider {

    /**
     * Bootstrap any application services.
     * 
     * @return void
     */
    public function boot() {
    
        $this->app->get('config')->set("logging.channels.custom", [
            'name' => 'custom',
            'driver' => 'custom',
            'via' => CustomizeJsonLogger::class,
            'path' => storage_path("logs/json/laravel.json"),
            'level' => 'debug',
            'permission' => 0755,
            'locking' => true,
            'days' => 30,
            'processors' => [
                HostnameProcessor::class,
                WebProcessor::class
            ]
        ]);
    }
}
```

<br>

### Логирование в формате LINE с форматированным контекстом

```php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use ProgLib\Logging\Taps\CustomizeLineFormatter;

class AppServiceProvider extends ServiceProvider {

    /**
     * Bootstrap any application services.
     * 
     * @return void
     */
    public function boot() {
    
        $this->app->get('config')->set("logging.channels.custom", [
            'name' => 'custom',
            'driver' => 'daily',
            'path' => storage_path("logs/laravel.log"),
            'level' => 'debug',
            'permission' => 0755,
            'locking' => true,
            'days' => 30,
            'tap' => [ CustomizeLineFormatter::class ]
        ]);
    }
}
```
