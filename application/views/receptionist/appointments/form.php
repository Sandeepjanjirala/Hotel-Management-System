<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box box-primary">
    <div class="box-header with-border"><h3 class="box-title"><?php echo html_escape($page_title); ?></h3></div>
    <?php echo form_open($form_action); ?>
        <div class="box-body">
            <?php echo validation_errors(); ?>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Patient</label>
                        <?php $selected_patient = set_value('patient_id', $appointment ? $appointment['patient_id'] : ''); ?>
                        <select name="patient_id" class="form-control" required>
                            <option value="">Select patient</option>
                            <?php foreach ($patients as $patient): ?>
                                <option value="<?php echo (int) $patient['id']; ?>" <?php echo (int) $selected_patient === (int) $patient['id'] ? 'selected' : ''; ?>>
                                    <?php echo html_escape($patient['patient_code'] . ' - ' . $patient['full_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Department</label>
                        <select id="department-id" class="form-control">
                            <option value="">All departments</option>
                            <?php foreach ($departments as $department): ?>
                                <option value="<?php echo (int) $department['id']; ?>"><?php echo html_escape($department['department_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Doctor</label>
                        <?php $selected_doctor = set_value('doctor_id', $appointment ? $appointment['doctor_id'] : ''); ?>
                        <select name="doctor_id" id="doctor-id" class="form-control" required data-selected="<?php echo (int) $selected_doctor; ?>">
                            <option value="">Select doctor</option>
                            <?php foreach ($doctors as $doctor): ?>
                                <option value="<?php echo (int) $doctor['id']; ?>" <?php echo (int) $selected_doctor === (int) $doctor['id'] ? 'selected' : ''; ?>>
                                    <?php echo html_escape($doctor['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="appointment_date" id="appointment-date-field" class="form-control" required value="<?php echo set_value('appointment_date', $appointment ? $appointment['appointment_date'] : date('Y-m-d')); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Available Slot</label>
                        <?php $selected_time = set_value('appointment_time', $appointment ? $appointment['appointment_time'] : ''); ?>
                        <select name="appointment_time" id="appointment-time" class="form-control" required data-selected="<?php echo html_escape($selected_time); ?>">
                            <option value="<?php echo html_escape($selected_time); ?>"><?php echo $selected_time ? html_escape($selected_time) : 'Select slot'; ?></option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Status</label>
                        <?php $status = set_value('status', $appointment ? $appointment['status'] : 'scheduled'); ?>
                        <select name="status" class="form-control">
                            <option value="scheduled" <?php echo $status === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                            <option value="completed" <?php echo $status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="cancelled" <?php echo $status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Reason</label>
                <input type="text" name="reason" class="form-control" value="<?php echo set_value('reason', $appointment ? $appointment['reason'] : ''); ?>">
            </div>
            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" class="form-control" rows="3"><?php echo set_value('notes', $appointment ? $appointment['notes'] : ''); ?></textarea>
            </div>
        </div>
        <div class="box-footer">
            <a href="<?php echo site_url('receptionist/appointments'); ?>" class="btn btn-default">Cancel</a>
            <button type="submit" class="btn btn-success pull-right"><i class="fa fa-save"></i> Save Appointment</button>
        </div>
    <?php echo form_close(); ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var doctorsEndpoint = '<?php echo site_url('receptionist/appointments/doctors'); ?>';
    var slotsEndpoint = '<?php echo site_url('receptionist/appointments/slots'); ?>';
    var appointmentId = '<?php echo $appointment ? (int) $appointment['id'] : ''; ?>';

    function loadDoctors() {
        $.get(doctorsEndpoint, { department_id: $('#department-id').val() }, function (response) {
            var selected = $('#doctor-id').data('selected');
            var options = '<option value="">Select doctor</option>';
            response.data.forEach(function (doctor) {
                options += '<option value="' + doctor.id + '"' + (parseInt(selected, 10) === parseInt(doctor.id, 10) ? ' selected' : '') + '>' + doctor.name + '</option>';
            });
            $('#doctor-id').html(options);
            loadSlots();
        }, 'json');
    }

    function loadSlots() {
        var doctorId = $('#doctor-id').val();
        var date = $('#appointment-date-field').val();
        var selected = $('#appointment-time').data('selected');

        if (!doctorId || !date) {
            $('#appointment-time').html('<option value="">Select slot</option>');
            return;
        }

        $.get(slotsEndpoint, { doctor_id: doctorId, appointment_date: date, appointment_id: appointmentId }, function (response) {
            var options = '<option value="">Select slot</option>';
            response.data.forEach(function (slot) {
                options += '<option value="' + slot.value + '"' + (selected === slot.value ? ' selected' : '') + '>' + slot.label + '</option>';
            });
            $('#appointment-time').html(options);
        }, 'json');
    }

    $('#department-id').on('change', loadDoctors);
    $('#doctor-id, #appointment-date-field').on('change', loadSlots);
    loadSlots();
});
</script>
