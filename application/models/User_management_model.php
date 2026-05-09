<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_management_model extends CI_Model
{
    protected $table = 'users';
    protected $department_table = 'departments';
    protected $managed_roles = array('admin', 'doctor', 'receptionist');

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all_users()
    {
        return $this->db
            ->select('u.id, u.name, u.email, u.role, u.department_id, u.phone, u.status, u.created_at, u.updated_at')
            ->select('d.department_name')
            ->from($this->table . ' u')
            ->join($this->department_table . ' d', 'd.id = u.department_id', 'left')
            ->where_in('u.role', $this->managed_roles)
            ->order_by('u.id', 'DESC')
            ->get()
            ->result_array();
    }

    public function get_user_by_id($id)
    {
        return $this->db
            ->select('u.id, u.name, u.email, u.role, u.department_id, u.phone, u.status, u.created_at, u.updated_at')
            ->select('d.department_name')
            ->from($this->table . ' u')
            ->join($this->department_table . ' d', 'd.id = u.department_id', 'left')
            ->where('u.id', (int) $id)
            ->where_in('u.role', $this->managed_roles)
            ->limit(1)
            ->get()
            ->row_array();
    }

    public function get_active_departments()
    {
        return $this->db
            ->select('id, department_name')
            ->from($this->department_table)
            ->where('status', 1)
            ->order_by('department_name', 'ASC')
            ->get()
            ->result_array();
    }

    public function active_department_exists($department_id)
    {
        return $this->db
            ->from($this->department_table)
            ->where('id', (int) $department_id)
            ->where('status', 1)
            ->count_all_results() > 0;
    }

    public function exists_by_email($email, $exclude_id = NULL)
    {
        $this->db
            ->from($this->table)
            ->where('email', $email);

        if ($exclude_id !== NULL) {
            $this->db->where('id !=', (int) $exclude_id);
        }

        return $this->db->count_all_results() > 0;
    }

    public function insert_user(array $data)
    {
        $now = date('Y-m-d H:i:s');

        $data['created_at'] = $now;
        $data['updated_at'] = $now;

        $inserted = $this->db->insert($this->table, $data);

        if (!$inserted) {
            return FALSE;
        }

        return $this->db->insert_id();
    }

    public function update_user($id, array $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db
            ->where('id', (int) $id)
            ->where_in('role', $this->managed_roles)
            ->update($this->table, $data);
    }

    public function toggle_status($id)
    {
        $user = $this->get_user_by_id((int) $id);

        if (empty($user)) {
            return FALSE;
        }

        $new_status = ((int) $user['status'] === 1) ? 0 : 1;

        $updated = $this->db
            ->where('id', (int) $id)
            ->where_in('role', $this->managed_roles)
            ->update($this->table, array(
                'status' => $new_status,
                'updated_at' => date('Y-m-d H:i:s')
            ));

        if (!$updated) {
            return FALSE;
        }

        return $this->get_user_by_id((int) $id);
    }
}
