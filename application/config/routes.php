<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Debug route - ADD THIS AT THE TOP OF YOUR ROUTES FILE
$route['admin/doctors/update/(:num)'] = 'admin/admin_doctors/update/$1';
$route['admin/doctors/edit/(:num)'] = 'admin/admin_doctors/edit/$1';

$route['default_controller'] = 'auth';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

$route['login'] = 'auth/index';
$route['auth/login'] = 'auth/login';
$route['logout'] = 'auth/logout';

/*
|--------------------------------------------------------------------------
| Role Dashboards
|--------------------------------------------------------------------------
*/

$route['admin'] = 'admin/dashboard';
$route['doctor'] = 'doctor/dashboard';
$route['receptionist'] = 'receptionist/dashboard';
$route['patient'] = 'patient/patient_dashboard';
$route['patient/list'] = 'patient/patients/index';
$route['patient/ajax/list'] = 'patient/patients/ajax_list';
$route['patient/ajax/add'] = 'patient/patients/ajax_add';
$route['patient/ajax/edit/(:num)'] = 'patient/patients/ajax_edit/$1';
$route['patient/ajax/toggle/(:num)'] = 'patient/patients/ajax_toggle_status/$1';
$route['patient/ajax/delete/(:num)'] = 'patient/patients/ajax_delete/$1';
/*
|--------------------------------------------------------------------------
| Admin Patients
|--------------------------------------------------------------------------
*/

$route['admin/patients'] = 'admin/patients/index';
$route['admin/patients/get_patients_ajax'] = 'admin/patients/get_patients_ajax';
$route['admin/patients/create'] = 'admin/patients/create';
$route['admin/patients/store'] = 'admin/patients/store';
$route['admin/patients/edit/(:num)'] = 'admin/patients/edit/$1';
$route['admin/patients/update/(:num)'] = 'admin/patients/update/$1';
$route['admin/patients/change_status/(:num)'] = 'admin/patients/change_status/$1';


/*
|--------------------------------------------------------------------------
| Admin Doctors (FIXED SECTION)
|--------------------------------------------------------------------------
*/

// Direct URLs for admin_doctors controller - ADD THESE
$route['admin/admin_doctors'] = 'admin/admin_doctors/index';
$route['admin/admin_doctors/get_doctors_ajax'] = 'admin/admin_doctors/get_doctors_ajax';
$route['admin/admin_doctors/get_setup_options'] = 'admin/admin_doctors/get_setup_options';
$route['admin/admin_doctors/store'] = 'admin/admin_doctors/store';
$route['admin/admin_doctors/edit/(:num)'] = 'admin/admin_doctors/edit/$1';
$route['admin/admin_doctors/update/(:num)'] = 'admin/admin_doctors/update/$1';
$route['admin/admin_doctors/change_status/(:num)'] = 'admin/admin_doctors/change_status/$1';

// Shorter URLs for convenience (these already exist)
$route['admin/doctors'] = 'admin/admin_doctors/index';
$route['admin/doctors/get_doctors_ajax'] = 'admin/admin_doctors/get_doctors_ajax';
$route['admin/doctors/get_setup_options'] = 'admin/admin_doctors/get_setup_options';
$route['admin/doctors/store'] = 'admin/admin_doctors/store';
$route['admin/doctors/edit/(:num)'] = 'admin/admin_doctors/edit/$1';
$route['admin/doctors/update/(:num)'] = 'admin/admin_doctors/update/$1';
$route['admin/doctors/change_status/(:num)'] = 'admin/admin_doctors/change_status/$1';


/*
|--------------------------------------------------------------------------
| Admin Appointments
|--------------------------------------------------------------------------
*/
$route['admin/appointments'] = 'admin/appointments/index';
$route['admin/appointments/ajax-list'] = 'admin/appointments/get_appointments_ajax';
$route['admin/appointments/create'] = 'admin/appointments/create';
$route['admin/appointments/store'] = 'admin/appointments/store';
$route['admin/appointments/edit/(:num)'] = 'admin/appointments/edit/$1';
$route['admin/appointments/update/(:num)'] = 'admin/appointments/update/$1';
$route['admin/appointments/change-status/(:num)'] = 'admin/appointments/change_status/$1';
/*
|--------------------------------------------------------------------------
| Admin Lab
|--------------------------------------------------------------------------
*/

$route['admin/lab-tests'] = 'admin/admin_lab/tests';
$route['admin/lab-tests/ajax-list'] = 'admin/admin_lab/get_tests_ajax';
$route['admin/lab-tests/store'] = 'admin/admin_lab/store_test';
$route['admin/lab-tests/edit/(:num)'] = 'admin/admin_lab/edit_test/$1';
$route['admin/lab-tests/update/(:num)'] = 'admin/admin_lab/update_test/$1';
$route['admin/lab-tests/change-status/(:num)'] = 'admin/admin_lab/change_test_status/$1';

$route['admin/lab-requests'] = 'admin/admin_lab/requests';
$route['admin/lab-requests/ajax-list'] = 'admin/admin_lab/get_requests_ajax';
$route['admin/lab-requests/create'] = 'admin/admin_lab/create_request';
$route['admin/lab-requests/store'] = 'admin/admin_lab/store_request';
$route['admin/lab-requests/appointment/(:num)'] = 'admin/admin_lab/get_appointment_ajax/$1';
$route['admin/lab-requests/change-status/(:num)'] = 'admin/admin_lab/change_request_status/$1';
$route['admin/lab-requests/results/(:num)'] = 'admin/admin_lab/results/$1';
$route['admin/lab-requests/save-results/(:num)'] = 'admin/admin_lab/save_results/$1';

/*
|--------------------------------------------------------------------------
| Admin Medicines (FIXED)
|--------------------------------------------------------------------------
*/

$route['admin/medicines'] = 'admin/admin_medicines/index';
$route['admin/medicines/ajax-list'] = 'admin/admin_medicines/get_medicines_ajax';
$route['admin/medicines/store'] = 'admin/admin_medicines/store';
$route['admin/medicines/edit/(:num)'] = 'admin/admin_medicines/edit/$1';
$route['admin/medicines/update/(:num)'] = 'admin/admin_medicines/update/$1';
$route['admin/medicines/change-status/(:num)'] = 'admin/admin_medicines/change_status/$1';

/*
|--------------------------------------------------------------------------
| Prescriptions
|--------------------------------------------------------------------------
*/

$route['admin/prescriptions'] = 'admin/admin_prescriptions/index';
$route['admin/prescriptions/ajax-list'] = 'admin/admin_prescriptions/get_prescriptions_ajax';
$route['admin/prescriptions/create'] = 'admin/admin_prescriptions/create';
$route['admin/prescriptions/store'] = 'admin/admin_prescriptions/store';


/*
|--------------------------------------------------------------------------
| Admin Pharmacy Billing (FIXED)
|--------------------------------------------------------------------------
*/
$route['admin/pharmacy-billing'] = 'admin/admin_pharmacy/index';
$route['admin/pharmacy-billing/ajax-list'] = 'admin/admin_pharmacy/get_bills_ajax';
$route['admin/pharmacy-billing/create'] = 'admin/admin_pharmacy/create';
$route['admin/pharmacy-billing/create/(:num)'] = 'admin/admin_pharmacy/create/$1';
$route['admin/pharmacy-billing/prescription/(:num)'] = 'admin/admin_pharmacy/get_prescription_ajax/$1';
$route['admin/pharmacy-billing/store'] = 'admin/admin_pharmacy/store';


/*
|--------------------------------------------------------------------------
| Billing
|--------------------------------------------------------------------------
*/

$route['admin/billing'] = 'admin/admin_billing/index';
$route['admin/billing/ajax-list'] = 'admin/admin_billing/get_bills_ajax';
$route['admin/billing/create'] = 'admin/admin_billing/create';
$route['admin/billing/create/(:num)'] = 'admin/admin_billing/create/$1';
$route['admin/billing/preview/(:num)'] = 'admin/admin_billing/preview/$1';
$route['admin/billing/store'] = 'admin/admin_billing/store';
$route['admin/billing/view/(:num)'] = 'admin/admin_billing/view/$1';
$route['admin/billing/pdf/(:num)'] = 'admin/admin_billing/pdf/$1';

/*
|--------------------------------------------------------------------------
| Reports
|--------------------------------------------------------------------------
*/

$route['admin/reports'] = 'admin/admin_reports/index';
$route['admin/reports/revenue-analytics'] = 'admin/admin_reports/revenue_analytics';
$route['admin/reports/appointment-analytics'] = 'admin/admin_reports/appointment_analytics';
$route['admin/reports/patient-analytics'] = 'admin/admin_reports/patient_analytics';
$route['admin/reports/doctor-analytics'] = 'admin/admin_reports/doctor_analytics';
$route['admin/reports/lab-analytics'] = 'admin/admin_reports/lab_analytics';

/*
|--------------------------------------------------------------------------
| Doctor Panel
|--------------------------------------------------------------------------
*/

$route['doctor/dashboard'] = 'doctor/dashboard';
$route['doctor/dashboard/stats'] = 'doctor/dashboard/stats';

$route['doctor/appointments'] = 'doctor/appointments';
$route['doctor/appointments/ajax-list'] = 'doctor/appointments/ajax_list';
$route['doctor/appointments/view/(:num)'] = 'doctor/appointments/view/$1';

$route['doctor/patients'] = 'doctor/patients';
$route['doctor/patients/ajax-list'] = 'doctor/patients/ajax_list';
$route['doctor/patients/view/(:num)'] = 'doctor/patients/view/$1';

$route['doctor/prescriptions/create/(:num)'] = 'doctor/prescriptions/create/$1';
$route['doctor/prescriptions/store'] = 'doctor/prescriptions/store';

$route['doctor/lab-requests/create/(:num)'] = 'doctor/lab_requests/create/$1';
$route['doctor/lab-requests/store'] = 'doctor/lab_requests/store';

/*
|--------------------------------------------------------------------------
| Receptionist Panel
|--------------------------------------------------------------------------
*/

$route['receptionist/dashboard'] = 'receptionist/dashboard';

$route['receptionist/patients'] = 'receptionist/patients';
$route['receptionist/patients/ajax-list'] = 'receptionist/patients/ajax_list';
$route['receptionist/patients/create'] = 'receptionist/patients/create';
$route['receptionist/patients/store'] = 'receptionist/patients/store';
$route['receptionist/patients/edit/(:num)'] = 'receptionist/patients/edit/$1';
$route['receptionist/patients/update/(:num)'] = 'receptionist/patients/update/$1';
$route['receptionist/patients/view/(:num)'] = 'receptionist/patients/view/$1';

$route['receptionist/appointments'] = 'receptionist/appointments';
$route['receptionist/appointments/ajax-list'] = 'receptionist/appointments/ajax_list';
$route['receptionist/appointments/create'] = 'receptionist/appointments/create';
$route['receptionist/appointments/store'] = 'receptionist/appointments/store';
$route['receptionist/appointments/edit/(:num)'] = 'receptionist/appointments/edit/$1';
$route['receptionist/appointments/update/(:num)'] = 'receptionist/appointments/update/$1';
$route['receptionist/appointments/cancel/(:num)'] = 'receptionist/appointments/cancel/$1';
$route['receptionist/appointments/doctors'] = 'receptionist/appointments/doctors';
$route['receptionist/appointments/slots'] = 'receptionist/appointments/slots';

$route['receptionist/billing'] = 'receptionist/billing';
$route['receptionist/billing/ajax-list'] = 'receptionist/billing/ajax_list';
$route['receptionist/billing/create'] = 'receptionist/billing/create';
$route['receptionist/billing/preview/(:num)'] = 'receptionist/billing/preview/$1';
$route['receptionist/billing/store'] = 'receptionist/billing/store';
$route['receptionist/billing/view/(:num)'] = 'receptionist/billing/view/$1';
$route['receptionist/billing/mark-payment/(:num)'] = 'receptionist/billing/mark_payment/$1';

$route['receptionist/pharmacy-billing'] = 'receptionist/pharmacy_billing';
$route['receptionist/pharmacy-billing/ajax-list'] = 'receptionist/pharmacy_billing/ajax_list';
$route['receptionist/pharmacy-billing/create'] = 'receptionist/pharmacy_billing/create';
$route['receptionist/pharmacy-billing/prescription/(:num)'] = 'receptionist/pharmacy_billing/prescription/$1';
$route['receptionist/pharmacy-billing/store'] = 'receptionist/pharmacy_billing/store';
$route['receptionist/pharmacy-billing/view/(:num)'] = 'receptionist/pharmacy_billing/view/$1';