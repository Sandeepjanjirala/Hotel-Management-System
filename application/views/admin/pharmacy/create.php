<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Generate Pharmacy Bill</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin/pharmacy-billing'); ?>">Pharmacy Billing</a></li>
                        <li class="breadcrumb-item active">Generate</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div id="billingAlert"></div>

            <form id="pharmacyBillForm" autocomplete="off">
                <input
                    type="hidden"
                    name="<?php echo $this->security->get_csrf_token_name(); ?>"
                    value="<?php echo $this->security->get_csrf_hash(); ?>"
                >

                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-file-medical-alt mr-1"></i> Prescription Selection
                        </h3>
                        <div class="card-tools">
                            <a href="<?php echo site_url('admin/pharmacy-billing'); ?>" class="btn btn-default btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Back
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="form-group">
                            <label for="prescription_id">Prescription <span class="text-danger">*</span></label>
                            <select class="form-control" id="prescription_id" name="prescription_id">
                                <option value="">Select Prescription</option>
                                <?php foreach ($prescriptions as $prescription): ?>
                                    <option value="<?php echo (int) $prescription['id']; ?>" <?php echo ((int) $selected_prescription_id === (int) $prescription['id']) ? 'selected' : ''; ?>>
                                        <?php echo html_escape($prescription['appointment_code'] . ' - ' . $prescription['patient_name'] . ' with Dr. ' . $prescription['doctor_name'] . ' (' . $prescription['prescription_date'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group mb-0">
                            <label for="bill_date">Bill Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control col-md-3" id="bill_date" name="bill_date" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                </div>

                <div id="prescriptionDetailsBox" class="card d-none">
                    <div class="card-header">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-prescription mr-1"></i> Prescription Details
                        </h3>
                    </div>
                    <div class="card-body" id="prescriptionDetailsContent"></div>
                </div>

                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-capsules mr-1"></i> Medicines For Billing
                        </h3>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Medicine</th>
                                        <th>Dosage</th>
                                        <th>Days</th>
                                        <th>Schedule</th>
                                        <th>Available Stock</th>
                                        <th style="width: 140px;">Quantity</th>
                                        <th style="width: 130px;">Unit Price</th>
                                        <th style="width: 140px;">Line Total</th>
                                    </tr>
                                </thead>
                                <tbody id="billItemsTableBody">
                                    <tr>
                                        <td colspan="8" class="text-center">Select a prescription to load medicines.</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="7" class="text-right">Total Amount</th>
                                        <th id="grandTotal">0.00</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <a href="<?php echo site_url('admin/pharmacy-billing'); ?>" class="btn btn-default">Cancel</a>
                        <button type="submit" class="btn btn-primary" id="saveBillButton" disabled>
                            <i class="fas fa-save mr-1"></i> Save Pharmacy Bill
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var prescriptionBaseUrl = '<?php echo site_url('admin/pharmacy-billing/prescription'); ?>';
    var storeUrl = '<?php echo site_url('admin/pharmacy-billing/store'); ?>';

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

    function money(value) {
        return parseFloat(value || 0).toFixed(2);
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

        $('#billingAlert').html(html);
    }

    function scheduleText(item) {
        var parts = [];

        if (parseInt(item.morning, 10) === 1) {
            parts.push('Morning');
        }

        if (parseInt(item.afternoon, 10) === 1) {
            parts.push('Afternoon');
        }

        if (parseInt(item.night, 10) === 1) {
            parts.push('Night');
        }

        return parts.length ? parts.join(', ') : '-';
    }

    function renderPrescriptionDetails(prescription) {
        var html = ''
            + '<div class="row">'
            + '<div class="col-md-4">'
            + '<strong>Patient:</strong> ' + escapeHtml(prescription.patient_name)
            + '<br><small class="text-muted">' + escapeHtml(prescription.patient_code) + ' | ' + escapeHtml(prescription.patient_phone) + '</small>'
            + '</div>'
            + '<div class="col-md-4">'
            + '<strong>Doctor:</strong> ' + escapeHtml(prescription.doctor_name)
            + '<br><small class="text-muted">Prescription Date: ' + escapeHtml(prescription.prescription_date) + '</small>'
            + '</div>'
            + '<div class="col-md-4">'
            + '<strong>Appointment:</strong> ' + escapeHtml(prescription.appointment_code)
            + '<br><small class="text-muted">' + escapeHtml(prescription.appointment_date + ' ' + prescription.appointment_time) + '</small>'
            + '</div>'
            + '</div>'
            + '<hr>'
            + '<strong>Diagnosis:</strong> ' + escapeHtml(prescription.diagnosis);

        $('#prescriptionDetailsContent').html(html);
        $('#prescriptionDetailsBox').removeClass('d-none');
    }

    function renderItems(items) {
        var html = '';

        if (!items || items.length === 0) {
            $('#billItemsTableBody').html('<tr><td colspan="8" class="text-center">No active medicines found for this prescription.</td></tr>');
            $('#saveBillButton').prop('disabled', true);
            calculateTotal();
            return;
        }

        $.each(items, function (index, item) {
            var suggestedQuantity = parseInt(item.suggested_quantity, 10);
            var availableStock = parseInt(item.stock_quantity, 10);
            var quantity = suggestedQuantity;

            if (quantity > availableStock) {
                quantity = availableStock;
            }

            if (quantity < 1) {
                quantity = 1;
            }

            html += '<tr>';
            html += '<td>';
            html += '<strong>' + escapeHtml(item.medicine_name) + '</strong>';
            html += '<br><small class="text-muted">' + escapeHtml(item.unit) + '</small>';
            html += '<input type="hidden" name="items[' + index + '][prescription_item_id]" value="' + escapeHtml(item.prescription_item_id) + '">';
            html += '</td>';
            html += '<td>' + escapeHtml(item.dosage) + '</td>';
            html += '<td>' + escapeHtml(item.days) + '</td>';
            html += '<td>' + escapeHtml(scheduleText(item)) + '</td>';
            html += '<td><span class="badge badge-info">' + escapeHtml(availableStock) + '</span></td>';
            html += '<td>';
            html += '<input type="number" min="1" step="1" class="form-control bill-quantity" name="items[' + index + '][quantity]" value="' + escapeHtml(quantity) + '" data-stock="' + escapeHtml(availableStock) + '" data-price="' + escapeHtml(item.unit_price) + '">';
            html += '</td>';
            html += '<td>' + money(item.unit_price) + '</td>';
            html += '<td class="line-total">' + money(quantity * parseFloat(item.unit_price)) + '</td>';
            html += '</tr>';
        });

        $('#billItemsTableBody').html(html);
        $('#saveBillButton').prop('disabled', false);
        calculateTotal();
    }

    function calculateTotal() {
        var total = 0;

        $('.bill-quantity').each(function () {
            var qty = parseInt($(this).val(), 10);
            var stock = parseInt($(this).data('stock'), 10);
            var price = parseFloat($(this).data('price'));

            if (isNaN(qty) || qty < 1) {
                qty = 1;
                $(this).val(qty);
            }

            if (qty > stock) {
                qty = stock;
                $(this).val(qty);
                showAlert('danger', 'Quantity cannot exceed available stock.');
            }

            var lineTotal = qty * price;

            $(this).closest('tr').find('.line-total').text(money(lineTotal));
            total += lineTotal;
        });

        $('#grandTotal').text(money(total));
    }

    function resetBillingForm() {
        $('#prescriptionDetailsBox').addClass('d-none');
        $('#prescriptionDetailsContent').html('');
        $('#billItemsTableBody').html('<tr><td colspan="8" class="text-center">Select a prescription to load medicines.</td></tr>');
        $('#grandTotal').text('0.00');
        $('#saveBillButton').prop('disabled', true);
    }

    function loadPrescription(prescriptionId) {
        resetBillingForm();

        if (!prescriptionId) {
            return;
        }

        $('#billItemsTableBody').html('<tr><td colspan="8" class="text-center">Loading prescription medicines...</td></tr>');

        $.ajax({
            url: prescriptionBaseUrl + '/' + prescriptionId,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    renderPrescriptionDetails(response.prescription);
                    renderItems(response.items);
                    return;
                }

                resetBillingForm();
                showAlert('danger', response.message || 'Unable to load prescription.');
            },
            error: function () {
                resetBillingForm();
                showAlert('danger', 'Unable to load prescription.');
            }
        });
    }

    $('#prescription_id').on('change', function () {
        loadPrescription($(this).val());
    });

    $(document).on('input', '.bill-quantity', function () {
        calculateTotal();
    });

    $('#pharmacyBillForm').on('submit', function (event) {
        event.preventDefault();

        var $button = $('#saveBillButton');
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
                    showAlert('success', response.message);

                    window.setTimeout(function () {
                        window.location.href = response.redirectUrl;
                    }, 700);

                    return;
                }

                showAlert('danger', response.message || 'Unable to save pharmacy bill.');
                $button.prop('disabled', false).text(originalText);
            },
            error: function () {
                showAlert('danger', 'Unable to save pharmacy bill.');
                $button.prop('disabled', false).text(originalText);
            }
        });
    });

    if ($('#prescription_id').val()) {
        loadPrescription($('#prescription_id').val());
    }
});
</script>
