/**
 * Medicine Management Module
 * Handles all medicine CRUD operations via AJAX
 */

var Medicines = (function() {
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
        loadMedicines();
    }

    function bindEvents() {
        $('#addMedicineButton').on('click', function() {
            resetAddForm();
            clearAlert();
            $('#addMedicineModal').modal('show');
        });

        $('#addMedicineForm').on('submit', function(event) {
            event.preventDefault();
            saveMedicine();
        });

        $(document).on('click', '.btn-edit-medicine', function() {
            var medicineId = $(this).data('id');
            editMedicine(medicineId);
        });

        $('#editMedicineForm').on('submit', function(event) {
            event.preventDefault();
            updateMedicine();
        });

        $(document).on('click', '.btn-toggle-status', function() {
            var medicineId = $(this).data('id');
            toggleMedicineStatus(medicineId);
        });

        $('#medicineSearch').on('keyup', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                loadMedicines();
            }, 300);
        });

        $('#statusFilter').on('change', function() {
            loadMedicines();
        });

        $('#clearFiltersButton').on('click', function() {
            $('#medicineSearch').val('');
            $('#statusFilter').val('');
            loadMedicines();
        });

        $('#addMedicineModal').on('hidden.bs.modal', function() {
            resetAddForm();
        });

        $('#editMedicineModal').on('hidden.bs.modal', function() {
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

        $('#medicineAlert').html(html);

        alertTimeout = setTimeout(function() {
            clearAlert();
        }, 5000);
    }

    function clearAlert() {
        if (alertTimeout) {
            clearTimeout(alertTimeout);
            alertTimeout = null;
        }
        $('#medicineAlert').html('');
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
            html = '<tr><td colspan="7" class="text-center">No medicines found.</td></tr>';
            $('#medicinesTableBody').html(html);
            return;
        }

        $.each(rows, function(index, medicine) {
            var toggleText = parseInt(medicine.status, 10) === 1 ? 'Deactivate' : 'Activate';
            var toggleClass = parseInt(medicine.status, 10) === 1 ? 'btn-warning' : 'btn-success';

            html += '<tr>';
            html += '<td><strong>' + escapeHtml(medicine.medicine_name) + '</strong></td>';
            html += '<td>' + (medicine.generic_name ? escapeHtml(medicine.generic_name) : '-') + '</td>';
            html += '<td>' + escapeHtml(medicine.unit) + '</td>';
            html += '<td>' + renderStockBadge(medicine.stock_quantity) + '</td>';
            html += '<td>₹' + escapeHtml(parseFloat(medicine.unit_price).toFixed(2)) + '</td>';
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
            success: function(response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    renderRows(response.data);
                    return;
                }

                renderRows([]);
                showAlert('danger', response.message || 'Unable to load medicines.');
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                renderRows([]);
                showAlert('danger', 'Unable to load medicines. Please check console for details.');
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

    function saveMedicine() {
        clearValidationErrors('#addMedicineForm');
        clearAlert();

        var $button = $('#saveMedicineButton');
        var originalText = $button.text();

        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        $.ajax({
            url: storeUrl,
            type: 'POST',
            dataType: 'json',
            data: $('#addMedicineForm').serialize(),
            success: function(response) {
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
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                showAlert('danger', 'Unable to create medicine. Please check console for details.');
            },
            complete: function() {
                $button.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save Medicine');
            }
        });
    }

    function editMedicine(medicineId) {
        resetEditForm();
        clearAlert();

        $.ajax({
            url: editBaseUrl + '/' + medicineId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
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
            error: function() {
                showAlert('danger', 'Unable to fetch medicine details.');
            }
        });
    }

    function updateMedicine() {
        clearValidationErrors('#editMedicineForm');
        clearAlert();

        var medicineId = $('#edit_medicine_id').val();
        var $button = $('#updateMedicineButton');
        var originalText = $button.text();

        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Updating...');

        $.ajax({
            url: updateBaseUrl + '/' + medicineId,
            type: 'POST',
            dataType: 'json',
            data: $('#editMedicineForm').serialize(),
            success: function(response) {
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
            error: function() {
                showAlert('danger', 'Unable to update medicine.');
            },
            complete: function() {
                $button.prop('disabled', false).html('<i class="fas fa-edit mr-1"></i> Update Medicine');
            }
        });
    }

    function toggleMedicineStatus(medicineId) {
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
            success: function(response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    loadMedicines();
                    showAlert('success', response.message);
                    return;
                }

                showAlert('danger', response.message || 'Unable to change medicine status.');
            },
            error: function() {
                showAlert('danger', 'Unable to change medicine status.');
            }
        });
    }

    // Public API
    return {
        init: init,
        loadMedicines: loadMedicines,
        clearAlert: clearAlert,
        showAlert: showAlert
    };

})();