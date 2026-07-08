<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Doctor Profile Setup</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Doctors</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div id="doctorAlert"></div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-user-md mr-1"></i> Doctor Profiles
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" id="addDoctorButton">
                            <i class="fas fa-plus mr-1"></i> Setup Doctor Profile
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Doctor</th>
                                    <th>Department</th>
                                    <th>Specialization</th>
                                    <th>Qualification</th>
                                    <th>Fee</th>
                                    <th>Available Days</th>
                                    <th>Status</th>
                                    <th style="width: 180px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="doctorsTableBody">
                                <tr>
                                    <td colspan="8" class="text-center">Loading doctor profiles...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add Doctor Modal -->
<div class="modal fade" id="addDoctorModal" tabindex="-1" role="dialog" aria-labelledby="addDoctorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="addDoctorForm" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDoctorModalLabel">Setup Doctor Profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input
                        type="hidden"
                        name="<?php echo $this->security->get_csrf_token_name(); ?>"
                        value="<?php echo $this->security->get_csrf_hash(); ?>"
                    >

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add_user_id">Doctor User</label>
                                <select class="form-control" id="add_user_id" name="user_id">
                                    <option value="">Loading doctor users...</option>
                                </select>
                                <small class="text-danger validation-message" data-field="user_id"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add_department_id">Department</label>
                                <select class="form-control" id="add_department_id" name="department_id">
                                    <option value="">Loading departments...</option>
                                </select>
                                <small class="text-danger validation-message" data-field="department_id"></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add_specialization">Specialization</label>
                                <input type="text" class="form-control" id="add_specialization" name="specialization" maxlength="150" placeholder="e.g., Cardiologist">
                                <small class="text-danger validation-message" data-field="specialization"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add_qualification">Qualification</label>
                                <input type="text" class="form-control" id="add_qualification" name="qualification" maxlength="200" placeholder="e.g., MBBS, MD">
                                <small class="text-danger validation-message" data-field="qualification"></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-md-0">
                                <label for="add_consultation_fee">Consultation Fee (₹)</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="add_consultation_fee" name="consultation_fee" placeholder="500.00">
                                <small class="text-danger validation-message" data-field="consultation_fee"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label for="add_status">Status</label>
                                <select class="form-control" id="add_status" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <small class="text-danger validation-message" data-field="status"></small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-3 mb-0">
                        <label>Available Days</label>
                        <div class="row">
                            <?php
                                $days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
                                foreach ($days as $day):
                            ?>
                                <div class="col-md-3 col-sm-4 col-6">
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input add-day" id="add_day_<?php echo html_escape($day); ?>" name="available_days[]" value="<?php echo html_escape($day); ?>">
                                        <label class="custom-control-label" for="add_day_<?php echo html_escape($day); ?>">
                                            <?php echo html_escape($day); ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <small class="text-danger validation-message" data-field="available_days"></small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveDoctorButton">
                        <i class="fas fa-save mr-1"></i> Save Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Doctor Modal -->
<div class="modal fade" id="editDoctorModal" tabindex="-1" role="dialog" aria-labelledby="editDoctorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="editDoctorForm" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDoctorModalLabel">Edit Doctor Profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input
                        type="hidden"
                        name="<?php echo $this->security->get_csrf_token_name(); ?>"
                        value="<?php echo $this->security->get_csrf_hash(); ?>"
                    >
                    <!-- FIXED: Use consistent ID name -->
                    <input type="hidden" id="edit_doctor_id" name="edit_doctor_id">

                    <div class="alert alert-light border">
                        <i class="fas fa-user-md mr-1"></i>
                        Doctor User:
                        <strong id="edit_doctor_user_name">-</strong>
                    </div>

                    <div class="form-group">
                        <label for="edit_department_id">Department</label>
                        <select class="form-control" id="edit_department_id" name="department_id">
                            <option value="">Loading departments...</option>
                        </select>
                        <small class="text-danger validation-message" data-field="department_id"></small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_specialization">Specialization</label>
                                <input type="text" class="form-control" id="edit_specialization" name="specialization" maxlength="150">
                                <small class="text-danger validation-message" data-field="specialization"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_qualification">Qualification</label>
                                <input type="text" class="form-control" id="edit_qualification" name="qualification" maxlength="200">
                                <small class="text-danger validation-message" data-field="qualification"></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-md-0">
                                <label for="edit_consultation_fee">Consultation Fee (₹)</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="edit_consultation_fee" name="consultation_fee">
                                <small class="text-danger validation-message" data-field="consultation_fee"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label for="edit_status">Status</label>
                                <select class="form-control" id="edit_status" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <small class="text-danger validation-message" data-field="status"></small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-3 mb-0">
                        <label>Available Days</label>
                        <div class="row">
                            <?php foreach ($days as $day): ?>
                                <div class="col-md-3 col-sm-4 col-6">
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input edit-day" id="edit_day_<?php echo html_escape($day); ?>" name="available_days[]" value="<?php echo html_escape($day); ?>">
                                        <label class="custom-control-label" for="edit_day_<?php echo html_escape($day); ?>">
                                            <?php echo html_escape($day); ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <small class="text-danger validation-message" data-field="available_days"></small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="updateDoctorButton">
                        <i class="fas fa-edit mr-1"></i> Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include Doctor Module Script -->
<script src="<?php echo base_url('assets/js/modules/doctors.js'); ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    Doctors.init({
        csrfName: '<?php echo $this->security->get_csrf_token_name(); ?>',
        csrfHash: '<?php echo $this->security->get_csrf_hash(); ?>',
        listUrl: '<?php echo site_url('admin/doctors/get_doctors_ajax'); ?>',
        optionsUrl: '<?php echo site_url('admin/doctors/get_setup_options'); ?>',
        storeUrl: '<?php echo site_url('admin/doctors/store'); ?>',
        editBaseUrl: '<?php echo site_url('admin/doctors/edit'); ?>',
        updateBaseUrl: '<?php echo site_url('admin/doctors/update'); ?>',
        changeStatusBaseUrl: '<?php echo site_url('admin/doctors/change_status'); ?>'
    });
});
</script>