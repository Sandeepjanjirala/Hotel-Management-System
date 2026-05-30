<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<a class="btn btn-xs btn-default" title="View" href="<?php echo site_url('doctor/appointments/view/' . (int) $appointment->appointment_id); ?>">
    <i class="fa fa-eye"></i>
</a>
<a class="btn btn-xs btn-success" title="Prescription" href="<?php echo site_url('doctor/prescriptions/create/' . (int) $appointment->appointment_id); ?>">
    <i class="fa fa-medkit"></i>
</a>
<a class="btn btn-xs btn-warning" title="Lab request" href="<?php echo site_url('doctor/lab-requests/create/' . (int) $appointment->appointment_id); ?>">
    <i class="fa fa-flask"></i>
</a>