<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'auth';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['login'] = 'auth/index';
$route['auth/login'] = 'auth/login';
$route['logout'] = 'auth/logout';

$route['admin'] = 'admin/dashboard';
$route['doctor'] = 'doctor/doctor_dashboard';
$route['receptionist'] = 'receptionist/rec_dashboard';
$route['patient'] = 'patient/patient_dashboard';

$route['admin/departments'] = 'admin/departments/index';
$route['admin/departments/store'] = 'admin/departments/store';
$route['admin/departments/edit/(:num)'] = 'admin/departments/edit/$1';
$route['admin/departments/update/(:num)'] = 'admin/departments/update/$1';
$route['admin/departments/change-status/(:num)'] = 'admin/departments/change_status/$1';

$route['admin/users'] = 'admin/users/index';
$route['admin/users/ajax-list'] = 'admin/users/get_users_ajax';
$route['admin/users/store'] = 'admin/users/store';
$route['admin/users/edit/(:num)'] = 'admin/users/edit/$1';
$route['admin/users/update/(:num)'] = 'admin/users/update/$1';
$route['admin/users/change-status/(:num)'] = 'admin/users/change_status/$1';
