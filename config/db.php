<?php

$driver = getenv('DB_DRIVER') ?: 'pgsql';
$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '5432';
$db = getenv('DB_NAME') ?: 'storyvalut';

$dsn = sprintf('%s:host=%s;port=%s;dbname=%s', $driver, $host, $port, $db);

$username = getenv('DB_USERNAME') ?: 'postgres';
$password = getenv('DB_PASSWORD') ?: 'postgres';

return [
    'class' => 'yii\db\Connection',
    'dsn' => $dsn,
    'username' => $username,
    'password' => $password,
    'charset' => 'UTF8',

    // Schema cache options (for production)
    'enableSchemaCache' => !YII_DEBUG,
    'schemaCacheDuration' => 3600,
    'schemaCache' => 'cache',
];
