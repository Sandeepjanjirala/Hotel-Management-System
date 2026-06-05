<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-4">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Patient</h3></div>
            <div class="box-body">
                <h4><?php echo html_escape($patient['full_name']); ?></h4>
                <p class="text-muted"><?php echo html_escape($patient['patient_code']); ?></p>
                <p><strong>Phone:</strong> <?php echo html_escape($patient['phone']); ?></p>
                <p><strong>Email:</strong> <?php echo html_escape($patient['email']); ?></p>
                <p><strong>Gender:</strong> <?php echo html_escape($patient['gender']); ?></p>
                <p><strong>Blood Group:</strong> <?php echo html_escape($patient['blood_group']); ?></p>
                <p><strong>Emergency:</strong> <?php echo html_escape($patient['emergency_contact']); ?></p>
                <hr>
                <a class="btn btn-primary" href="<?php echo site_url('receptionist/patients/edit/' . (int) $patient['id']); ?>">
                    <i class="fa fa-pencil"></i> Edit
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Appointments</h3></div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <thead><tr><th>Code</th><th>Date</th><th>Time</th><th>Doctor</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php if (empty($appointments)): ?>
                        <tr><td colspan="5" class="text-center text-muted">No appointments found.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td><?php echo html_escape($appointment['appointment_code']); ?></td>
                            <td><?php echo html_escape($appointment['appointment_date']); ?></td>
                            <td><?php echo html_escape($appointment['appointment_time']); ?></td>
                            <td><?php echo html_escape($appointment['doctor_name']); ?></td>
                            <td><span class="label label-info"><?php echo html_escape($appointment['status']); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
