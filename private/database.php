<?php

require_once('db_credentials.php');

// Connect to database
function db_connect()
{
    // Parse server and port
    $server = DB_SERVER;
    $port = 3306; // Default MySQL port

    if (strpos($server, ':') !== false) {
        list($server, $port) = explode(':', DB_SERVER, 2);
    }

    // Make database connection
    try {
        $connection = mysqli_connect($server, DB_USER, DB_PASS, DB_NAME, $port);
    } catch (mysqli_sql_exception $e) {
        $msg = "Database connection failed.\n";
        $msg .= "Check private/db_credentials.php and ensure the MySQL user exists with the correct password.\n\n";
        $msg .= "Attempted connection:\n";
        $msg .= "  Server: {$server}\n";
        $msg .= "  Port: {$port}\n";
        $msg .= "  User: " . DB_USER . "\n";
        $msg .= "  DB: " . DB_NAME . "\n\n";
        $msg .= "MySQL error: " . $e->getMessage();
        exit($msg);
    }

    // Confirm database connection (for non-exception modes)
    confirm_db_connect();

    // return database connection
    return $connection;
}

// Confirm database connection
function confirm_db_connect()
{
    // Display error message on error
    if (mysqli_connect_errno()) {
        $msg = "Database connection failed: ";
        $msg .= mysqli_connect_error();
        $msg .= " (" . mysqli_connect_errno() . ")";
        exit($msg);
    }
}

// Confirm result set
function confirm_result_set($result_set)
{
    if (!$result_set) {
        exit("Database query failed.");
    }
}

// Disconnect from database
function db_disconnect($connection)
{
    if (isset($connection)) {
        mysqli_close($connection);
    }
}

// Escape String
function db_escape($connection, $string)
{
    return mysqli_real_escape_string($connection, $string);
}

?>