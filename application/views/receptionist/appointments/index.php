<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-actions clearfix">
    <a class="btn btn-success pull-right" href="<?php echo site_url('receptionist/appointments/create'); ?>">
        <i class="fa fa-plus"></i> Book Appointment
    </a>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Appointment Search</h3>
        <div class="box-tools hms-filter">
            <input type="text" id="appointment-search" class="form-control input-sm" placeholder="Search">
            <select id="appointment-status" class="form-control input-sm">
                <option value="">All Status</option>
                <option value="scheduled">Scheduled</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <input type="date" id="appointment-date" class="form-control input-sm">
        </div>
    </div>
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover" id="appointments-table">
            <thead><tr><th>Code</th><th>Date</th><th>Time</th><th>Patient</th><th>Doctor</th><th>Status</th><th></th></tr></thead>
            <tbody><tr><td colspan="7" class="text-center text-muted">Loading appointments...</td></tr></tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var endpoint = '<?php echo site_url('receptionist/appointments/ajax-list'); ?>';
    var cancelBase = '<?php echo site_url('receptionist/appointments/cancel/'); ?>';
    var $tbody = $('#appointments-table tbody');

    function loadAppointments() {
        $tbody.html('<tr><td colspan="7" class="text-center text-muted">Loading appointments...</td></tr>');
        $.get(endpoint, {
            search: $('#appointment-search').val(),
            status: $('#appointment-status').val(),
            appointment_date: $('#appointment-date').val()
        }, function (response) {
            if (!response.data || response.data.length === 0) {
                $tbody.html('<tr><td colspan="7" class="text-center text-muted">No appointments found.</td></tr>');
                return;
            }

            $tbody.html(response.data.map(function (row) {
                var cancelButton = row.status === 'cancelled' ? '' : '<button class="btn btn-xs btn-danger js-cancel" data-id="' + row.id + '"><i class="fa fa-ban"></i></button>';
                return '<tr>' +
                    '<td>' + row.appointment_code + '</td>' +
                    '<td>' + row.appointment_date + '</td>' +
                    '<td>' + row.appointment_time + '</td>' +
                    '<td>' + row.patient_name + '</td>' +
                    '<td>' + row.doctor_name + '</td>' +
                    '<td><span class="label label-info">' + row.status + '</span></td>' +
                    '<td class="hms-actions">' +
                    '<a class="btn btn-xs btn-primary" href="<?php echo site_url('receptionist/appointments/edit/'); ?>' + row.id + '"><i class="fa fa-pencil"></i></a>' +
                    cancelButton +
                    '</td>' +
                    '</tr>';
            }).join(''));
        }, 'json');
    }

    $('#appointment-search, #appointment-status, #appointment-date').on('change keyup', function () {
        clearTimeout(window.appointmentTimer);
        window.appointmentTimer = setTimeout(loadAppointments, 250);
    });

    $('#appointments-table').on('click', '.js-cancel', function () {
        if (!confirm('Cancel this appointment?')) {
            return;
        }

        var payload = {};
        payload[window.HMS_CSRF.name] = window.HMS_CSRF.hash;

        $.post(cancelBase + $(this).data('id'), payload, function () {
            loadAppointments();
        }, 'json');
    });

    loadAppointments();
});
</script>
