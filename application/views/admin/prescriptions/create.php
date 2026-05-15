<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Create Prescription</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin/prescriptions'); ?>">Prescriptions</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo html_escape($this->session->flashdata('error')); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if (validation_errors()): ?>
                <div class="alert alert-danger" role="alert">
                    Please correct the highlighted errors and submit again.
                </div>
            <?php endif; ?>

            <?php echo form_open('admin/prescriptions/store', array('id' => 'prescriptionForm', 'autocomplete' => 'off')); ?>
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-prescription mr-1"></i> Appointment Prescription
                        </h3>
                        <div class="card-tools">
                            <a href="<?php echo site_url('admin/prescriptions'); ?>" class="btn btn-default btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Back
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div id="prescriptionAjaxAlert"></div>

                        <div class="form-group">
                            <label for="appointment_id">Appointment <span class="text-danger">*</span></label>
                            <?php
                                $selected_appointment_id = set_value(
                                    'appointment_id',
                                    !empty($selected_appointment) ? $selected_appointment['id'] : ''
                                );
                            ?>
                            <select class="form-control" id="appointment_id" name="appointment_id">
                                <option value="">Select Appointment</option>
                                <?php foreach ($appointments as $appointment): ?>
                                    <option value="<?php echo (int) $appointment['id']; ?>" <?php echo ((int) $selected_appointment_id === (int) $appointment['id']) ? 'selected' : ''; ?>>
                                        <?php echo html_escape($appointment['appointment_code'] . ' - ' . $appointment['patient_name'] . ' with Dr. ' . $appointment['doctor_name'] . ' (' . $appointment['appointment_date'] . ' ' . $appointment['appointment_time'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php echo form_error('appointment_id'); ?>
                        </div>

                        <div id="appointmentDetailsBox" class="alert alert-light border <?php echo empty($selected_appointment) ? 'd-none' : ''; ?>">
                            <?php if (!empty($selected_appointment)): ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Patient:</strong>
                                        <?php echo html_escape($selected_appointment['patient_name']); ?>
                                        <br>
                                        <small class="text-muted"><?php echo html_escape($selected_appointment['patient_code']); ?></small>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Doctor:</strong>
                                        <?php echo html_escape($selected_appointment['doctor_name']); ?>
                                        <br>
                                        <small class="text-muted"><?php echo html_escape($selected_appointment['department_name']); ?></small>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Appointment:</strong>
                                        <?php echo html_escape($selected_appointment['appointment_date'] . ' ' . $selected_appointment['appointment_time']); ?>
                                        <br>
                                        <small class="text-muted"><?php echo html_escape($selected_appointment['appointment_code']); ?></small>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="prescription_date">Prescription Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="prescription_date" name="prescription_date" value="<?php echo html_escape(set_value('prescription_date', date('Y-m-d'))); ?>">
                                    <?php echo form_error('prescription_date'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="symptoms">Symptoms <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="symptoms" name="symptoms" rows="3"><?php echo html_escape(set_value('symptoms')); ?></textarea>
                            <?php echo form_error('symptoms'); ?>
                        </div>

                        <div class="form-group">
                            <label for="diagnosis">Diagnosis <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="diagnosis" name="diagnosis" rows="3"><?php echo html_escape(set_value('diagnosis')); ?></textarea>
                            <?php echo form_error('diagnosis'); ?>
                        </div>

                        <div class="form-group mb-0">
                            <label for="notes">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo html_escape(set_value('notes')); ?></textarea>
                            <?php echo form_error('notes'); ?>
                        </div>
                    </div>
                </div>

                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-capsules mr-1"></i> Medicines
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" id="addMedicineRowButton">
                                <i class="fas fa-plus mr-1"></i> Add Medicine
                            </button>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th style="min-width: 220px;">Medicine</th>
                                        <th style="min-width: 130px;">Dosage</th>
                                        <th style="width: 90px;">Days</th>
                                        <th style="width: 90px;">Morning</th>
                                        <th style="width: 100px;">Afternoon</th>
                                        <th style="width: 80px;">Night</th>
                                        <th style="min-width: 220px;">Instructions</th>
                                        <th style="width: 70px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="medicineRows">
                                    <?php echo $initial_medicine_row; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <a href="<?php echo site_url('admin/prescriptions'); ?>" class="btn btn-default">Cancel</a>
                        <button type="submit" class="btn btn-primary" id="savePrescriptionButton">
                            <i class="fas fa-save mr-1"></i> Save Prescription
                        </button>
                    </div>
                </div>
            <?php echo form_close(); ?>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var appointmentBaseUrl = '<?php echo site_url('admin/prescriptions/appointment'); ?>';
    var medicineRowUrl = '<?php echo site_url('admin/prescriptions/medicine-row'); ?>';
    var rowIndex = 1;

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

    function showAjaxAlert(type, message) {
        var alertClass = type === 'success' ? 'success' : 'danger';
        var html = ''
            + '<div class="alert alert-' + alertClass + ' alert-dismissible fade show" role="alert">'
            + escapeHtml(message)
            + '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'
            + '<span aria-hidden="true">&times;</span>'
            + '</button>'
            + '</div>';

        $('#prescriptionAjaxAlert').html(html);
    }

    function renderAppointmentDetails(appointment) {
        var html = ''
            + '<div class="row">'
            + '<div class="col-md-4">'
            + '<strong>Patient:</strong> ' + escapeHtml(appointment.patient_name)
            + '<br><small class="text-muted">' + escapeHtml(appointment.patient_code) + '</small>'
            + '</div>'
            + '<div class="col-md-4">'
            + '<strong>Doctor:</strong> ' + escapeHtml(appointment.doctor_name)
            + '<br><small class="text-muted">' + escapeHtml(appointment.department_name || '-') + '</small>'
            + '</div>'
            + '<div class="col-md-4">'
            + '<strong>Appointment:</strong> ' + escapeHtml(appointment.appointment_date + ' ' + appointment.appointment_time)
            + '<br><small class="text-muted">' + escapeHtml(appointment.appointment_code) + '</small>'
            + '</div>'
            + '</div>';

        $('#appointmentDetailsBox').removeClass('d-none').html(html);
    }

    $('#appointment_id').on('change', function () {
        var appointmentId = $(this).val();

        $('#appointmentDetailsBox').addClass('d-none').html('');

        if (!appointmentId) {
            return;
        }

        $.ajax({
            url: appointmentBaseUrl + '/' + appointmentId,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    renderAppointmentDetails(response.data);
                    return;
                }

                showAjaxAlert('danger', response.message || 'Unable to load appointment details.');
            },
            error: function () {
                showAjaxAlert('danger', 'Unable to load appointment details.');
            }
        });
    });

    $('#addMedicineRowButton').on('click', function () {
        var payload = {};
        payload[csrfName] = csrfHash;
        payload.row_index = rowIndex;

        $.ajax({
            url: medicineRowUrl,
            type: 'POST',
            dataType: 'json',
            data: payload,
            success: function (response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    $('#medicineRows').append(response.html);
                    rowIndex++;
                    return;
                }

                showAjaxAlert('danger', response.message || 'Unable to add medicine row.');
            },
            error: function () {
                showAjaxAlert('danger', 'Unable to add medicine row.');
            }
        });
    });

    $(document).on('click', '.btn-remove-medicine-row', function () {
        if ($('#medicineRows tr').length <= 1) {
            showAjaxAlert('danger', 'At least one medicine row is required.');
            return;
        }

        $(this).closest('tr').remove();
    });

    $('#prescriptionForm').on('submit', function () {
        $('#savePrescriptionButton')
            .prop('disabled', true)
            .text('Saving...');
    });

    if ($('#appointment_id').val()) {
        $('#appointment_id').trigger('change');
    }
});
</script>
