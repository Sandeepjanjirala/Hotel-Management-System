<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Patient Records | HMS</title>

    <!-- Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Bootstrap 5.3 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <style>
        /* ── Root Variables ───────────────────────────────────────── */
        :root {
            --hms-primary:       #1a73e8;
            --hms-primary-dark:  #1557b0;
            --hms-primary-light: #e8f0fe;
            --hms-success:       #1e8e3e;
            --hms-danger:        #d93025;
            --hms-warning:       #f9ab00;
            --hms-sidebar-bg:    #0d1b2a;
            --hms-sidebar-text:  #a8b8cc;
            --hms-body-bg:       #f4f6fb;
            --hms-card-radius:   14px;
            --hms-transition:    all .22s cubic-bezier(.4,0,.2,1);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--hms-body-bg);
            color: #1a2332;
            min-height: 100vh;
            margin: 0;
        }

        /* ── Layout Shell ─────────────────────────────────────────── */
        .hms-shell { display: flex; min-height: 100vh; }

        /* ── Sidebar ──────────────────────────────────────────────── */
        .hms-sidebar {
            width: 260px;
            flex-shrink: 0;
            background: var(--hms-sidebar-bg);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 1030;
            transition: var(--hms-transition);
            overflow-y: auto;
        }

        .hms-sidebar .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 22px 20px;
            border-bottom: 1px solid rgba(255,255,255,.07);
        }

        .hms-sidebar .brand-icon {
            width: 40px; height: 40px;
            background: var(--hms-primary);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 18px; flex-shrink: 0;
        }

        .hms-sidebar .brand-name { color: #fff; font-weight: 700; font-size: 17px; line-height: 1.2; }
        .hms-sidebar .brand-name span { color: var(--hms-primary); font-size: 12px; display: block; font-weight: 400; }

        .sidebar-user {
            display: flex; align-items: center; gap: 12px;
            padding: 18px 20px;
            border-bottom: 1px solid rgba(255,255,255,.07);
            margin-bottom: 8px;
        }

        .sidebar-avatar {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, var(--hms-primary) 0%, #6c63ff 100%);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 15px; font-weight: 600; flex-shrink: 0;
        }

        .sidebar-uname { color: #fff; font-size: 13px; font-weight: 600; }
        .sidebar-role  { color: var(--hms-sidebar-text); font-size: 11px; }

        .sidebar-nav { list-style: none; padding: 0 10px; margin: 0; flex: 1; }

        .sidebar-nav .nav-label {
            font-size: 10px; font-weight: 600; letter-spacing: 1px;
            color: rgba(168,184,204,.5); text-transform: uppercase;
            padding: 14px 10px 6px;
        }

        .sidebar-nav a {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 14px; border-radius: 9px; margin-bottom: 2px;
            color: var(--hms-sidebar-text); text-decoration: none;
            font-size: 13.5px; font-weight: 500;
            transition: var(--hms-transition);
        }

        .sidebar-nav a:hover { background: rgba(255,255,255,.07); color: #fff; }
        .sidebar-nav a.active { background: var(--hms-primary); color: #fff; }
        .sidebar-nav a i { width: 18px; text-align: center; font-size: 14px; }

        .sidebar-footer {
            padding: 14px 20px;
            border-top: 1px solid rgba(255,255,255,.07);
        }

        .sidebar-footer a {
            color: var(--hms-sidebar-text); text-decoration: none; font-size: 13px;
            display: flex; align-items: center; gap: 8px;
            transition: var(--hms-transition);
        }

        .sidebar-footer a:hover { color: #fff; }

        /* ── Main Content ─────────────────────────────────────────── */
        .hms-main {
            margin-left: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ── Topbar ───────────────────────────────────────────────── */
        .hms-topbar {
            background: #fff;
            border-bottom: 1px solid #e8ecf4;
            padding: 0 28px;
            height: 64px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 100;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }

        .topbar-title { font-size: 17px; font-weight: 700; color: #1a2332; }
        .topbar-breadcrumb { font-size: 12.5px; color: #8a94a6; margin-top: 2px; }
        .topbar-actions { display: flex; align-items: center; gap: 12px; }

        .btn-hms-primary {
            background: var(--hms-primary);
            border: none; color: #fff;
            padding: 9px 20px; border-radius: 9px;
            font-size: 13.5px; font-weight: 600;
            display: inline-flex; align-items: center; gap: 8px;
            cursor: pointer; transition: var(--hms-transition);
            box-shadow: 0 2px 8px rgba(26,115,232,.3);
        }

        .btn-hms-primary:hover {
            background: var(--hms-primary-dark);
            box-shadow: 0 4px 16px rgba(26,115,232,.4);
            color: #fff;
            transform: translateY(-1px);
        }

        /* ── Page Body ────────────────────────────────────────────── */
        .hms-body { padding: 28px; flex: 1; }

        /* ── Stats Row ────────────────────────────────────────────── */
        .stat-card {
            background: #fff;
            border-radius: var(--hms-card-radius);
            padding: 22px 24px;
            display: flex; align-items: center; gap: 18px;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
            transition: var(--hms-transition);
        }

        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.1); }

        .stat-icon {
            width: 52px; height: 52px; border-radius: 13px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; flex-shrink: 0;
        }

        .stat-icon.blue   { background: #e8f0fe; color: var(--hms-primary); }
        .stat-icon.green  { background: #e6f4ea; color: var(--hms-success); }
        .stat-icon.orange { background: #fef3e2; color: var(--hms-warning); }

        .stat-label { font-size: 12px; color: #8a94a6; font-weight: 500; }
        .stat-value { font-size: 28px; font-weight: 800; color: #1a2332; line-height: 1; }

        /* ── Main Card ────────────────────────────────────────────── */
        .main-card {
            background: #fff;
            border-radius: var(--hms-card-radius);
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
            overflow: hidden;
        }

        .main-card-header {
            padding: 20px 24px;
            border-bottom: 1px solid #f0f3fa;
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 14px;
        }

        .main-card-title { font-size: 16px; font-weight: 700; color: #1a2332; }
        .main-card-body { padding: 22px 24px; }

        /* ── Search & Filter Bar ──────────────────────────────────── */
        .search-filter-bar {
            display: flex; gap: 12px; flex-wrap: wrap;
            align-items: center; margin-bottom: 22px;
        }

        .search-wrap { position: relative; flex: 1; min-width: 220px; max-width: 380px; }

        .search-wrap .search-icon {
            position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
            color: #aab; font-size: 14px;
        }

        .search-wrap input {
            width: 100%; padding: 9px 36px 9px 36px;
            border: 1.5px solid #e2e8f0; border-radius: 9px;
            font-size: 13.5px; color: #1a2332; font-family: 'Inter', sans-serif;
            transition: var(--hms-transition); background: #fafbfd;
        }

        .search-wrap input:focus {
            outline: none; border-color: var(--hms-primary);
            background: #fff; box-shadow: 0 0 0 3px rgba(26,115,232,.1);
        }

        .search-clear {
            position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: #bbb; cursor: pointer; font-size: 13px;
            padding: 4px; display: none;
        }

        .search-clear.visible { display: block; }

        .filter-btn-group { display: flex; gap: 6px; }

        .filter-btn {
            padding: 8px 16px; border-radius: 8px; border: 1.5px solid #e2e8f0;
            background: #fafbfd; font-size: 13px; font-weight: 500;
            color: #5a6475; cursor: pointer; transition: var(--hms-transition);
            font-family: 'Inter', sans-serif;
        }

        .filter-btn:hover  { border-color: var(--hms-primary); color: var(--hms-primary); }
        .filter-btn.active { background: var(--hms-primary); border-color: var(--hms-primary); color: #fff; }

        /* ── Table ────────────────────────────────────────────────── */
        .hms-table { width: 100%; border-collapse: collapse; }

        .hms-table thead th {
            background: #f8f9fd; color: #6b7280;
            font-size: 11.5px; font-weight: 700; letter-spacing: .6px;
            text-transform: uppercase; padding: 12px 16px;
            border-bottom: 2px solid #edf0f7; white-space: nowrap;
        }

        .hms-table tbody tr {
            border-bottom: 1px solid #f0f3fa;
            transition: var(--hms-transition);
        }

        .hms-table tbody tr:hover { background: #fafbff; }

        .hms-table tbody td {
            padding: 14px 16px; font-size: 13.5px; color: #374151; vertical-align: middle;
        }

        .patient-code {
            font-family: 'Courier New', monospace;
            font-size: 12.5px; font-weight: 700;
            background: var(--hms-primary-light);
            color: var(--hms-primary);
            padding: 3px 10px; border-radius: 6px; display: inline-block;
        }

        .patient-name { font-weight: 600; color: #1a2332; }
        .patient-meta { font-size: 12px; color: #8a94a6; }

        .badge-active   { background: #e6f4ea; color: var(--hms-success); }
        .badge-inactive { background: #fce8e6; color: var(--hms-danger); }

        .status-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
        }

        .status-badge::before {
            content: ''; width: 6px; height: 6px; border-radius: 50%;
        }

        .badge-active::before   { background: var(--hms-success); }
        .badge-inactive::before { background: var(--hms-danger); }

        /* ── Action Buttons ───────────────────────────────────────── */
        .action-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 32px; height: 32px; border-radius: 8px;
            border: none; cursor: pointer; font-size: 13px;
            transition: var(--hms-transition); text-decoration: none;
        }

        .action-btn.edit   { background: #e8f0fe; color: var(--hms-primary); }
        .action-btn.toggle { background: #fef3e2; color: #b45309; }
        .action-btn.del    { background: #fce8e6; color: var(--hms-danger); }

        .action-btn:hover { filter: brightness(.88); transform: scale(1.08); }

        /* ── Empty / Loader States ────────────────────────────────── */
        .empty-state {
            text-align: center; padding: 64px 20px; color: #8a94a6;
        }

        .empty-state .empty-icon {
            font-size: 56px; color: #dde2ef; margin-bottom: 16px;
        }

        .empty-state h5 { font-weight: 700; color: #374151; font-size: 16px; }
        .empty-state p  { font-size: 14px; }

        .loader-row td { padding: 48px !important; }

        .hms-spinner {
            width: 36px; height: 36px;
            border: 3px solid #e8ecf4;
            border-top-color: var(--hms-primary);
            border-radius: 50%;
            animation: hms-spin .7s linear infinite;
            margin: 0 auto 12px;
        }

        @keyframes hms-spin { to { transform: rotate(360deg); } }

        /* ── Pagination ───────────────────────────────────────────── */
        .hms-pagination {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px 0 4px; flex-wrap: wrap; gap: 10px;
        }

        .page-info { font-size: 13px; color: #8a94a6; }

        .page-btns { display: flex; gap: 4px; }

        .page-btn {
            min-width: 34px; height: 34px; padding: 0 10px;
            display: inline-flex; align-items: center; justify-content: center;
            border: 1.5px solid #e2e8f0; border-radius: 8px; background: #fff;
            font-size: 13px; color: #374151; cursor: pointer;
            transition: var(--hms-transition); font-family: 'Inter', sans-serif;
        }

        .page-btn:hover       { border-color: var(--hms-primary); color: var(--hms-primary); }
        .page-btn.active      { background: var(--hms-primary); border-color: var(--hms-primary); color: #fff; }
        .page-btn[disabled]   { opacity: .4; cursor: not-allowed; pointer-events: none; }

        /* ── Modal ────────────────────────────────────────────────── */
        .modal-content {
            border: none;
            border-radius: var(--hms-card-radius);
            box-shadow: 0 20px 60px rgba(0,0,0,.18);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--hms-primary) 0%, #1557b0 100%);
            color: #fff; border-radius: var(--hms-card-radius) var(--hms-card-radius) 0 0;
            padding: 18px 24px;
        }

        .modal-title { font-weight: 700; font-size: 16px; }
        .modal-header .btn-close { filter: brightness(0) invert(1); }

        .modal-body { padding: 24px; }
        .modal-footer { padding: 16px 24px; border-top: 1px solid #f0f3fa; }

        .form-label { font-size: 12.5px; font-weight: 600; color: #374151; margin-bottom: 5px; }

        .form-control {
            border: 1.5px solid #e2e8f0; border-radius: 8px;
            font-size: 13.5px; padding: 9px 13px; font-family: 'Inter', sans-serif;
            transition: var(--hms-transition); color: #1a2332;
        }

        .form-control:focus {
            border-color: var(--hms-primary); box-shadow: 0 0 0 3px rgba(26,115,232,.1);
        }

        .field-error { font-size: 11.5px; color: var(--hms-danger); margin-top: 4px; font-weight: 500; }

        .patient-code-box {
            background: var(--hms-primary-light); border: 1.5px dashed var(--hms-primary);
            border-radius: 9px; padding: 10px 16px; font-size: 13px;
            color: var(--hms-primary); font-weight: 600; margin-bottom: 18px;
        }

        /* ── Toast ────────────────────────────────────────────────── */
        #toastContainer {
            position: fixed; top: 22px; right: 22px; z-index: 9999;
            display: flex; flex-direction: column; gap: 10px;
        }

        .hms-toast {
            min-width: 300px; max-width: 400px;
            background: #fff; border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,.15);
            padding: 14px 18px;
            display: flex; align-items: flex-start; gap: 12px;
            animation: toastIn .3s cubic-bezier(.34,1.56,.64,1);
        }

        @keyframes toastIn {
            from { opacity: 0; transform: translateX(60px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        .toast-fadeout { animation: toastOut .3s ease forwards; }

        @keyframes toastOut {
            to { opacity: 0; transform: translateX(60px); }
        }

        .hms-toast .toast-icon {
            width: 34px; height: 34px; border-radius: 9px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px;
        }

        .hms-toast.success .toast-icon { background: #e6f4ea; color: var(--hms-success); }
        .hms-toast.error   .toast-icon { background: #fce8e6; color: var(--hms-danger); }
        .hms-toast.info    .toast-icon { background: #e8f0fe; color: var(--hms-primary); }

        .toast-title   { font-size: 13px; font-weight: 700; color: #1a2332; }
        .toast-message { font-size: 12.5px; color: #6b7280; margin-top: 2px; }

        .toast-close {
            margin-left: auto; background: none; border: none; color: #bbb;
            cursor: pointer; font-size: 16px; padding: 0; flex-shrink: 0;
        }

        /* ── Responsive ───────────────────────────────────────────── */
        @media (max-width: 768px) {
            .hms-sidebar { transform: translateX(-260px); }
            .hms-sidebar.open { transform: translateX(0); }
            .hms-main { margin-left: 0; }
            .hms-topbar { padding: 0 16px; }
            .hms-body { padding: 16px; }
            .main-card-header { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>

<!-- ─── Toast Container ────────────────────────────────────────────── -->
<div id="toastContainer"></div>

<!-- ─── Layout Shell ─────────────────────────────────────────────── -->
<div class="hms-shell">

    <!-- Sidebar -->
    <aside class="hms-sidebar" id="hmsSidebar">
        <div class="brand">
            <div class="brand-icon"><i class="fas fa-hospital-alt"></i></div>
            <div class="brand-name">HMS <span>Patient Portal</span></div>
        </div>

        <div class="sidebar-user">
            <div class="sidebar-avatar">
                <?php echo strtoupper(substr($current_user_name, 0, 1)); ?>
            </div>
            <div>
                <div class="sidebar-uname"><?php echo html_escape($current_user_name); ?></div>
                <div class="sidebar-role">Patient</div>
            </div>
        </div>

        <ul class="sidebar-nav">
            <li class="nav-label">Main Menu</li>
            <li>
                <a href="<?php echo site_url('patient'); ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="<?php echo site_url('patient/list'); ?>" class="active">
                    <i class="fas fa-procedures"></i> My Records
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <a href="<?php echo site_url('logout'); ?>">
                <i class="fas fa-sign-out-alt"></i> Sign Out
            </a>
        </div>
    </aside>

    <!-- Main -->
    <div class="hms-main">

        <!-- Topbar -->
        <header class="hms-topbar">
            <div>
                <div class="topbar-title">Patient Records</div>
                <div class="topbar-breadcrumb">HMS &rsaquo; Patient Portal &rsaquo; My Records</div>
            </div>
            <div class="topbar-actions">
                <button class="btn-hms-primary" id="btnOpenAdd">
                    <i class="fas fa-user-plus"></i> Register Patient
                </button>
            </div>
        </header>

        <!-- Body -->
        <div class="hms-body">

            <!-- Stats -->
            <div class="row g-3 mb-4" id="statsRow">
                <div class="col-sm-4">
                    <div class="stat-card">
                        <div class="stat-icon blue"><i class="fas fa-users"></i></div>
                        <div>
                            <div class="stat-label">Total Patients</div>
                            <div class="stat-value" id="statTotal">–</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="stat-card">
                        <div class="stat-icon green"><i class="fas fa-user-check"></i></div>
                        <div>
                            <div class="stat-label">Active</div>
                            <div class="stat-value" id="statActive">–</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="stat-card">
                        <div class="stat-icon orange"><i class="fas fa-user-times"></i></div>
                        <div>
                            <div class="stat-label">Inactive</div>
                            <div class="stat-value" id="statInactive">–</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Table Card -->
            <div class="main-card">
                <div class="main-card-header">
                    <div class="main-card-title"><i class="fas fa-list me-2 text-primary"></i>Patient List</div>
                </div>
                <div class="main-card-body">

                    <!-- Search & Filters -->
                    <div class="search-filter-bar">
                        <div class="search-wrap">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" id="patientSearch" placeholder="Search by ID, name, phone or email…">
                            <button class="search-clear" id="btnClearSearch" title="Clear search"><i class="fas fa-times"></i></button>
                        </div>

                        <div class="filter-btn-group" id="statusFilters">
                            <button class="filter-btn active" data-status="all">All</button>
                            <button class="filter-btn" data-status="1">Active</button>
                            <button class="filter-btn" data-status="0">Inactive</button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="hms-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Patient Code</th>
                                    <th>Patient</th>
                                    <th>Gender</th>
                                    <th>Blood</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="patientTableBody"></tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="hms-pagination" id="paginationWrap">
                        <div class="page-info" id="pageInfo"></div>
                        <div class="page-btns" id="pageBtns"></div>
                    </div>

                </div><!-- /card-body -->
            </div><!-- /main-card -->

        </div><!-- /hms-body -->
    </div><!-- /hms-main -->
</div><!-- /hms-shell -->

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- ADD PATIENT MODAL                                               -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalAdd" tabindex="-1" aria-labelledby="modalAddLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddLabel"><i class="fas fa-user-plus me-2"></i>Register New Patient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAdd" novalidate autocomplete="off">
                <div class="modal-body">
                    <div class="patient-code-box">
                        <i class="fas fa-barcode me-2"></i>
                        Patient code will be auto-generated on save.
                    </div>
                    <?php echo $this->load->view('patient/patients/_modal_form', array('mode' => 'add'), TRUE); ?>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn-hms-primary" id="btnAddSubmit">
                        <i class="fas fa-save me-1"></i>Register Patient
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- EDIT PATIENT MODAL                                              -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditLabel"><i class="fas fa-user-edit me-2"></i>Edit Patient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEdit" novalidate autocomplete="off">
                <input type="hidden" id="editPatientId" name="patient_id" value="">
                <div class="modal-body">
                    <div class="patient-code-box" id="editPatientCodeBox">
                        <i class="fas fa-barcode me-2"></i> Patient Code: <strong id="editPatientCode">–</strong>
                    </div>
                    <?php echo $this->load->view('patient/patients/_modal_form', array('mode' => 'edit'), TRUE); ?>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn-hms-primary" id="btnEditSubmit">
                        <i class="fas fa-save me-1"></i>Update Patient
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ─── Scripts ──────────────────────────────────────────────── -->
<script>
    window.HMS_CSRF_NAME = '<?php echo $this->security->get_csrf_token_name(); ?>';
    window.HMS_CSRF_HASH = '<?php echo $this->security->get_csrf_hash(); ?>';
    window.HMS_BASE_URL  = '<?php echo rtrim(base_url(), '/'); ?>';
</script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo base_url('assets/js/modules/patients.js'); ?>"></script>

</body>
</html>
