<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo html_escape($page_title); ?> | HMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >
    <style>
        body {
            background-color: #f4f7fb;
            color: #1f2937;
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        .dashboard-shell {
            padding-top: 32px;
            padding-bottom: 32px;
        }

        .welcome-card,
        .placeholder-card {
            border: 0;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.75rem rgba(15, 23, 42, 0.06);
        }

        .placeholder-card {
            min-height: 180px;
        }

        .section-label {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #6b7280;
            letter-spacing: 0.04em;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="javascript:void(0);">Hospital Management System</a>
        <div class="d-flex align-items-center ms-auto">
            <span class="text-white me-3 small">
                <?php echo html_escape($current_user_role); ?> | <?php echo html_escape($current_user_name); ?>
            </span>
            <a href="<?php echo site_url('logout'); ?>" class="btn btn-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container dashboard-shell">
