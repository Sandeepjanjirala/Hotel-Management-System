/**
 * Doctor Management Module
 * Handles all doctor profile CRUD operations via AJAX
 */

var Doctors = (function() {
    'use strict';

    var csrfName = '';
    var csrfHash = '';
    var listUrl = '';
    var storeUrl = '';
    var editBaseUrl = '';
    var updateBaseUrl = '';
    var changeStatusBaseUrl = '';
    var setupOptionsUrl = '';
    var alertTimeout = null;

    function init(config) {
        csrfName = config.csrfName || '';
        csrfHash = config.csrfHash || '';
        listUrl = config.listUrl || '';
        storeUrl = config.storeUrl || '';
        editBaseUrl = config.editBaseUrl || '';
        updateBaseUrl = config.updateBaseUrl || '';
        changeStatusBaseUrl = config.changeStatusBaseUrl || '';
        setupOptionsUrl = config.setupOptionsUrl || '';

        bindEvents();
        loadDoctors();
        loadSetupOptions();
    }

    function bindEvents() {
        $('#addDoctorButton').on('click', function() {
            resetAddForm();
            clearAlert();
            loadSetupOptions();
            $('#addDoctorModal').modal('show');
        });

        $('#addDoctorForm').on('submit', function(event) {
            event.preventDefault();
            saveDoctor();
        });

        $(document).on('click', '.btn-edit-doctor', function() {
            var doctorId = $(this).data('id');
            editDoctor(doctorId);
        });

        $('#editDoctorForm').on('submit', function(event) {
            event.preventDefault();
            updateDoctor();
        });

        $(document).on('click', '.btn-toggle-status', function() {
            var doctorId = $(this).data('id');
            toggleDoctorStatus(doctorId);
        });

        $('#addDoctorModal').on('hidden.bs.modal', function() {
            resetAddForm();
        });

        $('#editDoctorModal').on('hidden.bs.modal', function() {
            resetEditForm();
        });

        $(document).on('click', '.alert .close', function() {
            clearAlert();
        });
    }

    function updateCsrfToken(hash) {
        if (!hash) return;
        csrfHash = hash;
        $('input[name="' + csrfName + '"]').val(csrfHash);
    }

    function escapeHtml(value) {
        return $('<div>').text(value === null || value === undefined ? '' : value).html();
    }

    function showAlert(type, message) {
        if (alertTimeout) {
            clearTimeout(alertTimeout);
            alertTimeout = null;
        }

        var alertClass = type === 'success' ? 'success' : 'danger';
        var icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
        var html = ''
            + '<div class="alert alert-' + alertClass + ' alert-dismissible fade show" role="alert" style="position: relative; z-index: 1050;">'
            + '<i class="fas fa-' + icon + ' mr-2"></i>'
            + escapeHtml(message)
            + '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'
            + '<span aria-hidden="true">&times;</span>'
            + '</button>'
            + '</div>';

        $('#doctorAlert').html(html);

        alertTimeout = setTimeout(function() {
            clearAlert();
        }, 5000);
    }

    function clearAlert() {
        if (alertTimeout) {
            clearTimeout(alertTimeout);
            alertTimeout = null;
        }
        $('#doctorAlert').html('');
    }

    function clearValidationErrors(formSelector) {
        $(formSelector).find('.validation-message').text('');
        $(formSelector).find('.form-control').removeClass('is-invalid');
    }

    function applyValidationErrors(formSelector, errors) {
        if (!errors) return;

        $.each(errors, function(field, message) {
            if (!message) return;

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

    function renderRows(rows) {
        var html = '';

        if (!rows || rows.length === 0) {
            html = '<tr><td colspan="8" class="text-center">No doctor profiles found.</td></tr>';
            $('#doctorsTableBody').html(html);
            return;
        }

        $.each(rows, function(index, doctor) {
            var toggleText = parseInt(doctor.status, 10) === 1 ? 'Deactivate' : 'Activate';
            var toggleClass = parseInt(doctor.status, 10) === 1 ? 'btn-warning' : 'btn-success';
            var departmentName = doctor.department_name ? escapeHtml(doctor.department_name) : '-';
            var fee = parseFloat(doctor.consultation_fee || 0).toFixed(2);

            html += '<tr>';
            html += '<td>' + escapeHtml(doctor.id) + '</td>';
            html += '<td>' + escapeHtml(doctor.doctor_name || 'N/A') + '</td>';
            html += '<td>' + escapeHtml(doctor.doctor_email || 'N/A') + '</td>';
            html += '<td>' + departmentName + '</td>';
            html += '<td>' + escapeHtml(doctor.specialization || '-') + '</td>';
            html += '<td>₹' + fee + '</td>';
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
        $('#doctorsTableBody').html('<tr><td colspan="8" class="text-center">Loading doctors...</td></tr>');

        $.ajax({
            url: listUrl,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    renderRows(response.data);
                    return;
                }

                renderRows([]);
                showAlert('danger', response.message || 'Unable to load doctors.');
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                renderRows([]);
                showAlert('danger', 'Unable to load doctors. Please check console for details.');
            }
        });
    }

    function loadSetupOptions() {
        $.ajax({
            url: setupOptionsUrl,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                updateCsrfToken(response.csrfHash);

                if (!response.status) {
                    showAlert('danger', 'Unable to load setup options.');
                    return;
                }

                // Populate doctor users dropdown
                var $userSelect = $('#add_user_id');
                $userSelect.html('<option value="">Select Doctor User</option>');
                if (response.doctor_users && response.doctor_users.length > 0) {
                    $.each(response.doctor_users, function(index, user) {
                        $userSelect.append('<option value="' + escapeHtml(user.id) + '">' + 
                            escapeHtml(user.name) + ' (' + escapeHtml(user.email) + ')' + 
                            '</option>');
                    });
                } else {
                    $userSelect.append('<option value="">No available doctor users</option>');
                }

                // Populate departments dropdowns
                if (response.departments) {
                    populateDepartments('#add_department_id', response.departments);
                    populateDepartments('#edit_department_id', response.departments);
                }
            },
            error: function() {
                showAlert('danger', 'Unable to load setup options.');
            }
        });
    }

    function populateDepartments(selector, departments) {
        var $select = $(selector);
        $select.html('<option value="">Select Department</option>');
        if (departments && departments.length > 0) {
            $.each(departments, function(index, dept) {
                $select.append('<option value="' + escapeHtml(dept.id) + '">' + 
                    escapeHtml(dept.department_name) + 
                    '</option>');
            });
        }
    }

    function resetAddForm() {
        $('#addDoctorForm')[0].reset();
        clearValidationErrors('#addDoctorForm');
        $('#add_user_id').val('');
        $('#add_department_id').val('');
    }

    function resetEditForm() {
        $('#editDoctorForm')[0].reset();
        $('#edit_doctor_id').val('');
        clearValidationErrors('#editDoctorForm');
        $('#edit_department_id').val('');
        $('input[name="available_days[]"]').prop('checked', false);
    }

    function saveDoctor() {
        clearValidationErrors('#addDoctorForm');
        clearAlert();

        var $button = $('#saveDoctorButton');
        var originalText = $button.text();

        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        $.ajax({
            url: storeUrl,
            type: 'POST',
            dataType: 'json',
            data: $('#addDoctorForm').serialize(),
            success: function(response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    $('#addDoctorModal').modal('hide');
                    resetAddForm();
                    loadDoctors();
                    showAlert('success', response.message);
                    return;
                }

                applyValidationErrors('#addDoctorForm', response.errors);
                showAlert('danger', response.message || 'Unable to create doctor profile.');
            },
            error: function() {
                showAlert('danger', 'Unable to create doctor profile.');
            },
            complete: function() {
                $button.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save Doctor');
            }
        });
    }

    function editDoctor(doctorId) {
        resetEditForm();
        clearAlert();

        $.ajax({
            url: editBaseUrl + '/' + doctorId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                updateCsrfToken(response.csrfHash);

                if (!response.status) {
                    showAlert('danger', response.message || 'Doctor profile not found.');
                    return;
                }

                $('#edit_doctor_id').val(response.data.id);
                $('#edit_department_id').val(response.data.department_id);
                $('#edit_specialization').val(response.data.specialization);
                $('#edit_qualification').val(response.data.qualification);
                $('#edit_consultation_fee').val(response.data.consultation_fee);
                $('#edit_status').val(response.data.status);

                // Check available days
                if (response.data.available_days) {
                    var days = response.data.available_days.split(',');
                    $('input[name="available_days[]"]').prop('checked', false);
                    $.each(days, function(index, day) {
                        $('input[name="available_days[]"][value="' + day.trim() + '"]').prop('checked', true);
                    });
                }

                // Populate departments if provided
                if (response.departments) {
                    populateDepartments('#edit_department_id', response.departments);
                    $('#edit_department_id').val(response.data.department_id);
                }

                $('#editDoctorModal').modal('show');
            },
            error: function() {
                showAlert('danger', 'Unable to fetch doctor details.');
            }
        });
    }

    function updateDoctor() {
        clearValidationErrors('#editDoctorForm');
        clearAlert();

        var doctorId = $('#edit_doctor_id').val();
        var $button = $('#updateDoctorButton');
        var originalText = $button.text();

        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Updating...');

        $.ajax({
            url: updateBaseUrl + '/' + doctorId,
            type: 'POST',
            dataType: 'json',
            data: $('#editDoctorForm').serialize(),
            success: function(response) {
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
            error: function() {
                showAlert('danger', 'Unable to update doctor profile.');
            },
            complete: function() {
                $button.prop('disabled', false).html('<i class="fas fa-edit mr-1"></i> Update Doctor');
            }
        });
    }

    function toggleDoctorStatus(doctorId) {
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
            success: function(response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    loadDoctors();
                    showAlert('success', response.message);
                    return;
                }

                showAlert('danger', response.message || 'Unable to change doctor profile status.');
            },
            error: function() {
                showAlert('danger', 'Unable to change doctor profile status.');
            }
        });
    }

    // Public API
    return {
        init: init,
        loadDoctors: loadDoctors,
        clearAlert: clearAlert,
        showAlert: showAlert
    };

})();