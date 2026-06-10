<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if (!empty($patients)): ?>
    <?php foreach ($patients as $patient): ?>
        <?php
            $is_active = (int) $patient['status'] === 1;
            $toggle_text = $is_active ? 'Deactivate' : 'Activate';
            $toggle_class = $is_active ? 'btn-warning' : 'btn-success';
        ?>
        <tr id="patient-row-<?php echo (int) $patient['id']; ?>">
            <td><strong><?php echo html_escape($patient['patient_code']); ?></strong></td>
            <td><?php echo html_escape($patient['full_name']); ?></td>
            <td><?php echo html_escape(ucfirst($patient['gender'])); ?></td>
            <td><?php echo html_escape($patient['blood_group'] ?: 'Unknown'); ?></td>
            <td><?php echo html_escape($patient['phone']); ?></td>
            <td>
                <?php if ($is_active): ?>
                    <span class="badge badge-success">Active</span>
                <?php else: ?>
                    <span class="badge badge-secondary">Inactive</span>
                <?php endif; ?>
            </td>
            <td>
                <a href="<?php echo site_url('admin/patients/edit/' . (int) $patient['id']); ?>" class="btn btn-info btn-sm mr-1">
                    <i class="fas fa-edit mr-1"></i>Edit
                </a>
                <button
                    type="button"
                    class="btn <?php echo $toggle_class; ?> btn-sm js-patient-status"
                    data-id="<?php echo (int) $patient['id']; ?>"
                    data-url="<?php echo site_url('admin/patients/change-status/' . (int) $patient['id']); ?>"
                    data-confirm="Are you sure you want to <?php echo strtolower($toggle_text); ?> this patient?"
                >
                    <i class="fas fa-sync-alt mr-1"></i><?php echo $toggle_text; ?>
                </button>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="7" class="text-center">No patients found.</td>
    </tr>
<?php endif; ?>
