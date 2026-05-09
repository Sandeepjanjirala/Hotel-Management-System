<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>User Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div id="userAlert"></div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Admin, Doctor and Receptionist Accounts</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" id="addUserButton">
                            <i class="fas fa-plus mr-1"></i> Add User
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 170px;">Name</th>
                                    <th style="width: 220px;">Email</th>
                                    <th style="width: 130px;">Role</th>
                                    <th style="width: 170px;">Department</th>
                                    <th style="width: 130px;">Phone</th>
                                    <th style="width: 110px;">Status</th>
                                    <th style="width: 190px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody">
                                <tr>
                                    <td colspan="7" class="text-center">Loading users...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="addUserForm" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
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
                                <label for="add_name">Name</label>
                                <input type="text" class="form-control" id="add_name" name="name" maxlength="100">
                                <small class="text-danger validation-message" data-field="name"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add_email">Email</label>
                                <input type="email" class="form-control" id="add_email" name="email" maxlength="150">
                                <small class="text-danger validation-message" data-field="email"></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add_password">Password</label>
                                <input type="password" class="form-control" id="add_password" name="password">
                                <small class="text-danger validation-message" data-field="password"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add_phone">Phone</label>
                                <input type="text" class="form-control" id="add_phone" name="phone" maxlength="30">
                                <small class="text-danger validation-message" data-field="phone"></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-md-0">
                                <label for="add_role">Role</label>
                                <select class="form-control user-role-select" id="add_role" name="role" data-form="#addUserForm">
                                    <option value="">Select Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="doctor">Doctor</option>
                                    <option value="receptionist">Receptionist</option>
                                </select>
                                <small class="text-danger validation-message" data-field="role"></small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-md-0">
                                <label for="add_department_id">Department</label>
                                <select class="form-control" id="add_department_id" name="department_id">
                                    <option value="">Select Department</option>
                                    <?php foreach ($departments as $department): ?>
                                        <option value="<?php echo (int) $department['id']; ?>">
                                            <?php echo html_escape($department['department_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted department-help">Required for doctor and receptionist.</small>
                                <small class="text-danger validation-message d-block" data-field="department_id"></small>
                            </div>
                        </div>

                        <div class="col-md-4">
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
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveUserButton">Save User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="editUserForm" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
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
                    <input type="hidden" id="edit_user_id" name="user_id">

                    <div class="alert alert-light border">
                        Password is optional during edit. Leave it blank to keep the existing password.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_name">Name</label>
                                <input type="text" class="form-control" id="edit_name" name="name" maxlength="100">
                                <small class="text-danger validation-message" data-field="name"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_email">Email</label>
                                <input type="email" class="form-control" id="edit_email" name="email" maxlength="150">
                                <small class="text-danger validation-message" data-field="email"></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_password">New Password</label>
                                <input type="password" class="form-control" id="edit_password" name="password">
                                <small class="text-danger validation-message" data-field="password"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_phone">Phone</label>
                                <input type="text" class="form-control" id="edit_phone" name="phone" maxlength="30">
                                <small class="text-danger validation-message" data-field="phone"></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-md-0">
                                <label for="edit_role">Role</label>
                                <select class="form-control user-role-select" id="edit_role" name="role" data-form="#editUserForm">
                                    <option value="">Select Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="doctor">Doctor</option>
                                    <option value="receptionist">Receptionist</option>
                                </select>
                                <small class="text-danger validation-message" data-field="role"></small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-md-0">
                                <label for="edit_department_id">Department</label>
                                <select class="form-control" id="edit_department_id" name="department_id">
                                    <option value="">Select Department</option>
                                    <?php foreach ($departments as $department): ?>
                                        <option value="<?php echo (int) $department['id']; ?>">
                                            <?php echo html_escape($department['department_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted department-help">Required for doctor and receptionist.</small>
                                <small class="text-danger validation-message d-block" data-field="department_id"></small>
                            </div>
                        </div>

                        <div class="col-md-4">
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
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="updateUserButton">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

    var listUrl = '<?php echo site_url('admin/users/ajax-list'); ?>';
    var storeUrl = '<?php echo site_url('admin/users/store'); ?>';
    var editBaseUrl = '<?php echo site_url('admin/users/edit'); ?>';
    var updateBaseUrl = '<?php echo site_url('admin/users/update'); ?>';
    var changeStatusBaseUrl = '<?php echo site_url('admin/users/change-status'); ?>';

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

        $('#userAlert').html(html);
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

    function renderRoleBadge(role) {
        var badgeClass = 'badge-info';

        if (role === 'admin') {
            badgeClass = 'badge-primary';
        }

        if (role === 'doctor') {
            badgeClass = 'badge-success';
        }

        if (role === 'receptionist') {
            badgeClass = 'badge-warning';
        }

        return '<span class="badge ' + badgeClass + '">' + escapeHtml(role.charAt(0).toUpperCase() + role.slice(1)) + '</span>';
    }

    function renderRows(rows) {
        var html = '';

        if (!rows || rows.length === 0) {
            $('#usersTableBody').html('<tr><td colspan="7" class="text-center">No users found.</td></tr>');
            return;
        }

        $.each(rows, function (index, user) {
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

    function loadUsers() {
        $('#usersTableBody').html('<tr><td colspan="7" class="text-center">Loading users...</td></tr>');

        $.ajax({
            url: listUrl,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    renderRows(response.data);
                    return;
                }

                renderRows([]);
                showAlert('danger', response.message || 'Unable to load users.');
            },
            error: function () {
                renderRows([]);
                showAlert('danger', 'Unable to load users.');
            }
        });
    }

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

    function resetAddForm() {
        $('#addUserForm')[0].reset();
        clearValidationErrors('#addUserForm');
        updateDepartmentRequirement('#addUserForm');
    }

    function resetEditForm() {
        $('#editUserForm')[0].reset();
        $('#edit_user_id').val('');
        clearValidationErrors('#editUserForm');
        updateDepartmentRequirement('#editUserForm');
    }

    $('#addUserButton').on('click', function () {
        resetAddForm();
        $('#addUserModal').modal('show');
    });

    $('.user-role-select').on('change', function () {
        updateDepartmentRequirement($(this).data('form'));
    });

    $('#addUserForm').on('submit', function (event) {
        event.preventDefault();

        clearValidationErrors('#addUserForm');

        var $button = $('#saveUserButton');
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
                    $('#addUserModal').modal('hide');
                    resetAddForm();
                    loadUsers();
                    showAlert('success', response.message);
                    return;
                }

                applyValidationErrors('#addUserForm', response.errors);
                showAlert('danger', response.message || 'Unable to create user.');
            },
            error: function () {
                showAlert('danger', 'Unable to create user.');
            },
            complete: function () {
                $button.prop('disabled', false).text(originalText);
            }
        });
    });

    $(document).on('click', '.btn-edit-user', function () {
        var userId = $(this).data('id');

        resetEditForm();

        $.ajax({
            url: editBaseUrl + '/' + userId,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
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
            error: function () {
                showAlert('danger', 'Unable to fetch user details.');
            }
        });
    });

    $('#editUserForm').on('submit', function (event) {
        event.preventDefault();

        clearValidationErrors('#editUserForm');

        var userId = $('#edit_user_id').val();
        var $button = $('#updateUserButton');
        var originalText = $button.text();

        $button.prop('disabled', true).text('Updating...');

        $.ajax({
            url: updateBaseUrl + '/' + userId,
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
            success: function (response) {
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
            error: function () {
                showAlert('danger', 'Unable to update user.');
            },
            complete: function () {
                $button.prop('disabled', false).text(originalText);
            }
        });
    });

    $(document).on('click', '.btn-toggle-status', function () {
        var userId = $(this).data('id');

        if (!window.confirm('Are you sure you want to change this user status?')) {
            return;
        }

        var payload = {};
        payload[csrfName] = csrfHash;

        $.ajax({
            url: changeStatusBaseUrl + '/' + userId,
            type: 'POST',
            dataType: 'json',
            data: payload,
            success: function (response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    loadUsers();
                    showAlert('success', response.message);
                    return;
                }

                showAlert('danger', response.message || 'Unable to change user status.');
            },
            error: function () {
                showAlert('danger', 'Unable to change user status.');
            }
        });
    });

    $('#addUserModal').on('hidden.bs.modal', function () {
        resetAddForm();
    });

    $('#editUserModal').on('hidden.bs.modal', function () {
        resetEditForm();
    });

    updateDepartmentRequirement('#addUserForm');
    updateDepartmentRequirement('#editUserForm');
    loadUsers();
});
</script>
