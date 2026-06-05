<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<aside class="main-sidebar">
    <section class="sidebar">
        <div class="user-panel">
            <div class="pull-left image">
                <i class="fa fa-user-circle fa-2x text-green"></i>
            </div>
            <div class="pull-left info">
                <p><?php echo html_escape($current_user_name); ?></p>
                <a href="#"><i class="fa fa-circle text-success"></i> Receptionist</a>
            </div>
        </div>

        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">RECEPTION DESK</li>
            <li class="<?php echo $active_menu === 'dashboard' ? 'active' : ''; ?>">
                <a href="<?php echo site_url('receptionist/dashboard'); ?>"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a>
            </li>
            <li class="<?php echo $active_menu === 'patients' ? 'active' : ''; ?>">
                <a href="<?php echo site_url('receptionist/patients'); ?>"><i class="fa fa-address-book"></i> <span>Patients</span></a>
            </li>
            <li class="<?php echo $active_menu === 'appointments' ? 'active' : ''; ?>">
                <a href="<?php echo site_url('receptionist/appointments'); ?>"><i class="fa fa-calendar"></i> <span>Appointments</span></a>
            </li>
            <li class="<?php echo $active_menu === 'billing' ? 'active' : ''; ?>">
                <a href="<?php echo site_url('receptionist/billing'); ?>"><i class="fa fa-file-text-o"></i> <span>Billing</span></a>
            </li>
            <li class="<?php echo $active_menu === 'pharmacy' ? 'active' : ''; ?>">
                <a href="<?php echo site_url('receptionist/pharmacy-billing'); ?>"><i class="fa fa-medkit"></i> <span>Pharmacy Billing</span></a>
            </li>
        </ul>
    </section>
</aside>
