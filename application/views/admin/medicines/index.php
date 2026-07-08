<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1>Medicine Master</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Medicines</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div id="medicineAlert"></div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-capsules mr-1"></i> Medicine List
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" id="addMedicineButton">
                            <i class="fas fa-plus mr-1"></i> Add Medicine
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-lg-5 col-md-6 col-12 mb-2 mb-lg-0">
                            <input type="text" id="medicineSearch" class="form-control" placeholder="Search medicine, generic name or unit">
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 mb-2 mb-lg-0">
                            <select id="statusFilter" class="form-control">
                                <option value="">All Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
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
                                    <th>Medicine Name</th>
                                    <th>Generic Name</th>
                                    <th style="width: 100px;">Unit</th>
                                    <th style="width: 140px;">Stock Qty</th>
                                    <th style="width: 130px;">Unit Price</th>
                                    <th style="width: 110px;">Status</th>
                                    <th style="width: 180px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="medicinesTableBody">
                                <tr>
                                    <td colspan="7" class="text-center">Loading medicines...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add Medicine Modal -->
<div class="modal fade" id="addMedicineModal" tabindex="-1" role="dialog" aria-labelledby="addMedicineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="addMedicineForm" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMedicineModalLabel">Add Medicine</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input
                        type="hidden"
                        name="<?php echo $this->security->get_csrf_token_name(); ?>"
                        value="<?php echo $this->security->get_csrf_hash(); ?>"
                    >

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add_medicine_name">Medicine Name</label>
                                <input type="text" class="form-control" id="add_medicine_name" name="medicine_name" maxlength="150">
                                <small class="text-danger validation-message" data-field="medicine_name"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add_generic_name">Generic Name</label>
                                <input type="text" class="form-control" id="add_generic_name" name="generic_name" maxlength="150">
                                <small class="text-danger validation-message" data-field="generic_name"></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="add_unit">Unit</label>
                                <input type="text" class="form-control" id="add_unit" name="unit" maxlength="50" placeholder="Tablet, Syrup, Vial">
                                <small class="text-danger validation-message" data-field="unit"></small>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="add_stock_quantity">Stock Quantity</label>
                                <input type="number" min="0" step="1" class="form-control" id="add_stock_quantity" name="stock_quantity" value="0">
                                <small class="text-danger validation-message" data-field="stock_quantity"></small>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="add_unit_price">Unit Price</label>
                                <input type="number" min="0" step="0.01" class="form-control" id="add_unit_price" name="unit_price" value="0.00">
                                <small class="text-danger validation-message" data-field="unit_price"></small>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="add_status">Status</label>
                                <select class="form-control" id="add_status" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <small class="text-danger validation-message" data-field="status"></small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label for="add_description">Description</label>
                        <textarea class="form-control" id="add_description" name="description" rows="3"></textarea>
                        <small class="text-danger validation-message" data-field="description"></small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveMedicineButton">Save Medicine</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Medicine Modal -->
<div class="modal fade" id="editMedicineModal" tabindex="-1" role="dialog" aria-labelledby="editMedicineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="editMedicineForm" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMedicineModalLabel">Edit Medicine</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input
                        type="hidden"
                        name="<?php echo $this->security->get_csrf_token_name(); ?>"
                        value="<?php echo $this->security->get_csrf_hash(); ?>"
                    >
                    <input type="hidden" id="edit_medicine_id" name="medicine_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_medicine_name">Medicine Name</label>
                                <input type="text" class="form-control" id="edit_medicine_name" name="medicine_name" maxlength="150">
                                <small class="text-danger validation-message" data-field="medicine_name"></small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_generic_name">Generic Name</label>
                                <input type="text" class="form-control" id="edit_generic_name" name="generic_name" maxlength="150">
                                <small class="text-danger validation-message" data-field="generic_name"></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="edit_unit">Unit</label>
                                <input type="text" class="form-control" id="edit_unit" name="unit" maxlength="50">
                                <small class="text-danger validation-message" data-field="unit"></small>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="edit_stock_quantity">Stock Quantity</label>
                                <input type="number" min="0" step="1" class="form-control" id="edit_stock_quantity" name="stock_quantity">
                                <small class="text-danger validation-message" data-field="stock_quantity"></small>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="edit_unit_price">Unit Price</label>
                                <input type="number" min="0" step="0.01" class="form-control" id="edit_unit_price" name="unit_price">
                                <small class="text-danger validation-message" data-field="unit_price"></small>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="edit_status">Status</label>
                                <select class="form-control" id="edit_status" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <small class="text-danger validation-message" data-field="status"></small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label for="edit_description">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        <small class="text-danger validation-message" data-field="description"></small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="updateMedicineButton">Update Medicine</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include Medicine Module Script -->
<script src="<?php echo base_url('assets/js/modules/medicines.js'); ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Initialize the Medicines module
    Medicines.init({
        csrfName: '<?php echo $this->security->get_csrf_token_name(); ?>',
        csrfHash: '<?php echo $this->security->get_csrf_hash(); ?>',
        listUrl: '<?php echo site_url('admin/medicines/ajax-list'); ?>',
        storeUrl: '<?php echo site_url('admin/medicines/store'); ?>',
        editBaseUrl: '<?php echo site_url('admin/medicines/edit'); ?>',
        updateBaseUrl: '<?php echo site_url('admin/medicines/update'); ?>',
        changeStatusBaseUrl: '<?php echo site_url('admin/medicines/change-status'); ?>'
    });
});
</script>