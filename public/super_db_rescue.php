<?php
// super_db_rescue.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(300); // 5 minutes

echo "<div style='font-family: Arial; padding: 20px;'>";
echo "<h2>Database Encoding Rescue</h2>";

$baseDir = dirname(__DIR__);
$sqlitePath = $baseDir . '/database/database.sqlite';

if (!file_exists($sqlitePath)) {
    die("<b style='color:red'>Error: $sqlitePath not found. Please upload it from your PC to the database folder!</b>");
}

try {
    echo "<b>Step 1: Bootstrapping Laravel...</b><br>";
    require $baseDir.'/vendor/autoload.php';
    $app = require_once $baseDir.'/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    
    echo "<b>Step 2: Dropping all corrupted tables and recreating them as perfectly UTF-8 (utf8mb4)...</b><br>";
    // This wipes the DB and runs all migrations fresh!
    $kernel->call('migrate:fresh', ['--force' => true]);
    echo "<pre>" . \Illuminate\Support\Facades\Artisan::output() . "</pre>";

    echo "<b>Step 3: Transferring perfect Bengali data from SQLite to MySQL...</b><br>";
    
    // Read ENV for MySQL connection
    $envContent = file_get_contents($baseDir . '/.env');
    $lines = explode("\n", $envContent);
    $dbConfig = [];
    foreach ($lines as $line) {
        if (strpos(trim($line), 'DB_') === 0 && strpos($line, '=') !== false) {
            list($key, $val) = explode('=', trim($line), 2);
            $dbConfig[$key] = trim(str_replace('"', '', $val));
        }
    }

    $sqlitePdo = new PDO('sqlite:' . $sqlitePath);
    $sqlitePdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $mysqlPdo = new PDO("mysql:host={$dbConfig['DB_HOST']};port={$dbConfig['DB_PORT']};dbname={$dbConfig['DB_DATABASE']};charset=utf8mb4", $dbConfig['DB_USERNAME'], $dbConfig['DB_PASSWORD']);
    $mysqlPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $mysqlPdo->exec("SET FOREIGN_KEY_CHECKS=0; SET NAMES utf8mb4;");
    
    $tablesQuery = $sqlitePdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
    $tables = $tablesQuery->fetchAll(PDO::FETCH_COLUMN);

    $totalRowsImported = 0;

    foreach ($tables as $table) {
        if ($table === 'migrations') continue;

        // Truncate the freshly migrated table just to be sure it's 100% empty
        $mysqlPdo->exec("TRUNCATE TABLE `$table`");

        $rows = $sqlitePdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);

        if (count($rows) > 0) {
            $chunks = array_chunk($rows, 50);
            foreach ($chunks as $chunk) {
                $keys = array_keys($chunk[0]);
                
                $placeholders = [];
                $values = [];
                foreach ($chunk as $row) {
                    $rowParams = [];
                    foreach ($row as $k => $v) {
                        // Fix AppModels -> App\Models\
                        if (substr($k, -5) === '_type' && strpos((string)$v, 'AppModels') !== false) {
                            $v = str_replace('AppModels', 'App\\Models\\', $v);
                        }
                        $values[] = $v;
                        $rowParams[] = '?';
                    }
                    $placeholders[] = '(' . implode(',', $rowParams) . ')';
                }

                $insertSql = "INSERT INTO `$table` (`" . implode("`, `", $keys) . "`) VALUES " . implode(", ", $placeholders);
                $stmt = $mysqlPdo->prepare($insertSql);
                $stmt->execute($values);
                $totalRowsImported += count($chunk);
            }
        }
    }

    $mysqlPdo->exec("SET FOREIGN_KEY_CHECKS=1;");
    
    echo "<b>Step 4: Clearing Caches...</b><br>";
    $kernel->call('optimize:clear');
    
    echo "<br><b style='color:green; font-size:20px;'>✅ Success! $totalRowsImported rows transferred perfectly with Bengali UTF-8 fonts!</b>";

} catch (Exception $e) {
    echo "<div style='color:red;'><b>Fatal Error:</b> " . $e->getMessage() . "</div>";
}

echo "</div>";
