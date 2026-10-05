<?php declare(strict_types=1);

require __DIR__ . '/../../init.php';

use App\Library\Database\Database;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;

Database::init();

$migrationDirection = $_SERVER['argv'][1] ?? 'up';
if ($migrationDirection !== 'up' && $migrationDirection !== 'down') {
    die("Invalid migration direction. Allowed: up, down\n");
}

$databaseManager     = Database::getInstance()->getDatabaseManager();
$migrationRepository = new DatabaseMigrationRepository($databaseManager, 'migrations');

// Создаем таблицу migrations, если её ещё нет
if (!$migrationRepository->repositoryExists()) {
    $migrationRepository->createRepository();
}

// Получаем список уже выполненных миграций (метод возвращает массив строк)
$executedMigrations = $migrationRepository->getRan();

$migrationsDir  = __DIR__ . '/../migrations';
$migrationFiles = glob($migrationsDir . '/*.php');

// Получаем номер следующего шага
$nextBatch = $migrationRepository->getNextBatchNumber();
$runCount = 0;

foreach ($migrationFiles as $file) {
    $migrationName = basename($file, '.php');

    if (!in_array($migrationName, $executedMigrations)) {
        echo "Запуск миграции: $migrationName... ";

        $migrationInstance = require $file;
        if ($migrationDirection === 'up') {
            $migrationInstance->up();
        } else {
            $migrationInstance->down();
        }

        // Записываем лог через встроенный метод репозитория
        $migrationRepository->log($migrationName, $nextBatch);

        echo "Успешно!\n";
        $runCount++;
    }
}

if ($runCount === 0) {
    echo "База данных в актуальном состоянии.\n";
}
