<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * _modal_form.php
 *
 * Shared form body used by both Add and Edit patient modals.
 * Fields are prefixed with 'add_' or 'edit_' so IDs remain unique.
 *
 * Expected variable:
 *   $mode  – 'add' | 'edit'
 */
$p = ($mode === 'edit') ? 'edit_' : 'add_';
?>

<div class="row g-3">

    <!-- Full Name -->
    <div class="col-md-6">
        <label class="form-label" for="<?php echo $p; ?>full_name">
            Full Name <span class="text-danger">*</span>
        </label>
        <input type="text"
               class="form-control"
               id="<?php echo $p; ?>full_name"
               name="full_name"
               maxlength="150"
               placeholder="e.g. John Smith">
        <div class="field-error" id="<?php echo $p; ?>err_full_name"></div>
    </div>

    <!-- Gender -->
    <div class="col-md-3">
        <label class="form-label" for="<?php echo $p; ?>gender">
            Gender <span class="text-danger">*</span>
        </label>
        <select class="form-control" id="<?php echo $p; ?>gender" name="gender">
            <option value="">— Select —</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
        </select>
        <div class="field-error" id="<?php echo $p; ?>err_gender"></div>
    </div>

    <!-- Date of Birth -->
    <div class="col-md-3">
        <label class="form-label" for="<?php echo $p; ?>dob">Date of Birth</label>
        <input type="date"
               class="form-control"
               id="<?php echo $p; ?>dob"
               name="dob">
        <div class="field-error" id="<?php echo $p; ?>err_dob"></div>
    </div>

    <!-- Blood Group -->
    <div class="col-md-3">
        <label class="form-label" for="<?php echo $p; ?>blood_group">Blood Group</label>
        <select class="form-control" id="<?php echo $p; ?>blood_group" name="blood_group">
            <?php
            $blood_groups = array('Unknown','A+','A-','B+','B-','AB+','AB-','O+','O-');
            foreach ($blood_groups as $bg):
            ?>
            <option value="<?php echo html_escape($bg); ?>"><?php echo html_escape($bg); ?></option>
            <?php endforeach; ?>
        </select>
        <div class="field-error" id="<?php echo $p; ?>err_blood_group"></div>
    </div>

    <!-- Phone -->
    <div class="col-md-3">
        <label class="form-label" for="<?php echo $p; ?>phone">
            Phone <span class="text-danger">*</span>
        </label>
        <input type="text"
               class="form-control"
               id="<?php echo $p; ?>phone"
               name="phone"
               maxlength="30"
               placeholder="e.g. +91 9876543210">
        <div class="field-error" id="<?php echo $p; ?>err_phone"></div>
    </div>

    <!-- Email -->
    <div class="col-md-3">
        <label class="form-label" for="<?php echo $p; ?>email">Email</label>
        <input type="email"
               class="form-control"
               id="<?php echo $p; ?>email"
               name="email"
               maxlength="150"
               placeholder="e.g. patient@email.com">
        <div class="field-error" id="<?php echo $p; ?>err_email"></div>
    </div>

    <!-- Emergency Contact -->
    <div class="col-md-3">
        <label class="form-label" for="<?php echo $p; ?>emergency_contact">Emergency Contact</label>
        <input type="text"
               class="form-control"
               id="<?php echo $p; ?>emergency_contact"
               name="emergency_contact"
               maxlength="30"
               placeholder="Emergency phone">
        <div class="field-error" id="<?php echo $p; ?>err_emergency_contact"></div>
    </div>

    <!-- Status -->
    <div class="col-md-3">
        <label class="form-label" for="<?php echo $p; ?>status">
            Status <span class="text-danger">*</span>
        </label>
        <select class="form-control" id="<?php echo $p; ?>status" name="status">
            <option value="1">Active</option>
            <option value="0">Inactive</option>
        </select>
        <div class="field-error" id="<?php echo $p; ?>err_status"></div>
    </div>

    <!-- Address -->
    <div class="col-12">
        <label class="form-label" for="<?php echo $p; ?>address">Address</label>
        <textarea class="form-control"
                  id="<?php echo $p; ?>address"
                  name="address"
                  rows="3"
                  placeholder="Full residential address…"></textarea>
        <div class="field-error" id="<?php echo $p; ?>err_address"></div>
    </div>

</div>
