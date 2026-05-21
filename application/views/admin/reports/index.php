<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-7">
                    <h1>Reports & Analytics</h1>
                </div>
                <div class="col-sm-5">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo site_url('admin'); ?>">Home</a></li>
                        <li class="breadcrumb-item active">Reports</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="card card-primary card-outline">
                <div class="card-body">
                    <form id="reportFilterForm" class="row align-items-end">
                        <div class="col-md-4">
                            <label>Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo date('Y-m-01'); ?>">
                        </div>
                        <div class="col-md-4">
                            <label>End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sync-alt"></i> Load Analytics
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <h5 class="mb-3">Revenue Analytics</h5>

            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="todayRevenue">0.00</h3>
                            <p>Today Revenue</p>
                        </div>
                        <div class="icon"><i class="fas fa-rupee-sign"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="monthlyRevenue">0.00</h3>
                            <p>Monthly Revenue</p>
                        </div>
                        <div class="icon"><i class="fas fa-chart-line"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3 id="paidInvoices">0</h3>
                            <p>Paid Invoices</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="unpaidInvoices">0</h3>
                            <p>Unpaid Invoices</p>
                        </div>
                        <div class="icon"><i class="fas fa-file-invoice"></i></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-area mr-1"></i> Daily Revenue</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="dailyRevenueChart" height="120"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-bar mr-1"></i> Monthly Revenue</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="monthlyRevenueChart" height="170"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="mb-3">Appointment Analytics</h5>

            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-calendar-check"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Appointments</span>
                            <span class="info-box-number" id="totalAppointments">0</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Scheduled</span>
                            <span class="info-box-number" id="scheduledAppointments">0</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Completed</span>
                            <span class="info-box-number" id="completedAppointments">0</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-danger"><i class="fas fa-times"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Cancelled</span>
                            <span class="info-box-number" id="cancelledAppointments">0</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-md mr-1"></i> Doctor-wise Appointment Stats</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Doctor</th>
                                <th>Total</th>
                                <th>Completed</th>
                                <th>Cancelled</th>
                            </tr>
                        </thead>
                        <tbody id="doctorAppointmentRows">
                            <tr><td colspan="4" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <h5 class="mb-3">Patient Analytics</h5>

            <div class="row">
                <div class="col-lg-4">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="newPatients">0</h3>
                            <p>New Patients</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-plus"></i></div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3 id="repeatPatients">0</h3>
                            <p>Repeat Patients</p>
                        </div>
                        <div class="icon"><i class="fas fa-redo"></i></div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <canvas id="genderChart" height="130"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="mb-3">Doctor Analytics</h5>

            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Top Doctors</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Doctor</th>
                                        <th>Specialization</th>
                                        <th>Appointments</th>
                                    </tr>
                                </thead>
                                <tbody id="topDoctorRows">
                                    <tr><td colspan="3" class="text-center">Loading...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Revenue by Doctor</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="doctorRevenueChart" height="170"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="mb-3">Lab Analytics</h5>

            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-purple">
                        <div class="inner">
                            <h3 id="labRevenue">0.00</h3>
                            <p>Lab Revenue</p>
                        </div>
                        <div class="icon"><i class="fas fa-vials"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="pendingLabRequests">0</h3>
                            <p>Pending Requests</p>
                        </div>
                        <div class="icon"><i class="fas fa-hourglass-half"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="processingLabRequests">0</h3>
                            <p>Processing Requests</p>
                        </div>
                        <div class="icon"><i class="fas fa-flask"></i></div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="completedLabRequests">0</h3>
                            <p>Completed Requests</p>
                        </div>
                        <div class="icon"><i class="fas fa-check-double"></i></div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Top Lab Tests</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Test Code</th>
                                <th>Test Name</th>
                                <th>Category</th>
                                <th>Total Requests</th>
                                <th>Total Value</th>
                            </tr>
                        </thead>
                        <tbody id="topLabTestRows">
                            <tr><td colspan="5" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(function () {
    var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

    var dailyRevenueChart = null;
    var monthlyRevenueChart = null;
    var genderChart = null;
    var doctorRevenueChart = null;

    function money(value) {
        value = parseFloat(value || 0);
        return value.toFixed(2);
    }

    function postData() {
        var data = {
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val()
        };
        data[csrfName] = csrfHash;
        return data;
    }

    function updateCsrf(response) {
        if (response.csrfHash) {
            csrfHash = response.csrfHash;
        }
    }

    function renderChart(instance, canvasId, type, labels, values, label, colors) {
        if (instance) {
            instance.destroy();
        }

        var ctx = document.getElementById(canvasId).getContext('2d');

        return new Chart(ctx, {
            type: type,
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: values,
                    backgroundColor: colors.background,
                    borderColor: colors.border,
                    borderWidth: 2,
                    fill: type === 'line'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: type !== 'line'
                    }
                },
                scales: type === 'doughnut' ? {} : {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    function loadRevenue() {
        return $.ajax({
            url: '<?php echo site_url('admin/reports/revenue-analytics'); ?>',
            type: 'POST',
            dataType: 'json',
            data: postData(),
            success: function (response) {
                updateCsrf(response);

                if (!response.status) {
                    return;
                }

                var cards = response.data.cards;
                $('#todayRevenue').text(money(cards.today_revenue));
                $('#monthlyRevenue').text(money(cards.monthly_revenue));
                $('#paidInvoices').text(cards.paid_invoices);
                $('#unpaidInvoices').text(cards.unpaid_invoices);

                var dailyLabels = [];
                var dailyValues = [];

                $.each(response.data.daily_chart, function (_, row) {
                    dailyLabels.push(row.invoice_date);
                    dailyValues.push(parseFloat(row.total));
                });

                dailyRevenueChart = renderChart(
                    dailyRevenueChart,
                    'dailyRevenueChart',
                    'line',
                    dailyLabels,
                    dailyValues,
                    'Daily Revenue',
                    {
                        background: 'rgba(40, 167, 69, 0.15)',
                        border: 'rgba(40, 167, 69, 1)'
                    }
                );

                var monthlyLabels = [];
                var monthlyValues = [];

                $.each(response.data.monthly_chart, function (_, row) {
                    monthlyLabels.push(row.month);
                    monthlyValues.push(parseFloat(row.total));
                });

                monthlyRevenueChart = renderChart(
                    monthlyRevenueChart,
                    'monthlyRevenueChart',
                    'bar',
                    monthlyLabels,
                    monthlyValues,
                    'Monthly Revenue',
                    {
                        background: 'rgba(23, 162, 184, 0.75)',
                        border: 'rgba(23, 162, 184, 1)'
                    }
                );
            }
        });
    }

    function loadAppointments() {
        return $.ajax({
            url: '<?php echo site_url('admin/reports/appointment-analytics'); ?>',
            type: 'POST',
            dataType: 'json',
            data: postData(),
            success: function (response) {
                updateCsrf(response);

                var cards = response.data.cards;
                $('#totalAppointments').text(cards.total);
                $('#scheduledAppointments').text(cards.scheduled);
                $('#completedAppointments').text(cards.completed);
                $('#cancelledAppointments').text(cards.cancelled);

                var html = '';

                if (response.data.doctor_wise.length === 0) {
                    html = '<tr><td colspan="4" class="text-center">No records found</td></tr>';
                } else {
                    $.each(response.data.doctor_wise, function (_, row) {
                        html += '<tr>';
                        html += '<td>' + (row.doctor_name || 'Not Assigned') + '</td>';
                        html += '<td><span class="badge badge-info">' + row.total_appointments + '</span></td>';
                        html += '<td><span class="badge badge-success">' + row.completed_count + '</span></td>';
                        html += '<td><span class="badge badge-danger">' + row.cancelled_count + '</span></td>';
                        html += '</tr>';
                    });
                }

                $('#doctorAppointmentRows').html(html);
            }
        });
    }

    function loadPatients() {
        return $.ajax({
            url: '<?php echo site_url('admin/reports/patient-analytics'); ?>',
            type: 'POST',
            dataType: 'json',
            data: postData(),
            success: function (response) {
                updateCsrf(response);

                $('#newPatients').text(response.data.cards.new_patients);
                $('#repeatPatients').text(response.data.cards.repeat_patients);

                var labels = [];
                var values = [];

                $.each(response.data.gender_stats, function (_, row) {
                    labels.push(row.gender || 'Not Set');
                    values.push(parseInt(row.total, 10));
                });

                genderChart = renderChart(
                    genderChart,
                    'genderChart',
                    'doughnut',
                    labels,
                    values,
                    'Gender Statistics',
                    {
                        background: [
                            'rgba(0, 123, 255, 0.8)',
                            'rgba(220, 53, 69, 0.8)',
                            'rgba(255, 193, 7, 0.8)',
                            'rgba(108, 117, 125, 0.8)'
                        ],
                        border: [
                            'rgba(0, 123, 255, 1)',
                            'rgba(220, 53, 69, 1)',
                            'rgba(255, 193, 7, 1)',
                            'rgba(108, 117, 125, 1)'
                        ]
                    }
                );
            }
        });
    }

    function loadDoctors() {
        return $.ajax({
            url: '<?php echo site_url('admin/reports/doctor-analytics'); ?>',
            type: 'POST',
            dataType: 'json',
            data: postData(),
            success: function (response) {
                updateCsrf(response);

                var doctorRows = '';

                if (response.data.top_doctors.length === 0) {
                    doctorRows = '<tr><td colspan="3" class="text-center">No records found</td></tr>';
                } else {
                    $.each(response.data.top_doctors, function (_, row) {
                        doctorRows += '<tr>';
                        doctorRows += '<td>' + row.doctor_name + '</td>';
                        doctorRows += '<td>' + (row.specialization || '-') + '</td>';
                        doctorRows += '<td><span class="badge badge-primary">' + row.appointments_handled + '</span></td>';
                        doctorRows += '</tr>';
                    });
                }

                $('#topDoctorRows').html(doctorRows);

                var labels = [];
                var values = [];

                $.each(response.data.revenue_by_doctor, function (_, row) {
                    labels.push(row.doctor_name || 'Not Assigned');
                    values.push(parseFloat(row.revenue));
                });

                doctorRevenueChart = renderChart(
                    doctorRevenueChart,
                    'doctorRevenueChart',
                    'bar',
                    labels,
                    values,
                    'Revenue by Doctor',
                    {
                        background: 'rgba(111, 66, 193, 0.75)',
                        border: 'rgba(111, 66, 193, 1)'
                    }
                );
            }
        });
    }

    function loadLab() {
        return $.ajax({
            url: '<?php echo site_url('admin/reports/lab-analytics'); ?>',
            type: 'POST',
            dataType: 'json',
            data: postData(),
            success: function (response) {
                updateCsrf(response);

                $('#labRevenue').text(money(response.data.cards.lab_revenue));
                $('#pendingLabRequests').text(response.data.cards.pending_requests);
                $('#processingLabRequests').text(response.data.cards.processing_requests);
                $('#completedLabRequests').text(response.data.cards.completed_requests);

                var html = '';

                if (response.data.top_tests.length === 0) {
                    html = '<tr><td colspan="5" class="text-center">No records found</td></tr>';
                } else {
                    $.each(response.data.top_tests, function (_, row) {
                        html += '<tr>';
                        html += '<td>' + row.test_code + '</td>';
                        html += '<td>' + row.test_name + '</td>';
                        html += '<td>' + row.category + '</td>';
                        html += '<td><span class="badge badge-info">' + row.total_requests + '</span></td>';
                        html += '<td>' + money(row.total_value) + '</td>';
                        html += '</tr>';
                    });
                }

                $('#topLabTestRows').html(html);
            }
        });
    }

    function loadAllAnalytics() {
        loadRevenue()
            .then(loadAppointments)
            .then(loadPatients)
            .then(loadDoctors)
            .then(loadLab);
    }

    $('#reportFilterForm').on('submit', function (e) {
        e.preventDefault();
        loadAllAnalytics();
    });

    loadAllAnalytics();
});
</script>