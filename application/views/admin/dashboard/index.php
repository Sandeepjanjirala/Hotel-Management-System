<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Admin Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-2">Welcome, <?php echo html_escape($current_user_name); ?></h3>
                            <p class="text-muted mb-0">
                                Live administrative overview for departments, staff accounts, and appointments.
                            </p>
                        </div>
                        <div class="col-md-4 text-md-right mt-3 mt-md-0">
                            <span class="badge badge-primary p-2">
                                <i class="fas fa-database mr-1"></i> Live Data
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo (int) $statistics['total_departments']; ?></h3>
                            <p>Total Departments</p>
                        </div>
                        <div class="icon"><i class="fas fa-building"></i></div>
                        <a href="<?php echo site_url('admin/departments'); ?>" class="small-box-footer">
                            Manage Departments <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?php echo (int) $statistics['total_doctors']; ?></h3>
                            <p>Total Doctors</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-md"></i></div>
                        <a href="<?php echo site_url('admin/users'); ?>" class="small-box-footer">
                            Manage Users <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?php echo (int) $statistics['total_receptionists']; ?></h3>
                            <p>Total Receptionists</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-nurse"></i></div>
                        <a href="<?php echo site_url('admin/users'); ?>" class="small-box-footer">
                            Manage Users <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3><?php echo (int) $statistics['active_users']; ?></h3>
                            <p>Active Users</p>
                        </div>
                        <div class="icon"><i class="fas fa-users"></i></div>
                        <a href="<?php echo site_url('admin/users'); ?>" class="small-box-footer">
                            View Accounts <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 col-md-6 col-12">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3><?php echo (int) $statistics['total_appointments']; ?></h3>
                            <p>Total Appointments</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                        <a href="<?php echo site_url('admin/appointments'); ?>" class="small-box-footer">
                            View Appointments <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6 col-12">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?php echo (int) $statistics['today_appointments']; ?></h3>
                            <p>Today's Appointments</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-day"></i></div>
                        <a href="<?php echo site_url('admin/appointments'); ?>" class="small-box-footer">
                            Open Schedule <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Active Departments</span>
                            <span class="info-box-number"><?php echo (int) $status_summary['active_departments']; ?></span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-secondary"><i class="fas fa-pause-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Inactive Departments</span>
                            <span class="info-box-number"><?php echo (int) $status_summary['inactive_departments']; ?></span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Scheduled Appointments</span>
                            <span class="info-box-number"><?php echo (int) $status_summary['scheduled_appointments']; ?></span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-calendar-check"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Completed Appointments</span>
                            <span class="info-box-number"><?php echo (int) $status_summary['completed_appointments']; ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-users-cog mr-1"></i> Recent Users
                            </h3>
                            <div class="card-tools">
                                <a href="<?php echo site_url('admin/users'); ?>" class="btn btn-tool">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>

                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Role</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                        <th>Created Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recent_users)): ?>
                                        <?php foreach ($recent_users as $user): ?>
                                            <?php
                                                $role = strtolower($user['role']);
                                                $role_badge = 'badge-info';

                                                if ($role === 'admin') {
                                                    $role_badge = 'badge-primary';
                                                } elseif ($role === 'doctor') {
                                                    $role_badge = 'badge-success';
                                                } elseif ($role === 'receptionist') {
                                                    $role_badge = 'badge-warning';
                                                }

                                                $created_date = !empty($user['created_at'])
                                                    ? date('d M Y', strtotime($user['created_at']))
                                                    : '-';
                                            ?>
                                            <tr>
                                                <td><strong><?php echo html_escape($user['name']); ?></strong></td>
                                                <td><span class="badge <?php echo $role_badge; ?>"><?php echo html_escape(ucfirst($role)); ?></span></td>
                                                <td><?php echo !empty($user['department_name']) ? html_escape($user['department_name']) : '-'; ?></td>
                                                <td>
                                                    <?php if ((int) $user['status'] === 1): ?>
                                                        <span class="badge badge-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo html_escape($created_date); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">No users found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-building mr-1"></i> Recent Departments
                            </h3>
                            <div class="card-tools">
                                <a href="<?php echo site_url('admin/departments'); ?>" class="btn btn-tool">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>

                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Department Name</th>
                                        <th>Status</th>
                                        <th>Created Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recent_departments)): ?>
                                        <?php foreach ($recent_departments as $department): ?>
                                            <?php
                                                $created_date = !empty($department['created_at'])
                                                    ? date('d M Y', strtotime($department['created_at']))
                                                    : '-';
                                            ?>
                                            <tr>
                                                <td><strong><?php echo html_escape($department['department_name']); ?></strong></td>
                                                <td>
                                                    <?php if ((int) $department['status'] === 1): ?>
                                                        <span class="badge badge-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo html_escape($created_date); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">No departments found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
