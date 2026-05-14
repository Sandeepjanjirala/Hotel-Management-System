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
                        <a href="<?php echo site_url('admin/appointments/create'); ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i> Book Appointment
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-lg-4 col-md-6 col-12 mb-2 mb-lg-0">
                            <input type="text" id="appointmentSearch" class="form-control" placeholder="Search code, patient, phone or doctor">
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 mb-2 mb-lg-0">
                            <select id="statusFilter" class="form-control">
                                <option value="">All Status</option>
                                <option value="scheduled">Scheduled</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 mb-2 mb-lg-0">
                            <input type="date" id="dateFilter" class="form-control">
                        </div>
                        <div class="col-lg-2 col-12">
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
                </div>
            </div>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var listUrl = '<?php echo site_url('admin/appointments/ajax-list'); ?>';
    var editBaseUrl = '<?php echo site_url('admin/appointments/edit'); ?>';
    var changeStatusBaseUrl = '<?php echo site_url('admin/appointments/change-status'); ?>';
    var searchTimer = null;

    function updateCsrfToken(hash) {
        if (hash) {
            csrfHash = hash;
        }
    }

    function escapeHtml(value) {
        return $('<div>').text(value === null || value === undefined ? '' : value).html();
    }

    function showAlert(type, message) {
        var alertClass = type === 'success' ? 'success' : 'danger';
        var html = ''
            + '<div class="alert alert-' + alertClass + ' alert-dismissible fade show" role="alert">'
            + escapeHtml(message)
            + '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'
            + '<span aria-hidden="true">&times;</span>'
            + '</button>'
            + '</div>';

        $('#appointmentAlert').html(html);
    }

    function statusBadge(status) {
        if (status === 'completed') {
            return '<span class="badge badge-success">Completed</span>';
        }

        if (status === 'cancelled') {
            return '<span class="badge badge-danger">Cancelled</span>';
        }

        return '<span class="badge badge-primary">Scheduled</span>';
    }

    function renderRows(rows) {
        var html = '';

        if (!rows || rows.length === 0) {
            $('#appointmentsTableBody').html('<tr><td colspan="7" class="text-center">No appointments found.</td></tr>');
            return;
        }

        $.each(rows, function (index, appointment) {
            var department = appointment.department_name ? ' - ' + escapeHtml(appointment.department_name) : '';

            html += '<tr>';
            html += '<td><strong>' + escapeHtml(appointment.appointment_code) + '</strong></td>';
            html += '<td>' + escapeHtml(appointment.patient_name) + '<br><small class="text-muted">' + escapeHtml(appointment.patient_code) + '</small></td>';
            html += '<td>' + escapeHtml(appointment.doctor_name) + '<br><small class="text-muted">' + department + '</small></td>';
            html += '<td>' + escapeHtml(appointment.appointment_date) + '</td>';
            html += '<td>' + escapeHtml(appointment.appointment_time) + '</td>';
            html += '<td>' + statusBadge(appointment.status) + '</td>';
            html += '<td>';
            html += '<a href="' + editBaseUrl + '/' + escapeHtml(appointment.id) + '" class="btn btn-info btn-sm mr-1">';
            html += '<i class="fas fa-edit mr-1"></i>Edit';
            html += '</a>';
            html += '<select class="form-control form-control-sm d-inline-block appointment-status-select" data-id="' + escapeHtml(appointment.id) + '" style="width: 125px;">';
            html += '<option value="scheduled" ' + (appointment.status === 'scheduled' ? 'selected' : '') + '>Scheduled</option>';
            html += '<option value="completed" ' + (appointment.status === 'completed' ? 'selected' : '') + '>Completed</option>';
            html += '<option value="cancelled" ' + (appointment.status === 'cancelled' ? 'selected' : '') + '>Cancelled</option>';
            html += '</select>';
            html += '</td>';
            html += '</tr>';
        });

        $('#appointmentsTableBody').html(html);
    }

    function loadAppointments() {
        $('#appointmentsTableBody').html('<tr><td colspan="7" class="text-center">Loading appointments...</td></tr>');

        $.ajax({
            url: listUrl,
            type: 'GET',
            dataType: 'json',
            data: {
                search: $('#appointmentSearch').val(),
                status: $('#statusFilter').val(),
                appointment_date: $('#dateFilter').val()
            },
            success: function (response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    renderRows(response.data);
                    return;
                }

                renderRows([]);
                showAlert('danger', response.message || 'Unable to load appointments.');
            },
            error: function () {
                renderRows([]);
                showAlert('danger', 'Unable to load appointments.');
            }
        });
    }

    $('#appointmentSearch').on('keyup', function () {
        window.clearTimeout(searchTimer);

        searchTimer = window.setTimeout(function () {
            loadAppointments();
        }, 300);
    });

    $('#statusFilter, #dateFilter').on('change', function () {
        loadAppointments();
    });

    $('#clearFiltersButton').on('click', function () {
        $('#appointmentSearch').val('');
        $('#statusFilter').val('');
        $('#dateFilter').val('');
        loadAppointments();
    });

    $(document).on('change', '.appointment-status-select', function () {
        var appointmentId = $(this).data('id');
        var newStatus = $(this).val();
        var payload = {};

        payload[csrfName] = csrfHash;
        payload.status = newStatus;

        $.ajax({
            url: changeStatusBaseUrl + '/' + appointmentId,
            type: 'POST',
            dataType: 'json',
            data: payload,
            success: function (response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    loadAppointments();
                    showAlert('success', response.message);
                    return;
                }

                loadAppointments();
                showAlert('danger', response.message || 'Unable to update appointment status.');
            },
            error: function () {
                loadAppointments();
                showAlert('danger', 'Unable to update appointment status.');
            }
        });
    });

    loadAppointments();
});
</script>
