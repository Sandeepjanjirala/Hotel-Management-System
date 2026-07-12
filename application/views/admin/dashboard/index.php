<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <div class="content-header hms-page-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1>Admin Dashboard</h1>
                    <p class="mb-0">Live hospital administration overview.</p>
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
        <div class="container-fluid hms-dashboard">
            <div class="row">
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
                    <div class="hms-stat-card">
                        <div class="hms-stat-card__icon hms-tone-blue">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="hms-stat-card__body">
                            <span class="hms-stat-card__label">Total Departments</span>
                            <h3><?php echo (int) $statistics['total_departments']; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
                    <div class="hms-stat-card">
                        <div class="hms-stat-card__icon hms-tone-green">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <div class="hms-stat-card__body">
                            <span class="hms-stat-card__label">Total Doctors</span>
                            <h3><?php echo (int) $statistics['total_doctors']; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
                    <div class="hms-stat-card">
                        <div class="hms-stat-card__icon hms-tone-amber">
                            <i class="fas fa-user-nurse"></i>
                        </div>
                        <div class="hms-stat-card__body">
                            <span class="hms-stat-card__label">Total Receptionists</span>
                            <h3><?php echo (int) $statistics['total_receptionists']; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
                    <div class="hms-stat-card">
                        <div class="hms-stat-card__icon hms-tone-slate">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="hms-stat-card__body">
                            <span class="hms-stat-card__label">Active Users</span>
                            <h3><?php echo (int) $statistics['active_users']; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
                    <div class="hms-stat-card">
                        <div class="hms-stat-card__icon hms-tone-cyan">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="hms-stat-card__body">
                            <span class="hms-stat-card__label">Total Appointments</span>
                            <h3><?php echo (int) $statistics['total_appointments']; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
                    <div class="hms-stat-card">
                        <div class="hms-stat-card__icon hms-tone-red">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div class="hms-stat-card__body">
                            <span class="hms-stat-card__label">Today's Appointments</span>
                            <h3><?php echo (int) $statistics['today_appointments']; ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="hms-mini-stat">
                        <span class="hms-mini-stat__title">Active Departments</span>
                        <strong><?php echo (int) $status_summary['active_departments']; ?></strong>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="hms-mini-stat">
                        <span class="hms-mini-stat__title">Inactive Departments</span>
                        <strong><?php echo (int) $status_summary['inactive_departments']; ?></strong>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="hms-mini-stat">
                        <span class="hms-mini-stat__title">Scheduled Appointments</span>
                        <strong><?php echo (int) $status_summary['scheduled_appointments']; ?></strong>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6">
                    <div class="hms-mini-stat">
                        <span class="hms-mini-stat__title">Completed Appointments</span>
                        <strong><?php echo (int) $status_summary['completed_appointments']; ?></strong>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card hms-admin-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-users-cog mr-2"></i>Recent Users
                            </h3>
                            <div class="card-tools">
                                <a href="<?php echo site_url('admin/users'); ?>" class="btn btn-tool">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table hms-admin-table mb-0">
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

                                                $created_date = !empty($user['created_at']) ? date('d M Y', strtotime($user['created_at'])) : '-';
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
                </div>

                <div class="col-lg-4">
                    <div class="card hms-admin-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-hospital-alt mr-2"></i>Recent Departments
                            </h3>
                            <div class="card-tools">
                                <a href="<?php echo site_url('admin/departments'); ?>" class="btn btn-tool">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table hms-admin-table mb-0">
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
                                                <?php $created_date = !empty($department['created_at']) ? date('d M Y', strtotime($department['created_at'])) : '-'; ?>
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
        </div>
    </section>
</div>
