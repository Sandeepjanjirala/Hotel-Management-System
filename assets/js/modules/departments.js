/**
 * Department Management Module
 * Handles all department CRUD operations via AJAX
 */

var Departments = (function() {
    'use strict';

    // Private variables
    var csrfName = '';
    var csrfHash = '';
    var listUrl = '';
    var storeUrl = '';
    var editBaseUrl = '';
    var updateBaseUrl = '';
    var alertTimeout = null;
    var isModalOpen = false; // Track if modal is open

    /**
     * Initialize the module
     * @param {Object} config - Configuration object
     */
    function init(config) {
        csrfName = config.csrfName || '';
        csrfHash = config.csrfHash || '';
        listUrl = config.listUrl || '';
        storeUrl = config.storeUrl || '';
        editBaseUrl = config.editBaseUrl || '';
        updateBaseUrl = config.updateBaseUrl || '';

        // Bind events
        bindEvents();

        // Load initial data
        loadDepartments();
    }

    /**
     * Bind all DOM events
     */
    function bindEvents() {
        // Add Department Button
        $('#addDepartmentButton').on('click', function() {
            resetAddForm();
            clearAlert();
            isModalOpen = true;
            $('#addDepartmentModal').modal('show');
        });

        // Add Department Form Submit
        $('#addDepartmentForm').on('submit', function(event) {
            event.preventDefault();
            saveDepartment();
        });

        // Edit Department Button (delegated)
        $(document).on('click', '.btn-edit-department', function() {
            var departmentId = $(this).data('id');
            editDepartment(departmentId);
        });

        // Edit Department Form Submit
        $('#editDepartmentForm').on('submit', function(event) {
            event.preventDefault();
            updateDepartment();
        });

        // Modal Hidden Events - Reset forms but DON'T clear alert immediately
        $('#addDepartmentModal').on('hidden.bs.modal', function() {
            isModalOpen = false;
            resetAddForm();
            // Don't clear alert here - let it stay visible
        });

        $('#editDepartmentModal').on('hidden.bs.modal', function() {
            isModalOpen = false;
            resetEditForm();
            // Don't clear alert here - let it stay visible
        });

        // Clear alert when clicking the close button
        $(document).on('click', '.alert .close', function() {
            clearAlert();
        });
    }

    /**
     * Update CSRF token
     */
    function updateCsrfToken(hash) {
        if (!hash) return;
        csrfHash = hash;
        $('input[name="' + csrfName + '"]').val(csrfHash);
    }

    /**
     * Escape HTML to prevent XSS
     */
    function escapeHtml(value) {
        return $('<div>').text(value === null || value === undefined ? '' : value).html();
    }

    /**
     * Show alert message with auto-dismiss
     * @param {string} type - 'success' or 'danger'
     * @param {string} message - Alert message
     * @param {number} duration - Time in milliseconds (default: 10000 = 10 seconds)
     */
    function showAlert(type, message, duration) {
        // Default to 10 seconds if not specified
        var autoDismissDelay = duration || 10000;

        // Clear any existing timeout
        if (alertTimeout) {
            clearTimeout(alertTimeout);
            alertTimeout = null;
        }

        var alertClass = type === 'success' ? 'success' : 'danger';
        var alertHtml = ''
            + '<div class="alert alert-' + alertClass + ' alert-dismissible fade show" role="alert" style="position: relative; z-index: 1050;">'
            + '<i class="fas fa-' + (type === 'success' ? 'check-circle' : 'exclamation-circle') + ' mr-2"></i>'
            + escapeHtml(message)
            + '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'
            + '<span aria-hidden="true">&times;</span>'
            + '</button>'
            + '</div>';

        $('#departmentAlert').html(alertHtml);

        // Auto-dismiss after specified duration
        alertTimeout = setTimeout(function() {
            clearAlert();
        }, autoDismissDelay);
    }

    /**
     * Clear alert message
     */
    function clearAlert() {
        if (alertTimeout) {
            clearTimeout(alertTimeout);
            alertTimeout = null;
        }
        $('#departmentAlert').html('');
    }

    /**
     * Clear validation errors
     */
    function clearValidationErrors(formSelector) {
        $(formSelector).find('.validation-message').text('');
        $(formSelector).find('.form-control').removeClass('is-invalid');
    }

    /**
     * Apply validation errors
     */
    function applyValidationErrors(formSelector, errors) {
        if (!errors) return;

        $.each(errors, function(field, message) {
            if (!message) return;

            $(formSelector).find('[name="' + field + '"]').addClass('is-invalid');
            $(formSelector).find('.validation-message[data-field="' + field + '"]').text(message);
        });
    }

    /**
     * Render status badge
     */
    function renderStatusBadge(status) {
        if (parseInt(status, 10) === 1) {
            return '<span class="badge badge-success">Active</span>';
        }
        return '<span class="badge badge-secondary">Inactive</span>';
    }

    /**
     * Render department rows
     */
    function renderRows(rows) {
        var html = '';

        if (!rows || rows.length === 0) {
            html = '<tr><td colspan="5" class="text-center">No departments found.</td></tr>';
            $('#departmentsTableBody').html(html);
            return;
        }

        $.each(rows, function(index, department) {
            var description = department.description ? escapeHtml(department.description) : '-';

            html += '<tr>';
            html += '<td>' + escapeHtml(department.id) + '</td>';
            html += '<td>' + escapeHtml(department.department_name) + '</td>';
            html += '<td>' + description + '</td>';
            html += '<td>' + renderStatusBadge(department.status) + '</td>';
            html += '<td>';
            html += '<button type="button" class="btn btn-info btn-sm btn-edit-department" data-id="' + escapeHtml(department.id) + '">';
            html += '<i class="fas fa-edit mr-1"></i>Edit';
            html += '</button>';
            html += '</td>';
            html += '</tr>';
        });

        $('#departmentsTableBody').html(html);
    }

    /**
     * Load departments from server
     */
    function loadDepartments() {
        $('#departmentsTableBody').html('<tr><td colspan="5" class="text-center">Loading departments...</td></tr>');

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
                showAlert('danger', response.message || 'Unable to load departments.', 8000);
            },
            error: function() {
                renderRows([]);
                showAlert('danger', 'Unable to load departments.', 8000);
            }
        });
    }

    /**
     * Reset add form
     */
    function resetAddForm() {
        $('#addDepartmentForm')[0].reset();
        clearValidationErrors('#addDepartmentForm');
    }

    /**
     * Reset edit form
     */
    function resetEditForm() {
        $('#editDepartmentForm')[0].reset();
        $('#edit_department_id').val('');
        clearValidationErrors('#editDepartmentForm');
    }

    /**
     * Save new department
     */
    function saveDepartment() {
        clearValidationErrors('#addDepartmentForm');
        // Don't clear alert when saving - keep existing messages

        var $button = $('#saveDepartmentButton');
        var originalText = $button.text();

        $button.prop('disabled', true).text('Saving...');

        $.ajax({
            url: storeUrl,
            type: 'POST',
            dataType: 'json',
            data: $('#addDepartmentForm').serialize(),
            success: function(response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    $('#addDepartmentModal').modal('hide');
                    resetAddForm();
                    loadDepartments();
                    showAlert('success', response.message, 5000);
                    return;
                }

                applyValidationErrors('#addDepartmentForm', response.errors);
                showAlert('danger', response.message || 'Unable to create department.', 8000);
            },
            error: function() {
                showAlert('danger', 'Unable to create department.', 8000);
            },
            complete: function() {
                $button.prop('disabled', false).text(originalText);
            }
        });
    }

    /**
     * Edit department - load data into modal
     */
    function editDepartment(departmentId) {
        resetEditForm();
        // Don't clear alert when editing - let it stay visible

        $.ajax({
            url: editBaseUrl + '/' + departmentId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                updateCsrfToken(response.csrfHash);

                if (!response.status) {
                    showAlert('danger', response.message || 'Department not found.', 8000);
                    return;
                }

                $('#edit_department_id').val(response.data.id);
                $('#edit_department_name').val(response.data.department_name);
                $('#edit_description').val(response.data.description || '');
                isModalOpen = true;
                $('#editDepartmentModal').modal('show');
            },
            error: function() {
                showAlert('danger', 'Unable to fetch department details.', 8000);
            }
        });
    }

    /**
     * Update existing department
     */
    function updateDepartment() {
        clearValidationErrors('#editDepartmentForm');
        // Don't clear alert when updating - keep existing messages

        var departmentId = $('#edit_department_id').val();
        var $button = $('#updateDepartmentButton');
        var originalText = $button.text();

        $button.prop('disabled', true).text('Updating...');

        $.ajax({
            url: updateBaseUrl + '/' + departmentId,
            type: 'POST',
            dataType: 'json',
            data: $('#editDepartmentForm').serialize(),
            success: function(response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    $('#editDepartmentModal').modal('hide');
                    resetEditForm();
                    loadDepartments();
                    showAlert('success', response.message, 5000);
                    return;
                }

                applyValidationErrors('#editDepartmentForm', response.errors);
                showAlert('danger', response.message || 'Unable to update department.', 5000);
            },
            error: function() {
                showAlert('danger', 'Unable to update department.', 5000);
            },
            complete: function() {
                $button.prop('disabled', false).text(originalText);
            }
        });
    }

    // Public API
    return {
        init: init,
        loadDepartments: loadDepartments,
        clearAlert: clearAlert,
        showAlert: showAlert
    };

})();