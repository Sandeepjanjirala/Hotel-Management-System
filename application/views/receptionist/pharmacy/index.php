<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="page-actions clearfix">
    <a class="btn btn-success pull-right" href="<?php echo site_url('receptionist/pharmacy-billing/create'); ?>">
        <i class="fa fa-plus"></i> Generate Pharmacy Bill
    </a>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Pharmacy Bills</h3>
        <div class="box-tools hms-filter">
            <input type="text" id="pharmacy-search" class="form-control input-sm" placeholder="Search">
            <input type="date" id="bill-date" class="form-control input-sm">
        </div>
    </div>
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover" id="pharmacy-table">
            <thead><tr><th>Bill</th><th>Date</th><th>Patient</th><th>Doctor</th><th>Items</th><th>Total</th><th></th></tr></thead>
            <tbody><tr><td colspan="7" class="text-center text-muted">Loading pharmacy bills...</td></tr></tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var endpoint = '<?php echo site_url('receptionist/pharmacy-billing/ajax-list'); ?>';
    var $tbody = $('#pharmacy-table tbody');

    function loadBills() {
        $.get(endpoint, {
            search: $('#pharmacy-search').val(),
            bill_date: $('#bill-date').val()
        }, function (response) {
            if (!response.data || response.data.length === 0) {
                $tbody.html('<tr><td colspan="7" class="text-center text-muted">No pharmacy bills found.</td></tr>');
                return;
            }

            $tbody.html(response.data.map(function (bill) {
                return '<tr>' +
                    '<td>' + bill.bill_code + '</td>' +
                    '<td>' + bill.bill_date + '</td>' +
                    '<td>' + bill.patient_name + '</td>' +
                    '<td>' + bill.doctor_name + '</td>' +
                    '<td>' + bill.item_count + '</td>' +
                    '<td>' + bill.total_amount + '</td>' +
                    '<td><a class="btn btn-xs btn-default" href="<?php echo site_url('receptionist/pharmacy-billing/view/'); ?>' + bill.id + '"><i class="fa fa-eye"></i></a></td>' +
                    '</tr>';
            }).join(''));
        }, 'json');
    }

    $('#pharmacy-search, #bill-date').on('change keyup', function () {
        clearTimeout(window.pharmacyTimer);
        window.pharmacyTimer = setTimeout(loadBills, 250);
    });

    loadBills();
});
</script>
