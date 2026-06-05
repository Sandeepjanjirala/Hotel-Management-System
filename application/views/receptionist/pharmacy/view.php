<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-8">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"><?php echo html_escape($bill['bill_code']); ?></h3></div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <thead><tr><th>Medicine</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr></thead>
                    <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo html_escape($item['medicine_name']); ?></td>
                            <td><?php echo (int) $item['quantity']; ?></td>
                            <td><?php echo html_escape($item['unit_price']); ?></td>
                            <td><?php echo html_escape($item['line_total']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="box box-success">
            <div class="box-header with-border"><h3 class="box-title">Summary</h3></div>
            <div class="box-body">
                <p><strong>Patient:</strong> <?php echo html_escape($bill['patient_name']); ?></p>
                <p><strong>Doctor:</strong> <?php echo html_escape($bill['doctor_name']); ?></p>
                <p><strong>Date:</strong> <?php echo html_escape($bill['bill_date']); ?></p>
                <h4>Total: <?php echo html_escape($bill['total_amount']); ?></h4>
            </div>
        </div>
    </div>
</div>
