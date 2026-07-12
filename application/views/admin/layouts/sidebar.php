<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$sidebar_role = isset($current_user_role) ? $current_user_role : $this->session->userdata('user_role');
$active_menu = isset($active_menu) ? $active_menu : '';

if ($sidebar_role === 'admin') {
    $home_url = site_url('admin');
    $brand_suffix = 'Admin';
} elseif ($sidebar_role === 'doctor') {
    $home_url = site_url('doctor');
    $brand_suffix = 'Doctor';
} else {
    $home_url = site_url('receptionist');
    $brand_suffix = 'Reception';
}

$menu_groups = array();

if ($sidebar_role === 'admin') {
    $menu_groups = array(
        array(
            'label' => 'Hospital Management',
            'icon' => 'fas fa-hospital',
            'menus' => array('departments', 'users', 'doctors'),
            'items' => array(
                array('label' => 'Departments', 'uri' => 'admin/departments', 'menu' => 'departments'),
                array('label' => 'Users', 'uri' => 'admin/users', 'menu' => 'users'),
                array('label' => 'Doctors', 'uri' => 'admin/doctors', 'menu' => 'doctors')
            )
        ),
        array(
            'label' => 'Clinical Management',
            'icon' => 'fas fa-stethoscope',
            'menus' => array('prescriptions', 'lab_requests', 'lab_tests'),
            'items' => array(
                array('label' => 'Prescriptions', 'uri' => 'admin/prescriptions', 'menu' => 'prescriptions'),
                array('label' => 'Lab Requests', 'uri' => 'admin/lab-requests', 'menu' => 'lab_requests'),
                array('label' => 'Lab Tests', 'uri' => 'admin/lab-tests', 'menu' => 'lab_tests')
            )
        ),
        array(
            'label' => 'Pharmacy',
            'icon' => 'fas fa-capsules',
            'menus' => array('medicines', 'pharmacy_billing'),
            'items' => array(
                array('label' => 'Medicines', 'uri' => 'admin/medicines', 'menu' => 'medicines'),
                array('label' => 'Pharmacy Billing', 'uri' => 'admin/pharmacy-billing', 'menu' => 'pharmacy_billing')
            )
        ),
        array(
            'label' => 'Finance',
            'icon' => 'fas fa-coins',
            'menus' => array('billing'),
            'items' => array(
                array('label' => 'Billing', 'uri' => 'admin/billing', 'menu' => 'billing')
            )
        )
    );
} elseif ($sidebar_role === 'doctor') {
    $menu_groups = array(
        array(
            'label' => 'Clinical Management',
            'icon' => 'fas fa-stethoscope',
            'menus' => array('prescriptions', 'lab_requests'),
            'items' => array(
                array('label' => 'Prescriptions', 'uri' => 'doctor/appointments', 'menu' => 'prescriptions'),
                array('label' => 'Lab Requests', 'uri' => 'doctor/appointments', 'menu' => 'lab_requests')
            )
        )
    );
} else {
    $menu_groups = array(
        array(
            'label' => 'Pharmacy',
            'icon' => 'fas fa-capsules',
            'menus' => array('pharmacy', 'pharmacy_billing'),
            'items' => array(
                array('label' => 'Pharmacy Billing', 'uri' => 'receptionist/pharmacy-billing', 'menu' => 'pharmacy')
            )
        ),
        array(
            'label' => 'Finance',
            'icon' => 'fas fa-coins',
            'menus' => array('billing'),
            'items' => array(
                array('label' => 'Billing', 'uri' => 'receptionist/billing', 'menu' => 'billing')
            )
        )
    );
}
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4 hms-nav-sidebar">
    <a href="<?php echo $home_url; ?>" class="brand-link hms-brand-link">
        <span class="hms-brand-mark">
            <i class="fas fa-hospital-symbol"></i>
        </span>
        <span class="brand-text font-weight-light">HMS <?php echo html_escape($brand_suffix); ?></span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex hms-user-panel">
            <div class="image">
                <div class="hms-user-avatar">
                    <i class="fas fa-user"></i>
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
            <ul class="nav nav-pills nav-sidebar flex-column hms-sidebar-nav" data-widget="treeview" role="menu" data-accordion="false">
                <li class="hms-nav-section">WORKSPACE</li>
                <li class="nav-item">
                    <a href="<?php echo $home_url; ?>" class="nav-link <?php echo ($active_menu === 'dashboard') ? 'active' : ''; ?>" title="Dashboard">
                        <i class="nav-icon fas fa-chart-pie"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="hms-nav-section">OPERATIONS</li>
                <?php foreach ($menu_groups as $group): ?>
                    <?php $group_active = in_array($active_menu, $group['menus'], true); ?>
                    <li class="nav-item has-treeview <?php echo $group_active ? 'menu-open' : ''; ?>">
                        <a href="#" class="nav-link hms-group-link <?php echo $group_active ? 'active' : ''; ?>" title="<?php echo html_escape($group['label']); ?>">
                            <i class="nav-icon <?php echo html_escape($group['icon']); ?>"></i>
                            <p>
                                <?php echo html_escape($group['label']); ?>
                                <i class="right fas fa-angle-right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview hms-nav-children">
                            <?php foreach ($group['items'] as $item): ?>
                                <li class="nav-item">
                                    <a href="<?php echo site_url($item['uri']); ?>" class="nav-link <?php echo ($active_menu === $item['menu']) ? 'active' : ''; ?>">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p><?php echo html_escape($item['label']); ?></p>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>
</aside>
