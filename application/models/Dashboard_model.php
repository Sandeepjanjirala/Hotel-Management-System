<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model
{
    protected $users_table = 'users';
    protected $departments_table = 'departments';
    protected $managed_roles = array('admin', 'doctor', 'receptionist');

    public function __construct()
    {
        parent::__construct();
    }

    public function get_statistics()
    {
        return array(
            'total_departments' => $this->count_departments(),
            'total_doctors' => $this->count_users_by_role('doctor'),
            'total_receptionists' => $this->count_users_by_role('receptionist'),
            'active_users' => $this->count_active_users()
        );
    }

    public function get_status_summary()
    {
        return array(
            'active_departments' => $this->count_departments_by_status(1),
            'inactive_departments' => $this->count_departments_by_status(0),
            'active_doctors' => $this->count_users_by_role_and_status('doctor', 1),
            'active_receptionists' => $this->count_users_by_role_and_status('receptionist', 1)
        );
    }

    public function get_recent_users($limit = 5)
    {
        return $this->db
            ->select('u.id, u.name, u.email, u.role, u.status, u.created_at')
            ->select('d.department_name')
            ->from($this->users_table . ' u')
            ->join($this->departments_table . ' d', 'd.id = u.department_id', 'left')
            ->where_in('u.role', $this->managed_roles)
            ->order_by('u.id', 'DESC')
            ->limit((int) $limit)
            ->get()
            ->result_array();
    }

    public function get_recent_departments($limit = 5)
    {
        return $this->db
            ->select('id, department_name, status, created_at')
            ->from($this->departments_table)
            ->order_by('id', 'DESC')
            ->limit((int) $limit)
            ->get()
            ->result_array();
    }

    public function count_departments()
    {
        return (int) $this->db
            ->from($this->departments_table)
            ->count_all_results();
    }

    public function count_departments_by_status($status)
    {
        return (int) $this->db
            ->from($this->departments_table)
            ->where('status', (int) $status)
            ->count_all_results();
    }

    public function count_users_by_role($role)
    {
        return (int) $this->db
            ->from($this->users_table)
            ->where('role', $role)
            ->count_all_results();
    }

    public function count_users_by_role_and_status($role, $status)
    {
        return (int) $this->db
            ->from($this->users_table)
            ->where('role', $role)
            ->where('status', (int) $status)
            ->count_all_results();
    }

    public function count_active_users()
    {
        return (int) $this->db
            ->from($this->users_table)
            ->where_in('role', $this->managed_roles)
            ->where('status', 1)
            ->count_all_results();
    }
}
