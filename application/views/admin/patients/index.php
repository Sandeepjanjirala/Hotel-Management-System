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

            <div id="hmsNotificationArea"></div>

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
                            <tbody id="patientsTableBody" data-table-url="<?php echo site_url('admin/patients/ajax-list'); ?>">
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
    var listUrl = '<?php echo site_url('admin/patients/ajax-list'); ?>';

    function loadPatients() {
        $('#patientsTableBody').html('<tr><td colspan="7" class="text-center">Loading patients...</td></tr>');

        HMSAjax.refreshTable(listUrl, {
                search: $('#patientSearch').val()
            }, '#patientsTableBody');
    }

    $('#patientSearch').on('keyup', HMSAjax.debounce(loadPatients, 300));

    $('#clearSearchButton').on('click', function () {
        $('#patientSearch').val('');
        loadPatients();
    });

    $(document).on('click', '.js-patient-status', function () {
        var $button = $(this);
        var message = $button.data('confirm') || 'Are you sure you want to change this patient status?';

        if (!window.confirm(message)) {
            return;
        }

        HMSAjax.request({
            url: $button.data('url'),
            type: 'POST',
            dataType: 'json',
            hmsSuccess: function (response) {
                if (response.status) {
                    if (response.row_html) {
                        $('#patient-row-' + response.data.id).replaceWith(response.row_html);
                    } else {
                        loadPatients();
                    }

                    HMSAjax.notify('success', response.message);
                    return;
                }

                HMSAjax.notify('error', response.message || 'Unable to change patient status.');
            }
        });
    });

    loadPatients();
});
</script>
