<?php
// Diagnostic file to check production setup
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>GlobeBank Production Diagnostics</h1>";

// Check PHP version
echo "<h2>PHP Version</h2>";
echo "PHP Version: " . phpversion() . "<br>";

// Check required extensions
echo "<h2>Required Extensions</h2>";
echo "mysqli: " . (extension_loaded('mysqli') ? 'Installed' : 'NOT INSTALLED') . "<br>";
echo "session: " . (extension_loaded('session') ? 'Installed' : 'NOT INSTALLED') . "<br>";

// Check file paths
echo "<h2>File Paths</h2>";
echo "Current file: " . __FILE__ . "<br>";
echo "Private directory exists: " . (file_exists('../private') ? 'Yes' : 'No') . "<br>";
echo "Initialize.php exists: " . (file_exists('../private/initialize.php') ? 'Yes' : 'No') . "<br>";

// Try to include credentials
echo "<h2>Database Configuration</h2>";
if (file_exists('../private/db_credentials.php')) {
    require_once('../private/db_credentials.php');
    echo "DB_SERVER: " . DB_SERVER . "<br>";
    echo "DB_USER: " . DB_USER . "<br>";
    echo "DB_NAME: " . DB_NAME . "<br>";
    echo "DB_PASS: " . (defined('DB_PASS') ? '[SET]' : '[NOT SET]') . "<br>";

    // Try database connection
    echo "<h2>Database Connection Test</h2>";
    $server = DB_SERVER;
    $port = 3306;

    if (strpos($server, ':') !== false) {
        list($server, $port) = explode(':', DB_SERVER, 2);
        echo "Parsed Server: $server<br>";
        echo "Parsed Port: $port<br>";
    }

    $connection = @mysqli_connect($server, DB_USER, DB_PASS, DB_NAME, $port);

    if ($connection) {
        echo "<strong style='color: green;'>✓ Database connection successful!</strong><br>";

        // Check if tables exist
        $tables_check = mysqli_query($connection, "SHOW TABLES");
        if ($tables_check) {
            echo "<h3>Database Tables:</h3>";
            $table_count = 0;
            while ($row = mysqli_fetch_array($tables_check)) {
                echo "- " . $row[0] . "<br>";
                $table_count++;
            }
            if ($table_count === 0) {
                echo "<strong style='color: orange;'>⚠ No tables found. You need to import setup_database_production.sql</strong><br>";
            }
        }

        mysqli_close($connection);
    } else {
        echo "<strong style='color: red;'>✗ Database connection failed!</strong><br>";
        echo "Error: " . mysqli_connect_error() . "<br>";
        echo "Error number: " . mysqli_connect_errno() . "<br>";
    }
} else {
    echo "<strong style='color: red;'>✗ db_credentials.php not found!</strong><br>";
}

echo "<hr>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>If database connection failed, verify credentials in private/db_credentials.php</li>";
echo "<li>If no tables found, import setup_database_production.sql through phpMyAdmin</li>";
echo "<li>Once everything is working, DELETE this diagnostic.php file for security</li>";
echo "<li>Access the application at: <a href='index.php'>index.php</a></li>";
echo "</ol>";
?>