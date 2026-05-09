<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button" aria-label="Toggle Sidebar">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item d-none d-sm-inline-block">
            <span class="nav-link text-dark">
                <i class="fas fa-user-shield mr-1"></i>
                <?php echo html_escape($current_user_name); ?>
            </span>
        </li>
        <li class="nav-item">
            <a href="<?php echo site_url('logout'); ?>" class="nav-link text-danger">
                <i class="fas fa-sign-out-alt mr-1"></i> Logout
            </a>
        </li>
    </ul>
</nav>
