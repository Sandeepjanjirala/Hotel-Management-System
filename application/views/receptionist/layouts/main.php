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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@2.4.18/dist/css/skins/skin-green.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/hms-enterprise.css'); ?>">
    <style>
        .content-header > h1 { font-size: 22px; }
        .table > tbody > tr > td { vertical-align: middle; }
        .page-actions { margin-bottom: 15px; }
        .hms-filter .form-control { margin-right: 6px; max-width: 190px; }
        .hms-actions .btn { margin-right: 4px; }
        .summary-line { border-bottom: 1px solid #f4f4f4; padding: 8px 0; }
    </style>
</head>
<body class="hold-transition skin-green sidebar-mini hms-panel-shell hms-panel-receptionist">
<div class="wrapper">
    <header class="main-header">
        <a href="<?php echo site_url('receptionist/dashboard'); ?>" class="logo">
            <span class="logo-mini">HMS</span>
            <span class="logo-lg">HMS Reception</span>
        </a>
        <nav class="navbar navbar-static-top">
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="<?php echo site_url($logout_uri); ?>">
                            <i class="fa fa-sign-out"></i> Sign out
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <?php $this->load->view('receptionist/layouts/sidebar', array('active_menu' => $active_menu)); ?>

    <div class="content-wrapper">
        <section class="content-header">
            <h1><?php echo html_escape($page_title); ?></h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo site_url('receptionist/dashboard'); ?>"><i class="fa fa-dashboard"></i> Receptionist</a></li>
                <li class="active"><?php echo html_escape($page_title); ?></li>
            </ol>
        </section>

        <section class="content">
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success"><?php echo html_escape($this->session->flashdata('success')); ?></div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger"><?php echo html_escape($this->session->flashdata('error')); ?></div>
            <?php endif; ?>

            <?php echo $content_view; ?>
        </section>
    </div>

    <footer class="main-footer">
        <strong>HMS</strong> Receptionist Panel
    </footer>
</div>

<script>
window.HMS_CSRF = {
    name: '<?php echo $this->security->get_csrf_token_name(); ?>',
    hash: '<?php echo $this->security->get_csrf_hash(); ?>'
};
</script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@2.4.18/dist/js/adminlte.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?php echo base_url('assets/js/hms-ajax.js'); ?>"></script>
</body>
</html>
