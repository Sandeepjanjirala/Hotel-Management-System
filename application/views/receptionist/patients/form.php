<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="box box-primary">
    <div class="box-header with-border"><h3 class="box-title"><?php echo html_escape($page_title); ?></h3></div>
    <?php echo form_open($form_action); ?>
        <div class="box-body">
            <?php echo validation_errors(); ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" class="form-control" required value="<?php echo set_value('full_name', $patient ? $patient['full_name'] : ''); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Gender</label>
                        <?php $gender = set_value('gender', $patient ? $patient['gender'] : ''); ?>
                        <select name="gender" class="form-control" required>
                            <option value="">Select</option>
                            <option value="male" <?php echo $gender === 'male' ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo $gender === 'female' ? 'selected' : ''; ?>>Female</option>
                            <option value="other" <?php echo $gender === 'other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" class="form-control" value="<?php echo set_value('dob', $patient ? $patient['dob'] : ''); ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Blood Group</label>
                        <?php $blood_group = set_value('blood_group', $patient ? $patient['blood_group'] : 'Unknown'); ?>
                        <select name="blood_group" class="form-control">
                            <?php foreach (array('Unknown','A+','A-','B+','B-','AB+','AB-','O+','O-') as $group): ?>
                                <option value="<?php echo $group; ?>" <?php echo $blood_group === $group ? 'selected' : ''; ?>><?php echo $group; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" required value="<?php echo set_value('phone', $patient ? $patient['phone'] : ''); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo set_value('email', $patient ? $patient['email'] : ''); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Emergency Contact</label>
                        <input type="text" name="emergency_contact" class="form-control" value="<?php echo set_value('emergency_contact', $patient ? $patient['emergency_contact'] : ''); ?>">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Address</label>
                <textarea name="address" class="form-control" rows="3"><?php echo set_value('address', $patient ? $patient['address'] : ''); ?></textarea>
            </div>
        </div>
        <div class="box-footer">
            <a href="<?php echo site_url('receptionist/patients'); ?>" class="btn btn-default">Cancel</a>
            <button type="submit" class="btn btn-success pull-right"><i class="fa fa-save"></i> Save Patient</button>
        </div>
    <?php echo form_close(); ?>
</div>
