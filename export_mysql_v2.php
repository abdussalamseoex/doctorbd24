<?php
$dbPath = __DIR__ . '/database/database.sqlite';
$mysqlFile = __DIR__ . '/mysql_database.sql';

$pdo = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$tablesQuery = $pdo->query("SELECT name, sql FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
$tables = $tablesQuery->fetchAll(PDO::FETCH_ASSOC);

$out = "SET FOREIGN_KEY_CHECKS=0;\nSET NAMES utf8mb4;\nSET CHARACTER SET utf8mb4;\nSET TIME_ZONE='+00:00';\nSET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n\n";

foreach ($tables as $t) {
    if ($t['name'] === 'sqlite_sequence') continue; // Skip sqlite metadata
    
    $table = $t['name'];
    $create = $t['sql'];

    // Clean up SQLite double quotes to MySQL backticks
    $create = preg_replace('/"([^"]+)"/', '`$1`', $create);
    
    // Convert autoincrement primary keys
    $create = preg_replace('/`([a-zA-Z0-9_]+)`\s+integer\s+primary\s+key\s+autoincrement\s+not\s+null/i', '`$1` bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY', $create);
    $create = preg_replace('/`([a-zA-Z0-9_]+)`\s+integer\s+not\s+null\s+primary\s+key\s+autoincrement/i', '`$1` bigint(20) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY', $create);
    
    // Some basic type conversions
    $create = preg_replace('/(?<!`)\bvarchar\b(?!\()/i', 'VARCHAR(191)', $create);
    $create = str_ireplace('VARCHAR(255)', 'VARCHAR(191)', $create);

    // Fix composite index size limits for specific morph/uuid columns
    $create = preg_replace('/`model_type`\s+VARCHAR\(191\)/i', '`model_type` VARCHAR(125)', $create);
    $create = preg_replace('/`tokenable_type`\s+VARCHAR\(191\)/i', '`tokenable_type` VARCHAR(125)', $create);
    $create = preg_replace('/`uuid`\s+VARCHAR\(191\)/i', '`uuid` VARCHAR(125)', $create);
    $create = preg_replace('/(?<!`)\bdatetime\b/i', 'TIMESTAMP NULL DEFAULT NULL', $create);
    $create = preg_replace('/(?<!`)\btext\b/i', 'LONGTEXT', $create);
    
    // Ensure foreign key types (e.g. permission_id) match the bigint(20) PKs
    $create = preg_replace('/`([a-zA-Z0-9_]+_id)`\s+integer/i', '`$1` bigint(20) unsigned', $create);
    
    $create = preg_replace('/(?<!`)\binteger\b/i', 'INT(11)', $create);
    
    $out .= "DROP TABLE IF EXISTS `$table`;\n";
    $out .= "$create ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n";

    $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
    if (count($rows) > 0) {
        // chunk inserts to avoid massive lines
        $chunks = array_chunk($rows, 100);
        foreach ($chunks as $chunk) {
            $keys = array_keys($chunk[0]);
            $out .= "INSERT INTO `$table` (`" . implode("`, `", $keys) . "`) VALUES \n";
            $valuesArr = [];
            foreach ($chunk as $row) {
                array_walk($row, function(&$val, $key) use ($pdo) {
                    if ($val === null) {
                        $val = 'NULL';
                    } else {
                        $val = $pdo->quote($val);
                    }
                });
                $valuesArr[] = "(" . implode(", ", $row) . ")";
            }
            $out .= implode(",\n", $valuesArr) . ";\n";
        }
    }
    $out .= "\n\n";
}

$out .= "SET FOREIGN_KEY_CHECKS=1;\n";

file_put_contents($mysqlFile, $out);
echo "Done! Wrote to $mysqlFile";
