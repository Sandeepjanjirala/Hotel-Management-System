<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (!empty($appointments)): ?>
    <?php foreach ($appointments as $appointment): ?>
        <?php
            $department = !empty($appointment['department_name']) ? ' - ' . $appointment['department_name'] : '';
            $status = $appointment['status'];
            if ($status === 'completed') {
                $badge = '<span class="badge badge-success">Completed</span>';
            } elseif ($status === 'cancelled') {
                $badge = '<span class="badge badge-danger">Cancelled</span>';
            } else {
                $badge = '<span class="badge badge-primary">Scheduled</span>';
            }
        ?>
        <tr id="appointment-row-<?php echo (int) $appointment['id']; ?>">
            <td><strong><?php echo html_escape($appointment['appointment_code']); ?></strong></td>
            <td>
                <?php echo html_escape($appointment['patient_name']); ?><br>
                <small class="text-muted"><?php echo html_escape($appointment['patient_code']); ?></small>
            </td>
            <td>
                <?php echo html_escape($appointment['doctor_name']); ?><br>
                <small class="text-muted"><?php echo html_escape($department); ?></small>
            </td>
            <td><?php echo html_escape($appointment['appointment_date']); ?></td>
            <td><?php echo html_escape($appointment['appointment_time']); ?></td>
            <td><?php echo $badge; ?></td>
            <td>
                <a href="#" class="btn btn-info btn-sm mr-1 js-edit-appointment" data-id="<?php echo (int) $appointment['id']; ?>">
                    <i class="fas fa-edit mr-1"></i>Edit
                </a>
                <select class="form-control form-control-sm d-inline-block js-appointment-status" data-id="<?php echo (int) $appointment['id']; ?>" style="width: 125px; display: inline-block;">
                    <option value="scheduled" <?php echo $status === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                    <option value="completed" <?php echo $status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="7" class="text-center">No appointments found.</td>
    </tr>
<?php endif; ?>