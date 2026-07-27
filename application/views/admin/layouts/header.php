<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo html_escape($page_title); ?> | HMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@1.13.3/css/OverlayScrollbars.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/dashboard-pro.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/hms-enterprise.css'); ?>">

    <style>
        .brand-link .brand-image {
            float: none;
            margin-top: -3px;
            margin-right: 8px;
            margin-left: 0;
            opacity: 1;
        }

        .content-header h1 {
            font-size: 1.75rem;
            font-weight: 600;
        }

        .welcome-card .card-body {
            min-height: 170px;
        }

        .stat-box .inner h3 {
            font-size: 2rem;
            font-weight: 700;
        }

        .stat-box .icon {
            opacity: 0.2;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed hms-admin-shell">
<div class="wrapper">
