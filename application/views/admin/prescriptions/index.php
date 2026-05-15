<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Prescriptions</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin/prescriptions'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Prescriptions</li>
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

            <div id="prescriptionAlert"></div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-prescription mr-1"></i> Prescription List
                    </h3>
                    <div class="card-tools">
                        <a href="<?php echo site_url('admin/prescriptions/create'); ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i> Create Prescription
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-lg-5 col-md-6 col-12 mb-2 mb-lg-0">
                            <input type="text" id="prescriptionSearch" class="form-control" placeholder="Search appointment, patient, phone, doctor or diagnosis">
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 mb-2 mb-lg-0">
                            <input type="date" id="prescriptionDateFilter" class="form-control">
                        </div>
                        <div class="col-lg-2 col-md-3 col-6">
                            <button type="button" id="clearFiltersButton" class="btn btn-default btn-block">
                                <i class="fas fa-times mr-1"></i> Clear
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Prescription Date</th>
                                    <th>Appointment</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Diagnosis</th>
                                    <th style="width: 130px;">Medicines</th>
                                </tr>
                            </thead>
                            <tbody id="prescriptionsTableBody">
                                <tr>
                                    <td colspan="6" class="text-center">Loading prescriptions...</td>
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
    var listUrl = '<?php echo site_url('admin/prescriptions/ajax-list'); ?>';
    var searchTimer = null;

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

        $('#prescriptionAlert').html(html);
    }

    function renderRows(rows) {
        var html = '';

        if (!rows || rows.length === 0) {
            $('#prescriptionsTableBody').html('<tr><td colspan="6" class="text-center">No prescriptions found.</td></tr>');
            return;
        }

        $.each(rows, function (index, prescription) {
            html += '<tr>';
            html += '<td>' + escapeHtml(prescription.prescription_date) + '</td>';
            html += '<td><strong>' + escapeHtml(prescription.appointment_code) + '</strong><br><small class="text-muted">' + escapeHtml(prescription.appointment_date + ' ' + prescription.appointment_time) + '</small></td>';
            html += '<td>' + escapeHtml(prescription.patient_name) + '<br><small class="text-muted">' + escapeHtml(prescription.patient_code) + '</small></td>';
            html += '<td>' + escapeHtml(prescription.doctor_name) + '</td>';
            html += '<td>' + escapeHtml(prescription.diagnosis) + '</td>';
            html += '<td><span class="badge badge-info">' + escapeHtml(prescription.medicine_count) + '</span></td>';
            html += '</tr>';
        });

        $('#prescriptionsTableBody').html(html);
    }

    function loadPrescriptions() {
        $('#prescriptionsTableBody').html('<tr><td colspan="6" class="text-center">Loading prescriptions...</td></tr>');

        $.ajax({
            url: listUrl,
            type: 'GET',
            dataType: 'json',
            data: {
                search: $('#prescriptionSearch').val(),
                prescription_date: $('#prescriptionDateFilter').val()
            },
            success: function (response) {
                if (response.status) {
                    renderRows(response.data);
                    return;
                }

                renderRows([]);
                showAlert('danger', response.message || 'Unable to load prescriptions.');
            },
            error: function () {
                renderRows([]);
                showAlert('danger', 'Unable to load prescriptions.');
            }
        });
    }

    $('#prescriptionSearch').on('keyup', function () {
        window.clearTimeout(searchTimer);

        searchTimer = window.setTimeout(function () {
            loadPrescriptions();
        }, 300);
    });

    $('#prescriptionDateFilter').on('change', function () {
        loadPrescriptions();
    });

    $('#clearFiltersButton').on('click', function () {
        $('#prescriptionSearch').val('');
        $('#prescriptionDateFilter').val('');
        loadPrescriptions();
    });

    loadPrescriptions();
});
</script>
