<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$navbar_role = $this->session->userdata('user_role');
if (!$navbar_role) {
    $navbar_role = $this->session->userdata('role');
}
$navbar_role = $navbar_role ? ucfirst($navbar_role) : 'Admin';
?>
<nav class="main-header navbar navbar-expand navbar-white navbar-light hms-admin-navbar">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link hms-navbar-toggle" data-widget="pushmenu" href="#" role="button" aria-label="Toggle Sidebar">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto align-items-center">
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
