<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Department Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Departments</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div id="departmentAlert"></div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Department List</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" id="addDepartmentButton">
                            <i class="fas fa-plus mr-1"></i> Add Department
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 70px;">ID</th>
                                    <th style="width: 220px;">Department Name</th>
                                    <th>Description</th>
                                    <th style="width: 120px;">Status</th>
                                    <th style="width: 100px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="departmentsTableBody">
                                <tr>
                                    <td colspan="5" class="text-center">Loading departments...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add Department Modal -->
<div class="modal fade" id="addDepartmentModal" tabindex="-1" role="dialog" aria-labelledby="addDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="addDepartmentForm" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDepartmentModalLabel">Add Department</h5>
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

                    <div class="form-group">
                        <label for="add_department_name">Department Name</label>
                        <input type="text" class="form-control" id="add_department_name" name="department_name" maxlength="150">
                        <small class="text-danger validation-message" data-field="department_name"></small>
                    </div>

                    <div class="form-group mb-0">
                        <label for="add_description">Description</label>
                        <textarea class="form-control" id="add_description" name="description" rows="4"></textarea>
                        <small class="text-danger validation-message" data-field="description"></small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveDepartmentButton">
                        Save Department
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Department Modal -->
<div class="modal fade" id="editDepartmentModal" tabindex="-1" role="dialog" aria-labelledby="editDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="editDepartmentForm" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDepartmentModalLabel">Edit Department</h5>
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
                    <input type="hidden" name="department_id" id="edit_department_id">

                    <div class="form-group">
                        <label for="edit_department_name">Department Name</label>
                        <input type="text" class="form-control" id="edit_department_name" name="department_name" maxlength="150">
                        <small class="text-danger validation-message" data-field="department_name"></small>
                    </div>

                    <div class="form-group mb-0">
                        <label for="edit_description">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="4"></textarea>
                        <small class="text-danger validation-message" data-field="description"></small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="updateDepartmentButton">
                        Update Department
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include Department Module Script -->
<script src="<?php echo base_url('assets/js/modules/departments.js'); ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the Departments module
    Departments.init({
        csrfName: '<?php echo $this->security->get_csrf_token_name(); ?>',
        csrfHash: '<?php echo $this->security->get_csrf_hash(); ?>',
        listUrl: '<?php echo site_url('admin/departments/get_departments_ajax'); ?>',
        storeUrl: '<?php echo site_url('admin/departments/store'); ?>',
        editBaseUrl: '<?php echo site_url('admin/departments/edit'); ?>',
        updateBaseUrl: '<?php echo site_url('admin/departments/update'); ?>'
    });
});
</script>