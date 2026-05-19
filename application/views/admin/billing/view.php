<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6"><h1>Invoice Detail</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin/billing'); ?>">Billing</a></li>
                        <li class="breadcrumb-item active"><?php echo html_escape($invoice['invoice_no']); ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content invoice">
        <div class="container-fluid">
            <div class="row mb-3 no-print">
                <div class="col-12 text-right">
                    <a href="<?php echo site_url('admin/billing'); ?>" class="btn btn-default">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                    <button type="button" onclick="window.print();" class="btn btn-info">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                    <a href="<?php echo site_url('admin/billing/pdf/' . (int) $invoice['id']); ?>" target="_blank" class="btn btn-danger">
                        <i class="fas fa-file-pdf mr-1"></i> PDF
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="mb-1">Hospital Management System</h3>
                            <p class="text-muted mb-0">Central HMS Invoice</p>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <h4 class="mb-1"><?php echo html_escape($invoice['invoice_no']); ?></h4>
                            <p class="mb-0">Date: <?php echo html_escape($invoice['invoice_date']); ?></p>
                            <p class="mb-0">
                                Payment:
                                <?php if ($invoice['payment_status'] === 'paid'): ?>
                                    <span class="badge badge-success">Paid</span>
                                <?php elseif ($invoice['payment_status'] === 'partial'): ?>
                                    <span class="badge badge-warning">Partial</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Unpaid</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <strong>Patient</strong>
                            <p class="mb-0"><?php echo html_escape($invoice['patient_name']); ?></p>
                            <small class="text-muted"><?php echo html_escape($invoice['patient_code']); ?></small><br>
                            <small><?php echo html_escape($invoice['patient_phone']); ?></small>
                        </div>
                        <div class="col-md-4">
                            <strong>Doctor</strong>
                            <p class="mb-0">Dr. <?php echo html_escape($invoice['doctor_name']); ?></p>
                            <small class="text-muted"><?php echo html_escape($invoice['department_name']); ?></small>
                        </div>
                        <div class="col-md-4">
                            <strong>Appointment</strong>
                            <p class="mb-0"><?php echo html_escape($invoice['appointment_code']); ?></p>
                            <small class="text-muted"><?php echo html_escape($invoice['appointment_date'] . ' ' . $invoice['appointment_time']); ?></small>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Item</th>
                                    <th class="text-center" style="width: 90px;">Qty</th>
                                    <th class="text-right" style="width: 130px;">Unit Price</th>
                                    <th class="text-right" style="width: 130px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td><?php echo html_escape(ucfirst($item['item_type'])); ?></td>
                                        <td><?php echo html_escape($item['item_name']); ?></td>
                                        <td class="text-center"><?php echo (int) $item['quantity']; ?></td>
                                        <td class="text-right"><?php echo number_format((float) $item['unit_price'], 2); ?></td>
                                        <td class="text-right"><?php echo number_format((float) $item['line_total'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right">Consultation Fee</th>
                                    <th class="text-right"><?php echo number_format((float) $invoice['consultation_fee'], 2); ?></th>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-right">Pharmacy Charges</th>
                                    <th class="text-right"><?php echo number_format((float) $invoice['pharmacy_total'], 2); ?></th>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-right">Lab Charges</th>
                                    <th class="text-right"><?php echo number_format((float) $invoice['lab_total'], 2); ?></th>
                                </tr>
                                <tr>
                                    <th colspan="4" class="text-right h5">Grand Total</th>
                                    <th class="text-right h5"><?php echo number_format((float) $invoice['total_amount'], 2); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Payment Method:</strong> <?php echo html_escape($invoice['payment_method'] ? ucfirst($invoice['payment_method']) : '-'); ?></p>
                            <p class="mb-0"><strong>Generated By:</strong> <?php echo html_escape($invoice['generated_by_name']); ?></p>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <p class="text-muted mb-0">This is a computer-generated invoice.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
