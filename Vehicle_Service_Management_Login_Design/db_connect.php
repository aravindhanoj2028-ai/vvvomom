
<?php

// Report database errors to the server logs, not to website visitors.
ini_set('display_errors', '0');
ini_set('log_errors', '1');

try {
    // Render PostgreSQL connection URL.
    $databaseUrl = getenv('DATABASE_URL');

    if (!$databaseUrl) {
        throw new RuntimeException(
            'DATABASE_URL environment variable is not configured.'
        );
    }

    $databaseConfig = parse_url($databaseUrl);

    if (
        $databaseConfig === false ||
        empty($databaseConfig['host']) ||
        empty($databaseConfig['user']) ||
        !isset($databaseConfig['pass']) ||
        empty($databaseConfig['path'])
    ) {
        throw new RuntimeException(
            'DATABASE_URL is invalid or incomplete.'
        );
    }

    $host = $databaseConfig['host'];
    $port = $databaseConfig['port'] ?? 5432;
    $database = ltrim($databaseConfig['path'], '/');
    $user = rawurldecode($databaseConfig['user']);
    $password = rawurldecode($databaseConfig['pass']);

    if ($database === '') {
        throw new RuntimeException(
            'Database name is missing from DATABASE_URL.'
        );
    }

    // Use SSL when connecting to Render PostgreSQL.
    $dsn = sprintf(
        'pgsql:host=%s;port=%d;dbname=%s;sslmode=require',
        $host,
        $port,
        $database
    );

    $conn = new PDO(
        $dsn,
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

} catch (Throwable $e) {
    error_log('Database connection failed: ' . $e->getMessage());

    http_response_code(500);
    exit('Database connection is not ready. Please try again later.');
}