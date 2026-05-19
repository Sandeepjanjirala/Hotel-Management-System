<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6"><h1>Billing</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin/billing'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Billing</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div id="billingAlert"></div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-file-invoice-dollar mr-1"></i> Invoice List
                    </h3>
                    <div class="card-tools">
                        <a href="<?php echo site_url('admin/billing/create'); ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i> Generate Invoice
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-lg-4 col-md-6 col-12 mb-2 mb-lg-0">
                            <input type="text" id="invoiceSearch" class="form-control" placeholder="Search invoice, appointment, patient, phone or doctor">
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 mb-2 mb-lg-0">
                            <input type="date" id="invoiceDateFilter" class="form-control">
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 mb-2 mb-lg-0">
                            <select id="paymentStatusFilter" class="form-control">
                                <option value="">All Payment Status</option>
                                <option value="unpaid">Unpaid</option>
                                <option value="partial">Partial</option>
                                <option value="paid">Paid</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-12">
                            <button type="button" id="clearFiltersButton" class="btn btn-default btn-block">
                                <i class="fas fa-times mr-1"></i> Clear
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Invoice No</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Appointment</th>
                                    <th>Invoice Date</th>
                                    <th>Total</th>
                                    <th>Payment</th>
                                    <th style="width: 160px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="billingTableBody">
                                <tr><td colspan="8" class="text-center">Loading invoices...</td></tr>
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
    var listUrl = '<?php echo site_url('admin/billing/ajax-list'); ?>';
    var viewBaseUrl = '<?php echo site_url('admin/billing/view'); ?>';
    var pdfBaseUrl = '<?php echo site_url('admin/billing/pdf'); ?>';
    var searchTimer = null;

    function escapeHtml(value) {
        return $('<div>').text(value === null || value === undefined ? '' : value).html();
    }

    function showAlert(type, message) {
        $('#billingAlert').html(
            '<div class="alert alert-' + (type === 'success' ? 'success' : 'danger') + ' alert-dismissible fade show">' +
            escapeHtml(message) +
            '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>'
        );
    }

    function paymentBadge(status) {
        if (status === 'paid') {
            return '<span class="badge badge-success">Paid</span>';
        }

        if (status === 'partial') {
            return '<span class="badge badge-warning">Partial</span>';
        }

        return '<span class="badge badge-danger">Unpaid</span>';
    }

    function renderRows(rows) {
        var html = '';

        if (!rows || rows.length === 0) {
            $('#billingTableBody').html('<tr><td colspan="8" class="text-center">No invoices found.</td></tr>');
            return;
        }

        $.each(rows, function (_, invoice) {
            html += '<tr>';
            html += '<td><strong>' + escapeHtml(invoice.invoice_no) + '</strong></td>';
            html += '<td>' + escapeHtml(invoice.patient_name) + '<br><small class="text-muted">' + escapeHtml(invoice.patient_code) + '</small></td>';
            html += '<td>' + escapeHtml(invoice.doctor_name) + '</td>';
            html += '<td>' + escapeHtml(invoice.appointment_code) + '</td>';
            html += '<td>' + escapeHtml(invoice.invoice_date) + '</td>';
            html += '<td><strong>' + escapeHtml(parseFloat(invoice.total_amount).toFixed(2)) + '</strong></td>';
            html += '<td>' + paymentBadge(invoice.payment_status) + '<br><small class="text-muted">' + escapeHtml(invoice.payment_method || '-') + '</small></td>';
            html += '<td>';
            html += '<a href="' + viewBaseUrl + '/' + escapeHtml(invoice.id) + '" class="btn btn-info btn-sm mr-1"><i class="fas fa-eye"></i></a>';
            html += '<a href="' + pdfBaseUrl + '/' + escapeHtml(invoice.id) + '" target="_blank" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf"></i></a>';
            html += '</td>';
            html += '</tr>';
        });

        $('#billingTableBody').html(html);
    }

    function loadInvoices() {
        $('#billingTableBody').html('<tr><td colspan="8" class="text-center">Loading invoices...</td></tr>');

        $.ajax({
            url: listUrl,
            type: 'GET',
            dataType: 'json',
            data: {
                search: $('#invoiceSearch').val(),
                invoice_date: $('#invoiceDateFilter').val(),
                payment_status: $('#paymentStatusFilter').val()
            },
            success: function (response) {
                response.status ? renderRows(response.data) : showAlert('danger', response.message || 'Unable to load invoices.');
            },
            error: function () {
                showAlert('danger', 'Unable to load invoices.');
            }
        });
    }

    $('#invoiceSearch').on('keyup', function () {
        window.clearTimeout(searchTimer);
        searchTimer = window.setTimeout(loadInvoices, 300);
    });

    $('#invoiceDateFilter, #paymentStatusFilter').on('change', loadInvoices);

    $('#clearFiltersButton').on('click', function () {
        $('#invoiceSearch').val('');
        $('#invoiceDateFilter').val('');
        $('#paymentStatusFilter').val('');
        loadInvoices();
    });

    loadInvoices();
});
</script>
