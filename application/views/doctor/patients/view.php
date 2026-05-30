<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-4">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Patient</h3>
            </div>
            <div class="box-body">
                <strong><?php echo html_escape($patient->name); ?></strong>
                <p class="text-muted"><?php echo html_escape($patient->mobile); ?></p>
                <hr>
                <p><strong>Email</strong><br><?php echo html_escape(isset($patient->email) ? $patient->email : '-'); ?></p>
                <p><strong>Gender</strong><br><?php echo html_escape(isset($patient->gender) ? $patient->gender : '-'); ?></p>
                <p><strong>Age</strong><br><?php echo html_escape(isset($patient->age) ? $patient->age : '-'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Appointments With You</h3>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($appointments)): ?>
                            <tr><td colspan="5" class="text-center text-muted">No appointments found.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td><?php echo html_escape($appointment->appointment_date); ?></td>
                                <td><?php echo html_escape($appointment->appointment_time); ?></td>
                                <td><?php echo html_escape($appointment->reason); ?></td>
                                <td><span class="label label-info"><?php echo html_escape($appointment->status); ?></span></td>
                                <td>
                                    <a class="btn btn-xs btn-default" href="<?php echo site_url('doctor/appointments/view/' . (int) $appointment->appointment_id); ?>">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>