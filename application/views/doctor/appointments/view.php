<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-8">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Appointment</h3>
                <div class="box-tools pull-right">
                    <a class="btn btn-sm btn-success" href="<?php echo site_url('doctor/prescriptions/create/' . (int) $appointment->appointment_id); ?>">
                        <i class="fa fa-medkit"></i> Prescription
                    </a>
                    <a class="btn btn-sm btn-warning" href="<?php echo site_url('doctor/lab-requests/create/' . (int) $appointment->appointment_id); ?>">
                        <i class="fa fa-flask"></i> Lab Request
                    </a>
                </div>
            </div>
            <div class="box-body">
                <dl class="dl-horizontal">
                    <dt>Date</dt>
                    <dd><?php echo html_escape($appointment->appointment_date); ?></dd>
                    <dt>Time</dt>
                    <dd><?php echo html_escape($appointment->appointment_time); ?></dd>
                    <dt>Status</dt>
                    <dd><span class="label label-info"><?php echo html_escape($appointment->status); ?></span></dd>
                    <dt>Reason</dt>
                    <dd><?php echo html_escape($appointment->reason); ?></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="box box-widget widget-user-2">
            <div class="widget-user-header bg-aqua-active">
                <div class="widget-user-image">
                    <i class="fa fa-user fa-3x"></i>
                </div>
                <h3 class="widget-user-username"><?php echo html_escape($appointment->patient_name); ?></h3>
                <h5 class="widget-user-desc"><?php echo html_escape($appointment->patient_mobile); ?></h5>
            </div>
            <div class="box-footer no-padding">
                <ul class="nav nav-stacked">
                    <li><a href="#">Gender <span class="pull-right"><?php echo html_escape($appointment->patient_gender); ?></span></a></li>
                    <li><a href="#">Age <span class="pull-right"><?php echo html_escape($appointment->patient_age); ?></span></a></li>
                    <li>
                        <a href="<?php echo site_url('doctor/patients/view/' . (int) $appointment->patient_id); ?>">
                            Open patient profile <span class="pull-right"><i class="fa fa-angle-right"></i></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>