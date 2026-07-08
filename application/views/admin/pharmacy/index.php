<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Pharmacy Billing</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Pharmacy Billing</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div id="pharmacyBillAlert"></div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-file-medical-alt mr-1"></i> Pharmacy Bill List
                    </h3>
                    <div class="card-tools">
                        <a href="<?php echo site_url('admin/pharmacy-billing/create'); ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i> Generate Pharmacy Bill
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-lg-5 col-md-6 col-12 mb-2 mb-lg-0">
                            <input type="text" id="billSearch" class="form-control" placeholder="Search bill, appointment, patient, phone or doctor">
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 mb-2 mb-lg-0">
                            <input type="date" id="billDateFilter" class="form-control">
                        </div>
                        <div class="col-lg-2 col-md-3 col-6">
                            <button type="button" id="clearFiltersButton" class="btn btn-default btn-block">
                                <i class="fas fa-times mr-1"></i> Clear
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Bill Code</th>
                                    <th>Prescription</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Bill Date</th>
                                    <th>Items</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tbody id="billsTableBody">
                                <tr>
                                    <td colspan="7" class="text-center">Loading pharmacy bills...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script src="<?php echo base_url('assets/js/modules/pharmacy.js'); ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    PharmacyModule.init({
        listUrl: '<?php echo site_url('admin/pharmacy-billing/ajax-list'); ?>'
    });
});
</script>