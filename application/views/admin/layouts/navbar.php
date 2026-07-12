<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$navbar_role = $this->session->userdata('user_role');
if (!$navbar_role) {
    $navbar_role = $this->session->userdata('role');
}
$navbar_role = $navbar_role ? ucfirst($navbar_role) : 'Admin';
$active_menu = isset($active_menu) ? $active_menu : '';
$patient_workspace_active = in_array($active_menu, array('patients', 'appointments'), true);

if (strtolower($navbar_role) === 'doctor') {
    $patients_url = site_url('doctor/patients');
    $appointments_url = site_url('doctor/appointments');
} elseif (strtolower($navbar_role) === 'receptionist') {
    $patients_url = site_url('receptionist/patients');
    $appointments_url = site_url('receptionist/appointments');
} else {
    $patients_url = site_url('admin/patients');
    $appointments_url = site_url('admin/appointments');
}
?>
<nav class="main-header navbar navbar-expand navbar-white navbar-light hms-admin-navbar">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link hms-navbar-toggle" data-widget="pushmenu" href="#" role="button" aria-label="Toggle Sidebar">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>

    <div class="hms-navbar-context d-none d-lg-flex">
        <span class="hms-context-label">CONTROL CENTER</span>
        <span class="hms-context-divider"></span>
        <span>Hospital Operations</span>
    </div>

    <ul class="navbar-nav ml-auto align-items-center">
        <li class="nav-item dropdown hms-patient-menu">
            <a class="nav-link hms-patient-menu-toggle <?php echo $patient_workspace_active ? 'is-active' : ''; ?>" data-toggle="dropdown" href="#" role="button" aria-expanded="false">
                <span class="hms-patient-menu-icon"><i class="fas fa-user-injured"></i></span>
                <span class="hms-patient-menu-copy"><small>WORKSPACE</small>Patient Management</span>
                <i class="fas fa-chevron-down hms-patient-menu-chevron"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right hms-patient-dropdown">
                <div class="hms-patient-dropdown-head">
                    <span class="hms-patient-menu-icon"><i class="fas fa-user-injured"></i></span>
                    <div><strong>Patient Management</strong><small>Care delivery workspace</small></div>
                </div>
                <a class="dropdown-item <?php echo $active_menu === 'patients' ? 'active' : ''; ?>" href="<?php echo $patients_url; ?>">
                    <i class="fas fa-users"></i><span><strong>Patients</strong><small>Directory and patient records</small></span>
                </a>
                <a class="dropdown-item <?php echo $active_menu === 'appointments' ? 'active' : ''; ?>" href="<?php echo $appointments_url; ?>">
                    <i class="fas fa-calendar-check"></i><span><strong>Appointments</strong><small>Schedules and care visits</small></span>
                </a>
            </div>
        </li>
        <li class="nav-item">
            <div class="hms-navbar-user">
                <span class="hms-navbar-user-icon">
                    <i class="fas fa-user-shield"></i>
                </span>
                <span class="hms-navbar-user-meta">
                    <strong><?php echo html_escape($current_user_name); ?></strong>
                    <small><?php echo html_escape($navbar_role); ?></small>
                </span>
            </div>
        </li>
        <li class="nav-item">
            <a href="<?php echo site_url('logout'); ?>" class="nav-link hms-logout-link">
                <i class="fas fa-sign-out-alt mr-1"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</nav>
