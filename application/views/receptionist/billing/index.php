<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-actions clearfix">
    <a class="btn btn-success pull-right" href="<?php echo site_url('receptionist/billing/create'); ?>">
        <i class="fa fa-plus"></i> Generate Invoice
    </a>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Invoices</h3>
        <div class="box-tools hms-filter">
            <input type="text" id="billing-search" class="form-control input-sm" placeholder="Search invoice/patient">
            <select id="payment-status" class="form-control input-sm">
                <option value="">All Payment</option>
                <option value="unpaid">Unpaid</option>
                <option value="partial">Partial</option>
                <option value="paid">Paid</option>
            </select>
            <input type="date" id="invoice-date" class="form-control input-sm">
        </div>
    </div>
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover" id="billing-table">
            <thead><tr><th>Invoice</th><th>Date</th><th>Patient</th><th>Doctor</th><th>Total</th><th>Payment</th><th></th></tr></thead>
            <tbody><tr><td colspan="7" class="text-center text-muted">Loading invoices...</td></tr></tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var endpoint = '<?php echo site_url('receptionist/billing/ajax-list'); ?>';
    var $tbody = $('#billing-table tbody');

    function loadBills() {
        $.get(endpoint, {
            search: $('#billing-search').val(),
            payment_status: $('#payment-status').val(),
            invoice_date: $('#invoice-date').val()
        }, function (response) {
            if (!response.data || response.data.length === 0) {
                $tbody.html('<tr><td colspan="7" class="text-center text-muted">No invoices found.</td></tr>');
                return;
            }

            $tbody.html(response.data.map(function (bill) {
                return '<tr>' +
                    '<td>' + bill.invoice_no + '</td>' +
                    '<td>' + bill.invoice_date + '</td>' +
                    '<td>' + bill.patient_name + '</td>' +
                    '<td>' + bill.doctor_name + '</td>' +
                    '<td>' + bill.total_amount + '</td>' +
                    '<td><span class="label label-info">' + bill.payment_status + '</span></td>' +
                    '<td><a class="btn btn-xs btn-default" href="<?php echo site_url('receptionist/billing/view/'); ?>' + bill.id + '"><i class="fa fa-eye"></i></a></td>' +
                    '</tr>';
            }).join(''));
        }, 'json');
    }

    $('#billing-search, #payment-status, #invoice-date').on('change keyup', function () {
        clearTimeout(window.billingTimer);
        window.billingTimer = setTimeout(loadBills, 250);
    });

    loadBills();
});
</script>
