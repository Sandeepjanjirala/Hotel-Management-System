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
                                    <th style="width: 180px;">Actions</th>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var listUrl = '<?php echo site_url('admin/departments/get_departments_ajax'); ?>';
    var storeUrl = '<?php echo site_url('admin/departments/store'); ?>';
    var editBaseUrl = '<?php echo site_url('admin/departments/edit'); ?>';
    var updateBaseUrl = '<?php echo site_url('admin/departments/update'); ?>';
    var changeStatusBaseUrl = '<?php echo site_url('admin/departments/change-status'); ?>';

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
        var alertHtml = ''
            + '<div class="alert alert-' + alertClass + ' alert-dismissible fade show" role="alert">'
            + escapeHtml(message)
            + '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'
            + '<span aria-hidden="true">&times;</span>'
            + '</button>'
            + '</div>';

        $('#departmentAlert').html(alertHtml);
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

    function renderRows(rows) {
        var html = '';

        if (!rows || rows.length === 0) {
            html = '<tr><td colspan="5" class="text-center">No departments found.</td></tr>';
            $('#departmentsTableBody').html(html);
            return;
        }

        $.each(rows, function (index, department) {
            var description = department.description ? escapeHtml(department.description) : '-';
            var toggleText = parseInt(department.status, 10) === 1 ? 'Deactivate' : 'Activate';
            var toggleClass = parseInt(department.status, 10) === 1 ? 'btn-warning' : 'btn-success';

            html += '<tr>';
            html += '<td>' + escapeHtml(department.id) + '</td>';
            html += '<td>' + escapeHtml(department.department_name) + '</td>';
            html += '<td>' + description + '</td>';
            html += '<td>' + renderStatusBadge(department.status) + '</td>';
            html += '<td>';
            html += '<button type="button" class="btn btn-info btn-sm mr-1 btn-edit-department" data-id="' + escapeHtml(department.id) + '">';
            html += '<i class="fas fa-edit mr-1"></i>Edit';
            html += '</button>';
            html += '<button type="button" class="btn ' + toggleClass + ' btn-sm btn-toggle-status" data-id="' + escapeHtml(department.id) + '">';
            html += '<i class="fas fa-sync-alt mr-1"></i>' + toggleText;
            html += '</button>';
            html += '</td>';
            html += '</tr>';
        });

        $('#departmentsTableBody').html(html);
    }

    function loadDepartments() {
        $('#departmentsTableBody').html('<tr><td colspan="5" class="text-center">Loading departments...</td></tr>');

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
                showAlert('danger', response.message || 'Unable to load departments.');
            },
            error: function () {
                renderRows([]);
                showAlert('danger', 'Unable to load departments.');
            }
        });
    }

    function resetAddForm() {
        $('#addDepartmentForm')[0].reset();
        clearValidationErrors('#addDepartmentForm');
    }

    function resetEditForm() {
        $('#editDepartmentForm')[0].reset();
        $('#edit_department_id').val('');
        clearValidationErrors('#editDepartmentForm');
    }

    $('#addDepartmentButton').on('click', function () {
        resetAddForm();
        $('#addDepartmentModal').modal('show');
    });

    $('#addDepartmentForm').on('submit', function (event) {
        event.preventDefault();

        clearValidationErrors('#addDepartmentForm');

        var $button = $('#saveDepartmentButton');
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
                    $('#addDepartmentModal').modal('hide');
                    resetAddForm();
                    loadDepartments();
                    showAlert('success', response.message);
                    return;
                }

                applyValidationErrors('#addDepartmentForm', response.errors);
                showAlert('danger', response.message || 'Unable to create department.');
            },
            error: function () {
                showAlert('danger', 'Unable to create department.');
            },
            complete: function () {
                $button.prop('disabled', false).text(originalText);
            }
        });
    });

    $(document).on('click', '.btn-edit-department', function () {
        var departmentId = $(this).data('id');

        resetEditForm();

        $.ajax({
            url: editBaseUrl + '/' + departmentId,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                updateCsrfToken(response.csrfHash);

                if (!response.status) {
                    showAlert('danger', response.message || 'Department not found.');
                    return;
                }

                $('#edit_department_id').val(response.data.id);
                $('#edit_department_name').val(response.data.department_name);
                $('#edit_description').val(response.data.description || '');
                $('#editDepartmentModal').modal('show');
            },
            error: function () {
                showAlert('danger', 'Unable to fetch department details.');
            }
        });
    });

    $('#editDepartmentForm').on('submit', function (event) {
        event.preventDefault();

        clearValidationErrors('#editDepartmentForm');

        var departmentId = $('#edit_department_id').val();
        var $button = $('#updateDepartmentButton');
        var originalText = $button.text();

        $button.prop('disabled', true).text('Updating...');

        $.ajax({
            url: updateBaseUrl + '/' + departmentId,
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
            success: function (response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    $('#editDepartmentModal').modal('hide');
                    resetEditForm();
                    loadDepartments();
                    showAlert('success', response.message);
                    return;
                }

                applyValidationErrors('#editDepartmentForm', response.errors);
                showAlert('danger', response.message || 'Unable to update department.');
            },
            error: function () {
                showAlert('danger', 'Unable to update department.');
            },
            complete: function () {
                $button.prop('disabled', false).text(originalText);
            }
        });
    });

    $(document).on('click', '.btn-toggle-status', function () {
        var departmentId = $(this).data('id');

        if (!window.confirm('Are you sure you want to change this department status?')) {
            return;
        }

        $.ajax({
            url: changeStatusBaseUrl + '/' + departmentId,
            type: 'POST',
            dataType: 'json',
            data: (function () {
                var payload = {};
                payload[csrfName] = csrfHash;
                return payload;
            })(),
            success: function (response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    loadDepartments();
                    showAlert('success', response.message);
                    return;
                }

                showAlert('danger', response.message || 'Unable to change department status.');
            },
            error: function () {
                showAlert('danger', 'Unable to change department status.');
            }
        });
    });

    $('#addDepartmentModal').on('hidden.bs.modal', function () {
        resetAddForm();
    });

    $('#editDepartmentModal').on('hidden.bs.modal', function () {
        resetEditForm();
    });

    loadDepartments();
});
</script>
