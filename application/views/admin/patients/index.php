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
                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Patients</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div id="hmsNotificationArea"></div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-procedures mr-1"></i> Patient List
                    </h3>
                    <div class="card-tools">
                        <a href="<?php echo site_url('admin/patients/create'); ?>" class="btn btn-primary btn-sm" id="addPatientButton">
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

<!-- Include Patient Module Script -->
<script src="<?php echo base_url('assets/js/modules/patients.js'); ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    Patients.init({
        csrfName: '<?php echo $this->security->get_csrf_token_name(); ?>',
        csrfHash: '<?php echo $this->security->get_csrf_hash(); ?>',
        listUrl: '<?php echo site_url('admin/patients/get_patients_ajax'); ?>',
        storeUrl: '<?php echo site_url('admin/patients/store'); ?>',
        editBaseUrl: '<?php echo site_url('admin/patients/edit'); ?>',
        updateBaseUrl: '<?php echo site_url('admin/patients/update'); ?>',
        changeStatusBaseUrl: '<?php echo site_url('admin/patients/change_status'); ?>'
    });
});
</script>