(function (window, $) {
    'use strict';

    if (!$) {
        return;
    }

    var HMSAjax = {
        csrfName: null,
        csrfHash: null,
        activeRequests: 0,
        defaultTableTarget: null
    };

    function ensureLoader() {
        if ($('#hmsAjaxLoader').length) {
            return;
        }

        $('body').append(
            '<div id="hmsAjaxLoader" class="hms-ajax-loader" style="display:none;">' +
                '<div class="hms-ajax-loader-box">' +
                    '<i class="fas fa-spinner fa-spin mr-2"></i> Please wait...' +
                '</div>' +
            '</div>'
        );
    }

    function ensureStyles() {
        if ($('#hmsAjaxStyles').length) {
            return;
        }

        $('head').append(
            '<style id="hmsAjaxStyles">' +
                '.hms-ajax-loader{position:fixed;z-index:2050;top:0;right:0;bottom:0;left:0;background:rgba(255,255,255,.55);display:flex;align-items:center;justify-content:center;}' +
                '.hms-ajax-loader-box{background:#343a40;color:#fff;border-radius:4px;padding:12px 18px;box-shadow:0 0.5rem 1rem rgba(0,0,0,.15);font-weight:600;}' +
                '.hms-ajax-field-error{display:block;margin-top:4px;color:#dc3545;font-size:80%;}' +
            '</style>'
        );
    }

    function escapeHtml(value) {
        return $('<div>').text(value === null || value === undefined ? '' : value).html();
    }

    HMSAjax.init = function (options) {
        options = options || {};
        HMSAjax.csrfName = options.csrfName || $('meta[name="csrf-token-name"]').attr('content') || window.HMS_CSRF_NAME || HMSAjax.csrfName;
        HMSAjax.csrfHash = options.csrfHash || $('meta[name="csrf-token-hash"]').attr('content') || window.HMS_CSRF_HASH || HMSAjax.csrfHash;
        HMSAjax.defaultTableTarget = options.defaultTableTarget || HMSAjax.defaultTableTarget;

        ensureStyles();
        ensureLoader();
        HMSAjax.setupAjax();
    };

    HMSAjax.setupAjax = function () {
        $.ajaxSetup({
            beforeSend: function (xhr, settings) {
                if (HMSAjax.csrfName && HMSAjax.csrfHash && settings.type && settings.type.toUpperCase() !== 'GET') {
                    if (settings.data instanceof FormData) {
                        settings.data.append(HMSAjax.csrfName, HMSAjax.csrfHash);
                    } else if (typeof settings.data === 'string' && settings.data.indexOf(HMSAjax.csrfName + '=') === -1) {
                        settings.data += (settings.data ? '&' : '') + encodeURIComponent(HMSAjax.csrfName) + '=' + encodeURIComponent(HMSAjax.csrfHash);
                    } else if ($.isPlainObject(settings.data) && !settings.data[HMSAjax.csrfName]) {
                        settings.data[HMSAjax.csrfName] = HMSAjax.csrfHash;
                    }
                }
            },
            complete: function (xhr) {
                HMSAjax.updateCsrfFromResponse(xhr.responseJSON);
            }
        });
    };

    HMSAjax.updateCsrfFromResponse = function (response) {
        if (!response) {
            return;
        }

        if (response.csrfHash) {
            HMSAjax.csrfHash = response.csrfHash;
            window.HMS_CSRF_HASH = response.csrfHash;
            $('meta[name="csrf-token-hash"]').attr('content', response.csrfHash);
        }
    };

    HMSAjax.showLoader = function () {
        ensureLoader();
        HMSAjax.activeRequests++;
        $('#hmsAjaxLoader').stop(true, true).fadeIn(100);
    };

    HMSAjax.hideLoader = function () {
        HMSAjax.activeRequests = Math.max(0, HMSAjax.activeRequests - 1);
        if (HMSAjax.activeRequests === 0) {
            $('#hmsAjaxLoader').stop(true, true).fadeOut(100);
        }
    };

    HMSAjax.notify = function (type, message, target) {
        type = type || 'info';
        message = message || '';
        target = target || '#hmsNotificationArea';

        var map = {
            success: 'success',
            error: 'danger',
            warning: 'warning',
            info: 'info'
        };

        var alertClass = map[type] || 'info';
        var html = '<div class="alert alert-' + alertClass + ' alert-dismissible fade show" role="alert">' +
            escapeHtml(message) +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span>' +
            '</button>' +
            '</div>';

        if ($(target).length) {
            $(target).html(html);
            return;
        }

        if (!$('#hmsNotificationArea').length) {
            $('.content .container-fluid:first').prepend('<div id="hmsNotificationArea"></div>');
        }

        $('#hmsNotificationArea').html(html);
    };

    HMSAjax.clearFormErrors = function ($form) {
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.hms-ajax-field-error').remove();
        $form.find('.hms-ajax-validation-summary').remove();
    };

    HMSAjax.renderValidationErrors = function ($form, errors) {
        HMSAjax.clearFormErrors($form);

        if (!errors) {
            return;
        }

        var summary = [];
        $.each(errors, function (field, message) {
            if (!message) {
                return;
            }

            summary.push(message);
            var $field = $form.find('[name="' + field + '"]');
            if ($field.length) {
                $field.addClass('is-invalid');
                $field.closest('.form-group').append('<span class="hms-ajax-field-error">' + escapeHtml(message) + '</span>');
            }
        });

        if (summary.length) {
            $form.find('.card-body:first').prepend(
                '<div class="alert alert-danger hms-ajax-validation-summary">Please correct the highlighted errors.</div>'
            );
        }
    };

    HMSAjax.request = function (options) {
        options = options || {};
        var showLoader = options.showLoader !== false;

        if (HMSAjax.csrfName && HMSAjax.csrfHash && options.type && options.type.toUpperCase() !== 'GET' && options.data === undefined) {
            options.data = {};
        }

        if (showLoader) {
            HMSAjax.showLoader();
        }

        return $.ajax(options)
            .done(function (response) {
                HMSAjax.updateCsrfFromResponse(response);
                if ($.isFunction(options.hmsSuccess)) {
                    options.hmsSuccess(response);
                }
            })
            .fail(function (xhr) {
                var response = xhr.responseJSON || {};
                HMSAjax.updateCsrfFromResponse(response);

                if ($.isFunction(options.hmsError)) {
                    options.hmsError(xhr, response);
                    return;
                }

                HMSAjax.notify('error', response.message || 'Something went wrong. Please try again.');
            })
            .always(function () {
                if (showLoader) {
                    HMSAjax.hideLoader();
                }
            });
    };

    HMSAjax.refreshTable = function (url, data, target) {
        target = target || HMSAjax.defaultTableTarget;

        return HMSAjax.request({
            url: url,
            type: 'GET',
            data: data || {},
            dataType: 'json',
            hmsSuccess: function (response) {
                if (response.status && response.html && target) {
                    $(target).html(response.html);
                    return;
                }

                if (response.status && response.data && $.isFunction(HMSAjax.tableRenderer)) {
                    HMSAjax.tableRenderer(response.data, target);
                    return;
                }

                HMSAjax.notify('error', response.message || 'Unable to refresh table.');
            }
        });
    };

    HMSAjax.debounce = function (callback, delay) {
        var timer = null;

        return function () {
            var context = this;
            var args = arguments;

            window.clearTimeout(timer);
            timer = window.setTimeout(function () {
                callback.apply(context, args);
            }, delay || 300);
        };
    };

    HMSAjax.bindAjaxForm = function (selector, options) {
        options = options || {};

        $(document).on('submit', selector, function (event) {
            event.preventDefault();

            var $form = $(this);
            HMSAjax.clearFormErrors($form);

            HMSAjax.request({
                url: $form.attr('action'),
                type: ($form.attr('method') || 'POST').toUpperCase(),
                data: $form.serialize(),
                dataType: 'json',
                hmsSuccess: function (response) {
                    if (response.status) {
                        HMSAjax.notify('success', response.message || 'Saved successfully.', options.notificationTarget);

                        if (response.redirect) {
                            window.setTimeout(function () {
                                window.location.href = response.redirect;
                            }, options.redirectDelay || 700);
                            return;
                        }

                        if (options.tableUrl && options.tableTarget) {
                            HMSAjax.refreshTable(options.tableUrl, options.tableData || {}, options.tableTarget);
                        }

                        if (options.resetOnSuccess) {
                            $form[0].reset();
                        }

                        if ($.isFunction(options.success)) {
                            options.success(response, $form);
                        }

                        return;
                    }

                    HMSAjax.renderValidationErrors($form, response.errors);
                    HMSAjax.notify('error', response.message || 'Please correct the form errors.', options.notificationTarget);
                },
                hmsError: function (xhr, response) {
                    HMSAjax.renderValidationErrors($form, response.errors);
                    HMSAjax.notify('error', response.message || 'Unable to save. Please try again.', options.notificationTarget);
                }
            });
        });
    };

    window.HMSAjax = HMSAjax;

    $(function () {
        HMSAjax.init();
    });
})(window, window.jQuery);
