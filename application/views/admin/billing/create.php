<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6"><h1>Generate Invoice</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin/billing'); ?>">Billing</a></li>
                        <li class="breadcrumb-item active">Generate</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div id="invoiceAlert"></div>

            <form id="invoiceForm" autocomplete="off">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title mb-0"><i class="fas fa-file-invoice-dollar mr-1"></i> Invoice Setup</h3>
                        <div class="card-tools">
                            <a href="<?php echo site_url('admin/billing'); ?>" class="btn btn-default btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Back
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="form-group">
                            <label>Appointment <span class="text-danger">*</span></label>
                            <select class="form-control" id="appointment_id" name="appointment_id">
                                <option value="">Select Appointment</option>
                                <?php foreach ($appointments as $appointment): ?>
                                    <option value="<?php echo (int) $appointment['id']; ?>" <?php echo ((int) $selected_appointment_id === (int) $appointment['id']) ? 'selected' : ''; ?>>
                                        <?php echo html_escape($appointment['appointment_code'] . ' - ' . $appointment['patient_name'] . ' with Dr. ' . $appointment['doctor_name'] . ' (' . $appointment['appointment_date'] . ' ' . $appointment['appointment_time'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-md-0">
                                    <label>Invoice Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="invoice_date" value="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-md-0">
                                    <label>Payment Status <span class="text-danger">*</span></label>
                                    <select class="form-control" id="payment_status" name="payment_status">
                                        <option value="unpaid">Unpaid</option>
                                        <option value="partial">Partial</option>
                                        <option value="paid">Paid</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label>Payment Method</label>
                                    <select class="form-control" id="payment_method" name="payment_method" disabled>
                                        <option value="">Select Method</option>
                                        <option value="cash">Cash</option>
                                        <option value="card">Card</option>
                                        <option value="upi">UPI</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="invoicePreviewBox" class="card d-none">
                    <div class="card-header">
                        <h3 class="card-title mb-0"><i class="fas fa-receipt mr-1"></i> Invoice Preview</h3>
                    </div>

                    <div class="card-body">
                        <div id="appointmentSummary" class="alert alert-light border"></div>

                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Item</th>
                                        <th style="width: 100px;">Qty</th>
                                        <th style="width: 140px;">Unit Price</th>
                                        <th style="width: 140px;">Line Total</th>
                                    </tr>
                                </thead>
                                <tbody id="invoiceItemsBody"></tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-right">Consultation Fee</th>
                                        <th id="consultationTotal">0.00</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-right">Pharmacy Charges</th>
                                        <th id="pharmacyTotal">0.00</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-right">Lab Charges</th>
                                        <th id="labTotal">0.00</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-right h5">Grand Total</th>
                                        <th id="grandTotal" class="h5">0.00</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <a href="<?php echo site_url('admin/billing'); ?>" class="btn btn-default">Cancel</a>
                        <button type="submit" id="saveInvoiceButton" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Generate Invoice
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
    var previewBaseUrl = '<?php echo site_url('admin/billing/preview'); ?>';
    var storeUrl = '<?php echo site_url('admin/billing/store'); ?>';

    function updateCsrf(hash) {
        if (hash) {
            csrfHash = hash;
            $('input[name="' + csrfName + '"]').val(hash);
        }
    }

    function escapeHtml(value) {
        return $('<div>').text(value === null || value === undefined ? '' : value).html();
    }

    function money(value) {
        return parseFloat(value || 0).toFixed(2);
    }

    function showAlert(type, message) {
        $('#invoiceAlert').html(
            '<div class="alert alert-' + (type === 'success' ? 'success' : 'danger') + ' alert-dismissible fade show">' +
            escapeHtml(message) +
            '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>'
        );
    }

    function renderPreview(preview) {
        var appointment = preview.appointment;
        var html = '';

        $('#appointmentSummary').html(
            '<div class="row">' +
            '<div class="col-md-4"><strong>Patient:</strong> ' + escapeHtml(appointment.patient_name) + '<br><small class="text-muted">' + escapeHtml(appointment.patient_code) + ' | ' + escapeHtml(appointment.patient_phone) + '</small></div>' +
            '<div class="col-md-4"><strong>Doctor:</strong> ' + escapeHtml(appointment.doctor_name) + '<br><small class="text-muted">' + escapeHtml(appointment.department_name || '-') + '</small></div>' +
            '<div class="col-md-4"><strong>Appointment:</strong> ' + escapeHtml(appointment.appointment_code) + '<br><small class="text-muted">' + escapeHtml(appointment.appointment_date + ' ' + appointment.appointment_time) + '</small></div>' +
            '</div>'
        );

        $.each(preview.items, function (_, item) {
            html += '<tr>';
            html += '<td><span class="badge badge-info">' + escapeHtml(item.item_type) + '</span></td>';
            html += '<td>' + escapeHtml(item.item_name) + '</td>';
            html += '<td>' + escapeHtml(item.quantity) + '</td>';
            html += '<td>' + money(item.unit_price) + '</td>';
            html += '<td>' + money(item.line_total) + '</td>';
            html += '</tr>';
        });

        $('#invoiceItemsBody').html(html);
        $('#consultationTotal').text(money(preview.consultation_fee));
        $('#pharmacyTotal').text(money(preview.pharmacy_total));
        $('#labTotal').text(money(preview.lab_total));
        $('#grandTotal').text(money(preview.total_amount));
        $('#invoicePreviewBox').removeClass('d-none');
    }

    function resetPreview() {
        $('#invoicePreviewBox').addClass('d-none');
        $('#appointmentSummary').html('');
        $('#invoiceItemsBody').html('');
        $('#consultationTotal').text('0.00');
        $('#pharmacyTotal').text('0.00');
        $('#labTotal').text('0.00');
        $('#grandTotal').text('0.00');
    }

    function loadPreview(appointmentId) {
        resetPreview();

        if (!appointmentId) {
            return;
        }

        $.ajax({
            url: previewBaseUrl + '/' + appointmentId,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                updateCsrf(response.csrfHash);

                if (response.status) {
                    renderPreview(response.data);
                    return;
                }

                showAlert('danger', response.message || 'Unable to load invoice preview.');
            },
            error: function () {
                showAlert('danger', 'Unable to load invoice preview.');
            }
        });
    }

    $('#appointment_id').on('change', function () {
        loadPreview($(this).val());
    });

    $('#payment_status').on('change', function () {
        if ($(this).val() === 'unpaid') {
            $('#payment_method').prop('disabled', true).val('');
            return;
        }

        $('#payment_method').prop('disabled', false);
    });

    $('#invoiceForm').on('submit', function (event) {
        event.preventDefault();

        var $button = $('#saveInvoiceButton');
        var originalText = $button.text();

        $button.prop('disabled', true).text('Saving...');

        $.ajax({
            url: storeUrl,
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
            success: function (response) {
                updateCsrf(response.csrfHash);

                if (response.status) {
                    showAlert('success', response.message);
                    window.setTimeout(function () {
                        window.location.href = response.redirectUrl;
                    }, 700);
                    return;
                }

                showAlert('danger', response.message || 'Unable to generate invoice.');
                $button.prop('disabled', false).text(originalText);
            },
            error: function () {
                showAlert('danger', 'Unable to generate invoice.');
                $button.prop('disabled', false).text(originalText);
            }
        });
    });

    if ($('#appointment_id').val()) {
        loadPreview($('#appointment_id').val());
    }
});
</script>
