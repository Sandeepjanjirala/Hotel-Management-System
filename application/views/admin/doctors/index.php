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
                                <input type="text" class="form-control" id="add_specialization" name="specialization" maxlength="150">
                                <small class="text-danger validation-message" data-field="specialization"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add_qualification">Qualification</label>
                                <input type="text" class="form-control" id="add_qualification" name="qualification" maxlength="200">
                                <small class="text-danger validation-message" data-field="qualification"></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-md-0">
                                <label for="add_consultation_fee">Consultation Fee</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="add_consultation_fee" name="consultation_fee">
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
                    <button type="submit" class="btn btn-primary" id="saveDoctorButton">Save Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
                    <input type="hidden" id="edit_doctor_id" name="doctor_id">

                    <div class="alert alert-light border">
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
                                <label for="edit_consultation_fee">Consultation Fee</label>
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
                    <button type="submit" class="btn btn-primary" id="updateDoctorButton">Update Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

    var listUrl = '<?php echo site_url('admin/doctors/ajax-list'); ?>';
    var optionsUrl = '<?php echo site_url('admin/doctors/options'); ?>';
    var storeUrl = '<?php echo site_url('admin/doctors/store'); ?>';
    var editBaseUrl = '<?php echo site_url('admin/doctors/edit'); ?>';
    var updateBaseUrl = '<?php echo site_url('admin/doctors/update'); ?>';
    var changeStatusBaseUrl = '<?php echo site_url('admin/doctors/change-status'); ?>';

    var departments = [];

    function updateCsrfToken(hash) {
        if (!hash) {
            return;
        }

        csrfHash = hash;
        $('input[name="' + csrfName + '"]').val(csrfHash);
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

        $('#doctorAlert').html(html);
    }

    function clearValidationErrors(formSelector) {
        $(formSelector).find('.validation-message').text('');
        $(formSelector).find('.form-control').removeClass('is-invalid');
    }

    function applyValidationErrors(formSelector, errors) {
        if (!errors) {
            return;
        }

        $.each(errors, function (field, message) {
            if (!message) {
                return;
            }

            $(formSelector).find('[name="' + field + '"]').addClass('is-invalid');
            $(formSelector).find('.validation-message[data-field="' + field + '"]').text(message);
        });
    }

    function renderStatusBadge(status) {
        if (parseInt(status, 10) === 1) {
            return '<span class="badge badge-success">Active</span>';
        }

        return '<span class="badge badge-secondary">Inactive</span>';
    }

    function renderDays(days) {
        if (!days) {
            return '-';
        }

        var html = '';
        var parts = days.split(',');

        $.each(parts, function (index, day) {
            html += '<span class="badge badge-light border mr-1 mb-1">' + escapeHtml(day) + '</span>';
        });

        return html;
    }

    function renderDoctorRows(rows) {
        var html = '';

        if (!rows || rows.length === 0) {
            $('#doctorsTableBody').html('<tr><td colspan="8" class="text-center">No doctor profiles found.</td></tr>');
            return;
        }

        $.each(rows, function (index, doctor) {
            var toggleText = parseInt(doctor.status, 10) === 1 ? 'Deactivate' : 'Activate';
            var toggleClass = parseInt(doctor.status, 10) === 1 ? 'btn-warning' : 'btn-success';

            html += '<tr>';
            html += '<td><strong>' + escapeHtml(doctor.doctor_name) + '</strong><br><small class="text-muted">' + escapeHtml(doctor.doctor_email) + '</small></td>';
            html += '<td>' + escapeHtml(doctor.department_name) + '</td>';
            html += '<td>' + escapeHtml(doctor.specialization) + '</td>';
            html += '<td>' + escapeHtml(doctor.qualification) + '</td>';
            html += '<td>' + escapeHtml(parseFloat(doctor.consultation_fee).toFixed(2)) + '</td>';
            html += '<td>' + renderDays(doctor.available_days) + '</td>';
            html += '<td>' + renderStatusBadge(doctor.status) + '</td>';
            html += '<td>';
            html += '<button type="button" class="btn btn-info btn-sm mr-1 btn-edit-doctor" data-id="' + escapeHtml(doctor.id) + '">';
            html += '<i class="fas fa-edit mr-1"></i>Edit';
            html += '</button>';
            html += '<button type="button" class="btn ' + toggleClass + ' btn-sm btn-toggle-status" data-id="' + escapeHtml(doctor.id) + '">';
            html += '<i class="fas fa-sync-alt mr-1"></i>' + toggleText;
            html += '</button>';
            html += '</td>';
            html += '</tr>';
        });

        $('#doctorsTableBody').html(html);
    }

    function loadDoctors() {
        $('#doctorsTableBody').html('<tr><td colspan="8" class="text-center">Loading doctor profiles...</td></tr>');

        $.ajax({
            url: listUrl,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    renderDoctorRows(response.data);
                    return;
                }

                renderDoctorRows([]);
                showAlert('danger', response.message || 'Unable to load doctor profiles.');
            },
            error: function () {
                renderDoctorRows([]);
                showAlert('danger', 'Unable to load doctor profiles.');
            }
        });
    }

    function renderDepartments(selector, selectedId) {
        var html = '<option value="">Select Department</option>';

        $.each(departments, function (index, department) {
            var selected = parseInt(selectedId, 10) === parseInt(department.id, 10) ? ' selected' : '';

            html += '<option value="' + escapeHtml(department.id) + '"' + selected + '>';
            html += escapeHtml(department.department_name);
            html += '</option>';
        });

        $(selector).html(html);
    }

    function renderDoctorUsers(users) {
        var html = '<option value="">Select Doctor User</option>';

        if (!users || users.length === 0) {
            html += '<option value="" disabled>No available doctor-role users found</option>';
        } else {
            $.each(users, function (index, user) {
                html += '<option value="' + escapeHtml(user.id) + '">';
                html += escapeHtml(user.name + ' - ' + user.email);
                html += '</option>';
            });
        }

        $('#add_user_id').html(html);
    }

    function loadSetupOptions(callback) {
        $.ajax({
            url: optionsUrl,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                updateCsrfToken(response.csrfHash);

                if (!response.status) {
                    showAlert('danger', response.message || 'Unable to load setup options.');
                    return;
                }

                departments = response.departments || [];
                renderDoctorUsers(response.doctor_users || []);
                renderDepartments('#add_department_id', '');
                renderDepartments('#edit_department_id', '');

                if (typeof callback === 'function') {
                    callback();
                }
            },
            error: function () {
                showAlert('danger', 'Unable to load setup options.');
            }
        });
    }

    function resetAddForm() {
        $('#addDoctorForm')[0].reset();
        $('.add-day').prop('checked', false);
        clearValidationErrors('#addDoctorForm');
    }

    function resetEditForm() {
        $('#editDoctorForm')[0].reset();
        $('#edit_doctor_id').val('');
        $('#edit_doctor_user_name').text('-');
        $('.edit-day').prop('checked', false);
        clearValidationErrors('#editDoctorForm');
    }

    function applyAvailableDays(selector, availableDays) {
        var days = availableDays ? availableDays.split(',') : [];

        $(selector).prop('checked', false);

        $.each(days, function (index, day) {
            $(selector + '[value="' + day + '"]').prop('checked', true);
        });
    }

    $('#addDoctorButton').on('click', function () {
        resetAddForm();

        loadSetupOptions(function () {
            $('#addDoctorModal').modal('show');
        });
    });

    $('#addDoctorForm').on('submit', function (event) {
        event.preventDefault();

        clearValidationErrors('#addDoctorForm');

        var $button = $('#saveDoctorButton');
        var originalText = $button.text();

        $button.prop('disabled', true).text('Saving...');

        $.ajax({
            url: storeUrl,
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
            success: function (response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    $('#addDoctorModal').modal('hide');
                    resetAddForm();
                    loadSetupOptions();
                    loadDoctors();
                    showAlert('success', response.message);
                    return;
                }

                applyValidationErrors('#addDoctorForm', response.errors);
                showAlert('danger', response.message || 'Unable to create doctor profile.');
            },
            error: function () {
                showAlert('danger', 'Unable to create doctor profile.');
            },
            complete: function () {
                $button.prop('disabled', false).text(originalText);
            }
        });
    });

    $(document).on('click', '.btn-edit-doctor', function () {
        var doctorId = $(this).data('id');

        resetEditForm();

        $.ajax({
            url: editBaseUrl + '/' + doctorId,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                updateCsrfToken(response.csrfHash);

                if (!response.status) {
                    showAlert('danger', response.message || 'Doctor profile not found.');
                    return;
                }

                departments = response.departments || [];
                renderDepartments('#edit_department_id', response.data.department_id);

                $('#edit_doctor_id').val(response.data.id);
                $('#edit_doctor_user_name').text(response.data.doctor_name + ' - ' + response.data.doctor_email);
                $('#edit_specialization').val(response.data.specialization);
                $('#edit_qualification').val(response.data.qualification);
                $('#edit_consultation_fee').val(response.data.consultation_fee);
                $('#edit_status').val(response.data.status);

                applyAvailableDays('.edit-day', response.data.available_days);

                $('#editDoctorModal').modal('show');
            },
            error: function () {
                showAlert('danger', 'Unable to fetch doctor profile details.');
            }
        });
    });

    $('#editDoctorForm').on('submit', function (event) {
        event.preventDefault();

        clearValidationErrors('#editDoctorForm');

        var doctorId = $('#edit_doctor_id').val();
        var $button = $('#updateDoctorButton');
        var originalText = $button.text();

        $button.prop('disabled', true).text('Updating...');

        $.ajax({
            url: updateBaseUrl + '/' + doctorId,
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
            success: function (response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    $('#editDoctorModal').modal('hide');
                    resetEditForm();
                    loadDoctors();
                    showAlert('success', response.message);
                    return;
                }

                applyValidationErrors('#editDoctorForm', response.errors);
                showAlert('danger', response.message || 'Unable to update doctor profile.');
            },
            error: function () {
                showAlert('danger', 'Unable to update doctor profile.');
            },
            complete: function () {
                $button.prop('disabled', false).text(originalText);
            }
        });
    });

    $(document).on('click', '.btn-toggle-status', function () {
        var doctorId = $(this).data('id');

        if (!window.confirm('Are you sure you want to change this doctor profile status?')) {
            return;
        }

        var payload = {};
        payload[csrfName] = csrfHash;

        $.ajax({
            url: changeStatusBaseUrl + '/' + doctorId,
            type: 'POST',
            dataType: 'json',
            data: payload,
            success: function (response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    loadDoctors();
                    showAlert('success', response.message);
                    return;
                }

                showAlert('danger', response.message || 'Unable to change doctor profile status.');
            },
            error: function () {
                showAlert('danger', 'Unable to change doctor profile status.');
            }
        });
    });

    $('#addDoctorModal').on('hidden.bs.modal', function () {
        resetAddForm();
    });

    $('#editDoctorModal').on('hidden.bs.modal', function () {
        resetEditForm();
    });

    loadSetupOptions();
    loadDoctors();
});
</script>
