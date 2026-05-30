<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box box-warning">
    <div class="box-header with-border">
        <h3 class="box-title">Lab Request for <?php echo html_escape($appointment->patient_name); ?></h3>
    </div>
    <?php echo form_open('doctor/lab-requests/store'); ?>
        <div class="box-body">
            <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
            <input type="hidden" name="appointment_id" value="<?php echo (int) $appointment->appointment_id; ?>">

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Priority</label>
                        <select name="priority" class="form-control" required>
                            <option value="routine" <?php echo set_select('priority', 'routine', true); ?>>Routine</option>
                            <option value="urgent" <?php echo set_select('priority', 'urgent'); ?>>Urgent</option>
                            <option value="stat" <?php echo set_select('priority', 'stat'); ?>>STAT</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Clinical Notes</label>
                <textarea name="clinical_notes" class="form-control" rows="3"><?php echo set_value('clinical_notes'); ?></textarea>
            </div>

            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Tests</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-xs btn-primary" id="add-test"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div id="test-list">
                        <div class="input-group" style="margin-bottom: 8px;">
                            <input type="text" name="tests[]" class="form-control" placeholder="Test name">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-danger remove-test"><i class="fa fa-trash"></i></button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="<?php echo site_url('doctor/appointments/view/' . (int) $appointment->appointment_id); ?>" class="btn btn-default">Cancel</a>
            <button type="submit" class="btn btn-warning pull-right"><i class="fa fa-save"></i> Save Lab Request</button>
        </div>
    <?php echo form_close(); ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#add-test').on('click', function () {
        $('#test-list').append(
            '<div class="input-group" style="margin-bottom: 8px;">' +
            '<input type="text" name="tests[]" class="form-control" placeholder="Test name">' +
            '<span class="input-group-btn">' +
            '<button type="button" class="btn btn-danger remove-test"><i class="fa fa-trash"></i></button>' +
            '</span>' +
            '</div>'
        );
    });

    $('#test-list').on('click', '.remove-test', function () {
        if ($('#test-list .input-group').length > 1) {
            $(this).closest('.input-group').remove();
        }
    });
});
</script>