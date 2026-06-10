<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
$is_edit = isset($patient) && !empty($patient);
$action = $is_edit ? 'admin/patients/update/' . (int) $patient['id'] : 'admin/patients/store';
$form_class = $is_edit ? 'js-edit-patient-form' : 'js-create-patient-form';
$form_id = $is_edit ? 'editPatientForm' : 'createPatientForm';
$is_modal = isset($is_modal) && $is_modal;
?>

<?php echo form_open($action, array('autocomplete' => 'off', 'class' => $form_class, 'id' => $form_id)); ?>
    <div class="card-body">
        <?php if ($is_edit): ?>
            <div class="alert alert-light border">
                Patient Code:
                <strong><?php echo html_escape($patient['patient_code']); ?></strong>
            </div>
        <?php else: ?>
            <div class="alert alert-light border">
                Patient code will be generated automatically. Next expected code:
                <strong><?php echo html_escape($next_patient_code); ?></strong>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="<?php echo $is_edit ? 'edit_' : ''; ?>full_name">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="<?php echo $is_edit ? 'edit_' : ''; ?>full_name" name="full_name" value="<?php echo html_escape(set_value('full_name', $is_edit ? $patient['full_name'] : '')); ?>" maxlength="150">
                    <?php echo form_error('full_name'); ?>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="<?php echo $is_edit ? 'edit_' : ''; ?>gender">Gender <span class="text-danger">*</span></label>
                    <select class="form-control" id="<?php echo $is_edit ? 'edit_' : ''; ?>gender" name="gender">
                        <option value="">Select Gender</option>
                        <option value="male" <?php echo set_select('gender', 'male', $is_edit ? $patient['gender'] === 'male' : FALSE); ?>>Male</option>
                        <option value="female" <?php echo set_select('gender', 'female', $is_edit ? $patient['gender'] === 'female' : FALSE); ?>>Female</option>
                        <option value="other" <?php echo set_select('gender', 'other', $is_edit ? $patient['gender'] === 'other' : FALSE); ?>>Other</option>
                    </select>
                    <?php echo form_error('gender'); ?>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="<?php echo $is_edit ? 'edit_' : ''; ?>dob">Date of Birth</label>
                    <input type="date" class="form-control" id="<?php echo $is_edit ? 'edit_' : ''; ?>dob" name="dob" value="<?php echo html_escape(set_value('dob', $is_edit ? $patient['dob'] : '')); ?>">
                    <?php echo form_error('dob'); ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="<?php echo $is_edit ? 'edit_' : ''; ?>blood_group">Blood Group</label>
                    <select class="form-control" id="<?php echo $is_edit ? 'edit_' : ''; ?>blood_group" name="blood_group">
                        <?php
                            $blood_groups = array('Unknown', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-');
                            foreach ($blood_groups as $blood_group):
                        ?>
                            <option value="<?php echo html_escape($blood_group); ?>" <?php echo set_select('blood_group', $blood_group, $is_edit ? $patient['blood_group'] === $blood_group : $blood_group === 'Unknown'); ?>>
                                <?php echo html_escape($blood_group); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php echo form_error('blood_group'); ?>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="<?php echo $is_edit ? 'edit_' : ''; ?>phone">Phone <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="<?php echo $is_edit ? 'edit_' : ''; ?>phone" name="phone" value="<?php echo html_escape(set_value('phone', $is_edit ? $patient['phone'] : '')); ?>" maxlength="30">
                    <?php echo form_error('phone'); ?>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="<?php echo $is_edit ? 'edit_' : ''; ?>email">Email</label>
                    <input type="email" class="form-control" id="<?php echo $is_edit ? 'edit_' : ''; ?>email" name="email" value="<?php echo html_escape(set_value('email', $is_edit ? $patient['email'] : '')); ?>" maxlength="150">
                    <?php echo form_error('email'); ?>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="<?php echo $is_edit ? 'edit_' : ''; ?>emergency_contact">Emergency Contact</label>
                    <input type="text" class="form-control" id="<?php echo $is_edit ? 'edit_' : ''; ?>emergency_contact" name="emergency_contact" value="<?php echo html_escape(set_value('emergency_contact', $is_edit ? $patient['emergency_contact'] : '')); ?>" maxlength="30">
                    <?php echo form_error('emergency_contact'); ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-9">
                <div class="form-group mb-md-0">
                    <label for="<?php echo $is_edit ? 'edit_' : ''; ?>address">Address</label>
                    <textarea class="form-control" id="<?php echo $is_edit ? 'edit_' : ''; ?>address" name="address" rows="4"><?php echo html_escape(set_value('address', $is_edit ? $patient['address'] : '')); ?></textarea>
                    <?php echo form_error('address'); ?>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group mb-0">
                    <label for="<?php echo $is_edit ? 'edit_' : ''; ?>status">Status <span class="text-danger">*</span></label>
                    <select class="form-control" id="<?php echo $is_edit ? 'edit_' : ''; ?>status" name="status">
                        <option value="1" <?php echo set_select('status', '1', $is_edit ? (int) $patient['status'] === 1 : TRUE); ?>>Active</option>
                        <option value="0" <?php echo set_select('status', '0', $is_edit ? (int) $patient['status'] === 0 : FALSE); ?>>Inactive</option>
                    </select>
                    <?php echo form_error('status'); ?>
                </div>
            </div>
        </div>
    </div>

    <?php if ($is_modal): ?>
        <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary js-submit-btn">
                <i class="fas fa-save mr-1"></i> <?php echo $is_edit ? 'Update Patient' : 'Register Patient'; ?>
            </button>
        </div>
    <?php else: ?>
        <div class="card-footer text-right">
            <a href="<?php echo site_url('admin/patients'); ?>" class="btn btn-default">Cancel</a>
            <button type="submit" class="btn btn-primary js-submit-btn">
                <i class="fas fa-save mr-1"></i> <?php echo $is_edit ? 'Update Patient' : 'Register Patient'; ?>
            </button>
        </div>
    <?php endif; ?>
<?php echo form_close(); ?>
