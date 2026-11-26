# Phase 5A.1 AJAX Foundation

## Files Created

- `assets/js/hms-ajax.js`
- `application/views/admin/patients/_table.php`
- `AJAX_PHASE_5A_FOUNDATION.md`

## Files Modified

- `application/config/routes.php`
- `application/controllers/admin/Patients.php`
- `application/views/admin/layouts/footer.php`
- `application/views/admin/patients/index.php`
- `application/views/admin/patients/create.php`
- `application/views/admin/patients/edit.php`

## Architecture Overview

`assets/js/hms-ajax.js` is the central jQuery AJAX layer for HMS. It provides:

- Global AJAX setup
- CSRF token handling
- Loader utilities
- Notification utilities for success, error, warning, and info messages
- Debounce helper for live searches
- AJAX form binding
- Table refresh helper for partial HTML responses
- Validation error rendering for forms

The admin patient module now uses this foundation without changing its business logic. Normal form POST and redirect behavior still works when JavaScript is unavailable.

## Patient Module Enhancements

- Patient create and edit forms submit through AJAX.
- Server-side validation errors are returned as JSON for AJAX requests.
- Patient search uses keyup live search with a 300ms debounce.
- Patient status changes happen through AJAX with confirmation.
- The patient table body is rendered from `application/views/admin/patients/_table.php`.
- `admin/patients/ajax-list` returns both JSON data and partial table HTML.

## Reuse Pattern For Future Modules

1. Add a table partial such as:

   `application/views/admin/example/_table.php`

2. Return partial HTML from the existing controller method when the request is AJAX:

   ```php
   return $this->json_response(array(
       'status' => TRUE,
       'html' => $this->load->view('admin/example/_table', $data, TRUE)
   ));
   ```

3. Add a notification container in the page:

   ```html
   <div id="hmsNotificationArea"></div>
   ```

4. Refresh a table from JavaScript:

   ```js
   HMSAjax.refreshTable(listUrl, { search: $('#search').val() }, '#tableBody');
   ```

5. Bind a form:

   ```js
   HMSAjax.bindAjaxForm('.js-ajax-form', {
       notificationTarget: '#hmsNotificationArea'
   });
   ```

## Safety Notes

- No database tables were created or modified.
- Authentication logic was not changed.
- Doctor and receptionist panel logic was not changed.
- Billing, lab, prescription, and pharmacy calculations were not changed.
- Existing non-AJAX behavior remains available as a fallback.

## Rollback

To roll back this phase:

1. Remove `assets/js/hms-ajax.js`.
2. Remove `application/views/admin/patients/_table.php`.
3. Restore the previous versions of:
   - `application/config/routes.php`
   - `application/controllers/admin/Patients.php`
   - `application/views/admin/layouts/footer.php`
   - `application/views/admin/patients/index.php`
   - `application/views/admin/patients/create.php`
   - `application/views/admin/patients/edit.php`
4. Clear browser cache and reload the admin patient page.
