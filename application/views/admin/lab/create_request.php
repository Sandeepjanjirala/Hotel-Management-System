<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6"><h1>Create Lab Request</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin/lab-requests'); ?>">Lab Requests</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div id="labRequestCreateAlert"></div>

            <form id="labRequestForm" autocomplete="off">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title mb-0"><i class="fas fa-flask mr-1"></i> Request Details</h3>
                        <div class="card-tools">
                            <a href="<?php echo site_url('admin/lab-requests'); ?>" class="btn btn-default btn-sm">
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
                                    <option value="<?php echo (int) $appointment['id']; ?>">
                                        <?php echo html_escape($appointment['appointment_code'] . ' - ' . $appointment['patient_name'] . ' with Dr. ' . $appointment['doctor_name'] . ' (' . $appointment['appointment_date'] . ' ' . $appointment['appointment_time'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div id="appointmentDetailsBox" class="alert alert-light border d-none"></div>

                        <div class="form-group mb-0">
                            <label>Request Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control col-md-3" name="request_date" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                </div>

                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title mb-0"><i class="fas fa-vials mr-1"></i> Select Lab Tests</h3>
                    </div>

                    <div class="card-body">
                        <?php if (!empty($lab_tests)): ?>
                            <div class="row">
                                <?php foreach ($lab_tests as $test): ?>
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <div class="custom-control custom-checkbox border rounded p-3 mb-3">
                                            <input type="checkbox" class="custom-control-input lab-test-check" id="lab_test_<?php echo (int) $test['id']; ?>" name="lab_tests[]" value="<?php echo (int) $test['id']; ?>">
                                            <label class="custom-control-label w-100" for="lab_test_<?php echo (int) $test['id']; ?>">
                                                <strong><?php echo html_escape($test['test_name']); ?></strong>
                                                <br>
                                                <small class="text-muted">
                                                    <?php echo html_escape($test['test_code'] . ' | ' . $test['category']); ?>
                                                </small>
                                                <br>
                                                <span class="badge badge-info mt-1"><?php echo html_escape(number_format((float) $test['price'], 2)); ?></span>
                                                <?php if (!empty($test['normal_range'])): ?>
                                                    <span class="badge badge-light border mt-1">Range: <?php echo html_escape($test['normal_range']); ?></span>
                                                <?php endif; ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning mb-0">No active lab tests available.</div>
                        <?php endif; ?>
                    </div>

                    <div class="card-footer text-right">
                        <a href="<?php echo site_url('admin/lab-requests'); ?>" class="btn btn-default">Cancel</a>
                        <button type="submit" id="saveLabRequestButton" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Save Lab Request
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
    var appointmentBaseUrl = '<?php echo site_url('admin/lab-requests/appointment'); ?>';
    var storeUrl = '<?php echo site_url('admin/lab-requests/store'); ?>';

    function updateCsrf(hash) {
        if (hash) {
            csrfHash = hash;
            $('input[name="' + csrfName + '"]').val(hash);
        }
    }

    function escapeHtml(value) {
        return $('<div>').text(value === null || value === undefined ? '' : value).html();
    }

    function alertBox(type, message) {
        $('#labRequestCreateAlert').html(
            '<div class="alert alert-' + (type === 'success' ? 'success' : 'danger') + ' alert-dismissible fade show">' +
            escapeHtml(message) +
            '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>'
        );
    }

    function renderAppointment(appointment) {
        var html = ''
            + '<div class="row">'
            + '<div class="col-md-4"><strong>Patient:</strong> ' + escapeHtml(appointment.patient_name) + '<br><small class="text-muted">' + escapeHtml(appointment.patient_code) + ' | ' + escapeHtml(appointment.patient_phone) + '</small></div>'
            + '<div class="col-md-4"><strong>Doctor:</strong> ' + escapeHtml(appointment.doctor_name) + '<br><small class="text-muted">Appointment: ' + escapeHtml(appointment.appointment_code) + '</small></div>'
            + '<div class="col-md-4"><strong>Date:</strong> ' + escapeHtml(appointment.appointment_date + ' ' + appointment.appointment_time) + '<br><small class="text-muted">' + escapeHtml(appointment.reason || '-') + '</small></div>'
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
                updateCsrf(response.csrfHash);
                response.status ? renderAppointment(response.data) : alertBox('danger', response.message || 'Unable to load appointment.');
            },
            error: function () {
                alertBox('danger', 'Unable to load appointment.');
            }
        });
    });

    $('#labRequestForm').on('submit', function (event) {
        event.preventDefault();

        var $button = $('#saveLabRequestButton');
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
                    alertBox('success', response.message);
                    window.setTimeout(function () {
                        window.location.href = response.redirectUrl;
                    }, 700);
                    return;
                }

                alertBox('danger', response.message || 'Unable to create lab request.');
                $button.prop('disabled', false).text(originalText);
            },
            error: function () {
                alertBox('danger', 'Unable to create lab request.');
                $button.prop('disabled', false).text(originalText);
            }
        });
    });
});
</script>
