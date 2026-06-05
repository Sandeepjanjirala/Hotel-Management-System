<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box box-primary">
    <div class="box-header with-border"><h3 class="box-title">Generate Pharmacy Bill</h3></div>
    <?php echo form_open('receptionist/pharmacy-billing/store'); ?>
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Prescription</label>
                        <select name="prescription_id" id="prescription-id" class="form-control" required>
                            <option value="">Select prescription</option>
                            <?php foreach ($prescriptions as $prescription): ?>
                                <option value="<?php echo (int) $prescription['id']; ?>">
                                    <?php echo html_escape($prescription['appointment_code'] . ' - ' . $prescription['patient_name'] . ' - ' . $prescription['doctor_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Bill Date</label>
                        <input type="date" name="bill_date" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
            </div>

            <div id="prescription-items" class="well well-sm text-muted">Select a prescription to load medicines.</div>
        </div>
        <div class="box-footer">
            <a href="<?php echo site_url('receptionist/pharmacy-billing'); ?>" class="btn btn-default">Cancel</a>
            <button type="submit" class="btn btn-success pull-right"><i class="fa fa-save"></i> Generate Pharmacy Bill</button>
        </div>
    <?php echo form_close(); ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#prescription-id').on('change', function () {
        var id = $(this).val();
        if (!id) {
            $('#prescription-items').html('Select a prescription to load medicines.');
            return;
        }

        $.get('<?php echo site_url('receptionist/pharmacy-billing/prescription/'); ?>' + id, function (response) {
            if (!response.status || response.items.length === 0) {
                $('#prescription-items').html(response.message || 'No billable medicines found.');
                return;
            }

            var rows = '<table class="table table-bordered"><thead><tr><th>Medicine</th><th>Stock</th><th>Suggested</th><th>Issue Qty</th></tr></thead><tbody>';
            response.items.forEach(function (item, index) {
                rows += '<tr>' +
                    '<td>' + item.medicine_name + '<input type="hidden" name="items[' + index + '][prescription_item_id]" value="' + item.prescription_item_id + '"></td>' +
                    '<td>' + item.stock_quantity + '</td>' +
                    '<td>' + item.suggested_quantity + '</td>' +
                    '<td><input type="number" min="0" name="items[' + index + '][quantity]" class="form-control input-sm" value="' + item.suggested_quantity + '"></td>' +
                    '</tr>';
            });
            rows += '</tbody></table>';
            $('#prescription-items').html(rows);
        }, 'json');
    });
});
</script>
