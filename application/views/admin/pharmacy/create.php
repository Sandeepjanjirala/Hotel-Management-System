<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Generate Pharmacy Bill</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin/pharmacy-billing'); ?>">Pharmacy Billing</a></li>
                        <li class="breadcrumb-item active">Generate</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div id="billingAlert"></div>

            <form id="pharmacyBillForm" autocomplete="off">
                <input
                    type="hidden"
                    name="<?php echo $this->security->get_csrf_token_name(); ?>"
                    value="<?php echo $this->security->get_csrf_hash(); ?>"
                >

                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-file-medical-alt mr-1"></i> Prescription Selection
                        </h3>
                        <div class="card-tools">
                            <a href="<?php echo site_url('admin/pharmacy-billing'); ?>" class="btn btn-default btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Back
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="form-group">
                            <label for="prescription_id">Prescription <span class="text-danger">*</span></label>
                            <select class="form-control" id="prescription_id" name="prescription_id">
                                <option value="">Select Prescription</option>
                                <?php foreach ($prescriptions as $prescription): ?>
                                    <option value="<?php echo (int) $prescription['id']; ?>" <?php echo ((int) $selected_prescription_id === (int) $prescription['id']) ? 'selected' : ''; ?>>
                                        <?php echo html_escape($prescription['appointment_code'] . ' - ' . $prescription['patient_name'] . ' with Dr. ' . $prescription['doctor_name'] . ' (' . $prescription['prescription_date'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group mb-0">
                            <label for="bill_date">Bill Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control col-md-3" id="bill_date" name="bill_date" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                </div>

                <div id="prescriptionDetailsBox" class="card d-none">
                    <div class="card-header">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-prescription mr-1"></i> Prescription Details
                        </h3>
                    </div>
                    <div class="card-body" id="prescriptionDetailsContent"></div>
                </div>

                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-capsules mr-1"></i> Medicines For Billing
                        </h3>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Medicine</th>
                                        <th>Dosage</th>
                                        <th>Days</th>
                                        <th>Schedule</th>
                                        <th>Available Stock</th>
                                        <th style="width: 140px;">Quantity</th>
                                        <th style="width: 130px;">Unit Price</th>
                                        <th style="width: 140px;">Line Total</th>
                                    </tr>
                                </thead>
                                <tbody id="billItemsTableBody">
                                    <tr>
                                        <td colspan="8" class="text-center">Select a prescription to load medicines.</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="7" class="text-right">Total Amount</th>
                                        <th id="grandTotal">0.00</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <a href="<?php echo site_url('admin/pharmacy-billing'); ?>" class="btn btn-default">Cancel</a>
                        <button type="submit" class="btn btn-primary" id="saveBillButton" disabled>
                            <i class="fas fa-save mr-1"></i> Save Pharmacy Bill
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

<script src="<?php echo base_url('assets/js/modules/pharmacy.js'); ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    PharmacyModule.initCreate({
        csrfName: '<?php echo $this->security->get_csrf_token_name(); ?>',
        csrfHash: '<?php echo $this->security->get_csrf_hash(); ?>',
        prescriptionUrl: '<?php echo site_url('admin/pharmacy-billing/prescription'); ?>',
        storeUrl: '<?php echo site_url('admin/pharmacy-billing/store'); ?>'
    });
});
</script>