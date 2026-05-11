<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Edit Patient</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin/patients'); ?>">Patients</a></li>
                        <li class="breadcrumb-item active">Edit</li>
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
                        <i class="fas fa-user-edit mr-1"></i> Patient Details
                    </h3>
                    <div class="card-tools">
                        <a href="<?php echo site_url('admin/patients'); ?>" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i> Back
                        </a>
                    </div>
                </div>

                <?php echo form_open('admin/patients/update/' . (int) $patient['id'], array('autocomplete' => 'off')); ?>
                    <div class="card-body">
                        <div class="alert alert-light border">
                            Patient Code:
                            <strong><?php echo html_escape($patient['patient_code']); ?></strong>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="full_name">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo html_escape(set_value('full_name', $patient['full_name'])); ?>" maxlength="150">
                                    <?php echo form_error('full_name'); ?>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="gender">Gender <span class="text-danger">*</span></label>
                                    <select class="form-control" id="gender" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="male" <?php echo set_select('gender', 'male', $patient['gender'] === 'male'); ?>>Male</option>
                                        <option value="female" <?php echo set_select('gender', 'female', $patient['gender'] === 'female'); ?>>Female</option>
                                        <option value="other" <?php echo set_select('gender', 'other', $patient['gender'] === 'other'); ?>>Other</option>
                                    </select>
                                    <?php echo form_error('gender'); ?>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="dob">Date of Birth</label>
                                    <input type="date" class="form-control" id="dob" name="dob" value="<?php echo html_escape(set_value('dob', $patient['dob'])); ?>">
                                    <?php echo form_error('dob'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="blood_group">Blood Group</label>
                                    <select class="form-control" id="blood_group" name="blood_group">
                                        <?php
                                            $blood_groups = array('Unknown', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-');
                                            foreach ($blood_groups as $blood_group):
                                        ?>
                                            <option value="<?php echo html_escape($blood_group); ?>" <?php echo set_select('blood_group', $blood_group, $patient['blood_group'] === $blood_group); ?>>
                                                <?php echo html_escape($blood_group); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php echo form_error('blood_group'); ?>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="phone">Phone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo html_escape(set_value('phone', $patient['phone'])); ?>" maxlength="30">
                                    <?php echo form_error('phone'); ?>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo html_escape(set_value('email', $patient['email'])); ?>" maxlength="150">
                                    <?php echo form_error('email'); ?>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="emergency_contact">Emergency Contact</label>
                                    <input type="text" class="form-control" id="emergency_contact" name="emergency_contact" value="<?php echo html_escape(set_value('emergency_contact', $patient['emergency_contact'])); ?>" maxlength="30">
                                    <?php echo form_error('emergency_contact'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group mb-md-0">
                                    <label for="address">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="4"><?php echo html_escape(set_value('address', $patient['address'])); ?></textarea>
                                    <?php echo form_error('address'); ?>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="1" <?php echo set_select('status', '1', (int) $patient['status'] === 1); ?>>Active</option>
                                        <option value="0" <?php echo set_select('status', '0', (int) $patient['status'] === 0); ?>>Inactive</option>
                                    </select>
                                    <?php echo form_error('status'); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <a href="<?php echo site_url('admin/patients'); ?>" class="btn btn-default">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Update Patient
                        </button>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </section>
</div>
