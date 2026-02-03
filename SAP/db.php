<?php
/**
 * PHP 5.6 MySQL Connection Wrapper
 * Uses shell script to establish connection
 */

// Execute shell script and capture output
$output = shell_exec('/var/www/html/db_connect.sh 2>&1');

if (strpos($output, 'Shell connection established') !== false) {
    echo "Connection successful via shell script\n";
    
    // Alternative method using direct shell access
    //$query = "SELECT 1 AS test_value";
    $sql = "SELECT * FROM branch_dump ";
    $result = shell_exec("mysql --host=3.7.158.23 --port=3306 --user=sfa --password='Passw0rd123#$' " .
                         "--database=acedns_STAR --default-character-set=binary " .
                         "--execute=\"$sql\" 2>&1");
    
    echo "Query result: " . trim($result) . "\n";
} else {
    echo "Connection failed. Error output:\n";
    echo $output;
}

// More advanced option using temporary socket
/*
$socket_script = <<<'EOT'
#!/bin/bash
MYSQL_SOCKET=$(mktemp -u)
mysql --host=3.7.158.23 --port=3306 --user=sfa --password='Passw0rd123#$' \
      --database=acedns_STAR --socket="$MYSQL_SOCKET" \
      --execute="SET NAMES binary; SELECT CONCAT('unix://', '$MYSQL_SOCKET')" &
sleep 1
echo "$MYSQL_SOCKET"
EOT;

$socket_path = trim(shell_exec($socket_script));
if (file_exists($socket_path)) {
    $link = mysqli_connect('localhost', 'sfa', 'Passw0rd123#$', 'acedns_STAR', null, $socket_path);
    if ($link) {
        echo "Socket connection established\n";
        mysqli_close($link);
    }
    unlink($socket_path); // Clean up
}*/
?>