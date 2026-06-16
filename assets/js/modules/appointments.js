/**
 * assets/js/modules/appointments.js
 * 
 * Admin Appointment Module AJAX Actions
 * Self-contained jQuery module adhering to the HMS AJAX standards.
 */

var AppointmentModule = (function ($) {
    'use strict';

    if (!$) {
        console.error('jQuery is required for AppointmentModule.');
        return;
    }

    // Config URLs
    var config = {};
    
    // Page state
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
       TOAST NOTIFICATIONS
       ═══════════════════════════════════════════════════════════════ */
    function showToast(type, message) {
        var bgClass = 'bg-info';
        var title = 'Notification';
        if (type === 'success') {
            bgClass = 'bg-success';
            title = 'Success';
        } else if (type === 'error') {
            bgClass = 'bg-danger';
            title = 'Error';
        } else if (type === 'warning') {
            bgClass = 'bg-warning';
            title = 'Warning';
        }

        $(document).Toasts('create', {
            class: bgClass,
            title: title,
            autohide: true,
            delay: 4000,
            body: message
        });
    }

    /* ═══════════════════════════════════════════════════════════════
       BUTTON & TABLE LOADERS
       ═══════════════════════════════════════════════════════════════ */
    function showBtnLoader($btn) {
        var origHtml = $btn.html();
        $btn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Please wait...'
        );
        return origHtml;
    }

    function resetBtn($btn, origHtml) {
        $btn.prop('disabled', false).html(origHtml);
    }

    function showTableLoader() {
        $('#appointmentsTableBody').html(
            '<tr>' +
                '<td colspan="7" class="text-center py-4">' +
                    '<div class="spinner-border text-primary mr-2" role="status"></div>' +
                    '<div class="mt-2 text-muted small">Loading appointments...</div>' +
                '</td>' +
            '</tr>'
        );
    }

    /* ═══════════════════════════════════════════════════════════════
       VALIDATION ERRORS
       ═══════════════════════════════════════════════════════════════ */
    function clearFormErrors($form) {
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').remove();
        $form.find('.hms-ajax-validation-summary').remove();
    }

    function renderValidationErrors($form, errors) {
        clearFormErrors($form);
        if (!errors) return;

        var hasErrors = false;
        $.each(errors, function (field, message) {
            if (!message) return;
            hasErrors = true;
            var $input = $form.find('[name="' + field + '"]');
            if ($input.length) {
                $input.addClass('is-invalid');
                $input.closest('.form-group').append(
                    '<div class="invalid-feedback d-block">' + message + '</div>'
                );
            }
        });

        if (hasErrors) {
            $form.find('.card-body:first').prepend(
                '<div class="alert alert-danger hms-ajax-validation-summary">Please correct the highlighted errors.</div>'
            );
        }
    }

    /* ═══════════════════════════════════════════════════════════════
       DEBOUNCE HELPER
       ═══════════════════════════════════════════════════════════════ */
    function debounce(fn, delay) {
        var timer;
        return function () {
            var ctx = this;
            var args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
                fn.apply(ctx, args);
            }, delay || 300);
        };
    }

    /* ═══════════════════════════════════════════════════════════════
       PAGINATION RENDERING
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

        // Prev Link
        var $prevLi = $('<li class="page-item"></li>');
        var $prevA = $('<a class="page-link" href="#">&laquo;</a>');
        if (page <= 1) {
            $prevLi.addClass('disabled');
        } else {
            $prevA.on('click', function (e) {
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
            (function (p) {
                var $li = $('<li class="page-item"></li>');
                var $a = $('<a class="page-link" href="#">' + p + '</a>');
                if (p === page) {
                    $li.addClass('active');
                } else {
                    $a.on('click', function (e) {
                        e.preventDefault();
                        state.page = p;
                        refreshTable(state.page);
                    });
                }
                $li.append($a);
                $ul.append($li);
            })(i);
        }

        // Next Link
        var $nextLi = $('<li class="page-item"></li>');
        var $nextA = $('<a class="page-link" href="#">&raquo;</a>');
        if (page >= totalPages) {
            $nextLi.addClass('disabled');
        } else {
            $nextA.on('click', function (e) {
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
       MODULE ACTIONS
       ═══════════════════════════════════════════════════════════════ */
    
    function init(options) {
        config = options || window.HMS_APPOINTMENT_CONFIG || {};

        // Bind Search with debounce
        $('#appointmentSearch').on('input keyup', debounce(function () {
            search($(this).val());
        }, 300));

        // Bind filters
        $('#doctorFilter, #statusFilter, #dateFilter').on('change', function () {
            state.page = 1;
            state.doctor_id = $('#doctorFilter').val();
            state.status = $('#statusFilter').val();
            state.appointment_date = $('#dateFilter').val();
            refreshTable(1);
        });

        // Bind clear filters
        $('#clearFiltersButton').on('click', function () {
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

        // Bind Create click (Book Appointment)
        $(document).on('click', '.js-create-appointment, #btnOpenBook', function (e) {
            e.preventDefault();
            create();
        });

        // Bind Edit click
        $(document).on('click', '.js-edit-appointment', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            edit(id);
        });

        // Bind Status select dropdown
        $(document).on('focus', '.js-appointment-status', function () {
            $(this).data('prev-val', $(this).val());
        }).on('change', '.js-appointment-status', function () {
            var $select = $(this);
            var id = $select.data('id');
            var newStatus = $select.val();
            var prevStatus = $select.data('prev-val');

            if (newStatus === prevStatus) return;

            var message = 'Are you sure you want to change the appointment status to "' + newStatus + '"?';
            if (!window.confirm(message)) {
                $select.val(prevStatus);
                return;
            }

            changeStatus(id, newStatus, $select);
        });

        // Bind form submit triggers (Create Form)
        $(document).on('submit', '#createAppointmentForm', function (e) {
            e.preventDefault();
            var $form = $(this);
            clearFormErrors($form);

            var $btn = $form.find('.js-submit-btn');
            var origHtml = showBtnLoader($btn);

            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                dataType: 'json',
                data: $form.serialize(),
                success: function (response) {
                    resetBtn($btn, origHtml);
                    if (response.status) {
                        showToast('success', response.message || 'Appointment booked successfully.');
                        $('#appointmentModal').modal('hide');
                        refreshTable(1);
                    } else {
                        renderValidationErrors($form, response.errors);
                        showToast('error', response.message || 'Validation failed.');
                    }
                },
                error: function (xhr) {
                    resetBtn($btn, origHtml);
                    var response = {};
                    try {
                        response = JSON.parse(xhr.responseText);
                    } catch (err) {}
                    renderValidationErrors($form, response.errors);
                    showToast('error', response.message || 'An error occurred. Please try again.');
                }
            });
        });

        // Bind form submit triggers (Edit Form)
        $(document).on('submit', '#editAppointmentForm', function (e) {
            e.preventDefault();
            var $form = $(this);
            clearFormErrors($form);

            var $btn = $form.find('.js-submit-btn');
            var origHtml = showBtnLoader($btn);

            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                dataType: 'json',
                data: $form.serialize(),
                success: function (response) {
                    resetBtn($btn, origHtml);
                    if (response.status) {
                        showToast('success', response.message || 'Appointment updated successfully.');
                        $('#appointmentModal').modal('hide');
                        refreshTable(state.page);
                    } else {
                        renderValidationErrors($form, response.errors);
                        showToast('error', response.message || 'Validation failed.');
                    }
                },
                error: function (xhr) {
                    resetBtn($btn, origHtml);
                    var response = {};
                    try {
                        response = JSON.parse(xhr.responseText);
                    } catch (err) {}
                    renderValidationErrors($form, response.errors);
                    showToast('error', response.message || 'An error occurred. Please try again.');
                }
            });
        });

        // Initial Load
        refreshTable(1);
    }

    function search(query) {
        state.search = $.trim(query);
        state.page = 1;
        refreshTable(1);
    }

    function create() {
        $('#appointmentModalLabel').text('Book Appointment');
        $('#appointmentModalBody').html(
            '<div class="text-center py-5">' +
                '<div class="spinner-border text-primary mr-2" role="status"></div>' +
                '<div class="mt-2 text-muted small">Loading form...</div>' +
            '</div>'
        );
        $('#appointmentModal').modal('show');

        $.ajax({
            url: config.createUrl,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.status && response.html) {
                    $('#appointmentModalBody').html(response.html);
                } else {
                    $('#appointmentModal').modal('hide');
                    showToast('error', response.message || 'Could not load booking form.');
                }
            },
            error: function () {
                $('#appointmentModal').modal('hide');
                showToast('error', 'Unable to retrieve form from server.');
            }
        });
    }

    function edit(id) {
        $('#appointmentModalLabel').text('Edit Appointment');
        $('#appointmentModalBody').html(
            '<div class="text-center py-5">' +
                '<div class="spinner-border text-primary mr-2" role="status"></div>' +
                '<div class="mt-2 text-muted small">Loading appointment details...</div>' +
            '</div>'
        );
        $('#appointmentModal').modal('show');

        $.ajax({
            url: config.editUrl + '/' + id,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.status && response.html) {
                    $('#appointmentModalBody').html(response.html);
                } else {
                    $('#appointmentModal').modal('hide');
                    showToast('error', response.message || 'Could not load appointment details.');
                }
            },
            error: function () {
                $('#appointmentModal').modal('hide');
                showToast('error', 'Unable to retrieve data from server.');
            }
        });
    }

    function changeStatus(id, newStatus, $select) {
        $.ajax({
            url: config.changeStatusUrl + '/' + id,
            type: 'POST',
            dataType: 'json',
            data: { status: newStatus },
            success: function (response) {
                if (response.status) {
                    showToast('success', response.message || 'Status updated successfully.');
                    if ($select) {
                        $select.data('prev-val', newStatus);
                    }
                    refreshTable(state.page);
                } else {
                    showToast('error', response.message || 'Failed to update status.');
                    if ($select && $select.data('prev-val')) {
                        $select.val($select.data('prev-val'));
                    }
                }
            },
            error: function () {
                showToast('error', 'Failed to update status due to a server error.');
                if ($select && $select.data('prev-val')) {
                    $select.val($select.data('prev-val'));
                }
            }
        });
    }

    function refreshTable(page) {
        if (state.loading) return;
        state.loading = true;
        state.page = page || 1;

        showTableLoader();

        $.ajax({
            url: config.listUrl,
            type: 'GET',
            dataType: 'json',
            data: {
                search: state.search,
                doctor_id: state.doctor_id,
                status: state.status,
                appointment_date: state.appointment_date,
                page: state.page
            },
            success: function (response) {
                state.loading = false;
                if (response.status) {
                    $('#appointmentsTableBody').html(response.html);
                    state.total = response.total;
                    state.total_pages = response.total_pages;
                    renderPagination(state.page, state.total_pages, state.total);
                } else {
                    showToast('error', response.message || 'Failed to load appointments.');
                }
            },
            error: function () {
                state.loading = false;
                showToast('error', 'Could not refresh table due to a server error.');
            }
        });
    }

    return {
        init: init,
        search: search,
        create: create,
        edit: edit,
        changeStatus: changeStatus,
        refreshTable: refreshTable
    };
})(jQuery);

// Initialize on load
$(function () {
    AppointmentModule.init();
});
