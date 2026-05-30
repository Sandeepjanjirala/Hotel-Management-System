<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Assigned Patients</h3>
        <div class="box-tools">
            <form class="form-inline" id="patient-filter">
                <input type="text" class="form-control input-sm" name="search_text" placeholder="Search patient">
                <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-search"></i></button>
            </form>
        </div>
    </div>
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover" id="patients-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Email</th>
                    <th>Latest Appointment</th>
                    <th>Visits</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr><td colspan="6" class="text-center text-muted">Loading patients...</td></tr>
            </tbody>
        </table>
    </div>
    <div class="box-footer clearfix">
        <button type="button" class="btn btn-default btn-sm pull-left" id="patients-prev"><i class="fa fa-chevron-left"></i></button>
        <button type="button" class="btn btn-default btn-sm pull-right" id="patients-next"><i class="fa fa-chevron-right"></i></button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var start = 0;
    var length = 25;
    var endpoint = '<?php echo site_url('doctor/patients/ajax-list'); ?>';
    var $table = $('#patients-table tbody');

    function filters() {
        var data = $('#patient-filter').serializeArray();
        var payload = { start: start, length: length, draw: Date.now() };
        data.forEach(function (item) { payload[item.name] = item.value; });
        return payload;
    }

    function loadRows() {
        $table.html('<tr><td colspan="6" class="text-center text-muted">Loading patients...</td></tr>');
        $.post(endpoint, filters(), function (response) {
            if (!response.data || response.data.length === 0) {
                $table.html('<tr><td colspan="6" class="text-center text-muted">No patients found.</td></tr>');
                return;
            }

            var rows = response.data.map(function (item) {
                return '<tr>' +
                    '<td>' + item.name + '</td>' +
                    '<td>' + item.mobile + '</td>' +
                    '<td>' + item.email + '</td>' +
                    '<td>' + item.latest_appointment_at + '</td>' +
                    '<td>' + item.appointment_count + '</td>' +
                    '<td class="doctor-table-actions">' + item.actions + '</td>' +
                    '</tr>';
            }).join('');
            $table.html(rows);
        }, 'json');
    }

    $('#patient-filter').on('submit', function (event) {
        event.preventDefault();
        start = 0;
        loadRows();
    });

    $('#patients-prev').on('click', function () {
        start = Math.max(0, start - length);
        loadRows();
    });

    $('#patients-next').on('click', function () {
        start += length;
        loadRows();
    });

    loadRows();
});
</script>