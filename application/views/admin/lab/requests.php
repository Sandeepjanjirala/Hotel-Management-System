<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6"><h1>Lab Requests</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin/lab-requests'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Lab Requests</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div id="labRequestAlert"></div>

            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="small-box bg-info">
                        <div class="inner"><h3><?php echo (int) $counters['active_tests']; ?></h3><p>Active Lab Tests</p></div>
                        <div class="icon"><i class="fas fa-vials"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="small-box bg-warning">
                        <div class="inner"><h3><?php echo (int) $counters['pending_requests']; ?></h3><p>Pending Requests</p></div>
                        <div class="icon"><i class="fas fa-clock"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="small-box bg-primary">
                        <div class="inner"><h3><?php echo (int) $counters['processing_requests']; ?></h3><p>Processing Requests</p></div>
                        <div class="icon"><i class="fas fa-spinner"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="small-box bg-success">
                        <div class="inner"><h3><?php echo (int) $counters['completed_requests']; ?></h3><p>Completed Requests</p></div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0"><i class="fas fa-flask mr-1"></i> Patient Lab Requests</h3>
                    <div class="card-tools">
                        <a href="<?php echo site_url('admin/lab-requests/create'); ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i> New Lab Request
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-lg-4 col-md-6 col-12 mb-2 mb-lg-0">
                            <input type="text" id="requestSearch" class="form-control" placeholder="Search request, patient, phone, doctor">
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 mb-2 mb-lg-0">
                            <select id="requestStatusFilter" class="form-control">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-3 col-6 mb-2 mb-lg-0">
                            <input type="date" id="requestDateFilter" class="form-control">
                        </div>
                        <div class="col-lg-2 col-12">
                            <button type="button" id="clearRequestFiltersButton" class="btn btn-default btn-block">
                                <i class="fas fa-times mr-1"></i> Clear
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Request Code</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Appointment</th>
                                    <th>Request Date</th>
                                    <th>Tests</th>
                                    <th>Status</th>
                                    <th style="width: 260px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="labRequestsTableBody">
                                <tr><td colspan="8" class="text-center">Loading lab requests...</td></tr>
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
    var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var listUrl = '<?php echo site_url('admin/lab-requests/ajax-list'); ?>';
    var statusBaseUrl = '<?php echo site_url('admin/lab-requests/change-status'); ?>';
    var resultsBaseUrl = '<?php echo site_url('admin/lab-requests/results'); ?>';
    var searchTimer = null;

    function updateCsrf(hash) {
        if (hash) {
            csrfHash = hash;
        }
    }

    function escapeHtml(value) {
        return $('<div>').text(value === null || value === undefined ? '' : value).html();
    }

    function alertBox(type, message) {
        $('#labRequestAlert').html(
            '<div class="alert alert-' + (type === 'success' ? 'success' : 'danger') + ' alert-dismissible fade show">' +
            escapeHtml(message) +
            '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>'
        );
    }

    function statusBadge(status) {
        if (status === 'completed') {
            return '<span class="badge badge-success">Completed</span>';
        }

        if (status === 'processing') {
            return '<span class="badge badge-primary">Processing</span>';
        }

        return '<span class="badge badge-warning">Pending</span>';
    }

    function renderRows(rows) {
        var html = '';

        if (!rows || rows.length === 0) {
            $('#labRequestsTableBody').html('<tr><td colspan="8" class="text-center">No lab requests found.</td></tr>');
            return;
        }

        $.each(rows, function (_, request) {
            html += '<tr>';
            html += '<td><strong>' + escapeHtml(request.request_code) + '</strong></td>';
            html += '<td>' + escapeHtml(request.patient_name) + '<br><small class="text-muted">' + escapeHtml(request.patient_code) + '</small></td>';
            html += '<td>' + escapeHtml(request.doctor_name) + '</td>';
            html += '<td>' + escapeHtml(request.appointment_code) + '<br><small class="text-muted">' + escapeHtml(request.appointment_date + ' ' + request.appointment_time) + '</small></td>';
            html += '<td>' + escapeHtml(request.request_date) + '</td>';
            html += '<td><span class="badge badge-info">' + escapeHtml(request.test_count) + '</span></td>';
            html += '<td>' + statusBadge(request.status) + '</td>';
            html += '<td>';
            html += '<a href="' + resultsBaseUrl + '/' + escapeHtml(request.id) + '" class="btn btn-info btn-sm mr-1"><i class="fas fa-vial mr-1"></i>Results</a>';
            html += '<select class="form-control form-control-sm d-inline-block request-status-select" data-id="' + escapeHtml(request.id) + '" style="width: 130px;">';
            html += '<option value="pending" ' + (request.status === 'pending' ? 'selected' : '') + '>Pending</option>';
            html += '<option value="processing" ' + (request.status === 'processing' ? 'selected' : '') + '>Processing</option>';
            html += '<option value="completed" ' + (request.status === 'completed' ? 'selected' : '') + '>Completed</option>';
            html += '</select>';
            html += '</td>';
            html += '</tr>';
        });

        $('#labRequestsTableBody').html(html);
    }

    function loadRequests() {
        $('#labRequestsTableBody').html('<tr><td colspan="8" class="text-center">Loading lab requests...</td></tr>');

        $.ajax({
            url: listUrl,
            type: 'GET',
            dataType: 'json',
            data: {
                search: $('#requestSearch').val(),
                status: $('#requestStatusFilter').val(),
                request_date: $('#requestDateFilter').val()
            },
            success: function (response) {
                updateCsrf(response.csrfHash);
                response.status ? renderRows(response.data) : alertBox('danger', response.message || 'Unable to load lab requests.');
            },
            error: function () {
                alertBox('danger', 'Unable to load lab requests.');
            }
        });
    }

    $(document).on('change', '.request-status-select', function () {
        var payload = {};
        payload[csrfName] = csrfHash;
        payload.status = $(this).val();

        $.ajax({
            url: statusBaseUrl + '/' + $(this).data('id'),
            type: 'POST',
            dataType: 'json',
            data: payload,
            success: function (response) {
                updateCsrf(response.csrfHash);

                if (response.status) {
                    loadRequests();
                    alertBox('success', response.message);
                    return;
                }

                loadRequests();
                alertBox('danger', response.message || 'Unable to update status.');
            },
            error: function () {
                loadRequests();
                alertBox('danger', 'Unable to update status.');
            }
        });
    });

    $('#requestSearch').on('keyup', function () {
        window.clearTimeout(searchTimer);
        searchTimer = window.setTimeout(loadRequests, 300);
    });

    $('#requestStatusFilter, #requestDateFilter').on('change', loadRequests);

    $('#clearRequestFiltersButton').on('click', function () {
        $('#requestSearch').val('');
        $('#requestStatusFilter').val('');
        $('#requestDateFilter').val('');
        loadRequests();
    });

    loadRequests();
});
</script>
