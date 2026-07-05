/**
 * User Management Module
 * Handles all user CRUD operations via AJAX
 */

var Users = (function() {
    'use strict';

    // Private variables
    var csrfName = '';
    var csrfHash = '';
    var listUrl = '';
    var storeUrl = '';
    var editBaseUrl = '';
    var updateBaseUrl = '';
    var changeStatusBaseUrl = '';
    var alertTimeout = null;

    /**
     * Initialize the module
     */
    function init(config) {
        csrfName = config.csrfName || '';
        csrfHash = config.csrfHash || '';
        listUrl = config.listUrl || '';
        storeUrl = config.storeUrl || '';
        editBaseUrl = config.editBaseUrl || '';
        updateBaseUrl = config.updateBaseUrl || '';
        changeStatusBaseUrl = config.changeStatusBaseUrl || '';

        bindEvents();
        loadUsers();
        
        // Initialize department requirement
        updateDepartmentRequirement('#addUserForm');
        updateDepartmentRequirement('#editUserForm');
    }

    /**
     * Bind all DOM events
     */
    function bindEvents() {
        // Add User Button
        $('#addUserButton').on('click', function() {
            resetAddForm();
            clearAlert();
            $('#addUserModal').modal('show');
        });

        // Add User Form Submit
        $('#addUserForm').on('submit', function(event) {
            event.preventDefault();
            saveUser();
        });

        // Edit User Button (delegated)
        $(document).on('click', '.btn-edit-user', function() {
            var userId = $(this).data('id');
            editUser(userId);
        });

        // Edit User Form Submit
        $('#editUserForm').on('submit', function(event) {
            event.preventDefault();
            updateUser();
        });

        // Role change event
        $(document).on('change', '.user-role-select', function() {
            var formId = $(this).data('form');
            if (formId) {
                updateDepartmentRequirement(formId);
            }
        });

        // Toggle status button (delegated)
        $(document).on('click', '.btn-toggle-status', function() {
            var userId = $(this).data('id');
            toggleUserStatus(userId);
        });

        // Modal Hidden Events - Don't clear alert, let it stay
        $('#addUserModal').on('hidden.bs.modal', function() {
            resetAddForm();
        });

        $('#editUserModal').on('hidden.bs.modal', function() {
            resetEditForm();
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
     * Show alert message with auto-dismiss after 5 seconds
     */
    function showAlert(type, message) {
        // Clear any existing timeout
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

        $('#userAlert').html(html);

        // Auto-dismiss after 5 seconds
        alertTimeout = setTimeout(function() {
            clearAlert();
        }, 5000);
    }

    /**
     * Clear alert message
     */
    function clearAlert() {
        if (alertTimeout) {
            clearTimeout(alertTimeout);
            alertTimeout = null;
        }
        $('#userAlert').html('');
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
     * Render role badge
     */
    function renderRoleBadge(role) {
        var badgeClass = 'badge-info';
        if (role === 'admin') badgeClass = 'badge-primary';
        if (role === 'doctor') badgeClass = 'badge-success';
        if (role === 'receptionist') badgeClass = 'badge-warning';

        return '<span class="badge ' + badgeClass + '">' + 
               escapeHtml(role.charAt(0).toUpperCase() + role.slice(1)) + 
               '</span>';
    }

    /**
     * Render user rows
     */
    function renderRows(rows) {
        var html = '';

        if (!rows || rows.length === 0) {
            html = '<tr><td colspan="7" class="text-center">No users found.</td></tr>';
            $('#usersTableBody').html(html);
            return;
        }

        $.each(rows, function(index, user) {
            var departmentName = user.department_name ? escapeHtml(user.department_name) : '-';
            var phone = user.phone ? escapeHtml(user.phone) : '-';
            var toggleText = parseInt(user.status, 10) === 1 ? 'Deactivate' : 'Activate';
            var toggleClass = parseInt(user.status, 10) === 1 ? 'btn-warning' : 'btn-success';

            html += '<tr>';
            html += '<td>' + escapeHtml(user.name) + '</td>';
            html += '<td>' + escapeHtml(user.email) + '</td>';
            html += '<td>' + renderRoleBadge(user.role) + '</td>';
            html += '<td>' + departmentName + '</td>';
            html += '<td>' + phone + '</td>';
            html += '<td>' + renderStatusBadge(user.status) + '</td>';
            html += '<td>';
            html += '<button type="button" class="btn btn-info btn-sm mr-1 btn-edit-user" data-id="' + escapeHtml(user.id) + '">';
            html += '<i class="fas fa-edit mr-1"></i>Edit';
            html += '</button>';
            html += '<button type="button" class="btn ' + toggleClass + ' btn-sm btn-toggle-status" data-id="' + escapeHtml(user.id) + '">';
            html += '<i class="fas fa-sync-alt mr-1"></i>' + toggleText;
            html += '</button>';
            html += '</td>';
            html += '</tr>';
        });

        $('#usersTableBody').html(html);
    }

    /**
     * Load users from server
     */
    function loadUsers() {
        $('#usersTableBody').html('<tr><td colspan="7" class="text-center">Loading users...</td></tr>');

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
                showAlert('danger', response.message || 'Unable to load users.');
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                console.error('Response:', xhr.responseText);
                renderRows([]);
                showAlert('danger', 'Unable to load users. Please check console for details.');
            }
        });
    }

    /**
     * Update department requirement based on role
     */
    function updateDepartmentRequirement(formSelector) {
        var role = $(formSelector).find('[name="role"]').val();
        var $department = $(formSelector).find('[name="department_id"]');
        var $help = $(formSelector).find('.department-help');

        if (role === 'admin') {
            $department.prop('required', false).val('');
            $help.text('Optional for admin.');
            return;
        }

        if (role === 'doctor' || role === 'receptionist') {
            $department.prop('required', true);
            $help.text('Required for doctor and receptionist.');
            return;
        }

        $department.prop('required', false);
        $help.text('Required for doctor and receptionist.');
    }

    /**
     * Reset add form
     */
    function resetAddForm() {
        $('#addUserForm')[0].reset();
        clearValidationErrors('#addUserForm');
        updateDepartmentRequirement('#addUserForm');
    }

    /**
     * Reset edit form
     */
    function resetEditForm() {
        $('#editUserForm')[0].reset();
        $('#edit_user_id').val('');
        clearValidationErrors('#editUserForm');
        updateDepartmentRequirement('#editUserForm');
    }

    /**
     * Save new user
     */
    function saveUser() {
        clearValidationErrors('#addUserForm');
        // Don't clear alert when saving

        var $button = $('#saveUserButton');
        var originalText = $button.text();

        $button.prop('disabled', true).text('Saving...');

        $.ajax({
            url: storeUrl,
            type: 'POST',
            dataType: 'json',
            data: $('#addUserForm').serialize(),
            success: function(response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    $('#addUserModal').modal('hide');
                    resetAddForm();
                    loadUsers();
                    showAlert('success', response.message);
                    return;
                }

                applyValidationErrors('#addUserForm', response.errors);
                showAlert('danger', response.message || 'Unable to create user.');
            },
            error: function() {
                showAlert('danger', 'Unable to create user.');
            },
            complete: function() {
                $button.prop('disabled', false).text(originalText);
            }
        });
    }

    /**
     * Edit user - load data into modal
     */
    function editUser(userId) {
        resetEditForm();
        // Don't clear alert when editing

        $.ajax({
            url: editBaseUrl + '/' + userId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                updateCsrfToken(response.csrfHash);

                if (!response.status) {
                    showAlert('danger', response.message || 'User not found.');
                    return;
                }

                $('#edit_user_id').val(response.data.id);
                $('#edit_name').val(response.data.name);
                $('#edit_email').val(response.data.email);
                $('#edit_password').val('');
                $('#edit_phone').val(response.data.phone || '');
                $('#edit_role').val(response.data.role);
                $('#edit_department_id').val(response.data.department_id || '');
                $('#edit_status').val(response.data.status);

                updateDepartmentRequirement('#editUserForm');
                $('#editUserModal').modal('show');
            },
            error: function() {
                showAlert('danger', 'Unable to fetch user details.');
            }
        });
    }

    /**
     * Update existing user
     */
    function updateUser() {
        clearValidationErrors('#editUserForm');
        // Don't clear alert when updating

        var userId = $('#edit_user_id').val();
        var $button = $('#updateUserButton');
        var originalText = $button.text();

        $button.prop('disabled', true).text('Updating...');

        $.ajax({
            url: updateBaseUrl + '/' + userId,
            type: 'POST',
            dataType: 'json',
            data: $('#editUserForm').serialize(),
            success: function(response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    $('#editUserModal').modal('hide');
                    resetEditForm();
                    loadUsers();
                    showAlert('success', response.message);
                    return;
                }

                applyValidationErrors('#editUserForm', response.errors);
                showAlert('danger', response.message || 'Unable to update user.');
            },
            error: function() {
                showAlert('danger', 'Unable to update user.');
            },
            complete: function() {
                $button.prop('disabled', false).text(originalText);
            }
        });
    }

    /**
     * Toggle user status
     */
    function toggleUserStatus(userId) {
        if (!window.confirm('Are you sure you want to change this user status?')) {
            return;
        }

        // Don't clear existing alerts
        var payload = {};
        payload[csrfName] = csrfHash;

        $.ajax({
            url: changeStatusBaseUrl + '/' + userId,
            type: 'POST',
            dataType: 'json',
            data: payload,
            success: function(response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    loadUsers();
                    showAlert('success', response.message);
                    return;
                }

                showAlert('danger', response.message || 'Unable to change user status.');
            },
            error: function() {
                showAlert('danger', 'Unable to change user status.');
            }
        });
    }

    // Public API
    return {
        init: init,
        loadUsers: loadUsers,
        clearAlert: clearAlert,
        showAlert: showAlert
    };

})();