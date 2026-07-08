<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
$is_edit = isset($appointment) && !empty($appointment);
$action = $is_edit ? site_url('admin/appointments/update/' . (int) $appointment['id']) : site_url('admin/appointments/store');
$form_id = $is_edit ? 'editAppointmentForm' : 'createAppointmentForm';
$is_modal = isset($is_modal) && $is_modal;
?>

<?php echo form_open($action, array('autocomplete' => 'off', 'id' => $form_id)); ?>
    <div class="card-body">
        <?php if ($is_edit): ?>
            <div class="alert alert-light border">
                Appointment Code:
                <strong><?php echo html_escape($appointment['appointment_code']); ?></strong>
            </div>
        <?php else: ?>
            <div class="alert alert-light border">
                Appointment code will be generated automatically. Next expected code:
                <strong><?php echo html_escape($next_appointment_code); ?></strong>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="patient_id">Patient <span class="text-danger">*</span></label>
                    <select class="form-control" id="patient_id" name="patient_id">
                        <option value="">Select Patient</option>
                        <?php foreach ($patients as $patient): ?>
                            <option value="<?php echo (int) $patient['id']; ?>" <?php echo set_select('patient_id', (string) $patient['id'], $is_edit ? (int) $appointment['patient_id'] === (int) $patient['id'] : FALSE); ?>>
                                <?php echo html_escape($patient['patient_code'] . ' - ' . $patient['full_name'] . ' (' . $patient['phone'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php echo form_error('patient_id'); ?>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="doctor_id">Doctor <span class="text-danger">*</span></label>
                    <select class="form-control" id="doctor_id" name="doctor_id">
                        <option value="">Select Doctor</option>
                        <?php foreach ($doctors as $doctor): ?>
                            <?php
                                $department = !empty($doctor['department_name']) ? ' - ' . $doctor['department_name'] : '';
                            ?>
                            <option value="<?php echo (int) $doctor['id']; ?>" <?php echo set_select('doctor_id', (string) $doctor['id'], $is_edit ? (int) $appointment['doctor_id'] === (int) $doctor['id'] : FALSE); ?>>
                                <?php echo html_escape($doctor['name'] . $department); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php echo form_error('doctor_id'); ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="appointment_date">Appointment Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="appointment_date" name="appointment_date" value="<?php echo html_escape(set_value('appointment_date', $is_edit ? $appointment['appointment_date'] : '')); ?>">
                    <?php echo form_error('appointment_date'); ?>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="appointment_time">Appointment Time <span class="text-danger">*</span></label>
                    <input type="time" class="form-control" id="appointment_time" name="appointment_time" value="<?php echo html_escape(set_value('appointment_time', $is_edit ? $appointment['appointment_time'] : '')); ?>">
                    <?php echo form_error('appointment_time'); ?>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="status">Status <span class="text-danger">*</span></label>
                    <select class="form-control" id="status" name="status">
                        <option value="scheduled" <?php echo set_select('status', 'scheduled', $is_edit ? $appointment['status'] === 'scheduled' : TRUE); ?>>Scheduled</option>
                        <option value="completed" <?php echo set_select('status', 'completed', $is_edit ? $appointment['status'] === 'completed' : FALSE); ?>>Completed</option>
                        <option value="cancelled" <?php echo set_select('status', 'cancelled', $is_edit ? $appointment['status'] === 'cancelled' : FALSE); ?>>Cancelled</option>
                    </select>
                    <?php echo form_error('status'); ?>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="reason">Reason</label>
                    <input type="text" class="form-control" id="reason" name="reason" value="<?php echo html_escape(set_value('reason', $is_edit ? $appointment['reason'] : '')); ?>" maxlength="255">
                    <?php echo form_error('reason'); ?>
                </div>
            </div>
        </div>

        <div class="form-group mb-0">
            <label for="notes">Notes</label>
            <textarea class="form-control" id="notes" name="notes" rows="4"><?php echo html_escape(set_value('notes', $is_edit ? $appointment['notes'] : '')); ?></textarea>
            <?php echo form_error('notes'); ?>
        </div>
    </div>

    <?php if ($is_modal): ?>
        <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary js-submit-btn">
                <i class="fas fa-save mr-1"></i> <?php echo $is_edit ? 'Update Appointment' : 'Book Appointment'; ?>
            </button>
        </div>
    <?php else: ?>
        <div class="card-footer text-right">
            <a href="<?php echo site_url('admin/appointments'); ?>" class="btn btn-default">Cancel</a>
            <button type="submit" class="btn btn-primary js-submit-btn">
                <i class="fas fa-save mr-1"></i> <?php echo $is_edit ? 'Update Appointment' : 'Book Appointment'; ?>
            </button>
        </div>
    <?php endif; ?>
<?php echo form_close(); ?>