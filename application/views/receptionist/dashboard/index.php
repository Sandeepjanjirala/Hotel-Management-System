<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
            <div class="inner"><h3><?php echo (int) $counts['total_patients']; ?></h3><p>Total Patients</p></div>
            <div class="icon"><i class="fa fa-address-book"></i></div>
            <a href="<?php echo site_url('receptionist/patients'); ?>" class="small-box-footer">Open <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
            <div class="inner"><h3><?php echo (int) $counts['today_appointments']; ?></h3><p>Today Appointments</p></div>
            <div class="icon"><i class="fa fa-calendar"></i></div>
            <a href="<?php echo site_url('receptionist/appointments'); ?>" class="small-box-footer">Open <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner"><h3><?php echo (int) $counts['pending_bills']; ?></h3><p>Pending Bills</p></div>
            <div class="icon"><i class="fa fa-file-text-o"></i></div>
            <a href="<?php echo site_url('receptionist/billing'); ?>" class="small-box-footer">Open <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
            <div class="inner"><h3><?php echo (int) $counts['pharmacy_bills']; ?></h3><p>Pharmacy Bills</p></div>
            <div class="icon"><i class="fa fa-medkit"></i></div>
            <a href="<?php echo site_url('receptionist/pharmacy-billing'); ?>" class="small-box-footer">Open <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title">Today Appointments</h3></div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <thead><tr><th>Time</th><th>Patient</th><th>Doctor</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php if (empty($today_appointments)): ?>
                        <tr><td colspan="4" class="text-center text-muted">No appointments today.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($today_appointments as $appointment): ?>
                        <tr>
                            <td><?php echo html_escape($appointment['appointment_time']); ?></td>
                            <td><?php echo html_escape($appointment['patient_name']); ?></td>
                            <td><?php echo html_escape($appointment['doctor_name']); ?></td>
                            <td><span class="label label-info"><?php echo html_escape($appointment['status']); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="box box-success">
            <div class="box-header with-border"><h3 class="box-title">Recent Patients</h3></div>
            <div class="box-body">
                <?php foreach ($recent_patients as $patient): ?>
                    <div class="summary-line">
                        <strong><?php echo html_escape($patient['full_name']); ?></strong>
                        <span class="pull-right text-muted"><?php echo html_escape($patient['patient_code']); ?></span><br>
                        <span class="text-muted"><?php echo html_escape($patient['phone']); ?></span>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($recent_patients)): ?>
                    <p class="text-muted">No recent registrations.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="box box-warning">
            <div class="box-header with-border"><h3 class="box-title">Pending Payment Summary</h3></div>
            <div class="box-body">
                <p><strong>Bills:</strong> <?php echo (int) $payment_summary['total_bills']; ?></p>
                <p><strong>Amount:</strong> <?php echo number_format((float) $payment_summary['total_amount'], 2); ?></p>
            </div>
        </div>
    </div>
</div>
