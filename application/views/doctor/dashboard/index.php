<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3><?php echo (int) $stats['today_appointments']; ?></h3>
                <p>Today Appointments</p>
            </div>
            <div class="icon"><i class="fa fa-calendar"></i></div>
            <a href="<?php echo site_url('doctor/appointments'); ?>" class="small-box-footer">View <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
            <div class="inner">
                <h3><?php echo (int) $stats['assigned_patients']; ?></h3>
                <p>Assigned Patients</p>
            </div>
            <div class="icon"><i class="fa fa-heartbeat"></i></div>
            <a href="<?php echo site_url('doctor/patients'); ?>" class="small-box-footer">View <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3><?php echo (int) $stats['prescriptions']; ?></h3>
                <p>Prescriptions</p>
            </div>
            <div class="icon"><i class="fa fa-medkit"></i></div>
            <span class="small-box-footer">&nbsp;</span>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
            <div class="inner">
                <h3><?php echo (int) $stats['lab_requests']; ?></h3>
                <p>Lab Requests</p>
            </div>
            <div class="icon"><i class="fa fa-flask"></i></div>
            <span class="small-box-footer">&nbsp;</span>
        </div>
    </div>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Today Schedule</h3>
    </div>
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Patient</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($today_appointments)): ?>
                    <tr><td colspan="5" class="text-muted text-center">No appointments scheduled today.</td></tr>
                <?php endif; ?>
                <?php foreach ($today_appointments as $appointment): ?>
                    <tr>
                        <td><?php echo html_escape($appointment->appointment_time); ?></td>
                        <td><?php echo html_escape($appointment->patient_name); ?></td>
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