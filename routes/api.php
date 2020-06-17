<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// cron request for schedule ride
Route::get('cron_request_car', 'RiderController@cron_request_car');
Route::get('update_referral_cron', 'RiderController@update_referral_cron');
Route::get('check_version', 'RiderController@check_version');

//TokenAuthController
Route::get('register', 'TokenAuthController@register');
Route::get('socialsignup', 'TokenAuthController@socialsignup');
Route::match(array('GET', 'POST'),'apple_callback', 'TokenAuthController@apple_callback');

Route::get('login', 'TokenAuthController@login');
Route::get('numbervalidation', 'TokenAuthController@numbervalidation');
Route::get('emailvalidation', 'TokenAuthController@emailvalidation');
Route::get('forgotpassword', 'TokenAuthController@forgotpassword');

Route::get('language_list', 'TokenAuthController@language_list');
Route::get('currency_list', 'TokenAuthController@currency_list');

// With Login Routes
Route::group(['middleware' => 'jwt.verify'], function () {

	Route::get('logout', 'TokenAuthController@logout');
	
	Route::get('language','TokenAuthController@language');
	Route::get('update_device', 'TokenAuthController@update_device');
	Route::get('updatelocation', 'DriverController@updatelocation');
	Route::get('check_status', 'DriverController@check_status');

	// Common API for Both Driver & Rider
	Route::get('country_list', 'DriverController@country_list');
	Route::get('toll_reasons', 'TripController@toll_reasons');
	Route::get('cancel_reasons', 'TripController@cancel_reasons');
	Route::get('get_referral_details', 'ReferralsController@get_referral_details');
	Route::get('get_trip_details', 'TripController@get_trip_details');
	Route::match(array('GET', 'POST'),'common_data','TokenAuthController@common_data');
	Route::get('send_message', 'TripController@send_message');

	// Rider Only APIs
	Route::get('get_nearest_vehicles', 'RiderController@get_nearest_vehicles');
	Route::get('search_cars', 'RiderController@search_cars');
	Route::get('request_cars', 'RiderController@request_cars');
	Route::get('track_driver', 'RiderController@track_driver');
	Route::get('updateriderlocation', 'RiderController@updateriderlocation');
	Route::get('promo_details','RiderController@promo_details');
	Route::get('sos','RiderController@sos');
	Route::get('sosalert','RiderController@sosalert');
	Route::get('save_schedule_ride', 'RiderController@save_schedule_ride');
	Route::get('schedule_ride_cancel', 'RiderController@schedule_ride_cancel');
	Route::post('add_wallet', 'EarningController@add_wallet');
	Route::post('after_payment', 'EarningController@after_payment');
	Route::get('get_past_trips','TripController@get_past_trips');
	Route::get('get_upcoming_trips','TripController@get_upcoming_trips');
	Route::post('currency_conversion', 'TokenAuthController@currency_conversion');

	// Driver Only APIs
	Route::get('get_pending_trips','TripController@get_pending_trips');
	Route::get('get_completed_trips','TripController@get_completed_trips');
	Route::get('arive_now', 'TripController@arive_now');
	Route::get('begin_trip', 'TripController@begin_trip');
	Route::get('accept_request', 'TripController@accept_trip');
	Route::get('cash_collected', 'DriverController@cash_collected');
	
	Route::match(array('GET', 'POST'), 'document_upload','ProfileController@document_upload');
	Route::match(array('GET', 'POST'), 'map_upload','TripController@map_upload');
	Route::match(array('GET', 'POST'), 'end_trip','TripController@end_trip');
	Route::match(array('GET', 'POST'), 'upload_profile_image','ProfileController@upload_profile_image');
	
	Route::get('heat_map', 'MapController@heat_map');
	Route::post('pay_to_admin', 'DriverController@pay_to_admin');
	Route::match(array('GET', 'POST'), 'driver_bank_details','DriverController@driver_bank_details');

	// TripController
	Route::get('cancel_trip', 'TripController@cancel_trip');
	
	// Earning Controller
	Route::get('earning_chart', 'EarningController@earning_chart');
	Route::get('add_payout', 'EarningController@add_payout');
	Route::get('add_promo_code', 'EarningController@add_promo_code');

	// Rating Controller
	Route::get('driver_rating', 'RatingController@driver_rating');
	Route::get('rider_feedback', 'RatingController@rider_feedback');
	Route::get('trip_rating', 'RatingController@trip_rating');
	Route::get('get_invoice', 'RatingController@getinvoice');

	//profile Controller
	Route::get('get_rider_profile', 'ProfileController@get_rider_profile');
	Route::get('update_rider_profile', 'ProfileController@update_rider_profile');
	Route::get('get_driver_profile', 'ProfileController@get_driver_profile');
	Route::get('update_driver_profile', 'ProfileController@update_driver_profile');
	Route::get('vehicle_details', 'ProfileController@vehicle_details');
	Route::get('update_rider_location', 'ProfileController@update_rider_location');
	Route::get('update_user_currency', 'ProfileController@update_user_currency');
	Route::get('get_caller_detail', 'ProfileController@get_caller_detail');

	// Manage Driver Payout Routes
	Route::get('earning_list', 'PayoutDetailController@earning_list');
	Route::get('weekly_trip', 'PayoutDetailController@weekly_trip');
	Route::get('weekly_statement', 'PayoutDetailController@weekly_statement');
	Route::get('daily_statement', 'PayoutDetailController@daily_statement');
});