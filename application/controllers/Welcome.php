<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

    public function index()
    {
        $conn = $this->db->conn_id;

        if($conn){
            echo "<h2 style='color:green;'>Database Connected Successfully to CI3 ✅</h2>";
        }else{
            echo "<h2 style='color:red;'>Database Connection Failed ❌</h2>";
        }
    }
}