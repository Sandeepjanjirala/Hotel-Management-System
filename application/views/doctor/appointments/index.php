<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Appointments</h3>
        <div class="box-tools">
            <form class="form-inline" id="appointment-filter">
                <input type="text" class="form-control input-sm" name="search_text" placeholder="Search patient">
                <select class="form-control input-sm" name="status">
                    <option value="">All Status</option>
                    <option value="scheduled">Scheduled</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <input type="date" class="form-control input-sm" name="date_from">
                <input type="date" class="form-control input-sm" name="date_to">
                <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
            </form>
        </div>
    </div>
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover" id="appointments-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Patient</th>
                    <th>Mobile</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr><td colspan="7" class="text-center text-muted">Loading appointments...</td></tr>
            </tbody>
        </table>
    </div>
    <div class="box-footer clearfix">
        <button type="button" class="btn btn-default btn-sm pull-left" id="appointments-prev"><i class="fa fa-chevron-left"></i></button>
        <button type="button" class="btn btn-default btn-sm pull-right" id="appointments-next"><i class="fa fa-chevron-right"></i></button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var start = 0;
    var length = 25;
    var endpoint = '<?php echo site_url('doctor/appointments/ajax-list'); ?>';
    var $table = $('#appointments-table tbody');

    function filters() {
        var data = $('#appointment-filter').serializeArray();
        var payload = { start: start, length: length, draw: Date.now() };
        data.forEach(function (item) { payload[item.name] = item.value; });
        return payload;
    }

    function loadRows() {
        $table.html('<tr><td colspan="7" class="text-center text-muted">Loading appointments...</td></tr>');
        $.post(endpoint, filters(), function (response) {
            if (!response.data || response.data.length === 0) {
                $table.html('<tr><td colspan="7" class="text-center text-muted">No appointments found.</td></tr>');
                return;
            }

            var rows = response.data.map(function (item) {
                return '<tr>' +
                    '<td>' + item.appointment_date + '</td>' +
                    '<td>' + item.appointment_time + '</td>' +
                    '<td>' + item.patient_name + '</td>' +
                    '<td>' + item.patient_mobile + '</td>' +
                    '<td>' + item.reason + '</td>' +
                    '<td><span class="label label-info">' + item.status + '</span></td>' +
                    '<td class="doctor-table-actions">' + item.actions + '</td>' +
                    '</tr>';
            }).join('');
            $table.html(rows);
        }, 'json');
    }

    $('#appointment-filter').on('submit', function (event) {
        event.preventDefault();
        start = 0;
        loadRows();
    });

    $('#appointments-prev').on('click', function () {
        start = Math.max(0, start - length);
        loadRows();
    });

    $('#appointments-next').on('click', function () {
        start += length;
        loadRows();
    });

    loadRows();
});
</script>