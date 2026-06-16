<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Appointment Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin/appointments'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Appointments</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo html_escape($this->session->flashdata('success')); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo html_escape($this->session->flashdata('error')); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div id="appointmentAlert"></div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-calendar-check mr-1"></i> Appointment List
                    </h3>
                    <div class="card-tools">
                        <a href="<?php echo site_url('admin/appointments/create'); ?>" class="btn btn-primary btn-sm js-create-appointment" id="btnOpenBook">
                            <i class="fas fa-plus mr-1"></i> Book Appointment
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-lg-3 col-md-6 col-12 mb-2 mb-lg-0">
                            <input type="text" id="appointmentSearch" class="form-control" placeholder="Search code, patient, phone or doctor">
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 mb-2 mb-lg-0">
                            <select id="doctorFilter" class="form-control">
                                <option value="">All Doctors</option>
                                <?php if (!empty($doctors)): ?>
                                    <?php foreach ($doctors as $doctor): ?>
                                        <?php $dept = !empty($doctor['department_name']) ? ' - ' . $doctor['department_name'] : ''; ?>
                                        <option value="<?php echo (int) $doctor['id']; ?>">
                                            <?php echo html_escape($doctor['name'] . $dept); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6 mb-2 mb-lg-0">
                            <select id="statusFilter" class="form-control">
                                <option value="">All Status</option>
                                <option value="scheduled">Scheduled</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6 mb-2 mb-lg-0">
                            <input type="date" id="dateFilter" class="form-control">
                        </div>
                        <div class="col-lg-2 col-md-4 col-12">
                            <button type="button" id="clearFiltersButton" class="btn btn-default btn-block">
                                <i class="fas fa-times mr-1"></i> Clear
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Appointment Code</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th style="width: 230px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="appointmentsTableBody">
                                <tr>
                                    <td colspan="7" class="text-center">Loading appointments...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- AJAX Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">
                        <div id="paginationInfo" class="text-muted small mb-2 mb-md-0"></div>
                        <div id="paginationLinks"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Appointment Modal -->
<div class="modal fade" id="appointmentModal" tabindex="-1" role="dialog" aria-labelledby="appointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="appointmentModalLabel">Book Appointment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" id="appointmentModalBody">
                <!-- Loaded dynamically via AJAX -->
            </div>
        </div>
    </div>
</div>

<script>
    window.HMS_APPOINTMENT_CONFIG = {
        listUrl: '<?php echo site_url('admin/appointments/ajax-list'); ?>',
        createUrl: '<?php echo site_url('admin/appointments/create'); ?>',
        editUrl: '<?php echo site_url('admin/appointments/edit'); ?>',
        changeStatusUrl: '<?php echo site_url('admin/appointments/change-status'); ?>'
    };
</script>
<script src="<?php echo base_url('assets/js/modules/appointments.js'); ?>"></script>

