<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo html_escape($page_title); ?> | HMS</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/v4-shims.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@2.4.18/dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@2.4.18/dist/css/skins/skin-blue.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/hms-enterprise.css'); ?>">
    <style>
        .content-header > h1 { font-size: 22px; }
        .doctor-table-actions .btn { margin-right: 4px; }
        .box-tools .form-inline .form-control { margin-left: 6px; max-width: 180px; }
        .table > tbody > tr > td { vertical-align: middle; }
        .flash-wrapper { margin-bottom: 15px; }
    </style>
</head>
<body class="hold-transition skin-blue sidebar-mini hms-panel-shell hms-panel-doctor">
<div class="wrapper">
    <header class="main-header">
        <a href="<?php echo site_url('doctor/dashboard'); ?>" class="logo">
            <span class="logo-mini">HMS</span>
            <span class="logo-lg">HMS Doctor</span>
        </a>
        <nav class="navbar navbar-static-top">
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-user-md"></i>
                            <span class="hidden-xs"><?php echo html_escape(isset($doctor_profile->name) ? $doctor_profile->name : 'Doctor'); ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="user-header">
                                <p>
                                    <?php echo html_escape(isset($doctor_profile->name) ? $doctor_profile->name : 'Doctor'); ?>
                                    <small><?php echo html_escape(isset($doctor_profile->department) ? $doctor_profile->department : 'Doctor Panel'); ?></small>
                                </p>
                            </li>
                            <li class="user-footer">
                                <div class="pull-right">
                                    <a href="<?php echo site_url($logout_uri); ?>" class="btn btn-default btn-flat">Sign out</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <?php $this->load->view('doctor/layouts/sidebar', array('active_menu' => $active_menu)); ?>

    <div class="content-wrapper">
        <section class="content-header">
            <h1><?php echo html_escape($page_title); ?></h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo site_url('doctor/dashboard'); ?>"><i class="fa fa-dashboard"></i> Doctor</a></li>
                <li class="active"><?php echo html_escape($page_title); ?></li>
            </ol>
        </section>

        <section class="content">
            <div class="flash-wrapper">
                <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success"><?php echo html_escape($this->session->flashdata('success')); ?></div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger"><?php echo html_escape($this->session->flashdata('error')); ?></div>
                <?php endif; ?>
            </div>
            <?php echo $content_view; ?>
        </section>
    </div>

    <footer class="main-footer">
        <strong>HMS</strong> Doctor Panel
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@2.4.18/dist/js/adminlte.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
window.HMS_CSRF = {
    name: '<?php echo $this->security->get_csrf_token_name(); ?>',
    hash: '<?php echo $this->security->get_csrf_hash(); ?>'
};
</script>
<script src="<?php echo base_url('assets/js/hms-ajax.js'); ?>"></script>
</body>
</html>
