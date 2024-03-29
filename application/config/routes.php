<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'pages';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['login'] = 'pages/login';
//user
$route['user'] = 'pages/view_user_list';
$route['user/add'] = 'pages/view_user_add';
$route['user/add/process']['post'] = 'pages/process_user_add';
$route['user/edit/(:num)'] = 'pages/view_user_edit/$1';
$route['user/edit/process']['post'] = 'pages/process_user_edit';
$route['user/delete/(:num)'] = 'pages/process_user_delete/$1';

// dashboard
$route['dashboard'] = 'pages/dashboard';
//organization
$route['org'] = 'pages/view_org_list';
$route['org/add'] = 'pages/view_org_add';
$route['org/add/process']['post'] = 'pages/process_org_add';
$route['org/edit/(:num)'] = 'pages/view_org_edit/$1';
$route['org/edit/process']['post'] = 'pages/process_org_edit';
$route['org/delete/(:num)'] = 'pages/process_org_delete/$1';
//period
$route['period'] = 'pages/view_period_list';
$route['period/add'] = 'pages/view_period_add';
$route['period/add/process']['post'] = 'pages/process_period_add';
$route['period/edit/(:num)'] = 'pages/view_period_edit/$1';
$route['period/edit/process']['post'] = 'pages/process_period_edit';
//draft
$route['draft'] = 'pages/view_draft_list';
$route['draft/add'] = 'pages/view_draft_add';
$route['draft/add/process']['post'] = 'pages/process_draft_add';
$route['draft/edit/(:num)'] = 'pages/view_draft_edit/$1';
$route['draft/edit/process']['post'] = 'pages/process_draft_edit';
$route['draft/rfa/(:num)'] = 'pages/process_draft_rfa/$1';

$route['draft-approval'] = 'pages/view_draft_approval_list';
$route['draft-approval/edit/(:num)'] = 'pages/view_draft_approval_edit/$1';
$route['draft-approval/edit/process']['post'] = 'pages/process_draft_approval_edit';
$route['draft-approval/cancel/(:num)'] = 'pages/cancel_draft_approval/$1';

$route['indicator/edit/(:num)'] = 'pages/view_indicator_edit/$1';
$route['indicator/edit/process']['post'] = 'pages/process_indicator_edit';
$route['indicator/delete/(:num)'] = 'pages/process_indicator_delete/$1';
//indicator
$route['indicator'] = 'pages/view_indicator_list';
