<?php
// $host = "3.7.158.23";
// $port = 3306;
// $user = "sfa";
// $pass = "Passw0rd123#$";
// $db = "acedns_STAR";

// $link = mysqli_connect($host, $user, $pass, $db, $port);

// if (!$link) {
//     die("Connection failed: " . mysqli_connect_error());
// }

// mysqli_set_charset($link, 'utf8mb4');
// echo "✅ Connected successfully from server.";
?>

<?php
// $host = "3.7.158.23";
// $port = 3306;
// $user = "sfa";
// $pass = "Passw0rd123#$";
// $db = "acedns_STAR";

// $link = mysqli_init();

// if (!$link) {
//     die("MySQLi initialization failed");
// }

// if (!mysqli_real_connect($link, $host, $user, $pass, $db, $port)) {
//     die("Connection failed: " . mysqli_connect_error());
// }

// // Instead of mysqli_options, run this query AFTER connection
// if (!mysqli_query($link, "SET NAMES 'utf8' COLLATE 'utf8_general_ci'")) {
//     die("Failed to set names: " . mysqli_error($link));
// }

// echo "Connected successfully from server.";
?>


<?php
//phpinfo();
// $host = "3.7.158.23";
// $port = 3306;
// $user = "sfa";
// $pass = "Passw0rd123#$";
// $db = "acedns_STAR";

// $link = mysqli_init();

// if (!$link) {
//     die("MySQLi initialization failed");
// }

// // Connect to MySQL
// if (!mysqli_real_connect($link, $host, $user, $pass, $db, $port)) {
//     die("Connection failed: " . mysqli_connect_error());
// }

// // ✅ Set proper charset BEFORE any query
// if (!mysqli_set_charset($link, "utf8")) {
//     die("Error loading character set utf8: " . mysqli_error($link));
// }

// echo "Connected successfully from server.";



// $output = shell_exec('/var/www/html/db_connect.sh 2>&1');

// if (strpos($output, 'Shell connection established') !== false) {
//     echo "Connection successful via shell script\n";
    
//     // Alternative method using direct shell access
//     //$query = "SELECT 1 AS test_value";
//     $sql = "SELECT * FROM branch_dump ";
//     $result = shell_exec("mysql --host=3.7.158.23 --port=3306 --user=sfa --password='Passw0rd123#$' " .
//                          "--database=acedns_STAR --default-character-set=binary " .
//                          "--execute=\"$sql\" 2>&1");
    
//     echo "Query result: " . trim($result) . "\n";
// } else {
//     echo "Connection failed. Error output:\n";
//     echo $output;
// }



class sfa_connection
{
    public $linkremote;

    private $host = '3.7.158.23';
    private $port = '3306';
    private $user = 'sfa';
    private $password = 'Passw0rd123#$';
    private $database = 'acedns_STAR';

    public function __construct()
    {
        
        $this->linkremote = uniqid('shell_db_');
    }

   
    public function runQuery($sql)
    {
        $escaped_sql = escapeshellarg($sql);
        $command = "mysql --host={$this->host} --port={$this->port} --user={$this->user} --password='{$this->password}' " .
                   "--database={$this->database} --batch --skip-column-names --execute={$escaped_sql} 2>&1";

        return trim(shell_exec($command));
    }


    public function getRows($sql)
    {
        $output = $this->runQuery($sql);
        $lines = explode("\n", $output);
        return array_filter($lines, function ($line) {
            return trim($line) !== '';
        });
    }

  
    public function getRowCount($sql)
    {
        $rows = $this->getRows($sql);
        return count($rows);
    }
}


if (!function_exists('mysql_query')) {
    function mysql_query($sql, $link = null)
    {
        if ($link && strpos($link, 'shell_db_') === 0) {
            $conn = new sfa_connection();
            return $conn->runQuery($sql);
        }
        return false; 
    }
}
?>



