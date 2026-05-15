<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Medicine Master</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Medicines</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div id="medicineAlert"></div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-capsules mr-1"></i> Medicine List
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" id="addMedicineButton">
                            <i class="fas fa-plus mr-1"></i> Add Medicine
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-lg-5 col-md-6 col-12 mb-2 mb-lg-0">
                            <input type="text" id="medicineSearch" class="form-control" placeholder="Search medicine, generic name or unit">
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 mb-2 mb-lg-0">
                            <select id="statusFilter" class="form-control">
                                <option value="">All Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-3 col-6">
                            <button type="button" id="clearFiltersButton" class="btn btn-default btn-block">
                                <i class="fas fa-times mr-1"></i> Clear
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Medicine Name</th>
                                    <th>Generic Name</th>
                                    <th style="width: 100px;">Unit</th>
                                    <th style="width: 140px;">Stock Qty</th>
                                    <th style="width: 130px;">Unit Price</th>
                                    <th style="width: 110px;">Status</th>
                                    <th style="width: 180px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="medicinesTableBody">
                                <tr>
                                    <td colspan="7" class="text-center">Loading medicines...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="addMedicineModal" tabindex="-1" role="dialog" aria-labelledby="addMedicineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="addMedicineForm" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMedicineModalLabel">Add Medicine</h5>
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
                                <label for="add_medicine_name">Medicine Name</label>
                                <input type="text" class="form-control" id="add_medicine_name" name="medicine_name" maxlength="150">
                                <small class="text-danger validation-message" data-field="medicine_name"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add_generic_name">Generic Name</label>
                                <input type="text" class="form-control" id="add_generic_name" name="generic_name" maxlength="150">
                                <small class="text-danger validation-message" data-field="generic_name"></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="add_unit">Unit</label>
                                <input type="text" class="form-control" id="add_unit" name="unit" maxlength="50" placeholder="Tablet, Syrup, Vial">
                                <small class="text-danger validation-message" data-field="unit"></small>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="add_stock_quantity">Stock Quantity</label>
                                <input type="number" min="0" step="1" class="form-control" id="add_stock_quantity" name="stock_quantity" value="0">
                                <small class="text-danger validation-message" data-field="stock_quantity"></small>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="add_unit_price">Unit Price</label>
                                <input type="number" min="0" step="0.01" class="form-control" id="add_unit_price" name="unit_price" value="0.00">
                                <small class="text-danger validation-message" data-field="unit_price"></small>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="add_status">Status</label>
                                <select class="form-control" id="add_status" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <small class="text-danger validation-message" data-field="status"></small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label for="add_description">Description</label>
                        <textarea class="form-control" id="add_description" name="description" rows="3"></textarea>
                        <small class="text-danger validation-message" data-field="description"></small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveMedicineButton">Save Medicine</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editMedicineModal" tabindex="-1" role="dialog" aria-labelledby="editMedicineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="editMedicineForm" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMedicineModalLabel">Edit Medicine</h5>
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
                    <input type="hidden" id="edit_medicine_id" name="medicine_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_medicine_name">Medicine Name</label>
                                <input type="text" class="form-control" id="edit_medicine_name" name="medicine_name" maxlength="150">
                                <small class="text-danger validation-message" data-field="medicine_name"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_generic_name">Generic Name</label>
                                <input type="text" class="form-control" id="edit_generic_name" name="generic_name" maxlength="150">
                                <small class="text-danger validation-message" data-field="generic_name"></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="edit_unit">Unit</label>
                                <input type="text" class="form-control" id="edit_unit" name="unit" maxlength="50">
                                <small class="text-danger validation-message" data-field="unit"></small>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="edit_stock_quantity">Stock Quantity</label>
                                <input type="number" min="0" step="1" class="form-control" id="edit_stock_quantity" name="stock_quantity">
                                <small class="text-danger validation-message" data-field="stock_quantity"></small>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="edit_unit_price">Unit Price</label>
                                <input type="number" min="0" step="0.01" class="form-control" id="edit_unit_price" name="unit_price">
                                <small class="text-danger validation-message" data-field="unit_price"></small>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="edit_status">Status</label>
                                <select class="form-control" id="edit_status" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <small class="text-danger validation-message" data-field="status"></small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label for="edit_description">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        <small class="text-danger validation-message" data-field="description"></small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="updateMedicineButton">Update Medicine</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

    var listUrl = '<?php echo site_url('admin/medicines/ajax-list'); ?>';
    var storeUrl = '<?php echo site_url('admin/medicines/store'); ?>';
    var editBaseUrl = '<?php echo site_url('admin/medicines/edit'); ?>';
    var updateBaseUrl = '<?php echo site_url('admin/medicines/update'); ?>';
    var changeStatusBaseUrl = '<?php echo site_url('admin/medicines/change-status'); ?>';

    var searchTimer = null;

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

        $('#medicineAlert').html(html);
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

    function renderStockBadge(quantity) {
        var qty = parseInt(quantity, 10);

        if (qty <= 0) {
            return '<span class="badge badge-danger">' + qty + '</span>';
        }

        return '<span class="badge badge-info">' + qty + '</span>';
    }

    function renderRows(rows) {
        var html = '';

        if (!rows || rows.length === 0) {
            $('#medicinesTableBody').html('<tr><td colspan="7" class="text-center">No medicines found.</td></tr>');
            return;
        }

        $.each(rows, function (index, medicine) {
            var toggleText = parseInt(medicine.status, 10) === 1 ? 'Deactivate' : 'Activate';
            var toggleClass = parseInt(medicine.status, 10) === 1 ? 'btn-warning' : 'btn-success';

            html += '<tr>';
            html += '<td><strong>' + escapeHtml(medicine.medicine_name) + '</strong></td>';
            html += '<td>' + (medicine.generic_name ? escapeHtml(medicine.generic_name) : '-') + '</td>';
            html += '<td>' + escapeHtml(medicine.unit) + '</td>';
            html += '<td>' + renderStockBadge(medicine.stock_quantity) + '</td>';
            html += '<td>' + escapeHtml(parseFloat(medicine.unit_price).toFixed(2)) + '</td>';
            html += '<td>' + renderStatusBadge(medicine.status) + '</td>';
            html += '<td>';
            html += '<button type="button" class="btn btn-info btn-sm mr-1 btn-edit-medicine" data-id="' + escapeHtml(medicine.id) + '">';
            html += '<i class="fas fa-edit mr-1"></i>Edit';
            html += '</button>';
            html += '<button type="button" class="btn ' + toggleClass + ' btn-sm btn-toggle-status" data-id="' + escapeHtml(medicine.id) + '">';
            html += '<i class="fas fa-sync-alt mr-1"></i>' + toggleText;
            html += '</button>';
            html += '</td>';
            html += '</tr>';
        });

        $('#medicinesTableBody').html(html);
    }

    function loadMedicines() {
        $('#medicinesTableBody').html('<tr><td colspan="7" class="text-center">Loading medicines...</td></tr>');

        $.ajax({
            url: listUrl,
            type: 'GET',
            dataType: 'json',
            data: {
                search: $('#medicineSearch').val(),
                status: $('#statusFilter').val()
            },
            success: function (response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    renderRows(response.data);
                    return;
                }

                renderRows([]);
                showAlert('danger', response.message || 'Unable to load medicines.');
            },
            error: function () {
                renderRows([]);
                showAlert('danger', 'Unable to load medicines.');
            }
        });
    }

    function resetAddForm() {
        $('#addMedicineForm')[0].reset();
        $('#add_stock_quantity').val('0');
        $('#add_unit_price').val('0.00');
        clearValidationErrors('#addMedicineForm');
    }

    function resetEditForm() {
        $('#editMedicineForm')[0].reset();
        $('#edit_medicine_id').val('');
        clearValidationErrors('#editMedicineForm');
    }

    $('#addMedicineButton').on('click', function () {
        resetAddForm();
        $('#addMedicineModal').modal('show');
    });

    $('#medicineSearch').on('keyup', function () {
        window.clearTimeout(searchTimer);

        searchTimer = window.setTimeout(function () {
            loadMedicines();
        }, 300);
    });

    $('#statusFilter').on('change', function () {
        loadMedicines();
    });

    $('#clearFiltersButton').on('click', function () {
        $('#medicineSearch').val('');
        $('#statusFilter').val('');
        loadMedicines();
    });

    $('#addMedicineForm').on('submit', function (event) {
        event.preventDefault();

        clearValidationErrors('#addMedicineForm');

        var $button = $('#saveMedicineButton');
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
                    $('#addMedicineModal').modal('hide');
                    resetAddForm();
                    loadMedicines();
                    showAlert('success', response.message);
                    return;
                }

                applyValidationErrors('#addMedicineForm', response.errors);
                showAlert('danger', response.message || 'Unable to create medicine.');
            },
            error: function () {
                showAlert('danger', 'Unable to create medicine.');
            },
            complete: function () {
                $button.prop('disabled', false).text(originalText);
            }
        });
    });

    $(document).on('click', '.btn-edit-medicine', function () {
        var medicineId = $(this).data('id');

        resetEditForm();

        $.ajax({
            url: editBaseUrl + '/' + medicineId,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                updateCsrfToken(response.csrfHash);

                if (!response.status) {
                    showAlert('danger', response.message || 'Medicine not found.');
                    return;
                }

                $('#edit_medicine_id').val(response.data.id);
                $('#edit_medicine_name').val(response.data.medicine_name);
                $('#edit_generic_name').val(response.data.generic_name || '');
                $('#edit_unit').val(response.data.unit);
                $('#edit_stock_quantity').val(response.data.stock_quantity);
                $('#edit_unit_price').val(response.data.unit_price);
                $('#edit_description').val(response.data.description || '');
                $('#edit_status').val(response.data.status);

                $('#editMedicineModal').modal('show');
            },
            error: function () {
                showAlert('danger', 'Unable to fetch medicine details.');
            }
        });
    });

    $('#editMedicineForm').on('submit', function (event) {
        event.preventDefault();

        clearValidationErrors('#editMedicineForm');

        var medicineId = $('#edit_medicine_id').val();
        var $button = $('#updateMedicineButton');
        var originalText = $button.text();

        $button.prop('disabled', true).text('Updating...');

        $.ajax({
            url: updateBaseUrl + '/' + medicineId,
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
            success: function (response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    $('#editMedicineModal').modal('hide');
                    resetEditForm();
                    loadMedicines();
                    showAlert('success', response.message);
                    return;
                }

                applyValidationErrors('#editMedicineForm', response.errors);
                showAlert('danger', response.message || 'Unable to update medicine.');
            },
            error: function () {
                showAlert('danger', 'Unable to update medicine.');
            },
            complete: function () {
                $button.prop('disabled', false).text(originalText);
            }
        });
    });

    $(document).on('click', '.btn-toggle-status', function () {
        var medicineId = $(this).data('id');

        if (!window.confirm('Are you sure you want to change this medicine status?')) {
            return;
        }

        var payload = {};
        payload[csrfName] = csrfHash;

        $.ajax({
            url: changeStatusBaseUrl + '/' + medicineId,
            type: 'POST',
            dataType: 'json',
            data: payload,
            success: function (response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    loadMedicines();
                    showAlert('success', response.message);
                    return;
                }

                showAlert('danger', response.message || 'Unable to change medicine status.');
            },
            error: function () {
                showAlert('danger', 'Unable to change medicine status.');
            }
        });
    });

    $('#addMedicineModal').on('hidden.bs.modal', function () {
        resetAddForm();
    });

    $('#editMedicineModal').on('hidden.bs.modal', function () {
        resetEditForm();
    });

    loadMedicines();
});
</script>
