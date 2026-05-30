<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<aside class="main-sidebar">
    <section class="sidebar">
        <div class="user-panel">
            <div class="pull-left image">
                <i class="fa fa-user-md fa-2x text-aqua"></i>
            </div>
            <div class="pull-left info">
                <p><?php echo html_escape(isset($doctor_profile->name) ? $doctor_profile->name : 'Doctor'); ?></p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">DOCTOR PANEL</li>
            <li class="<?php echo $active_menu === 'dashboard' ? 'active' : ''; ?>">
                <a href="<?php echo site_url('doctor/dashboard'); ?>">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="<?php echo $active_menu === 'appointments' ? 'active' : ''; ?>">
                <a href="<?php echo site_url('doctor/appointments'); ?>">
                    <i class="fa fa-calendar-check-o"></i> <span>My Appointments</span>
                </a>
            </li>
            <li class="<?php echo $active_menu === 'patients' ? 'active' : ''; ?>">
                <a href="<?php echo site_url('doctor/patients'); ?>">
                    <i class="fa fa-heartbeat"></i> <span>Assigned Patients</span>
                </a>
            </li>
        </ul>
    </section>
</aside>