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
                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin/pharmacy-billing'); ?>">Home</a></li>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    var listUrl = '<?php echo site_url('admin/pharmacy-billing/ajax-list'); ?>';
    var searchTimer = null;

    function escapeHtml(value) {
        return $('<div>').text(value === null || value === undefined ? '' : value).html();
    }

    function showAlert(type, message) {
        var alertClass = type === 'success' ? 'success' : 'danger';
        var html = ''
            + '<div class="alert alert-' + alertClass + ' alert-dismissible fade show" role="alert">'
            + escapeHtml(message)
            + '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'
            + '<span aria-hidden="true">&times;</span>'
            + '</button>'
            + '</div>';

        $('#pharmacyBillAlert').html(html);
    }

    function renderRows(rows) {
        var html = '';

        if (!rows || rows.length === 0) {
            $('#billsTableBody').html('<tr><td colspan="7" class="text-center">No pharmacy bills found.</td></tr>');
            return;
        }

        $.each(rows, function (index, bill) {
            html += '<tr>';
            html += '<td><strong>' + escapeHtml(bill.bill_code) + '</strong></td>';
            html += '<td>' + escapeHtml(bill.appointment_code) + '<br><small class="text-muted">Prescription: ' + escapeHtml(bill.prescription_date) + '</small></td>';
            html += '<td>' + escapeHtml(bill.patient_name) + '<br><small class="text-muted">' + escapeHtml(bill.patient_code) + '</small></td>';
            html += '<td>' + escapeHtml(bill.doctor_name) + '</td>';
            html += '<td>' + escapeHtml(bill.bill_date) + '</td>';
            html += '<td><span class="badge badge-info">' + escapeHtml(bill.item_count) + '</span></td>';
            html += '<td><strong>' + escapeHtml(parseFloat(bill.total_amount).toFixed(2)) + '</strong></td>';
            html += '</tr>';
        });

        $('#billsTableBody').html(html);
    }

    function loadBills() {
        $('#billsTableBody').html('<tr><td colspan="7" class="text-center">Loading pharmacy bills...</td></tr>');

        $.ajax({
            url: listUrl,
            type: 'GET',
            dataType: 'json',
            data: {
                search: $('#billSearch').val(),
                bill_date: $('#billDateFilter').val()
            },
            success: function (response) {
                if (response.status) {
                    renderRows(response.data);
                    return;
                }

                renderRows([]);
                showAlert('danger', response.message || 'Unable to load pharmacy bills.');
            },
            error: function () {
                renderRows([]);
                showAlert('danger', 'Unable to load pharmacy bills.');
            }
        });
    }

    $('#billSearch').on('keyup', function () {
        window.clearTimeout(searchTimer);

        searchTimer = window.setTimeout(function () {
            loadBills();
        }, 300);
    });

    $('#billDateFilter').on('change', function () {
        loadBills();
    });

    $('#clearFiltersButton').on('click', function () {
        $('#billSearch').val('');
        $('#billDateFilter').val('');
        loadBills();
    });

    loadBills();
});
</script>
