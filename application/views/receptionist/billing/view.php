<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-8">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"><?php echo html_escape($invoice['invoice_no']); ?></h3></div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <thead><tr><th>Item</th><th>Qty</th><th>Unit</th><th>Total</th></tr></thead>
                    <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo html_escape($item['item_name']); ?></td>
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
            <div class="box-header with-border"><h3 class="box-title">Payment</h3></div>
            <div class="box-body">
                <p><strong>Patient:</strong> <?php echo html_escape($invoice['patient_name']); ?></p>
                <p><strong>Total:</strong> <?php echo html_escape($invoice['total_amount']); ?></p>
                <p><strong>Status:</strong> <?php echo html_escape($invoice['payment_status']); ?></p>
                <?php echo form_open('receptionist/billing/mark-payment/' . (int) $invoice['id']); ?>
                    <div class="form-group">
                        <label>Payment Status</label>
                        <select name="payment_status" class="form-control">
                            <?php foreach (array('unpaid','partial','paid') as $status): ?>
                                <option value="<?php echo $status; ?>" <?php echo $invoice['payment_status'] === $status ? 'selected' : ''; ?>><?php echo ucfirst($status); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Payment Method</label>
                        <select name="payment_method" class="form-control">
                            <option value="">None</option>
                            <?php foreach (array('cash','card','upi','other') as $method): ?>
                                <option value="<?php echo $method; ?>" <?php echo $invoice['payment_method'] === $method ? 'selected' : ''; ?>><?php echo strtoupper($method); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success btn-block"><i class="fa fa-save"></i> Update Payment</button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
