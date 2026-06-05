<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box box-primary">
    <div class="box-header with-border"><h3 class="box-title">Generate Invoice</h3></div>
    <?php echo form_open('receptionist/billing/store'); ?>
        <div class="box-body">
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label>Appointment</label>
                        <select name="appointment_id" id="appointment-id" class="form-control" required>
                            <option value="">Select appointment</option>
                            <?php foreach ($appointments as $appointment): ?>
                                <option value="<?php echo (int) $appointment['id']; ?>">
                                    <?php echo html_escape($appointment['appointment_code'] . ' - ' . $appointment['patient_name'] . ' - ' . $appointment['doctor_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Invoice Date</label>
                        <input type="date" name="invoice_date" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Payment</label>
                        <select name="payment_status" class="form-control">
                            <option value="unpaid">Unpaid</option>
                            <option value="partial">Partial</option>
                            <option value="paid">Paid</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Method</label>
                        <select name="payment_method" class="form-control">
                            <option value="">None</option>
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="upi">UPI</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="invoice-preview" class="well well-sm text-muted">Select an appointment to preview charges.</div>
        </div>
        <div class="box-footer">
            <a href="<?php echo site_url('receptionist/billing'); ?>" class="btn btn-default">Cancel</a>
            <button type="submit" class="btn btn-success pull-right"><i class="fa fa-save"></i> Generate Invoice</button>
        </div>
    <?php echo form_close(); ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#appointment-id').on('change', function () {
        var id = $(this).val();
        if (!id) {
            $('#invoice-preview').html('Select an appointment to preview charges.');
            return;
        }

        $.get('<?php echo site_url('receptionist/billing/preview/'); ?>' + id, function (response) {
            if (!response.status) {
                $('#invoice-preview').html(response.message || 'Preview unavailable.');
                return;
            }

            var rows = response.data.items.map(function (item) {
                return '<div class="summary-line">' + item.item_name + '<span class="pull-right">' + item.line_total + '</span></div>';
            }).join('');
            $('#invoice-preview').html(rows + '<h4 class="text-right">Total: ' + response.data.total_amount + '</h4>');
        }, 'json');
    });
});
</script>
