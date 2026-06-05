<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-actions clearfix">
    <a class="btn btn-success pull-right" href="<?php echo site_url('receptionist/patients/create'); ?>">
        <i class="fa fa-plus"></i> Register Patient
    </a>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Patient Search</h3>
        <div class="box-tools hms-filter">
            <input type="text" id="patient-search" class="form-control input-sm" placeholder="Name, code, phone, email">
        </div>
    </div>
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover" id="patients-table">
            <thead><tr><th>Code</th><th>Name</th><th>Phone</th><th>Email</th><th>Status</th><th></th></tr></thead>
            <tbody><tr><td colspan="6" class="text-center text-muted">Loading patients...</td></tr></tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var endpoint = '<?php echo site_url('receptionist/patients/ajax-list'); ?>';
    var $tbody = $('#patients-table tbody');

    function loadPatients() {
        $tbody.html('<tr><td colspan="6" class="text-center text-muted">Loading patients...</td></tr>');
        $.get(endpoint, { search: $('#patient-search').val() }, function (response) {
            if (!response.data || response.data.length === 0) {
                $tbody.html('<tr><td colspan="6" class="text-center text-muted">No patients found.</td></tr>');
                return;
            }

            $tbody.html(response.data.map(function (patient) {
                return '<tr>' +
                    '<td>' + patient.patient_code + '</td>' +
                    '<td>' + patient.full_name + '</td>' +
                    '<td>' + patient.phone + '</td>' +
                    '<td>' + (patient.email || '') + '</td>' +
                    '<td><span class="label label-' + (parseInt(patient.status, 10) === 1 ? 'success' : 'default') + '">' + (parseInt(patient.status, 10) === 1 ? 'Active' : 'Inactive') + '</span></td>' +
                    '<td class="hms-actions">' +
                    '<a class="btn btn-xs btn-default" href="<?php echo site_url('receptionist/patients/view/'); ?>' + patient.id + '"><i class="fa fa-eye"></i></a>' +
                    '<a class="btn btn-xs btn-primary" href="<?php echo site_url('receptionist/patients/edit/'); ?>' + patient.id + '"><i class="fa fa-pencil"></i></a>' +
                    '</td>' +
                    '</tr>';
            }).join(''));
        }, 'json');
    }

    $('#patient-search').on('keyup', function () {
        clearTimeout(window.patientSearchTimer);
        window.patientSearchTimer = setTimeout(loadPatients, 250);
    });

    loadPatients();
});
</script>
