<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Book Appointment</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin/appointments'); ?>">Appointments</a></li>
                        <li class="breadcrumb-item active">Book</li>
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

            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-calendar-plus mr-1"></i> Appointment Booking Form
                    </h3>
                    <div class="card-tools">
                        <a href="<?php echo site_url('admin/appointments'); ?>" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i> Back
                        </a>
                    </div>
                </div>

                <?php echo form_open('admin/appointments/store', array('autocomplete' => 'off')); ?>
                    <div class="card-body">
                        <div class="alert alert-light border">
                            Appointment code will be generated automatically. Next expected code:
                            <strong><?php echo html_escape($next_appointment_code); ?></strong>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="patient_id">Patient <span class="text-danger">*</span></label>
                                    <select class="form-control" id="patient_id" name="patient_id">
                                        <option value="">Select Patient</option>
                                        <?php foreach ($patients as $patient): ?>
                                            <option value="<?php echo (int) $patient['id']; ?>" <?php echo set_select('patient_id', (string) $patient['id']); ?>>
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
                                            <option value="<?php echo (int) $doctor['id']; ?>" <?php echo set_select('doctor_id', (string) $doctor['id']); ?>>
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
                                    <input type="date" class="form-control" id="appointment_date" name="appointment_date" value="<?php echo html_escape(set_value('appointment_date')); ?>">
                                    <?php echo form_error('appointment_date'); ?>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="appointment_time">Appointment Time <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" id="appointment_time" name="appointment_time" value="<?php echo html_escape(set_value('appointment_time')); ?>">
                                    <?php echo form_error('appointment_time'); ?>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="scheduled" <?php echo set_select('status', 'scheduled', TRUE); ?>>Scheduled</option>
                                        <option value="completed" <?php echo set_select('status', 'completed'); ?>>Completed</option>
                                        <option value="cancelled" <?php echo set_select('status', 'cancelled'); ?>>Cancelled</option>
                                    </select>
                                    <?php echo form_error('status'); ?>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="reason">Reason</label>
                                    <input type="text" class="form-control" id="reason" name="reason" value="<?php echo html_escape(set_value('reason')); ?>" maxlength="255">
                                    <?php echo form_error('reason'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <label for="notes">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="4"><?php echo html_escape(set_value('notes')); ?></textarea>
                            <?php echo form_error('notes'); ?>
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <a href="<?php echo site_url('admin/appointments'); ?>" class="btn btn-default">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Book Appointment
                        </button>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </section>
</div>
