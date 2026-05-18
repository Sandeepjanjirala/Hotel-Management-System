<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6"><h1>Lab Test Master</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Lab Tests</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div id="labTestAlert"></div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0"><i class="fas fa-vials mr-1"></i> Lab Tests</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" id="addTestButton">
                            <i class="fas fa-plus mr-1"></i> Add Test
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-lg-5 col-md-6 col-12 mb-2 mb-lg-0">
                            <input type="text" id="testSearch" class="form-control" placeholder="Search test, code or category">
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 mb-2 mb-lg-0">
                            <select id="testStatusFilter" class="form-control">
                                <option value="">All Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-3 col-6">
                            <button type="button" id="clearTestFiltersButton" class="btn btn-default btn-block">
                                <i class="fas fa-times mr-1"></i> Clear
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Test Code</th>
                                    <th>Test Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Normal Range</th>
                                    <th>Status</th>
                                    <th style="width: 180px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="labTestsTableBody">
                                <tr><td colspan="7" class="text-center">Loading lab tests...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="labTestModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="labTestForm" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title" id="labTestModalTitle">Add Lab Test</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" id="test_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Test Name</label>
                                <input type="text" class="form-control" name="test_name" id="test_name" maxlength="150">
                                <small class="text-danger validation-message" data-field="test_name"></small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Test Code</label>
                                <input type="text" class="form-control" name="test_code" id="test_code" maxlength="30">
                                <small class="text-danger validation-message" data-field="test_code"></small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Category</label>
                                <input type="text" class="form-control" name="category" id="category" maxlength="100">
                                <small class="text-danger validation-message" data-field="category"></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group mb-md-0">
                                <label>Price</label>
                                <input type="number" step="0.01" min="0" class="form-control" name="price" id="price" value="0.00">
                                <small class="text-danger validation-message" data-field="price"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-md-0">
                                <label>Normal Range</label>
                                <input type="text" class="form-control" name="normal_range" id="normal_range" maxlength="150">
                                <small class="text-danger validation-message" data-field="normal_range"></small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label>Status</label>
                                <select class="form-control" name="status" id="test_status">
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
                    <button type="submit" class="btn btn-primary" id="saveTestButton">Save Test</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var listUrl = '<?php echo site_url('admin/lab-tests/ajax-list'); ?>';
    var storeUrl = '<?php echo site_url('admin/lab-tests/store'); ?>';
    var editBaseUrl = '<?php echo site_url('admin/lab-tests/edit'); ?>';
    var updateBaseUrl = '<?php echo site_url('admin/lab-tests/update'); ?>';
    var statusBaseUrl = '<?php echo site_url('admin/lab-tests/change-status'); ?>';
    var searchTimer = null;
    var editMode = false;

    function updateCsrf(hash) {
        if (hash) {
            csrfHash = hash;
            $('input[name="' + csrfName + '"]').val(hash);
        }
    }

    function escapeHtml(value) {
        return $('<div>').text(value === null || value === undefined ? '' : value).html();
    }

    function alertBox(type, message) {
        $('#labTestAlert').html(
            '<div class="alert alert-' + (type === 'success' ? 'success' : 'danger') + ' alert-dismissible fade show">' +
            escapeHtml(message) +
            '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>'
        );
    }

    function clearErrors() {
        $('#labTestForm .validation-message').text('');
        $('#labTestForm .form-control').removeClass('is-invalid');
    }

    function applyErrors(errors) {
        $.each(errors || {}, function (field, message) {
            if (message) {
                $('#labTestForm [name="' + field + '"]').addClass('is-invalid');
                $('#labTestForm .validation-message[data-field="' + field + '"]').text(message);
            }
        });
    }

    function badge(status) {
        return parseInt(status, 10) === 1
            ? '<span class="badge badge-success">Active</span>'
            : '<span class="badge badge-secondary">Inactive</span>';
    }

    function renderRows(rows) {
        var html = '';

        if (!rows || rows.length === 0) {
            $('#labTestsTableBody').html('<tr><td colspan="7" class="text-center">No lab tests found.</td></tr>');
            return;
        }

        $.each(rows, function (_, test) {
            var toggleText = parseInt(test.status, 10) === 1 ? 'Deactivate' : 'Activate';
            var toggleClass = parseInt(test.status, 10) === 1 ? 'btn-warning' : 'btn-success';

            html += '<tr>';
            html += '<td><strong>' + escapeHtml(test.test_code) + '</strong></td>';
            html += '<td>' + escapeHtml(test.test_name) + '</td>';
            html += '<td>' + escapeHtml(test.category) + '</td>';
            html += '<td>' + parseFloat(test.price).toFixed(2) + '</td>';
            html += '<td>' + (test.normal_range ? escapeHtml(test.normal_range) : '-') + '</td>';
            html += '<td>' + badge(test.status) + '</td>';
            html += '<td>';
            html += '<button class="btn btn-info btn-sm mr-1 btn-edit-test" data-id="' + escapeHtml(test.id) + '"><i class="fas fa-edit mr-1"></i>Edit</button>';
            html += '<button class="btn ' + toggleClass + ' btn-sm btn-toggle-test" data-id="' + escapeHtml(test.id) + '"><i class="fas fa-sync-alt mr-1"></i>' + toggleText + '</button>';
            html += '</td>';
            html += '</tr>';
        });

        $('#labTestsTableBody').html(html);
    }

    function loadTests() {
        $('#labTestsTableBody').html('<tr><td colspan="7" class="text-center">Loading lab tests...</td></tr>');

        $.ajax({
            url: listUrl,
            type: 'GET',
            dataType: 'json',
            data: {search: $('#testSearch').val(), status: $('#testStatusFilter').val()},
            success: function (response) {
                updateCsrf(response.csrfHash);
                response.status ? renderRows(response.data) : alertBox('danger', response.message || 'Unable to load lab tests.');
            },
            error: function () {
                alertBox('danger', 'Unable to load lab tests.');
            }
        });
    }

    function resetForm() {
        $('#labTestForm')[0].reset();
        $('#test_id').val('');
        $('#price').val('0.00');
        $('#test_status').val('1');
        editMode = false;
        clearErrors();
    }

    $('#addTestButton').on('click', function () {
        resetForm();
        $('#labTestModalTitle').text('Add Lab Test');
        $('#labTestModal').modal('show');
    });

    $('#labTestForm').on('submit', function (e) {
        e.preventDefault();
        clearErrors();

        var id = $('#test_id').val();
        var url = editMode ? updateBaseUrl + '/' + id : storeUrl;

        $('#saveTestButton').prop('disabled', true).text('Saving...');

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
            success: function (response) {
                updateCsrf(response.csrfHash);

                if (response.status) {
                    $('#labTestModal').modal('hide');
                    resetForm();
                    loadTests();
                    alertBox('success', response.message);
                    return;
                }

                applyErrors(response.errors);
                alertBox('danger', response.message || 'Unable to save lab test.');
            },
            error: function () {
                alertBox('danger', 'Unable to save lab test.');
            },
            complete: function () {
                $('#saveTestButton').prop('disabled', false).text('Save Test');
            }
        });
    });

    $(document).on('click', '.btn-edit-test', function () {
        resetForm();

        $.ajax({
            url: editBaseUrl + '/' + $(this).data('id'),
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                updateCsrf(response.csrfHash);

                if (!response.status) {
                    alertBox('danger', response.message || 'Lab test not found.');
                    return;
                }

                editMode = true;
                $('#labTestModalTitle').text('Edit Lab Test');
                $('#test_id').val(response.data.id);
                $('#test_name').val(response.data.test_name);
                $('#test_code').val(response.data.test_code);
                $('#category').val(response.data.category);
                $('#price').val(response.data.price);
                $('#normal_range').val(response.data.normal_range || '');
                $('#test_status').val(response.data.status);
                $('#labTestModal').modal('show');
            }
        });
    });

    $(document).on('click', '.btn-toggle-test', function () {
        if (!window.confirm('Change this lab test status?')) {
            return;
        }

        var payload = {};
        payload[csrfName] = csrfHash;

        $.ajax({
            url: statusBaseUrl + '/' + $(this).data('id'),
            type: 'POST',
            dataType: 'json',
            data: payload,
            success: function (response) {
                updateCsrf(response.csrfHash);
                response.status ? loadTests() : alertBox('danger', response.message || 'Unable to change status.');
                if (response.status) {
                    alertBox('success', response.message);
                }
            },
            error: function () {
                alertBox('danger', 'Unable to change status.');
            }
        });
    });

    $('#testSearch').on('keyup', function () {
        window.clearTimeout(searchTimer);
        searchTimer = window.setTimeout(loadTests, 300);
    });

    $('#testStatusFilter').on('change', loadTests);

    $('#clearTestFiltersButton').on('click', function () {
        $('#testSearch').val('');
        $('#testStatusFilter').val('');
        loadTests();
    });

    loadTests();
});
</script>
