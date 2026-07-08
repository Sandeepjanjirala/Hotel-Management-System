/**
 * Patient Management Module
 * Handles all patient CRUD operations via AJAX
 */

var Patients = (function() {
    'use strict';

    var csrfName = '';
    var csrfHash = '';
    var listUrl = '';
    var storeUrl = '';
    var editBaseUrl = '';
    var updateBaseUrl = '';
    var changeStatusBaseUrl = '';
    var alertTimeout = null;
    var searchTimer = null;

    function init(config) {
        csrfName = config.csrfName || '';
        csrfHash = config.csrfHash || '';
        listUrl = config.listUrl || '';
        storeUrl = config.storeUrl || '';
        editBaseUrl = config.editBaseUrl || '';
        updateBaseUrl = config.updateBaseUrl || '';
        changeStatusBaseUrl = config.changeStatusBaseUrl || '';

        bindEvents();
        loadPatients();
        bindFormEvents();
    }

    function bindEvents() {
        // Search with debounce
        $('#patientSearch').on('keyup', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                loadPatients();
            }, 300);
        });

        // Clear search
        $('#clearSearchButton').on('click', function() {
            $('#patientSearch').val('');
            loadPatients();
        });

        // Status toggle (delegated)
        $(document).on('click', '.js-patient-status', function() {
            var $button = $(this);
            var message = $button.data('confirm') || 'Are you sure you want to change this patient status?';

            if (!window.confirm(message)) {
                return;
            }

            togglePatientStatus($button);
        });
    }

    function bindFormEvents() {
        // Create/Register form
        $('#createPatientForm').on('submit', function(event) {
            event.preventDefault();
            submitPatientForm($(this), 'create');
        });

        // Edit form
        $('#editPatientForm').on('submit', function(event) {
            event.preventDefault();
            submitPatientForm($(this), 'edit');
        });
    }

    function submitPatientForm($form, mode) {
        var $button = $form.find('button[type="submit"]');
        var originalText = $button.html();
        var formData = $form.serialize();

        // Show loading state
        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> ' + (mode === 'create' ? 'Registering...' : 'Updating...'));

        // Clear previous alerts
        clearAlert();

        var url = mode === 'create' ? storeUrl : updateBaseUrl + '/' + $form.find('input[name="patient_id"]').val();

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: formData,
            success: function(response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    showAlert('success', response.message);

                    // Redirect after success
                    if (response.redirect) {
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 1000);
                    } else {
                        // If no redirect, reload the list
                        setTimeout(function() {
                            window.location.href = editBaseUrl;
                        }, 1000);
                    }
                    return;
                }

                // Show validation errors
                if (response.errors) {
                    showValidationErrors($form, response.errors);
                }

                showAlert('danger', response.message || 'Unable to ' + (mode === 'create' ? 'register' : 'update') + ' patient.');
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                showAlert('danger', 'Unable to ' + (mode === 'create' ? 'register' : 'update') + ' patient. Please try again.');
            },
            complete: function() {
                $button.prop('disabled', false).html(originalText);
            }
        });
    }

    function showValidationErrors($form, errors) {
        // Clear previous errors
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.text-danger.error-message').remove();

        // Show new errors
        $.each(errors, function(field, message) {
            var $input = $form.find('[name="' + field + '"]');
            if ($input.length) {
                $input.addClass('is-invalid');
                // Add error message below the field
                var $error = $('<small class="text-danger error-message d-block">' + message + '</small>');
                $input.closest('.form-group').append($error);
            }
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

        $('#hmsNotificationArea').html(html);

        alertTimeout = setTimeout(function() {
            clearAlert();
        }, 5000);
    }

    function clearAlert() {
        if (alertTimeout) {
            clearTimeout(alertTimeout);
            alertTimeout = null;
        }
        $('#hmsNotificationArea').html('');
    }

    function renderStatusBadge(status) {
        if (parseInt(status, 10) === 1) {
            return '<span class="badge badge-success">Active</span>';
        }
        return '<span class="badge badge-secondary">Inactive</span>';
    }

    function renderPatientRows(rows) {
        var html = '';

        if (!rows || rows.length === 0) {
            html = '<tr><td colspan="7" class="text-center">No patients found.</td></tr>';
            $('#patientsTableBody').html(html);
            return;
        }

        $.each(rows, function(index, patient) {
            var isActive = parseInt(patient.status, 10) === 1;
            var toggleText = isActive ? 'Deactivate' : 'Activate';
            var toggleClass = isActive ? 'btn-warning' : 'btn-success';

            html += '<tr id="patient-row-' + escapeHtml(patient.id) + '">';
            html += '<td><strong>' + escapeHtml(patient.patient_code) + '</strong></td>';
            html += '<td>' + escapeHtml(patient.full_name) + '</td>';
            html += '<td>' + escapeHtml(ucfirst(patient.gender)) + '</td>';
            html += '<td>' + escapeHtml(patient.blood_group ? patient.blood_group : 'Unknown') + '</td>';
            html += '<td>' + escapeHtml(patient.phone) + '</td>';
            html += '<td>' + renderStatusBadge(patient.status) + '</td>';
            html += '<td>';
            html += '<a href="' + editBaseUrl + '/' + escapeHtml(patient.id) + '" class="btn btn-info btn-sm mr-1">';
            html += '<i class="fas fa-edit mr-1"></i>Edit';
            html += '</a>';
            html += '<button type="button" class="btn ' + toggleClass + ' btn-sm js-patient-status" data-id="' + escapeHtml(patient.id) + '" data-confirm="Are you sure you want to ' + toggleText.toLowerCase() + ' this patient?">';
            html += '<i class="fas fa-sync-alt mr-1"></i>' + toggleText;
            html += '</button>';
            html += '</td>';
            html += '</tr>';
        });

        $('#patientsTableBody').html(html);
    }

    function ucfirst(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function loadPatients() {
        $('#patientsTableBody').html('<tr><td colspan="7" class="text-center">Loading patients...</td></tr>');

        var search = $('#patientSearch').val();

        $.ajax({
            url: listUrl,
            type: 'GET',
            dataType: 'json',
            data: {
                search: search
            },
            success: function(response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    if (response.html) {
                        $('#patientsTableBody').html(response.html);
                    } else {
                        renderPatientRows(response.data);
                    }
                    return;
                }

                renderPatientRows([]);
                showAlert('danger', response.message || 'Unable to load patients.');
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                renderPatientRows([]);
                showAlert('danger', 'Unable to load patients. Please check console for details.');
            }
        });
    }

    function togglePatientStatus($button) {
        var patientId = $button.data('id');
        var originalText = $button.html();

        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Please wait...');

        $.ajax({
            url: changeStatusBaseUrl + '/' + patientId,
            type: 'POST',
            dataType: 'json',
            data: (function() {
                var payload = {};
                payload[csrfName] = csrfHash;
                return payload;
            })(),
            success: function(response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    if (response.row_html) {
                        $('#patient-row-' + patientId).replaceWith(response.row_html);
                    } else {
                        loadPatients();
                    }
                    showAlert('success', response.message);
                    return;
                }

                showAlert('danger', response.message || 'Unable to change patient status.');
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                showAlert('danger', 'Unable to change patient status. Please try again.');
            },
            complete: function() {
                $button.prop('disabled', false).html(originalText);
            }
        });
    }

    // Public API
    return {
        init: init,
        loadPatients: loadPatients,
        clearAlert: clearAlert,
        showAlert: showAlert
    };

})();