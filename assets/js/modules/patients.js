/**
 * assets/js/modules/patients.js
 *
 * HMS — Patient Module  (Phase 5A.1)
 * -----------------------------------
 * Self-contained jQuery module.  No dependency on hms-ajax.js.
 *
 * Handles:
 *   • AJAX paginated list   (GET  patient/ajax/list)
 *   • Live search + filters (debounced, no page reload)
 *   • Add Patient modal     (POST patient/ajax/add)
 *   • Edit Patient modal    (GET patient/ajax/edit/:id  then POST)
 *   • Status toggle         (POST patient/ajax/toggle/:id)
 *   • Delete patient        (POST patient/ajax/delete/:id)
 *   • Toast notifications
 *   • Spinner / loader states
 *   • Per-field validation error display
 *   • CSRF token refresh after each mutation
 */

;(function ($) {
    'use strict';

    /* ═══════════════════════════════════════════════════════════════
       CONFIGURATION
    ═══════════════════════════════════════════════════════════════ */
    var BASE    = window.HMS_BASE_URL || '';
    var URLS    = {
        list   : BASE + '/patient/ajax/list',
        add    : BASE + '/patient/ajax/add',
        edit   : function (id) { return BASE + '/patient/ajax/edit/'   + id; },
        toggle : function (id) { return BASE + '/patient/ajax/toggle/' + id; },
        del    : function (id) { return BASE + '/patient/ajax/delete/' + id; }
    };

    /* ═══════════════════════════════════════════════════════════════
       STATE
    ═══════════════════════════════════════════════════════════════ */
    var state = {
        search      : '',
        status      : 'all',
        page        : 1,
        per_page    : 10,
        total       : 0,
        total_pages : 1,
        loading     : false
    };

    /* ═══════════════════════════════════════════════════════════════
       CSRF HELPERS
    ═══════════════════════════════════════════════════════════════ */
    function csrfData() {
        var obj = {};
        obj[window.HMS_CSRF_NAME] = window.HMS_CSRF_HASH;
        return obj;
    }

    function refreshCsrf(hash) {
        if (hash) { window.HMS_CSRF_HASH = hash; }
    }

    /* ═══════════════════════════════════════════════════════════════
       TOAST NOTIFICATIONS
    ═══════════════════════════════════════════════════════════════ */
    var TOAST_ICONS = {
        success : 'fas fa-check-circle',
        error   : 'fas fa-times-circle',
        info    : 'fas fa-info-circle'
    };

    var TOAST_TITLES = { success: 'Success', error: 'Error', info: 'Info' };

    function showToast(type, message) {
        type = type || 'info';
        var icon  = TOAST_ICONS[type]  || TOAST_ICONS.info;
        var title = TOAST_TITLES[type] || 'Notice';

        var $t = $(
            '<div class="hms-toast ' + type + '">' +
                '<div class="toast-icon"><i class="' + icon + '"></i></div>' +
                '<div class="flex-fill">' +
                    '<div class="toast-title">'   + title   + '</div>' +
                    '<div class="toast-message">' + message + '</div>' +
                '</div>' +
                '<button class="toast-close">&times;</button>' +
            '</div>'
        );

        $('#toastContainer').append($t);

        $t.find('.toast-close').on('click', function () { dismissToast($t); });

        // Auto-dismiss after 4 s
        setTimeout(function () { dismissToast($t); }, 4000);
    }

    function dismissToast($t) {
        $t.addClass('toast-fadeout');
        setTimeout(function () { $t.remove(); }, 310);
    }

    /* ═══════════════════════════════════════════════════════════════
       LOADER HELPERS
    ═══════════════════════════════════════════════════════════════ */
    function showTableLoader() {
        $('#patientTableBody').html(
            '<tr class="loader-row">' +
                '<td colspan="8" class="text-center">' +
                    '<div class="hms-spinner"></div>' +
                    '<div style="color:#8a94a6;font-size:13px;">Loading patients…</div>' +
                '</td>' +
            '</tr>'
        );
    }

    function showBtnLoader($btn, originalHtml) {
        $btn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Please wait…'
        );
        return originalHtml;
    }

    function resetBtn($btn, html) {
        $btn.prop('disabled', false).html(html);
    }

    /* ═══════════════════════════════════════════════════════════════
       VALIDATION ERROR HELPERS
    ═══════════════════════════════════════════════════════════════ */
    function clearErrors(prefix) {
        $('[id^="' + prefix + 'err_"]').text('').hide();
        $('[id^="' + prefix + '"]').filter('.form-control').removeClass('is-invalid');
    }

    function showErrors(errors, prefix) {
        clearErrors(prefix);
        $.each(errors, function (field, msg) {
            var $err = $('#' + prefix + 'err_' + field);
            var $inp = $('#' + prefix + field);
            if ($err.length) { $err.text(msg).show(); }
            if ($inp.length) { $inp.addClass('is-invalid'); }
        });
    }

    /* ═══════════════════════════════════════════════════════════════
       DEBOUNCE
    ═══════════════════════════════════════════════════════════════ */
    function debounce(fn, delay) {
        var t;
        return function () {
            var ctx  = this;
            var args = arguments;
            clearTimeout(t);
            t = setTimeout(function () { fn.apply(ctx, args); }, delay);
        };
    }

    /* ═══════════════════════════════════════════════════════════════
       BUILD TABLE ROW HTML
    ═══════════════════════════════════════════════════════════════ */
    function buildRow(p, index) {
        var isActive   = parseInt(p.status, 10) === 1;
        var badgeClass = isActive ? 'badge-active' : 'badge-inactive';
        var badgeText  = isActive ? 'Active'       : 'Inactive';
        var toggleTip  = isActive ? 'Deactivate'   : 'Activate';

        var name  = escHtml(p.full_name);
        var email = p.email ? escHtml(p.email) : '<span class="text-muted">—</span>';

        return '<tr id="patient-row-' + p.id + '">' +
            '<td class="text-muted" style="font-size:12px;">' + index + '</td>' +
            '<td><span class="patient-code">' + escHtml(p.patient_code) + '</span></td>' +
            '<td>' +
                '<div class="patient-name">' + name + '</div>' +
                '<div class="patient-meta">' + email + '</div>' +
            '</td>' +
            '<td style="font-size:13px;">' + ucfirst(escHtml(p.gender)) + '</td>' +
            '<td><span style="font-size:12px;background:#f4f6fb;padding:3px 9px;border-radius:6px;font-weight:600;">' + escHtml(p.blood_group || 'Unknown') + '</span></td>' +
            '<td style="font-size:13px;">' + escHtml(p.phone) + '</td>' +
            '<td><span class="status-badge ' + badgeClass + '">' + badgeText + '</span></td>' +
            '<td>' +
                '<div class="d-flex gap-1">' +
                    '<button class="action-btn edit js-edit-patient" title="Edit" data-id="' + p.id + '"><i class="fas fa-pen"></i></button>' +
                    '<button class="action-btn toggle js-toggle-patient" title="' + toggleTip + '" data-id="' + p.id + '" data-active="' + (isActive ? 1 : 0) + '"><i class="fas fa-sync-alt"></i></button>' +
                    '<button class="action-btn del js-delete-patient" title="Delete" data-id="' + p.id + '" data-name="' + name + '"><i class="fas fa-trash-alt"></i></button>' +
                '</div>' +
            '</td>' +
        '</tr>';
    }

    function escHtml(str) {
        return $('<div>').text(String(str || '')).html();
    }

    function ucfirst(str) {
        if (!str) { return ''; }
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    /* ═══════════════════════════════════════════════════════════════
       BUILD EMPTY STATE
    ═══════════════════════════════════════════════════════════════ */
    function buildEmpty() {
        return '<tr><td colspan="8">' +
            '<div class="empty-state">' +
                '<div class="empty-icon"><i class="fas fa-user-slash"></i></div>' +
                '<h5>No Patients Found</h5>' +
                '<p>Try adjusting your search or filters, or register a new patient.</p>' +
            '</div>' +
        '</td></tr>';
    }

    /* ═══════════════════════════════════════════════════════════════
       LOAD TABLE (AJAX)
    ═══════════════════════════════════════════════════════════════ */
    function loadTable() {
        if (state.loading) { return; }
        state.loading = true;

        showTableLoader();

        $.ajax({
            url      : URLS.list,
            type     : 'GET',
            dataType : 'json',
            data     : {
                search  : state.search,
                status  : state.status,
                page    : state.page
            },
            success  : function (res) {
                state.loading = false;

                if (!res.status) {
                    showToast('error', res.message || 'Failed to load patients.');
                    $('#patientTableBody').html(buildEmpty());
                    return;
                }

                state.total       = res.total;
                state.total_pages = res.total_pages;

                // Render rows
                var rows   = res.data || [];
                var offset = (res.page - 1) * res.per_page;
                var html   = '';

                if (rows.length === 0) {
                    html = buildEmpty();
                } else {
                    $.each(rows, function (i, p) {
                        html += buildRow(p, offset + i + 1);
                    });
                }

                $('#patientTableBody').html(html);

                // Stats (counts across all pages — use query totals)
                updateStats(res);

                // Pagination
                renderPagination(res.page, res.total_pages, res.total);
            },
            error    : function () {
                state.loading = false;
                showToast('error', 'Server error. Please try again.');
                $('#patientTableBody').html(buildEmpty());
            }
        });
    }

    /* ═══════════════════════════════════════════════════════════════
       STATS (load separately for all / active / inactive counts)
    ═══════════════════════════════════════════════════════════════ */
    function updateStats(listRes) {
        // If current filter = 'all' we can use the total from list response.
        // We fire two quick extra calls for active/inactive counts.
        $('#statTotal').text(listRes.total !== undefined ? listRes.total : '–');

        $.ajax({ url: URLS.list, type: 'GET', dataType: 'json', data: { status: '1', per_page: 1 },
            success: function (r) { if (r.status) { $('#statActive').text(r.total); } } });

        $.ajax({ url: URLS.list, type: 'GET', dataType: 'json', data: { status: '0', per_page: 1 },
            success: function (r) { if (r.status) { $('#statInactive').text(r.total); } } });

        // If the current filter is not 'all', total shown in stat card should still be all
        if (state.status !== 'all') {
            $.ajax({ url: URLS.list, type: 'GET', dataType: 'json', data: { status: 'all', per_page: 1 },
                success: function (r) { if (r.status) { $('#statTotal').text(r.total); } } });
        }
    }

    /* ═══════════════════════════════════════════════════════════════
       PAGINATION
    ═══════════════════════════════════════════════════════════════ */
    function renderPagination(page, totalPages, total) {
        var start = total === 0 ? 0 : (page - 1) * state.per_page + 1;
        var end   = Math.min(page * state.per_page, total);

        $('#pageInfo').text('Showing ' + start + '–' + end + ' of ' + total + ' patients');

        var $btns = $('#pageBtns').empty();

        // Prev
        var $prev = $('<button class="page-btn">&laquo;</button>');
        if (page <= 1) { $prev.attr('disabled', true); }
        else { $prev.on('click', function () { state.page = page - 1; loadTable(); }); }
        $btns.append($prev);

        // Page numbers (show up to 5)
        var startP = Math.max(1, page - 2);
        var endP   = Math.min(totalPages, startP + 4);
        startP     = Math.max(1, endP - 4);

        for (var i = startP; i <= endP; i++) {
            (function (p) {
                var $btn = $('<button class="page-btn">' + p + '</button>');
                if (p === page) { $btn.addClass('active'); }
                else {
                    $btn.on('click', function () { state.page = p; loadTable(); });
                }
                $btns.append($btn);
            }(i));
        }

        // Next
        var $next = $('<button class="page-btn">&raquo;</button>');
        if (page >= totalPages) { $next.attr('disabled', true); }
        else { $next.on('click', function () { state.page = page + 1; loadTable(); }); }
        $btns.append($next);
    }

    /* ═══════════════════════════════════════════════════════════════
       ADD PATIENT
    ═══════════════════════════════════════════════════════════════ */
    function initAddModal() {
        var $modal = $('#modalAdd');
        var $form  = $('#formAdd');
        var $btn   = $('#btnAddSubmit');
        var PREFIX = 'add_';
        var bsModal = new bootstrap.Modal($modal[0]);

        // Open
        $('#btnOpenAdd').on('click', function () {
            $form[0].reset();
            clearErrors(PREFIX);
            bsModal.show();
        });

        // Submit
        $form.on('submit', function (e) {
            e.preventDefault();
            clearErrors(PREFIX);

            var originalHtml = $btn.html();
            showBtnLoader($btn, originalHtml);

            var formData = $form.serialize();

            $.ajax({
                url      : URLS.add,
                type     : 'POST',
                dataType : 'json',
                data     : formData + '&' + encodeURIComponent(window.HMS_CSRF_NAME) + '=' + encodeURIComponent(window.HMS_CSRF_HASH),
                success  : function (res) {
                    resetBtn($btn, originalHtml);
                    refreshCsrf(res.csrfHash);

                    if (!res.status) {
                        if (res.errors) { showErrors(res.errors, PREFIX); }
                        showToast('error', res.message || 'Validation failed.');
                        return;
                    }

                    bsModal.hide();
                    $form[0].reset();
                    state.page = 1;
                    loadTable();
                    showToast('success', res.message || 'Patient registered successfully.');
                },
                error    : function (xhr) {
                    resetBtn($btn, originalHtml);
                    var res = {};
                    try { res = JSON.parse(xhr.responseText); } catch (ex) {}
                    refreshCsrf(res.csrfHash);
                    if (res.errors) { showErrors(res.errors, PREFIX); }
                    showToast('error', res.message || 'Server error. Please try again.');
                }
            });
        });
    }

    /* ═══════════════════════════════════════════════════════════════
       EDIT PATIENT
    ═══════════════════════════════════════════════════════════════ */
    function initEditModal() {
        var $modal = $('#modalEdit');
        var $form  = $('#formEdit');
        var $btn   = $('#btnEditSubmit');
        var PREFIX = 'edit_';
        var bsModal = new bootstrap.Modal($modal[0]);

        // Open — fetch patient data via GET
        $(document).on('click', '.js-edit-patient', function () {
            var id = $(this).data('id');
            clearErrors(PREFIX);

            // Show spinner inside modal body while loading
            $form.find('.modal-body .row').hide();
            $('#editPatientCodeBox').hide();
            $modal.find('.modal-body').prepend(
                '<div id="editModalLoader" class="text-center py-4">' +
                    '<div class="hms-spinner"></div>' +
                    '<div style="color:#8a94a6;font-size:13px;margin-top:8px;">Loading patient data…</div>' +
                '</div>'
            );

            bsModal.show();

            $.ajax({
                url      : URLS.edit(id),
                type     : 'GET',
                dataType : 'json',
                success  : function (res) {
                    $('#editModalLoader').remove();
                    $form.find('.modal-body .row').show();
                    $('#editPatientCodeBox').show();

                    if (!res.status || !res.data) {
                        bsModal.hide();
                        showToast('error', res.message || 'Patient not found.');
                        return;
                    }

                    var p = res.data;
                    $('#editPatientId').val(p.id);
                    $('#editPatientCode').text(p.patient_code);

                    // Populate fields
                    $('#' + PREFIX + 'full_name').val(p.full_name || '');
                    $('#' + PREFIX + 'gender').val(p.gender || '');
                    $('#' + PREFIX + 'dob').val(p.dob || '');
                    $('#' + PREFIX + 'blood_group').val(p.blood_group || 'Unknown');
                    $('#' + PREFIX + 'phone').val(p.phone || '');
                    $('#' + PREFIX + 'email').val(p.email || '');
                    $('#' + PREFIX + 'emergency_contact').val(p.emergency_contact || '');
                    $('#' + PREFIX + 'address').val(p.address || '');
                    $('#' + PREFIX + 'status').val(String(p.status));
                },
                error    : function () {
                    $('#editModalLoader').remove();
                    bsModal.hide();
                    showToast('error', 'Could not load patient data.');
                }
            });
        });

        // Submit
        $form.on('submit', function (e) {
            e.preventDefault();
            clearErrors(PREFIX);

            var id = $('#editPatientId').val();
            if (!id) { showToast('error', 'No patient selected.'); return; }

            var originalHtml = $btn.html();
            showBtnLoader($btn, originalHtml);

            $.ajax({
                url      : URLS.edit(id),
                type     : 'POST',
                dataType : 'json',
                data     : $form.serialize() + '&' + encodeURIComponent(window.HMS_CSRF_NAME) + '=' + encodeURIComponent(window.HMS_CSRF_HASH),
                success  : function (res) {
                    resetBtn($btn, originalHtml);
                    refreshCsrf(res.csrfHash);

                    if (!res.status) {
                        if (res.errors) { showErrors(res.errors, PREFIX); }
                        showToast('error', res.message || 'Validation failed.');
                        return;
                    }

                    bsModal.hide();
                    loadTable();
                    showToast('success', res.message || 'Patient updated successfully.');
                },
                error    : function (xhr) {
                    resetBtn($btn, originalHtml);
                    var res = {};
                    try { res = JSON.parse(xhr.responseText); } catch (ex) {}
                    refreshCsrf(res.csrfHash);
                    if (res.errors) { showErrors(res.errors, PREFIX); }
                    showToast('error', res.message || 'Server error. Please try again.');
                }
            });
        });
    }

    /* ═══════════════════════════════════════════════════════════════
       STATUS TOGGLE
    ═══════════════════════════════════════════════════════════════ */
    function initToggle() {
        $(document).on('click', '.js-toggle-patient', function () {
            var $btn   = $(this);
            var id     = $btn.data('id');
            var active = parseInt($btn.data('active'), 10) === 1;
            var action = active ? 'deactivate' : 'activate';

            if (!window.confirm('Are you sure you want to ' + action + ' this patient?')) {
                return;
            }

            var origHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="fas fa-circle-notch fa-spin"></i>');

            $.ajax({
                url      : URLS.toggle(id),
                type     : 'POST',
                dataType : 'json',
                data     : csrfData(),
                success  : function (res) {
                    $btn.prop('disabled', false).html(origHtml);
                    refreshCsrf(res.csrfHash);

                    if (!res.status) {
                        showToast('error', res.message || 'Status change failed.');
                        return;
                    }

                    // Refresh just the affected row
                    loadTable();
                    showToast('success', res.message);
                },
                error    : function (xhr) {
                    $btn.prop('disabled', false).html(origHtml);
                    var res = {};
                    try { res = JSON.parse(xhr.responseText); } catch (ex) {}
                    refreshCsrf(res.csrfHash);
                    showToast('error', res.message || 'Server error.');
                }
            });
        });
    }

    /* ═══════════════════════════════════════════════════════════════
       DELETE PATIENT
    ═══════════════════════════════════════════════════════════════ */
    function initDelete() {
        $(document).on('click', '.js-delete-patient', function () {
            var $btn = $(this);
            var id   = $btn.data('id');
            var name = $btn.data('name') || 'this patient';

            if (!window.confirm('Are you sure you want to remove ' + name + '?\nThis action cannot be undone.')) {
                return;
            }

            var origHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="fas fa-circle-notch fa-spin"></i>');

            $.ajax({
                url      : URLS.del(id),
                type     : 'POST',
                dataType : 'json',
                data     : csrfData(),
                success  : function (res) {
                    refreshCsrf(res.csrfHash);

                    if (!res.status) {
                        $btn.prop('disabled', false).html(origHtml);
                        showToast('error', res.message || 'Delete failed.');
                        return;
                    }

                    // Animate row out then reload
                    $('#patient-row-' + id).fadeOut(300, function () {
                        state.page = 1;
                        loadTable();
                    });

                    showToast('success', res.message || 'Patient removed.');
                },
                error    : function (xhr) {
                    $btn.prop('disabled', false).html(origHtml);
                    var res = {};
                    try { res = JSON.parse(xhr.responseText); } catch (ex) {}
                    refreshCsrf(res.csrfHash);
                    showToast('error', res.message || 'Server error.');
                }
            });
        });
    }

    /* ═══════════════════════════════════════════════════════════════
       SEARCH & FILTER
    ═══════════════════════════════════════════════════════════════ */
    function initSearchFilter() {
        var debouncedLoad = debounce(function () {
            state.page   = 1;
            state.search = $.trim($('#patientSearch').val());
            loadTable();
        }, 350);

        // Live search
        $('#patientSearch').on('input keyup', function () {
            var val = $(this).val();
            $('#btnClearSearch').toggleClass('visible', val.length > 0);
            debouncedLoad();
        });

        // Clear search
        $('#btnClearSearch').on('click', function () {
            $('#patientSearch').val('').trigger('input');
        });

        // Status filters
        $(document).on('click', '.filter-btn', function () {
            $('.filter-btn').removeClass('active');
            $(this).addClass('active');
            state.status = $(this).data('status');
            state.page   = 1;
            loadTable();
        });
    }

    /* ═══════════════════════════════════════════════════════════════
       INIT
    ═══════════════════════════════════════════════════════════════ */
    $(function () {
        initSearchFilter();
        initAddModal();
        initEditModal();
        initToggle();
        initDelete();

        // Initial table load
        loadTable();
    });

}(jQuery));
