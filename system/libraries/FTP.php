<?php

class FTP {

    private $conn;

    public function __construct() {
        $this->conn = ftp_connect(FTP_SERVER) or die("Could not connect to " . FTP_SERVER);
    }

    public function login() {
        return ftp_login($this->conn, FTP_USERNAME, FTP_PASSWORD);
    }

    public function close() {
        if (!$this->conn)
            return ftp_close($this->conn);
        else
            return FALSE;
    }

}
