<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$sidebar_role = isset($current_user_role) ? $current_user_role : $this->session->userdata('user_role');
$active_menu = isset($active_menu) ? $active_menu : '';

if ($sidebar_role === 'admin') {
    $home_url = site_url('admin');
    $brand_suffix = 'Admin';
} elseif ($sidebar_role === 'doctor') {
    $home_url = site_url('admin/prescriptions');
    $brand_suffix = 'Doctor';
} else {
    $home_url = site_url('admin/patients');
    $brand_suffix = 'Reception';
}
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="<?php echo $home_url; ?>" class="brand-link">
        <i class="fas fa-hospital-symbol brand-image img-circle elevation-3"></i>
        <span class="brand-text font-weight-light">HMS <?php echo html_escape($brand_suffix); ?></span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <div class="img-circle elevation-2 bg-info d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                    <i class="fas fa-user text-white"></i>
                </div>
            </div>
            <div class="info">
                <a href="<?php echo $home_url; ?>" class="d-block">
                    <?php echo html_escape($current_user_name); ?>
                </a>
                <span class="text-muted text-sm">
                    <?php echo html_escape(ucfirst($sidebar_role)); ?>
                </span>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <?php if ($sidebar_role === 'admin'): ?>
                    <li class="nav-item">
                        <a href="<?php echo site_url('admin'); ?>" class="nav-link <?php echo ($active_menu === 'dashboard') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo site_url('admin/departments'); ?>" class="nav-link <?php echo ($active_menu === 'departments') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-building"></i>
                            <p>Departments</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo site_url('admin/users'); ?>" class="nav-link <?php echo ($active_menu === 'users') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-users-cog"></i>
                            <p>Users</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo site_url('admin/doctors'); ?>" class="nav-link <?php echo ($active_menu === 'doctors') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-user-md"></i>
                            <p>Doctors</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo site_url('admin/medicines'); ?>" class="nav-link <?php echo ($active_menu === 'medicines') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-capsules"></i>
                            <p>Medicines</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo site_url('admin/lab-tests'); ?>" class="nav-link <?php echo ($active_menu === 'lab_tests') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-vials"></i>
                            <p>Lab Tests</p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($sidebar_role === 'admin' || $sidebar_role === 'receptionist'): ?>
                    <li class="nav-item">
                        <a href="<?php echo site_url('admin/patients'); ?>" class="nav-link <?php echo ($active_menu === 'patients') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-procedures"></i>
                            <p>Patients</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo site_url('admin/appointments'); ?>" class="nav-link <?php echo ($active_menu === 'appointments') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-calendar-check"></i>
                            <p>Appointments</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo site_url('admin/pharmacy-billing'); ?>" class="nav-link <?php echo ($active_menu === 'pharmacy_billing') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-file-medical-alt"></i>
                            <p>Pharmacy Billing</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo site_url('admin/billing'); ?>" class="nav-link <?php echo ($active_menu === 'billing') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-file-invoice-dollar"></i>
                            <p>Billing</p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($sidebar_role === 'admin' || $sidebar_role === 'doctor'): ?>
                    <li class="nav-item">
                        <a href="<?php echo site_url('admin/prescriptions'); ?>" class="nav-link <?php echo ($active_menu === 'prescriptions') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-prescription"></i>
                            <p>Prescriptions</p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($sidebar_role === 'admin' || $sidebar_role === 'doctor' || $sidebar_role === 'receptionist'): ?>
                    <li class="nav-item">
                        <a href="<?php echo site_url('admin/lab-requests'); ?>" class="nav-link <?php echo ($active_menu === 'lab_requests') ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-flask"></i>
                            <p>Lab Requests</p>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</aside>
