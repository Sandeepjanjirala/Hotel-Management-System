/**
 * assets/js/modules/appointments.js
 * 
 * Admin Appointment Module AJAX Actions
 */

var AppointmentModule = (function() {
    'use strict';

    var config = {};
    var alertTimeout = null;
    
    var state = {
        search: '',
        doctor_id: '',
        status: '',
        appointment_date: '',
        page: 1,
        per_page: 10,
        total: 0,
        total_pages: 1,
        loading: false
    };

    /* ═══════════════════════════════════════════════════════════════
       INIT
       ═══════════════════════════════════════════════════════════════ */
    function init(options) {
        config = options || {};

        console.log('AppointmentModule initialized with config:', config);

        // Bind events
        bindEvents();
        
        // Initial Load
        refreshTable(1);
    }

    /* ═══════════════════════════════════════════════════════════════
       BIND EVENTS
       ═══════════════════════════════════════════════════════════════ */
    function bindEvents() {
        // Search with debounce
        var searchTimer;
        $('#appointmentSearch').on('input keyup', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                state.search = $.trim($('#appointmentSearch').val());
                state.page = 1;
                refreshTable(1);
            }, 300);
        });

        // Filters
        $('#doctorFilter, #statusFilter, #dateFilter').on('change', function() {
            state.page = 1;
            state.doctor_id = $('#doctorFilter').val();
            state.status = $('#statusFilter').val();
            state.appointment_date = $('#dateFilter').val();
            refreshTable(1);
        });

        // Clear filters
        $('#clearFiltersButton').on('click', function() {
            $('#appointmentSearch').val('');
            $('#doctorFilter').val('');
            $('#statusFilter').val('');
            $('#dateFilter').val('');
            state.search = '';
            state.doctor_id = '';
            state.status = '';
            state.appointment_date = '';
            state.page = 1;
            refreshTable(1);
        });

        // Book Appointment button
        $(document).on('click', '#btnOpenBook', function(e) {
            e.preventDefault();
            create();
        });

        // Edit Appointment button
        $(document).on('click', '.js-edit-appointment', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            edit(id);
        });

        // Status change dropdown
        $(document).on('change', '.js-appointment-status', function() {
            var $select = $(this);
            var id = $select.data('id');
            var newStatus = $select.val();
            
            if (!window.confirm('Are you sure you want to change the appointment status to "' + newStatus + '"?')) {
                // Revert to previous value
                var prevVal = $select.data('prev-val') || 'scheduled';
                $select.val(prevVal);
                return;
            }
            
            changeStatus(id, newStatus, $select);
        });

        // Store previous value on focus
        $(document).on('focus', '.js-appointment-status', function() {
            $(this).data('prev-val', $(this).val());
        });

        // Create form submit (delegated)
        $(document).on('submit', '#createAppointmentForm', function(e) {
            e.preventDefault();
            handleFormSubmit($(this), 'create');
        });

        // Edit form submit (delegated)
        $(document).on('submit', '#editAppointmentForm', function(e) {
            e.preventDefault();
            handleFormSubmit($(this), 'edit');
        });
    }

    /* ═══════════════════════════════════════════════════════════════
       FORM HANDLING
       ═══════════════════════════════════════════════════════════════ */
    function handleFormSubmit($form, mode) {
        clearFormErrors($form);
        clearAlert();

        var $btn = $form.find('.js-submit-btn');
        var origHtml = $btn.html();
        var actionUrl = $form.attr('action');

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> ' + (mode === 'create' ? 'Booking...' : 'Updating...'));

        $.ajax({
            url: actionUrl,
            type: 'POST',
            dataType: 'json',
            data: $form.serialize(),
            success: function(response) {
                $btn.prop('disabled', false).html(origHtml);
                
                if (response.status) {
                    showAlert('success', response.message || (mode === 'create' ? 'Appointment booked successfully.' : 'Appointment updated successfully.'));
                    $('#appointmentModal').modal('hide');
                    refreshTable(1);
                } else {
                    if (response.errors) {
                        renderValidationErrors($form, response.errors);
                    }
                    showAlert('danger', response.message || 'Validation failed.');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html(origHtml);
                var response = {};
                try {
                    response = JSON.parse(xhr.responseText);
                } catch (err) {}
                if (response.errors) {
                    renderValidationErrors($form, response.errors);
                }
                showAlert('danger', response.message || 'An error occurred. Please try again.');
            }
        });
    }

    /* ═══════════════════════════════════════════════════════════════
       CRUD OPERATIONS
       ═══════════════════════════════════════════════════════════════ */
    function create() {
        clearAlert();
        $('#appointmentModalLabel').text('Book Appointment');
        $('#appointmentModalBody').html(
            '<div class="text-center py-5">' +
                '<div class="spinner-border text-primary" role="status"></div>' +
                '<div class="mt-2 text-muted">Loading form...</div>' +
            '</div>'
        );
        $('#appointmentModal').modal('show');

        $.ajax({
            url: config.createUrl,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status && response.html) {
                    $('#appointmentModalBody').html(response.html);
                } else {
                    $('#appointmentModal').modal('hide');
                    showAlert('danger', response.message || 'Could not load booking form.');
                }
            },
            error: function() {
                $('#appointmentModal').modal('hide');
                showAlert('danger', 'Unable to retrieve form from server.');
            }
        });
    }

    function edit(id) {
        clearAlert();
        $('#appointmentModalLabel').text('Edit Appointment');
        $('#appointmentModalBody').html(
            '<div class="text-center py-5">' +
                '<div class="spinner-border text-primary" role="status"></div>' +
                '<div class="mt-2 text-muted">Loading appointment details...</div>' +
            '</div>'
        );
        $('#appointmentModal').modal('show');

        $.ajax({
            url: config.editUrl + '/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status && response.html) {
                    $('#appointmentModalBody').html(response.html);
                } else {
                    $('#appointmentModal').modal('hide');
                    showAlert('danger', response.message || 'Could not load appointment details.');
                }
            },
            error: function() {
                $('#appointmentModal').modal('hide');
                showAlert('danger', 'Unable to retrieve data from server.');
            }
        });
    }

    function changeStatus(id, newStatus, $select) {
        $select.prop('disabled', true);

        $.ajax({
            url: config.changeStatusUrl + '/' + id,
            type: 'POST',
            dataType: 'json',
            data: { status: newStatus },
            success: function(response) {
                $select.prop('disabled', false);
                if (response.status) {
                    showAlert('success', response.message || 'Status updated successfully.');
                    refreshTable(state.page);
                } else {
                    showAlert('danger', response.message || 'Failed to update status.');
                    $select.val($select.data('prev-val') || 'scheduled');
                }
            },
            error: function() {
                $select.prop('disabled', false);
                showAlert('danger', 'Failed to update status due to a server error.');
                $select.val($select.data('prev-val') || 'scheduled');
            }
        });
    }

    /* ═══════════════════════════════════════════════════════════════
       TABLE REFRESH
       ═══════════════════════════════════════════════════════════════ */
    function refreshTable(page) {
        if (state.loading) return;
        state.loading = true;
        state.page = page || 1;

        $('#appointmentsTableBody').html(
            '<tr><td colspan="7" class="text-center py-4">' +
                '<div class="spinner-border text-primary" role="status"></div>' +
                '<div class="mt-2 text-muted">Loading appointments...</div>' +
            '</td></tr>'
        );

        var params = {
            search: state.search,
            doctor_id: state.doctor_id,
            status: state.status,
            appointment_date: state.appointment_date,
            page: state.page
        };

        console.log('Loading appointments with params:', params);

        $.ajax({
            url: config.listUrl,
            type: 'GET',
            dataType: 'json',
            data: params,
            success: function(response) {
                state.loading = false;
                console.log('Response received:', response);

                if (response.status) {
                    if (response.html) {
                        $('#appointmentsTableBody').html(response.html);
                    } else if (response.data && response.data.length > 0) {
                        renderAppointmentRows(response.data);
                    } else {
                        $('#appointmentsTableBody').html('<tr><td colspan="7" class="text-center">No appointments found.</td></tr>');
                    }

                    state.total = response.total || 0;
                    state.total_pages = response.total_pages || 1;
                    renderPagination(state.page, state.total_pages, state.total);
                } else {
                    showAlert('danger', response.message || 'Failed to load appointments.');
                    $('#appointmentsTableBody').html('<tr><td colspan="7" class="text-center text-danger">' + (response.message || 'No appointments found.') + '</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                state.loading = false;
                console.error('AJAX Error:', status, error);
                console.error('Response:', xhr.responseText);
                showAlert('danger', 'Could not refresh table due to a server error.');
                $('#appointmentsTableBody').html('<tr><td colspan="7" class="text-center text-danger">Error loading appointments. Please refresh the page.</td></tr>');
            }
        });
    }

    function renderAppointmentRows(rows) {
        var html = '';
        if (!rows || rows.length === 0) {
            html = '<tr><td colspan="7" class="text-center">No appointments found.</td></tr>';
        } else {
            $.each(rows, function(index, appointment) {
                var status = appointment.status || 'scheduled';
                var badge = '';
                if (status === 'completed') {
                    badge = '<span class="badge badge-success">Completed</span>';
                } else if (status === 'cancelled') {
                    badge = '<span class="badge badge-danger">Cancelled</span>';
                } else {
                    badge = '<span class="badge badge-primary">Scheduled</span>';
                }
                
                html += '<tr id="appointment-row-' + appointment.id + '">';
                html += '<td><strong>' + escapeHtml(appointment.appointment_code) + '</strong></td>';
                html += '<td>' + escapeHtml(appointment.patient_name) + '<br><small class="text-muted">' + escapeHtml(appointment.patient_code) + '</small></td>';
                html += '<td>' + escapeHtml(appointment.doctor_name) + '</td>';
                html += '<td>' + escapeHtml(appointment.appointment_date) + '</td>';
                html += '<td>' + escapeHtml(appointment.appointment_time) + '</td>';
                html += '<td>' + badge + '</td>';
                html += '<td>';
                html += '<a href="#" class="btn btn-info btn-sm mr-1 js-edit-appointment" data-id="' + appointment.id + '">';
                html += '<i class="fas fa-edit mr-1"></i>Edit';
                html += '</a>';
                html += '<select class="form-control form-control-sm d-inline-block js-appointment-status" data-id="' + appointment.id + '" style="width: 125px; display: inline-block;">';
                html += '<option value="scheduled" ' + (status === 'scheduled' ? 'selected' : '') + '>Scheduled</option>';
                html += '<option value="completed" ' + (status === 'completed' ? 'selected' : '') + '>Completed</option>';
                html += '<option value="cancelled" ' + (status === 'cancelled' ? 'selected' : '') + '>Cancelled</option>';
                html += '</select>';
                html += '</td>';
                html += '</tr>';
            });
        }
        $('#appointmentsTableBody').html(html);
    }

    /* ═══════════════════════════════════════════════════════════════
       PAGINATION
       ═══════════════════════════════════════════════════════════════ */
    function renderPagination(page, totalPages, total) {
        var start = total === 0 ? 0 : (page - 1) * state.per_page + 1;
        var end = Math.min(page * state.per_page, total);

        $('#paginationInfo').text('Showing ' + start + ' to ' + end + ' of ' + total + ' appointments');

        var $links = $('#paginationLinks').empty();
        if (totalPages <= 1) {
            return;
        }

        var $ul = $('<ul class="pagination pagination-sm m-0"></ul>');

        // Prev
        var $prevLi = $('<li class="page-item"></li>');
        var $prevA = $('<a class="page-link" href="#">&laquo;</a>');
        if (page <= 1) {
            $prevLi.addClass('disabled');
        } else {
            $prevA.on('click', function(e) {
                e.preventDefault();
                state.page = page - 1;
                refreshTable(state.page);
            });
        }
        $prevLi.append($prevA);
        $ul.append($prevLi);

        // Pages
        var startP = Math.max(1, page - 2);
        var endP = Math.min(totalPages, startP + 4);
        startP = Math.max(1, endP - 4);

        for (var i = startP; i <= endP; i++) {
            (function(p) {
                var $li = $('<li class="page-item"></li>');
                var $a = $('<a class="page-link" href="#">' + p + '</a>');
                if (p === page) {
                    $li.addClass('active');
                } else {
                    $a.on('click', function(e) {
                        e.preventDefault();
                        state.page = p;
                        refreshTable(state.page);
                    });
                }
                $li.append($a);
                $ul.append($li);
            })(i);
        }

        // Next
        var $nextLi = $('<li class="page-item"></li>');
        var $nextA = $('<a class="page-link" href="#">&raquo;</a>');
        if (page >= totalPages) {
            $nextLi.addClass('disabled');
        } else {
            $nextA.on('click', function(e) {
                e.preventDefault();
                state.page = page + 1;
                refreshTable(state.page);
            });
        }
        $nextLi.append($nextA);
        $ul.append($nextLi);

        $links.append($ul);
    }

    /* ═══════════════════════════════════════════════════════════════
       HELPER FUNCTIONS
       ═══════════════════════════════════════════════════════════════ */
    function showAlert(type, message) {
        if (alertTimeout) {
            clearTimeout(alertTimeout);
            alertTimeout = null;
        }

        var alertClass = type === 'success' ? 'success' : 'danger';
        var icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
        var html = ''
            + '<div class="alert alert-' + alertClass + ' alert-dismissible fade show" role="alert">'
            + '<i class="fas fa-' + icon + ' mr-2"></i>'
            + escapeHtml(message)
            + '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'
            + '<span aria-hidden="true">&times;</span>'
            + '</button>'
            + '</div>';

        $('#appointmentAlert').html(html);

        alertTimeout = setTimeout(function() {
            clearAlert();
        }, 5000);
    }

    function clearAlert() {
        if (alertTimeout) {
            clearTimeout(alertTimeout);
            alertTimeout = null;
        }
        $('#appointmentAlert').html('');
    }

    function escapeHtml(value) {
        return $('<div>').text(value === null || value === undefined ? '' : value).html();
    }

    function clearFormErrors($form) {
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').remove();
    }

    function renderValidationErrors($form, errors) {
        clearFormErrors($form);
        if (!errors) return;

        $.each(errors, function(field, message) {
            if (!message) return;
            var $input = $form.find('[name="' + field + '"]');
            if ($input.length) {
                $input.addClass('is-invalid');
                $input.closest('.form-group').append(
                    '<div class="invalid-feedback d-block">' + message + '</div>'
                );
            }
        });
    }

    // Public API
    return {
        init: init,
        refreshTable: refreshTable,
        create: create,
        edit: edit,
        changeStatus: changeStatus,
        showAlert: showAlert,
        clearAlert: clearAlert
    };

})();

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.HMS_APPOINTMENT_CONFIG !== 'undefined') {
        AppointmentModule.init(window.HMS_APPOINTMENT_CONFIG);
    }
});