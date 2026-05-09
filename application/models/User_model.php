<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
    protected $table = 'users';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_by_email($email)
    {
        return $this->db
            ->select('id, name, email, password, role, status, last_login')
            ->from($this->table)
            ->where('email', $email)
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function update_last_login($user_id)
    {
        return $this->db
            ->where('id', (int) $user_id)
            ->update($this->table, array(
                'last_login' => date('Y-m-d H:i:s')
            ));
    }
}
