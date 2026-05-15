<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<tr>
    <td>
        <select class="form-control" name="items[<?php echo (int) $row_index; ?>][medicine_id]">
            <option value="">Select Medicine</option>
            <?php foreach ($medicines as $medicine): ?>
                <option value="<?php echo (int) $medicine['id']; ?>">
                    <?php
                        $label = $medicine['medicine_name'] . ' - ' . $medicine['unit'];
                        if (!empty($medicine['generic_name'])) {
                            $label .= ' (' . $medicine['generic_name'] . ')';
                        }
                        echo html_escape($label);
                    ?>
                </option>
            <?php endforeach; ?>
        </select>
    </td>
    <td>
        <input type="text" class="form-control" name="items[<?php echo (int) $row_index; ?>][dosage]" placeholder="1 tablet" maxlength="100">
    </td>
    <td>
        <input type="number" class="form-control" name="items[<?php echo (int) $row_index; ?>][days]" min="1" step="1" value="1">
    </td>
    <td class="text-center">
        <input type="hidden" name="items[<?php echo (int) $row_index; ?>][morning]" value="0">
        <input type="checkbox" name="items[<?php echo (int) $row_index; ?>][morning]" value="1">
    </td>
    <td class="text-center">
        <input type="hidden" name="items[<?php echo (int) $row_index; ?>][afternoon]" value="0">
        <input type="checkbox" name="items[<?php echo (int) $row_index; ?>][afternoon]" value="1">
    </td>
    <td class="text-center">
        <input type="hidden" name="items[<?php echo (int) $row_index; ?>][night]" value="0">
        <input type="checkbox" name="items[<?php echo (int) $row_index; ?>][night]" value="1">
    </td>
    <td>
        <input type="text" class="form-control" name="items[<?php echo (int) $row_index; ?>][instructions]" placeholder="After food">
    </td>
    <td class="text-center">
        <button type="button" class="btn btn-danger btn-sm btn-remove-medicine-row">
            <i class="fas fa-trash"></i>
        </button>
    </td>
</tr>
