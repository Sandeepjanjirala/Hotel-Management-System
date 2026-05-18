<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6"><h1>Lab Results</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin/lab-requests'); ?>">Lab Requests</a></li>
                        <li class="breadcrumb-item active">Results</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div id="labResultAlert"></div>

            <form id="labResultForm" autocomplete="off">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-vial mr-1"></i> <?php echo html_escape($request['request_code']); ?>
                        </h3>
                        <div class="card-tools">
                            <a href="<?php echo site_url('admin/lab-requests'); ?>" class="btn btn-default btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Back
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Patient:</strong> <?php echo html_escape($request['patient_name']); ?>
                                <br>
                                <small class="text-muted"><?php echo html_escape($request['patient_code'] . ' | ' . $request['patient_phone']); ?></small>
                            </div>
                            <div class="col-md-4">
                                <strong>Doctor:</strong> <?php echo html_escape($request['doctor_name']); ?>
                                <br>
                                <small class="text-muted"><?php echo html_escape($request['appointment_code']); ?></small>
                            </div>
                            <div class="col-md-4">
                                <strong>Request Date:</strong> <?php echo html_escape($request['request_date']); ?>
                                <br>
                                <small class="text-muted"><?php echo html_escape($request['appointment_date'] . ' ' . $request['appointment_time']); ?></small>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group mb-0">
                            <label>Request Status</label>
                            <select class="form-control col-md-3" name="status">
                                <option value="pending" <?php echo ($request['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="processing" <?php echo ($request['status'] === 'processing') ? 'selected' : ''; ?>>Processing</option>
                                <option value="completed" <?php echo ($request['status'] === 'completed') ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0"><i class="fas fa-vials mr-1"></i> Test Results</h3>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Test</th>
                                        <th>Normal Range</th>
                                        <th style="min-width: 180px;">Result Value</th>
                                        <th style="width: 150px;">Result Status</th>
                                        <th style="min-width: 220px;">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $index => $item): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo html_escape($item['test_name']); ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo html_escape($item['test_code'] . ' | ' . $item['category']); ?></small>
                                                <input type="hidden" name="items[<?php echo (int) $index; ?>][item_id]" value="<?php echo (int) $item['id']; ?>">
                                            </td>
                                            <td><?php echo !empty($item['normal_range']) ? html_escape($item['normal_range']) : '-'; ?></td>
                                            <td>
                                                <input type="text" class="form-control" name="items[<?php echo (int) $index; ?>][result_value]" value="<?php echo html_escape($item['result_value']); ?>">
                                            </td>
                                            <td>
                                                <select class="form-control" name="items[<?php echo (int) $index; ?>][result_status]">
                                                    <option value="pending" <?php echo ($item['result_status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="normal" <?php echo ($item['result_status'] === 'normal') ? 'selected' : ''; ?>>Normal</option>
                                                    <option value="abnormal" <?php echo ($item['result_status'] === 'abnormal') ? 'selected' : ''; ?>>Abnormal</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="items[<?php echo (int) $index; ?>][remarks]" value="<?php echo html_escape($item['remarks']); ?>">
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <a href="<?php echo site_url('admin/lab-requests'); ?>" class="btn btn-default">Cancel</a>
                        <button type="submit" id="saveLabResultsButton" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Save Results
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var saveUrl = '<?php echo site_url('admin/lab-requests/save-results/' . (int) $request['id']); ?>';

    function updateCsrf(hash) {
        if (hash) {
            csrfHash = hash;
            $('input[name="' + csrfName + '"]').val(hash);
        }
    }

    function escapeHtml(value) {
        return $('<div>').text(value === null || value === undefined ? '' : value).html();
    }

    function alertBox(type, message) {
        $('#labResultAlert').html(
            '<div class="alert alert-' + (type === 'success' ? 'success' : 'danger') + ' alert-dismissible fade show">' +
            escapeHtml(message) +
            '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>'
        );
    }

    $('#labResultForm').on('submit', function (event) {
        event.preventDefault();

        var $button = $('#saveLabResultsButton');
        var originalText = $button.text();

        $button.prop('disabled', true).text('Saving...');

        $.ajax({
            url: saveUrl,
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
            success: function (response) {
                updateCsrf(response.csrfHash);

                if (response.status) {
                    alertBox('success', response.message);
                    window.setTimeout(function () {
                        window.location.href = response.redirectUrl;
                    }, 700);
                    return;
                }

                alertBox('danger', response.message || 'Unable to save lab results.');
                $button.prop('disabled', false).text(originalText);
            },
            error: function () {
                alertBox('danger', 'Unable to save lab results.');
                $button.prop('disabled', false).text(originalText);
            }
        });
    });
});
</script>
