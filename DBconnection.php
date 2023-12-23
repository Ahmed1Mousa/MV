<?php
class DBConnection {
    private $servername = "localhost"; // Change this to your MySQL server name if different
    private $username = "root"; // Change this to your MySQL username
    private $password = ""; // Change this to your MySQL password if set
    private $dbname = "mvogms_db"; // Change this to your database name

    public $conn;

    public function __construct() {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        return $this->conn;
    }
}
?>
