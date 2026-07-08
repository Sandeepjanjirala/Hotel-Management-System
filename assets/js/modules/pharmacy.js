/**
 * Pharmacy Module
 * Handles pharmacy billing operations via AJAX
 */

var PharmacyModule = (function() {
    'use strict';

    var config = {};
    var alertTimeout = null;
    var searchTimer = null;

    /* ═══════════════════════════════════════════════════════════════
       INIT - List Page
       ═══════════════════════════════════════════════════════════════ */
    function init(options) {
        config = options || {};
        bindListEvents();
        loadBills();
    }

    /* ═══════════════════════════════════════════════════════════════
       INIT - Create Page
       ═══════════════════════════════════════════════════════════════ */
    function initCreate(options) {
        config = options || {};
        bindCreateEvents();

        // Load prescription if preselected
        var $prescriptionSelect = $('#prescription_id');
        if ($prescriptionSelect.val()) {
            loadPrescription($prescriptionSelect.val());
        }
    }

    /* ═══════════════════════════════════════════════════════════════
       BIND LIST EVENTS
       ═══════════════════════════════════════════════════════════════ */
    function bindListEvents() {
        $('#billSearch').on('keyup', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                loadBills();
            }, 300);
        });

        $('#billDateFilter').on('change', function() {
            loadBills();
        });

        $('#clearFiltersButton').on('click', function() {
            $('#billSearch').val('');
            $('#billDateFilter').val('');
            loadBills();
        });
    }

    /* ═══════════════════════════════════════════════════════════════
       BIND CREATE EVENTS
       ═══════════════════════════════════════════════════════════════ */
    function bindCreateEvents() {
        $('#prescription_id').on('change', function() {
            loadPrescription($(this).val());
        });

        $(document).on('input', '.bill-quantity', function() {
            calculateTotal();
        });

        $('#pharmacyBillForm').on('submit', function(e) {
            e.preventDefault();
            saveBill();
        });
    }

    /* ═══════════════════════════════════════════════════════════════
       LOAD BILLS
       ═══════════════════════════════════════════════════════════════ */
    function loadBills() {
        $('#billsTableBody').html('<tr><td colspan="7" class="text-center">Loading pharmacy bills...</td></tr>');

        $.ajax({
            url: config.listUrl,
            type: 'GET',
            dataType: 'json',
            data: {
                search: $('#billSearch').val(),
                bill_date: $('#billDateFilter').val()
            },
            success: function(response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    renderBillRows(response.data);
                    return;
                }

                renderBillRows([]);
                showAlert('danger', response.message || 'Unable to load pharmacy bills.', '#pharmacyBillAlert');
            },
            error: function() {
                renderBillRows([]);
                showAlert('danger', 'Unable to load pharmacy bills.', '#pharmacyBillAlert');
            }
        });
    }

    /* ═══════════════════════════════════════════════════════════════
       RENDER BILL ROWS
       ═══════════════════════════════════════════════════════════════ */
    function renderBillRows(rows) {
        var html = '';

        if (!rows || rows.length === 0) {
            html = '<tr><td colspan="7" class="text-center">No pharmacy bills found.</td></tr>';
            $('#billsTableBody').html(html);
            return;
        }

        $.each(rows, function(index, bill) {
            html += '<tr>';
            html += '<td><strong>' + escapeHtml(bill.bill_code) + '</strong></td>';
            html += '<td>' + escapeHtml(bill.appointment_code) + '<br><small class="text-muted">Prescription: ' + escapeHtml(bill.prescription_date) + '</small></td>';
            html += '<td>' + escapeHtml(bill.patient_name) + '<br><small class="text-muted">' + escapeHtml(bill.patient_code) + '</small></td>';
            html += '<td>' + escapeHtml(bill.doctor_name) + '</td>';
            html += '<td>' + escapeHtml(bill.bill_date) + '</td>';
            html += '<td><span class="badge badge-info">' + escapeHtml(bill.item_count) + '</span></td>';
            html += '<td><strong>₹' + escapeHtml(parseFloat(bill.total_amount).toFixed(2)) + '</strong></td>';
            html += '</tr>';
        });

        $('#billsTableBody').html(html);
    }

    /* ═══════════════════════════════════════════════════════════════
       LOAD PRESCRIPTION
       ═══════════════════════════════════════════════════════════════ */
    function loadPrescription(prescriptionId) {
        resetBillingForm();

        if (!prescriptionId) {
            return;
        }

        $('#billItemsTableBody').html('<tr><td colspan="8" class="text-center">Loading prescription medicines...</td></tr>');

        $.ajax({
            url: config.prescriptionUrl + '/' + prescriptionId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    renderPrescriptionDetails(response.prescription);
                    renderItems(response.items);
                    return;
                }

                resetBillingForm();
                showAlert('danger', response.message || 'Unable to load prescription.', '#billingAlert');
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                console.error('Response:', xhr.responseText);
                resetBillingForm();
                showAlert('danger', 'Unable to load prescription. Please check console for details.', '#billingAlert');
            }
        });
    }

    /* ═══════════════════════════════════════════════════════════════
       RENDER PRESCRIPTION DETAILS
       ═══════════════════════════════════════════════════════════════ */
    function renderPrescriptionDetails(prescription) {
        var html = ''
            + '<div class="row">'
            + '<div class="col-md-4">'
            + '<strong>Patient:</strong> ' + escapeHtml(prescription.patient_name)
            + '<br><small class="text-muted">' + escapeHtml(prescription.patient_code) + ' | ' + escapeHtml(prescription.patient_phone) + '</small>'
            + '</div>'
            + '<div class="col-md-4">'
            + '<strong>Doctor:</strong> ' + escapeHtml(prescription.doctor_name)
            + '<br><small class="text-muted">Prescription Date: ' + escapeHtml(prescription.prescription_date) + '</small>'
            + '</div>'
            + '<div class="col-md-4">'
            + '<strong>Appointment:</strong> ' + escapeHtml(prescription.appointment_code)
            + '<br><small class="text-muted">' + escapeHtml(prescription.appointment_date + ' ' + prescription.appointment_time) + '</small>'
            + '</div>'
            + '</div>'
            + '<hr>'
            + '<strong>Diagnosis:</strong> ' + escapeHtml(prescription.diagnosis);

        $('#prescriptionDetailsContent').html(html);
        $('#prescriptionDetailsBox').removeClass('d-none');
    }

    /* ═══════════════════════════════════════════════════════════════
       RENDER ITEMS
       ═══════════════════════════════════════════════════════════════ */
    function renderItems(items) {
        var html = '';

        if (!items || items.length === 0) {
            html = '<tr><td colspan="8" class="text-center">No active medicines found for this prescription.</td></tr>';
            $('#billItemsTableBody').html(html);
            $('#saveBillButton').prop('disabled', true);
            calculateTotal();
            return;
        }

        $.each(items, function(index, item) {
            var suggestedQuantity = parseInt(item.suggested_quantity, 10);
            var availableStock = parseInt(item.stock_quantity, 10);
            var quantity = suggestedQuantity;

            if (quantity > availableStock) {
                quantity = availableStock;
            }

            if (quantity < 1) {
                quantity = 1;
            }

            html += '<tr>';
            html += '<td>';
            html += '<strong>' + escapeHtml(item.medicine_name) + '</strong>';
            html += '<br><small class="text-muted">' + escapeHtml(item.unit) + '</small>';
            html += '<input type="hidden" name="items[' + index + '][prescription_item_id]" value="' + escapeHtml(item.prescription_item_id) + '">';
            html += '</td>';
            html += '<td>' + escapeHtml(item.dosage) + '</td>';
            html += '<td>' + escapeHtml(item.days) + '</td>';
            html += '<td>' + escapeHtml(scheduleText(item)) + '</td>';
            html += '<td><span class="badge badge-info">' + escapeHtml(availableStock) + '</span></td>';
            html += '<td>';
            html += '<input type="number" min="1" step="1" class="form-control bill-quantity" name="items[' + index + '][quantity]" value="' + escapeHtml(quantity) + '" data-stock="' + escapeHtml(availableStock) + '" data-price="' + escapeHtml(item.unit_price) + '">';
            html += '</td>';
            html += '<td>₹' + money(item.unit_price) + '</td>';
            html += '<td class="line-total">₹' + money(quantity * parseFloat(item.unit_price)) + '</td>';
            html += '</tr>';
        });

        $('#billItemsTableBody').html(html);
        $('#saveBillButton').prop('disabled', false);
        calculateTotal();
    }

    /* ═══════════════════════════════════════════════════════════════
       SCHEDULE TEXT
       ═══════════════════════════════════════════════════════════════ */
    function scheduleText(item) {
        var parts = [];

        if (parseInt(item.morning, 10) === 1) {
            parts.push('Morning');
        }

        if (parseInt(item.afternoon, 10) === 1) {
            parts.push('Afternoon');
        }

        if (parseInt(item.night, 10) === 1) {
            parts.push('Night');
        }

        return parts.length ? parts.join(', ') : '-';
    }

    /* ═══════════════════════════════════════════════════════════════
       CALCULATE TOTAL
       ═══════════════════════════════════════════════════════════════ */
    function calculateTotal() {
        var total = 0;

        $('.bill-quantity').each(function() {
            var qty = parseInt($(this).val(), 10);
            var stock = parseInt($(this).data('stock'), 10);
            var price = parseFloat($(this).data('price'));

            if (isNaN(qty) || qty < 1) {
                qty = 1;
                $(this).val(qty);
            }

            if (qty > stock) {
                qty = stock;
                $(this).val(qty);
                showAlert('danger', 'Quantity cannot exceed available stock.', '#billingAlert');
            }

            var lineTotal = qty * price;

            $(this).closest('tr').find('.line-total').text('₹' + money(lineTotal));
            total += lineTotal;
        });

        $('#grandTotal').text('₹' + money(total));
    }

    /* ═══════════════════════════════════════════════════════════════
       RESET BILLING FORM
       ═══════════════════════════════════════════════════════════════ */
    function resetBillingForm() {
        $('#prescriptionDetailsBox').addClass('d-none');
        $('#prescriptionDetailsContent').html('');
        $('#billItemsTableBody').html('<tr><td colspan="8" class="text-center">Select a prescription to load medicines.</td></tr>');
        $('#grandTotal').text('₹0.00');
        $('#saveBillButton').prop('disabled', true);
    }

    /* ═══════════════════════════════════════════════════════════════
       SAVE BILL
       ═══════════════════════════════════════════════════════════════ */
    function saveBill() {
        var $button = $('#saveBillButton');
        var originalText = $button.html();

        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        // Clear any previous alerts
        clearAlert('#billingAlert');

        $.ajax({
            url: config.storeUrl,
            type: 'POST',
            dataType: 'json',
            data: $('#pharmacyBillForm').serialize(),
            success: function(response) {
                updateCsrfToken(response.csrfHash);

                if (response.status) {
                    showAlert('success', response.message, '#billingAlert');

                    setTimeout(function() {
                        if (response.redirectUrl) {
                            window.location.href = response.redirectUrl;
                        }
                    }, 1000);

                    return;
                }

                showAlert('danger', response.message || 'Unable to save pharmacy bill.', '#billingAlert');
                $button.prop('disabled', false).html(originalText);
            },
            error: function(xhr) {
                var response = {};
                try {
                    response = JSON.parse(xhr.responseText);
                } catch (err) {}

                showAlert('danger', response.message || 'Unable to save pharmacy bill.', '#billingAlert');
                $button.prop('disabled', false).html(originalText);
            }
        });
    }

    /* ═══════════════════════════════════════════════════════════════
       HELPER FUNCTIONS
       ═══════════════════════════════════════════════════════════════ */
    function updateCsrfToken(hash) {
        if (!hash) {
            return;
        }
        $('input[name="' + config.csrfName + '"]').val(hash);
    }

    function escapeHtml(value) {
        return $('<div>').text(value === null || value === undefined ? '' : value).html();
    }

    function money(value) {
        return parseFloat(value || 0).toFixed(2);
    }

    function showAlert(type, message, target) {
        if (alertTimeout) {
            clearTimeout(alertTimeout);
            alertTimeout = null;
        }

        var alertClass = type === 'success' ? 'success' : 'danger';
        var icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
        var html = ''
            + '<div class="alert alert-' + alertClass + ' alert-dismissible fade show" role="alert" style="position: relative; z-index: 1050;">'
            + '<i class="fas fa-' + icon + ' mr-2"></i>'
            + escapeHtml(message)
            + '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'
            + '<span aria-hidden="true">&times;</span>'
            + '</button>'
            + '</div>';

        $(target || '#billingAlert').html(html);

        alertTimeout = setTimeout(function() {
            clearAlert(target);
        }, 5000);
    }

    function clearAlert(target) {
        if (alertTimeout) {
            clearTimeout(alertTimeout);
            alertTimeout = null;
        }
        $(target || '#billingAlert').html('');
    }

    // Public API
    return {
        init: init,
        initCreate: initCreate,
        loadBills: loadBills,
        loadPrescription: loadPrescription,
        clearAlert: clearAlert,
        showAlert: showAlert
    };

})();