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

<!-- Add User Modal -->
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

<!-- Edit User Modal -->
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

<!-- Include User Module Script -->
<script src="<?php echo base_url('assets/js/modules/users.js'); ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the Users module
    Users.init({
        csrfName: '<?php echo $this->security->get_csrf_token_name(); ?>',
        csrfHash: '<?php echo $this->security->get_csrf_hash(); ?>',
        listUrl: '<?php echo site_url('admin/users/get_users_ajax'); ?>',
        storeUrl: '<?php echo site_url('admin/users/store'); ?>',
        editBaseUrl: '<?php echo site_url('admin/users/edit'); ?>',
        updateBaseUrl: '<?php echo site_url('admin/users/update'); ?>',
        changeStatusBaseUrl: '<?php echo site_url('admin/users/change_status'); ?>'
    });
});
</script>