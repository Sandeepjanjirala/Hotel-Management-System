<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">Prescription for <?php echo html_escape($appointment->patient_name); ?></h3>
    </div>
    <?php echo form_open('doctor/prescriptions/store'); ?>
        <div class="box-body">
            <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
            <input type="hidden" name="appointment_id" value="<?php echo (int) $appointment->appointment_id; ?>">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Diagnosis</label>
                        <input type="text" name="diagnosis" class="form-control" value="<?php echo set_value('diagnosis'); ?>" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Follow-up Date</label>
                        <input type="date" name="follow_up_date" class="form-control" value="<?php echo set_value('follow_up_date'); ?>">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" class="form-control" rows="3"><?php echo set_value('notes'); ?></textarea>
            </div>

            <div class="form-group">
                <label>Advice</label>
                <textarea name="advice" class="form-control" rows="2"><?php echo set_value('advice'); ?></textarea>
            </div>

            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Medication</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-xs btn-primary" id="add-medicine"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
                <div class="box-body table-responsive no-padding">
                    <table class="table table-bordered" id="medicine-table">
                        <thead>
                            <tr>
                                <th>Medicine</th>
                                <th>Dosage</th>
                                <th>Frequency</th>
                                <th>Duration</th>
                                <th>Instructions</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" name="items[0][medicine_name]" class="form-control input-sm"></td>
                                <td><input type="text" name="items[0][dosage]" class="form-control input-sm"></td>
                                <td><input type="text" name="items[0][frequency]" class="form-control input-sm"></td>
                                <td><input type="text" name="items[0][duration]" class="form-control input-sm"></td>
                                <td><input type="text" name="items[0][instructions]" class="form-control input-sm"></td>
                                <td><button type="button" class="btn btn-xs btn-danger remove-row"><i class="fa fa-trash"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="<?php echo site_url('doctor/appointments/view/' . (int) $appointment->appointment_id); ?>" class="btn btn-default">Cancel</a>
            <button type="submit" class="btn btn-success pull-right"><i class="fa fa-save"></i> Save Prescription</button>
        </div>
    <?php echo form_close(); ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var index = 1;
    $('#add-medicine').on('click', function () {
        var row = '<tr>' +
            '<td><input type="text" name="items[' + index + '][medicine_name]" class="form-control input-sm"></td>' +
            '<td><input type="text" name="items[' + index + '][dosage]" class="form-control input-sm"></td>' +
            '<td><input type="text" name="items[' + index + '][frequency]" class="form-control input-sm"></td>' +
            '<td><input type="text" name="items[' + index + '][duration]" class="form-control input-sm"></td>' +
            '<td><input type="text" name="items[' + index + '][instructions]" class="form-control input-sm"></td>' +
            '<td><button type="button" class="btn btn-xs btn-danger remove-row"><i class="fa fa-trash"></i></button></td>' +
            '</tr>';
        $('#medicine-table tbody').append(row);
        index += 1;
    });

    $('#medicine-table').on('click', '.remove-row', function () {
        if ($('#medicine-table tbody tr').length > 1) {
            $(this).closest('tr').remove();
        }
    });
});
</script>