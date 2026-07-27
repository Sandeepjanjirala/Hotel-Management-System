(function (window, document, $) {
    'use strict';

    if (!$) {
        return;
    }

    var TABLE_SELECTOR = 'table';
    var descriptionMap = {
        dashboard: 'Track operational health, team activity, and service flow from a single enterprise workspace.',
        patients: 'Review patient records, profile activity, and coordinate care administration with live filters.',
        appointments: 'Manage schedules, visit status, provider allocation, and daily patient flow with confidence.',
        doctors: 'Oversee clinician profiles, department alignment, and workforce readiness across the hospital.',
        departments: 'Maintain clinical departments, ownership, and service availability across the organization.',
        users: 'Control platform access, staff roles, and account readiness with governed administration.',
        billing: 'Monitor invoices, payments, and financial follow-through across care and operations.',
        pharmacy: 'Coordinate medicine dispensing, stock visibility, and pharmacy billing execution.',
        prescriptions: 'Review medication plans, issuing workflows, and prescription coverage by patient and doctor.',
        reports: 'Surface performance insights, trends, and operational metrics for executive decision-making.',
        lab: 'Coordinate test requests, result readiness, and diagnostic throughput across the lab network.'
    };

    function init() {
        installDataTableGuard();
        $('body').addClass('hms-enterprise-shell');
        normalizeLayout();
        enhancePageHeaders();
        enhanceCardsAndBoxes();
        enhanceForms(document);
        enhanceTables(document);
        bridgeAlerts(document);
        installGlobalAjaxFeedback();
        observeDynamicContent();
    }

    function installDataTableGuard() {
        if (!$.fn.dataTable) {
            return;
        }

        $.fn.dataTable.ext.errMode = 'none';

        $(document).off('error.dt.hms').on('error.dt.hms', function (_event, _settings, _techNote, message) {
            if (window.console) {
                console.warn('DataTables warning:', message);
            }

            toast('A table layout issue was detected and safely skipped.', 'warning');
        });
    }

    function normalizeLayout() {
        $('.content > .container-fluid, .content-wrapper > .container-fluid').each(function () {
            $(this).addClass('hms-page-container');
        });

        $('.box').addClass('card');
        $('.box-header').addClass('card-header');
        $('.box-body').addClass('card-body');
        $('.box-footer').addClass('card-footer');

        $('.sidebar-menu > li.treeview').addClass('has-treeview');
        $('.sidebar-menu .treeview-menu').addClass('nav nav-treeview');
    }

    function enhancePageHeaders() {
        $('.content-header').each(function () {
            var $header = $(this);
            if ($header.find('.hms-page-hero').length) {
                return;
            }

            var $title = $header.find('h1').first();
            if (!$title.length) {
                return;
            }

            var title = $.trim($title.text());
            var existingDescription = $.trim($header.find('p').first().text());
            var description = existingDescription || inferDescription(title);
            var $breadcrumb = $header.find('.breadcrumb, ol').first();
            var breadcrumbHtml = $breadcrumb.length ? $breadcrumb.prop('outerHTML') : '';
            var $primaryAction = pickPrimaryAction($header);
            var primaryActionHtml = $primaryAction.length ? $('<div>').append($primaryAction.clone(true, true)).html() : '';
            if ($primaryAction.length) {
                $primaryAction.addClass('d-none hms-source-action');
            }

            var heroHtml = ''
                + '<div class="container-fluid">'
                +   '<div class="hms-page-hero">'
                +     '<div class="hms-page-hero__main">'
                +       '<div class="hms-page-hero__eyebrow"><i class="fas fa-heartbeat"></i><span>Enterprise Workspace</span></div>'
                +       '<h1 class="hms-page-hero__title">' + escapeHtml(title) + '</h1>'
                +       '<p class="hms-page-hero__description">' + escapeHtml(description) + '</p>'
                +       (breadcrumbHtml ? '<div class="mt-3">' + breadcrumbHtml + '</div>' : '')
                +     '</div>'
                +     '<div class="hms-page-hero__side">'
                +       '<div class="hms-page-hero__meta">'
                +         '<span class="hms-meta-chip"><i class="far fa-clock"></i>Last updated ' + formattedTimestamp() + '</span>'
                +       '</div>'
                +       '<div class="hms-page-hero__actions">' + primaryActionHtml + '</div>'
                +     '</div>'
                +   '</div>'
                + '</div>';

            $header.empty().append(heroHtml).addClass('hms-page-header');
            $header.find('.breadcrumb').addClass('bg-transparent p-0 m-0');
            $header.find('.breadcrumb-item a').css('color', 'rgba(255,255,255,0.86)');
            $header.find('.breadcrumb-item.active').css('color', 'rgba(255,255,255,0.68)');
            $header.find('.hms-page-hero__actions .btn').removeClass('btn-sm btn-xs');
        });
    }

    function pickPrimaryAction($header) {
        var $content = $header.closest('.content-wrapper').find('.content').first();
        var selectors = [
            '.page-actions .btn:visible:first',
            '.card-tools .btn:visible:first',
            '.box-tools .btn:visible:first',
            'a.btn-primary:visible:first',
            'button.btn-primary:visible:first',
            'a.btn-success:visible:first',
            'button.btn-success:visible:first'
        ];
        var $match = $();

        $.each(selectors, function (_, selector) {
            $match = $content.find(selector).not('.close, [data-dismiss], .btn-tool').first();
            if ($match.length) {
                return false;
            }
        });

        return $match;
    }

    function inferDescription(title) {
        var normalized = String(title || '').toLowerCase();
        var picked = 'Monitor records, actions, and workflows in a secure, role-aware hospital operations console.';

        $.each(descriptionMap, function (key, value) {
            if (normalized.indexOf(key) !== -1) {
                picked = value;
                return false;
            }
        });

        return picked;
    }

    function enhanceCardsAndBoxes() {
        if ($('.hms-dashboard').length) {
            return;
        }

        $('.content .card, .content .box').each(function () {
            var $panel = $(this);
            if ($panel.find('.table').length && shouldEnhancePanel($panel)) {
                injectSummaryCards($panel);
                injectFilterToolbar($panel);
            }
        });
    }

    function shouldEnhancePanel($panel) {
        if (!$panel || !$panel.length) {
            return false;
        }

        if ($panel.closest('.hms-dashboard').length) {
            return false;
        }

        return $panel.find('tbody[id], [id$="TableBody"]').length > 0;
    }

    function injectSummaryCards($panel) {
        if ($panel.find('.hms-summary-grid').length) {
            return;
        }

        var $table = $panel.find('table').first();
        if (!$table.length) {
            return;
        }

        var $summary = $('<div class="hms-summary-grid"></div>');
        $panel.find('.card-body, .box-body').first().prepend($summary);

        var updateSummary = function () {
            var metrics = summarizeTable($table);
            $summary.empty().append(renderSummaryCard('Total Records', metrics.total, 'fas fa-database'));
            $summary.append(renderSummaryCard('Active', metrics.active, 'fas fa-check-circle'));
            $summary.append(renderSummaryCard('Inactive', metrics.inactive, 'fas fa-pause-circle'));
            $summary.append(renderSummaryCard('Today\'s Records', metrics.today, 'fas fa-calendar-day'));
            $summary.append(renderSummaryCard('Visible Rows', metrics.visible, 'fas fa-list'));
            $summary.append(renderSummaryCard('Status Signals', metrics.statuses, 'fas fa-wave-square'));
        };

        updateSummary();
        observeTableBody($table, updateSummary);
    }

    function summarizeTable($table) {
        var $rows = $table.find('tbody tr').filter(function () {
            var text = normalizeText($(this).text());
            return text && text.indexOf('loading') === -1 && text.indexOf('no ') !== 0;
        });
        var active = 0;
        var inactive = 0;
        var today = 0;
        var statuses = 0;
        var now = new Date();
        var todayParts = [pad(now.getDate()), monthShort(now.getMonth()), now.getFullYear()];
        var todayAlt = now.toISOString().slice(0, 10);

        $rows.each(function () {
            var text = normalizeText($(this).text());
            if (text.indexOf('active') !== -1) {
                active += 1;
            }
            if (text.indexOf('inactive') !== -1 || text.indexOf('cancelled') !== -1) {
                inactive += 1;
            }
            if (text.indexOf(todayAlt) !== -1 || text.indexOf(todayParts.join(' ').toLowerCase()) !== -1) {
                today += 1;
            }
            if ($(this).find('.badge, .label').length) {
                statuses += 1;
            }
        });

        return {
            total: $rows.length,
            visible: $rows.length,
            active: active,
            inactive: inactive,
            today: today,
            statuses: statuses
        };
    }

    function renderSummaryCard(label, value, iconClass) {
        return ''
            + '<div class="hms-summary-card">'
            +   '<span class="hms-summary-card__icon"><i class="' + iconClass + '"></i></span>'
            +   '<span class="hms-summary-card__label">' + escapeHtml(label) + '</span>'
            +   '<span class="hms-summary-card__value">' + formatMetric(value) + '</span>'
            +   '<span class="hms-summary-card__subtle">Operational snapshot</span>'
            + '</div>';
    }

    function injectFilterToolbar($panel) {
        if ($panel.find('.hms-filter-toolbar').length) {
            return;
        }

        var $controls = $panel.find('.card-body > .row.mb-3, .box-header .box-tools form, .box-header .box-tools, .card-body .row.mb-3').first();
        var $table = $panel.find('table').first();
        if (!$table.length) {
            return;
        }

        var $toolbar = $('<div class="hms-filter-toolbar"></div>');
        if ($controls.length) {
            var $inputs = $controls.find('input, select, .input-group, button').filter(function () {
                return !$(this).closest('.modal').length;
            });

            if ($inputs.length) {
                $inputs.each(function () {
                    var $item = $(this);
                    if ($item.closest('.input-group').length) {
                        if (!$toolbar.find('.input-group').length) {
                            $toolbar.append($item.closest('.input-group'));
                        }
                    } else {
                        $toolbar.append($item);
                    }
                });
            }
        }

        if (!$toolbar.children().length) {
            var $datatableSearch = $('<input type="search" class="form-control hms-toolbar-search" placeholder="Search records">');
            $datatableSearch.on('input', function () {
                if ($.fn.DataTable && $.fn.DataTable.isDataTable($table)) {
                    $table.DataTable().search(this.value).draw();
                }
            });
            $toolbar.append($datatableSearch);
        }

        $toolbar.append('<button type="button" class="btn btn-default hms-toolbar-refresh"><i class="fas fa-sync-alt mr-1"></i>Refresh</button>');
        $toolbar.append('<button type="button" class="btn btn-default hms-toolbar-reset"><i class="fas fa-eraser mr-1"></i>Reset</button>');

        $toolbar.insertBefore($panel.find('.table-responsive, table').first());

        $toolbar.on('click', '.hms-toolbar-refresh', function () {
            if ($.fn.DataTable && $.fn.DataTable.isDataTable($table)) {
                $table.DataTable().draw(false);
            }
            $(document).trigger('hms:refresh');
        });

        $toolbar.on('click', '.hms-toolbar-reset', function () {
            $toolbar.find('input[type="search"], input[type="text"], input[type="date"], select').each(function () {
                $(this).val('').trigger('change');
            });
            if ($.fn.DataTable && $.fn.DataTable.isDataTable($table)) {
                $table.DataTable().search('').draw();
            }
        });
    }

    function enhanceForms(root) {
        $(root).find('.form-group').each(function () {
            var $group = $(this);
            if ($group.hasClass('hms-floating-field')) {
                return;
            }

            var $label = $group.children('label').first();
            var $control = $group.children('input.form-control, select.form-control, textarea.form-control, .custom-select').first();

            if (!$label.length || !$control.length || $control.attr('type') === 'checkbox' || $control.attr('type') === 'radio') {
                return;
            }

            $group.addClass('hms-floating-field');
            $label.insertAfter($control);
            updateFloatingState($group);

            $control.on('focus blur input change', function () {
                updateFloatingState($group);
            });
        });
    }

    function updateFloatingState($group) {
        var $control = $group.children('input, select, textarea').first();
        var hasValue = !!$.trim($control.val());
        $group.toggleClass('has-value', hasValue);
        $group.toggleClass('is-active', document.activeElement === $control.get(0));
    }

    function enhanceTables(root) {
        $(root).find(TABLE_SELECTOR).each(function () {
            var $table = $(this);
            if ($table.closest('.modal').length || $table.closest('.swal2-container').length) {
                return;
            }

            if (isDashboardTable($table)) {
                return;
            }

            decorateTableState($table);
            decorateActionButtons($table);
            initializeDataTable($table);
            observeTableBody($table, function () {
                decorateTableState($table);
                decorateActionButtons($table);
                if ($.fn.DataTable && $.fn.DataTable.isDataTable($table)) {
                    var api = $table.DataTable();
                    api.rows().invalidate().draw(false);
                } else {
                    initializeDataTable($table);
                }
            });
        });
    }

    function decorateTableState($table) {
        var $rows = $table.find('tbody tr');
        if (!$rows.length) {
            return;
        }

        $rows.each(function () {
            var $row = $(this);
            var text = normalizeText($row.text());

            $row.removeClass('hms-loading-row');
            $row.find('td').removeClass('hms-empty-state');

            if (text.indexOf('loading') !== -1) {
                $row.addClass('hms-loading-row');
            } else if (text.indexOf('no ') === 0 || text.indexOf('no ') > -1 && $row.children('td').length === 1) {
                $row.find('td').addClass('hms-empty-state').html('<i class="fas fa-inbox"></i>' + escapeHtml($.trim($row.text())));
            }
        });
    }

    function decorateActionButtons($table) {
        $table.find('tbody tr').each(function () {
            $(this).find('td:last-child .btn, td:last-child a.btn').each(function () {
                var $btn = $(this);
                var title = normalizeButtonText($btn);
                if (title) {
                    $btn.attr('title', title).attr('aria-label', title);
                }

                if (!$btn.hasClass('hms-action-btn')) {
                    var textNodes = $btn.contents().filter(function () {
                        return this.nodeType === 3 && $.trim(this.nodeValue);
                    });

                    textNodes.each(function () {
                        var value = $.trim(this.nodeValue);
                        if (value) {
                            $(this).replaceWith('<span class="hms-action-text">' + escapeHtml(value) + '</span>');
                        }
                    });

                    $btn.addClass('hms-action-btn');
                }
            });
        });
    }

    function initializeDataTable($table) {
        if (!$table.find('thead th').length || !$table.find('tbody').length) {
            return;
        }

        if (!$.fn.DataTable || $.fn.DataTable.isDataTable($table)) {
            return;
        }

        if (isDashboardTable($table) || isAjaxManagedTable($table) || hasColumnMismatch($table)) {
            return;
        }

        try {
            $table.DataTable({
                responsive: true,
                autoWidth: false,
                searching: true,
                ordering: true,
                paging: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                scrollX: true,
                dom: '<"d-flex flex-wrap justify-content-between align-items-center mb-3"lf>rt<"d-flex flex-wrap justify-content-between align-items-center"ip>',
                language: {
                    emptyTable: 'No records available for this view.',
                    zeroRecords: 'No matching records found.',
                    search: 'Search',
                    lengthMenu: 'Show _MENU_',
                    paginate: {
                        previous: '<i class="fas fa-chevron-left"></i>',
                        next: '<i class="fas fa-chevron-right"></i>'
                    }
                },
                columnDefs: [
                    { targets: -1, orderable: false }
                ]
            });
        } catch (error) {
            window.console && console.warn('HMS DataTable skipped:', error);
        }
    }

    function isDashboardTable($table) {
        return $table.closest('.hms-dashboard').length > 0;
    }

    function isAjaxManagedTable($table) {
        var $tbody = $table.find('tbody').first();
        if (!$tbody.length) {
            return false;
        }

        if ($tbody.attr('id')) {
            return true;
        }

        return $table.is('[data-hms-ajax-table="true"]');
    }

    function hasColumnMismatch($table) {
        var headerCount = $table.find('thead tr:first-child th').length;
        if (!headerCount) {
            return false;
        }

        var mismatch = false;
        $table.find('tbody tr').each(function () {
            var $cells = $(this).children('td, th');
            if (!$cells.length) {
                return;
            }

            var effectiveCount = 0;
            $cells.each(function () {
                var span = parseInt($(this).attr('colspan'), 10);
                effectiveCount += isNaN(span) || span < 1 ? 1 : span;
            });

            if (effectiveCount !== headerCount) {
                mismatch = true;
                return false;
            }
        });

        return mismatch;
    }

    function bridgeAlerts(root) {
        $(root).find('.alert').each(function () {
            var $alert = $(this);
            if ($alert.data('hmsToastBound')) {
                return;
            }

            $alert.data('hmsToastBound', true);
            var tone = 'info';
            if ($alert.hasClass('alert-success')) {
                tone = 'success';
            } else if ($alert.hasClass('alert-danger')) {
                tone = 'error';
            } else if ($alert.hasClass('alert-warning')) {
                tone = 'warning';
            }

            toast(normalizeText($alert.text()), tone);
            if (!$alert.closest('.modal').length) {
                $alert.addClass('d-none');
            }
        });
    }

    function observeDynamicContent() {
        var observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                Array.prototype.forEach.call(mutation.addedNodes, function (node) {
                    if (!node || node.nodeType !== 1) {
                        return;
                    }

                    enhanceForms(node);
                    enhanceTables(node);
                    bridgeAlerts(node);
                });
            });
        });

        observer.observe(document.body, { childList: true, subtree: true });
    }

    function observeTableBody($table, callback) {
        var body = $table.find('tbody').get(0);
        if (!body || body.__hmsObserved) {
            return;
        }

        body.__hmsObserved = true;
        var observer = new MutationObserver(function () {
            callback();
        });
        observer.observe(body, { childList: true, subtree: true, characterData: true });
    }

    function installGlobalAjaxFeedback() {
        $(document).ajaxError(function (_event, xhr) {
            if (xhr && xhr.status >= 500) {
                toast('A server error interrupted the last action.', 'error');
            }
        });

        window.HMSUI = window.HMSUI || {};
        window.HMSUI.toast = toast;
        window.HMSUI.refreshTables = function () {
            $('table').each(function () {
                if ($.fn.DataTable && $.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable().rows().invalidate().draw(false);
                }
            });
        };
    }

    function toast(message, icon) {
        if (!message) {
            return;
        }

        if (window.Swal && typeof window.Swal.fire === 'function') {
            window.Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: icon === 'error' ? 4200 : 2800,
                timerProgressBar: true,
                icon: icon || 'info',
                title: message
            });
            return;
        }

        if (window.console) {
            console.log(message);
        }
    }

    function normalizeButtonText($btn) {
        return $.trim($btn.text()).replace(/\s+/g, ' ');
    }

    function normalizeText(value) {
        return $.trim(String(value || '')).replace(/\s+/g, ' ').toLowerCase();
    }

    function escapeHtml(value) {
        return $('<div>').text(value).html();
    }

    function formatMetric(value) {
        var numeric = parseInt(value, 10);
        return isNaN(numeric) ? '0' : numeric.toLocaleString();
    }

    function formattedTimestamp() {
        var now = new Date();
        return monthShort(now.getMonth()) + ' ' + pad(now.getDate()) + ', ' + now.getFullYear() + ' ' + pad(now.getHours()) + ':' + pad(now.getMinutes());
    }

    function monthShort(index) {
        return ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'][index];
    }

    function pad(value) {
        return String(value).padStart(2, '0');
    }

    $(init);
})(window, document, window.jQuery);
