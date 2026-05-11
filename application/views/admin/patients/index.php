<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Patient Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin/patients'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Patients</li>
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

            <div id="patientAlert"></div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-procedures mr-1"></i> Patient List
                    </h3>
                    <div class="card-tools">
                        <a href="<?php echo site_url('admin/patients/create'); ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i> Add Patient
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-5 col-sm-12">
                            <div class="input-group">
                                <input type="text" id="patientSearch" class="form-control" placeholder="Search by code, name or phone">
                                <div class="input-group-append">
                                    <button class="btn btn-default" type="button" id="clearSearchButton">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 150px;">Patient Code</th>
                                    <th>Name</th>
                                    <th style="width: 110px;">Gender</th>
                                    <th style="width: 120px;">Blood Group</th>
                                    <th style="width: 140px;">Phone</th>
                                    <th style="width: 110px;">Status</th>
                                    <th style="width: 190px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="patientsTableBody">
                                <tr>
                                    <td colspan="7" class="text-center">Loading patients...</td>
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
    var listUrl = '<?php echo site_url('admin/patients/ajax-list'); ?>';
    var editBaseUrl = '<?php echo site_url('admin/patients/edit'); ?>';
    var changeStatusBaseUrl = '<?php echo site_url('admin/patients/change-status'); ?>';
    var searchTimer = null;

    function updateCsrfToken(hash) {
        if (!hash) {
            return;
        }

        csrfHash = hash;
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

        $('#patientAlert').html(html);
    }

    function genderLabel(gender) {
        if (!gender) {
            return '-';
        }

        return gender.charAt(0).toUpperCase() + gender.slice(1);
    }

    function renderStatusBadge(status) {
        if (parseInt(status, 10) === 1) {
            return '<span class="badge badge-success">Active</span>';
        }

        return '<span class="badge badge-secondary">Inactive</span>';
    }

    function renderRows(rows) {
        var html = '';

        if (!rows || rows.length === 0) {
            $('#patientsTableBody').html('<tr><td colspan="7" class="text-center">No patients found.</td></tr>');
            return;
        }

        $.each(rows, function (index, patient) {
            var toggleText = parseInt(patient.status, 10) === 1 ? 'Deactivate' : 'Activate';
            var toggleClass = parseInt(patient.status, 10) === 1 ? 'btn-warning' : 'btn-success';

            html += '<tr>';
            html += '<td><strong>' + escapeHtml(patient.patient_code) + '</strong></td>';
            html += '<td>' + escapeHtml(patient.full_name) + '</td>';
            html += '<td>' + escapeHtml(genderLabel(patient.gender)) + '</td>';
            html += '<td>' + escapeHtml(patient.blood_group || 'Unknown') + '</td>';
            html += '<td>' + escapeHtml(patient.phone) + '</td>';
            html += '<td>' + renderStatusBadge(patient.status) + '</td>';
            html += '<td>';
            html += '<a href="' + editBaseUrl + '/' + escapeHtml(patient.id) + '" class="btn btn-info btn-sm mr-1">';
            html += '<i class="fas fa-edit mr-1"></i>Edit';
            html += '</a>';
            html += '<button type="button" class="btn ' + toggleClass + ' btn-sm btn-toggle-status" data-id="' + escapeHtml(patient.id) + '">';
            html += '<i class="fas fa-sync-alt mr-1"></i>' + toggleText;
            html += '</button>';
            html += '</td>';
            html += '</tr>';
        });

        $('#patientsTableBody').html(html);
    }

    function loadPatients() {
        $('#patientsTableBody').html('<tr><td colspan="7" class="text-center">Loading patients...</td></tr>');

        $.ajax({
            url: listUrl,
            type: 'GET',
            dataType: 'json',
            data: {
                search: $('#patientSearch').val()
            },
            success: function (response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    renderRows(response.data);
                    return;
                }

                renderRows([]);
                showAlert('danger', response.message || 'Unable to load patients.');
            },
            error: function () {
                renderRows([]);
                showAlert('danger', 'Unable to load patients.');
            }
        });
    }

    $('#patientSearch').on('keyup', function () {
        window.clearTimeout(searchTimer);

        searchTimer = window.setTimeout(function () {
            loadPatients();
        }, 300);
    });

    $('#clearSearchButton').on('click', function () {
        $('#patientSearch').val('');
        loadPatients();
    });

    $(document).on('click', '.btn-toggle-status', function () {
        var patientId = $(this).data('id');

        if (!window.confirm('Are you sure you want to change this patient status?')) {
            return;
        }

        var payload = {};
        payload[csrfName] = csrfHash;

        $.ajax({
            url: changeStatusBaseUrl + '/' + patientId,
            type: 'POST',
            dataType: 'json',
            data: payload,
            success: function (response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    loadPatients();
                    showAlert('success', response.message);
                    return;
                }

                showAlert('danger', response.message || 'Unable to change patient status.');
            },
            error: function () {
                showAlert('danger', 'Unable to change patient status.');
            }
        });
    });

    loadPatients();
});
</script>
