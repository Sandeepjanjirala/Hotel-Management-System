/**
 * Doctor Management Module
 * Handles all doctor profile CRUD operations via AJAX
 */

var Doctors = (function() {
    'use strict';

    var csrfName = '';
    var csrfHash = '';
    var listUrl = '';
    var optionsUrl = '';
    var storeUrl = '';
    var editBaseUrl = '';
    var updateBaseUrl = '';
    var changeStatusBaseUrl = '';
    var alertTimeout = null;
    var departments = [];

    function init(config) {
        csrfName = config.csrfName || '';
        csrfHash = config.csrfHash || '';
        listUrl = config.listUrl || '';
        optionsUrl = config.optionsUrl || '';
        storeUrl = config.storeUrl || '';
        editBaseUrl = config.editBaseUrl || '';
        updateBaseUrl = config.updateBaseUrl || '';
        changeStatusBaseUrl = config.changeStatusBaseUrl || '';

        bindEvents();
        loadSetupOptions();
        loadDoctors();
    }

    function bindEvents() {
        $('#addDoctorButton').on('click', function() {
            resetAddForm();
            clearAlert();
            loadSetupOptions(function() {
                $('#addDoctorModal').modal('show');
            });
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
            var currentStatus = $(this).data('status');
            var action = parseInt(currentStatus, 10) === 1 ? 'deactivate' : 'activate';
            
            if (!window.confirm('Are you sure you want to ' + action + ' this doctor profile?')) {
                return;
            }
            
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

    function renderDays(days) {
        if (!days) {
            return '-';
        }

        var html = '';
        var parts = days.split(',');

        $.each(parts, function(index, day) {
            html += '<span class="badge badge-light border mr-1 mb-1">' + escapeHtml(day.trim()) + '</span>';
        });

        return html;
    }

    function renderDoctorRows(rows) {
        var html = '';

        if (!rows || rows.length === 0) {
            html = '<tr><td colspan="8" class="text-center">No doctor profiles found.</td></tr>';
            $('#doctorsTableBody').html(html);
            return;
        }

        $.each(rows, function(index, doctor) {
            var isActive = parseInt(doctor.status, 10) === 1;
            var toggleText = isActive ? 'Deactivate' : 'Activate';
            var toggleClass = isActive ? 'btn-warning' : 'btn-success';
            var fee = parseFloat(doctor.consultation_fee || 0).toFixed(2);

            html += '<tr>';
            html += '<td><strong>' + escapeHtml(doctor.doctor_name) + '</strong><br><small class="text-muted">' + escapeHtml(doctor.doctor_email) + '</small></td>';
            html += '<td>' + escapeHtml(doctor.department_name) + '</td>';
            html += '<td>' + escapeHtml(doctor.specialization) + '</td>';
            html += '<td>' + escapeHtml(doctor.qualification) + '</td>';
            html += '<td>₹' + fee + '</td>';
            html += '<td>' + renderDays(doctor.available_days) + '</td>';
            html += '<td>' + renderStatusBadge(doctor.status) + '</td>';
            html += '<td>';
            html += '<button type="button" class="btn btn-info btn-sm mr-1 btn-edit-doctor" data-id="' + escapeHtml(doctor.id) + '">';
            html += '<i class="fas fa-edit mr-1"></i>Edit';
            html += '</button>';
            html += '<button type="button" class="btn ' + toggleClass + ' btn-sm btn-toggle-status" data-id="' + escapeHtml(doctor.id) + '" data-status="' + escapeHtml(doctor.status) + '">';
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
            success: function(response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    renderDoctorRows(response.data);
                    return;
                }

                renderDoctorRows([]);
                showAlert('danger', response.message || 'Unable to load doctor profiles.');
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                renderDoctorRows([]);
                showAlert('danger', 'Unable to load doctor profiles. Please check console for details.');
            }
        });
    }

    function renderDepartments(selector, selectedId) {
        var html = '<option value="">Select Department</option>';

        $.each(departments, function(index, department) {
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
            $.each(users, function(index, user) {
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
            success: function(response) {
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
            error: function() {
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

        $.each(days, function(index, day) {
            $(selector + '[value="' + day.trim() + '"]').prop('checked', true);
        });
    }

    function saveDoctor() {
        clearValidationErrors('#addDoctorForm');
        clearAlert();

        var $button = $('#saveDoctorButton');
        var originalText = $button.text();

        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        $.ajax({
            url: storeUrl,
            method: 'POST',
            dataType: 'json',
            data: $('#addDoctorForm').serialize(),
            success: function(response) {
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
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                showAlert('danger', 'Unable to create doctor profile. Please check console for details.');
            },
            complete: function() {
                $button.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save Profile');
            }
        });
    }

function editDoctor(doctorId) {
    resetEditForm();
    clearAlert();

    console.log('=== EDIT DOCTOR ===');
    console.log('Doctor ID to edit:', doctorId);

    $.ajax({
        url: editBaseUrl + '/' + doctorId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Edit response:', response);
            updateCsrfToken(response.csrfHash);

            if (!response.status) {
                showAlert('danger', response.message || 'Doctor profile not found.');
                return;
            }

            departments = response.departments || [];
            renderDepartments('#edit_department_id', response.data.department_id);

            // FIXED: Set the ID properly
            $('#edit_doctor_id').val(response.data.id);
            console.log('Set edit_doctor_id to:', response.data.id);
            
            $('#edit_doctor_user_name').text(response.data.doctor_name + ' - ' + response.data.doctor_email);
            $('#edit_specialization').val(response.data.specialization);
            $('#edit_qualification').val(response.data.qualification);
            $('#edit_consultation_fee').val(response.data.consultation_fee);
            $('#edit_status').val(response.data.status);

            applyAvailableDays('.edit-day', response.data.available_days);

            $('#editDoctorModal').modal('show');
        },
        error: function(xhr, status, error) {
            console.error('Error fetching doctor details:', error);
            showAlert('danger', 'Unable to fetch doctor profile details.');
        }
    });
}

function updateDoctor() {
    clearValidationErrors('#editDoctorForm');
    clearAlert();

    var doctorId = $('#edit_doctor_id').val();
    
    // DEBUG: Log the ID
    console.log('=== UPDATE DOCTOR ===');
    console.log('Doctor ID from hidden field:', doctorId);
    
    if (!doctorId || doctorId === '') {
        console.error('Doctor ID is empty!');
        showAlert('danger', 'Doctor ID not found. Please try again.');
        return;
    }

    var $button = $('#updateDoctorButton');
    var originalText = $button.text();

    // Get form data
    var formData = $('#editDoctorForm').serialize();
    console.log('Form data being sent:', formData);

    $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Updating...');

    $.ajax({
        url: updateBaseUrl + '/' + doctorId,
        method: 'POST',
        dataType: 'json',
        data: formData,
        beforeSend: function(xhr) {
            // Add CSRF token to headers
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfHash);
        },
        success: function(response) {
            console.log('Update response:', response);
            updateCsrfToken(response.csrfHash);

            if (response.status) {
                $('#editDoctorModal').modal('hide');
                resetEditForm();
                loadDoctors();
                showAlert('success', response.message);
                return;
            }

            // Handle validation errors
            if (response.errors) {
                applyValidationErrors('#editDoctorForm', response.errors);
            }
            showAlert('danger', response.message || 'Unable to update doctor profile.');
        },
        error: function(xhr, status, error) {
            console.error('=== AJAX ERROR ===');
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('Response Text:', xhr.responseText);
            console.error('Response Status:', xhr.status);
            
            // Try to parse the response if it's JSON
            try {
                var responseJson = JSON.parse(xhr.responseText);
                console.error('Parsed response:', responseJson);
                showAlert('danger', responseJson.message || 'Unable to update doctor profile.');
            } catch(e) {
                showAlert('danger', 'Unable to update doctor profile. Please check console for details.');
            }
        },
        complete: function() {
            $button.prop('disabled', false).html('<i class="fas fa-edit mr-1"></i> Update Profile');
        }
    });
}

    function toggleDoctorStatus(doctorId) {
        var payload = {};
        payload[csrfName] = csrfHash;

        $.ajax({
            url: changeStatusBaseUrl + '/' + doctorId,
            method: 'POST',
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
            error: function(xhr, status, error) {
                console.error('AJAX Error Details:');
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('Response:', xhr.responseText);
                showAlert('danger', 'Unable to change doctor profile status. Please check console for details.');
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