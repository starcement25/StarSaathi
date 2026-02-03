<?php
date_default_timezone_set('Asia/Kolkata');
/**
 * Kill all idle (Sleep) RDS MySQL sessions and log actions
 * PHP 5.6 compatible
 */

// ==== CONFIG ====
$host     = 'starsaathi-rds-server.clcy6zb4izp8.ap-south-1.rds.amazonaws.com';
$user     = 'admin';
$pass     = 'zwPB6L65ZC}p8L89';
$dbname   = 'starsaathi_STARS';
$logFile  = '/var/www/kill_idle_rds.log';
$idleTime = 60; // Only kill sessions idle more than X seconds

// Users to never kill (system/replication users)
$skipUsers = array('rdsadmin', 'replication');

// ==== CONNECT ====
$mysqli = new mysqli($host, $user, $pass, $dbname);
if ($mysqli->connect_errno) {
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Connection error: " . $mysqli->connect_error . PHP_EOL, FILE_APPEND);
    exit(1);
}

// ==== GET PROCESS LIST ====
$result = $mysqli->query("SHOW FULL PROCESSLIST");
if (!$result) {
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Failed to run SHOW PROCESSLIST: " . $mysqli->error . PHP_EOL, FILE_APPEND);
    $mysqli->close();
    exit(1);
}

$killedCount = 0;
while ($row = $result->fetch_assoc()) {
    $id       = (int)$row['Id'];
    $userName = $row['User'];
    $command  = $row['Command'];
    $time     = (int)$row['Time'];

    // Skip self connection, system users, and non-Sleep states
    if ($id === $mysqli->thread_id) continue;
    if (in_array($userName, $skipUsers)) continue;
    if (strtoupper($command) !== 'SLEEP') continue;
    if ($time < $idleTime) continue;

    // Kill session
    if ($mysqli->query("KILL $id")) {
        $killedCount++;
        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Killed idle session: ID=$id, User=$userName, IdleTime={$time}s" . PHP_EOL, FILE_APPEND);
    } else {
        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Failed to kill ID=$id: " . $mysqli->error . PHP_EOL, FILE_APPEND);
    }
}

$result->free();
$mysqli->close();

file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Run complete. Total killed: $killedCount" . PHP_EOL, FILE_APPEND);
?>