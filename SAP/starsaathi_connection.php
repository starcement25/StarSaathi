<?php
class starsaathi_connection  {
    public $conn;

    public function __construct() {
        $servername = "starsaathi-rds-server.clcy6zb4izp8.ap-south-1.rds.amazonaws.com";
        $username = "admin";
        $password = "zwPB6L65ZC}p8L89";
        $dbname = "starsaathi_STARS";

        $this->conn = mysql_connect($servername, $username, $password);
        if (!$this->conn) {
            die("Could not connect (local): " . mysql_error());
        }

        if (!mysql_select_db($dbname, $this->conn)) {
            die("Can't select database (local): " . mysql_error());
        }
    }
}
?>