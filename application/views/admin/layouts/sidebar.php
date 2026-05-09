<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="<?php echo site_url('admin'); ?>" class="brand-link">
        <i class="fas fa-hospital-symbol brand-image img-circle elevation-3"></i>
        <span class="brand-text font-weight-light">HMS Admin</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <div class="img-circle elevation-2 bg-info d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                    <i class="fas fa-user text-white"></i>
                </div>
            </div>
            <div class="info">
                <a href="<?php echo site_url('admin'); ?>" class="d-block">
                    <?php echo html_escape($current_user_name); ?>
                </a>
                <span class="text-muted text-sm">Administrator</span>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
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
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-procedures"></i>
                        <p>Patients</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-calendar-check"></i>
                        <p>Appointments</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>Billing</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
