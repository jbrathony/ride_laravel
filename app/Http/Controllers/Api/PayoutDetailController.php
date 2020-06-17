<?php

/**
 * Payout Detail Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Payout Detail
 * @author      Trioangle Product Team
 * @version     2.1
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Routing\Controller as BaseController;
use App\Models\User;
use App\Models\Trips;
use App\Models\Currency;
use App\Models\Payment;
use JWTAuth;
use DB;
use App\Models\CurrencyConversion;
use Validator;

class PayoutDetailController extends BaseController
{
    use CurrencyConversion;
	
	/**
	* View Over All Payout Details of driver
	*
    * @return payout data json
	*/

    public function earning_list(Request $request)
    {
        $user_details = JWTAuth::parseToken()->authenticate();

        $rules = [
            'type' => 'required|in:week,weekly,date',
            'start_date' => 'required|date|date_format:Y-m-d',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first(),
            ]);
        }

        $start_of_the_week = 0;

        if ($request->type == 'week') {
            $start_date = strtotime($request->start_date);
            $end_date = strtotime("+6 day", $start_date);
            $week_start_date = date('Y-m-d' . ' 00:00:00', $start_date);
            $week_end_date = date('Y-m-d' . ' 23:59:59', $end_date);

            $trips = Trips::where('driver_id',$user_details->id)
                ->whereHas('payment',function($q){
                    // $q->where('driver_payout_status','Pending');
                })
                ->where('status','Completed')
                ->where('payment_mode','<>','Cash')
                ->whereBetween('created_at', [$week_start_date, $week_end_date])
                ->select('*',DB::raw('DATE_FORMAT(created_at,"%Y-%m-%d") as created_at_date'))
                ->orderBy('id')
                ->get();

            $trips_grouping = $trips->groupBy('created_at_date');
            $date_list = array();

            $current_date = strtotime($week_start_date);
            while ($current_date <= strtotime("+6 days", strtotime($week_start_date))) {
                $date = date('d-m-Y', $current_date);
                if ($trips_grouping->has(date('Y-m-d',$current_date))){
                    $trip = $trips_grouping[date('Y-m-d',$current_date)];
                    
                    $order_data = [
                        "total_fare" => number_format($trip->sum('driver_or_company_earning'),2),
                        "day" => date('l', strtotime($date)),
                        "date" => $date,
                    ];
                }else{
                    $order_data = [
                        "total_fare" => "0",
                        "day" => date('l', strtotime($date)),
                        "date" => $date,
                    ];
                }

                $date_list[] = $order_data;
                $current_date = strtotime("+1 day", $current_date);
            }

            $last_trip_total_fare = @$trips->last()->driver_or_company_earning;

            $earning_list = [
                'total_fare' => number_format($trips->sum('driver_or_company_earning'),2),
                'date_list' => $date_list,
                'last_trip_total_fare' => $last_trip_total_fare,
                'last_payout' => '0',
            ];
        }

        $to_currency = $this->getSessionOrDefaultCode();
        $to_currency = Currency::whereCode($to_currency)->first();
        $symbol = html_entity_decode($to_currency->symbol);
        $earning_list['currency_code'] = $to_currency->code;
        $earning_list['currency_symbol'] = $symbol;

        return response()->json([
            'status_code' => '1',
            'status_message' => 'Earning list listed successfully',
            'earning_list' => $earning_list,
        ]);
    }

	/**
	* View Weekly Payout Details of Driver
	*
    * @return payout data json
	*/
	public function weekly_trip()
	{
    	$data['filter'] = 'Weekly';
    	$user_details = JWTAuth::parseToken()->authenticate();
    	$data['driver_id'] = $user_details->id;

        $to_currency = $this->getSessionOrDefaultCode();
        $to_currency = Currency::whereCode($to_currency)->first();
        $symbol = html_entity_decode($to_currency->symbol);

        $trips = DB::table('trips')
                ->where('trips.driver_id',$user_details->id)
                ->join('users', 'users.id', '=', 'trips.driver_id')
                ->join('payment', 'payment.trip_id', '=', 'trips.id')
                ->where('trips.status','Completed')
                ->groupBy(DB::raw('WEEK(trips.created_at,1)'))
                ->select(
                    DB::raw("GROUP_CONCAT(trips.id) as trip_ids"),
                    DB::raw('DATE(DATE_FORMAT(trips.created_at,"%Y-%m-%d") + INTERVAL ( - WEEKDAY(trips.created_at)) DAY) as date'),
                    DB::raw('DATE_FORMAT(trips.created_at,"%Y") as year')
                )
                ->orderBy('date','DESC')
                ->get();

        foreach ($trips as $trip) {
            //total amount
            $total_rides = Trips::whereIn('id',explode(",",$trip->trip_ids))->get();
            $trip->driver_earnings = number_format($total_rides->sum('company_driver_earnings'),2);

            //week date
            $start_date = strtotime($trip->date);
            $end_date   = strtotime("+6 day", $start_date);
            $data['from_date']  = date('d M', $start_date);
            $data['to_date']    = date('d M', $end_date);
            $trip->week         = $data['from_date'].' - '.$data['to_date'];

            unset($trip->trip_ids);
        }

        return response()->json([
            'trip_week_details' => $trips,
            'status_message' => __('messages.api.listed_successfully'),
            'status_code' => '1',
            'currency_code'=> $to_currency->code,
            'symbol'=> $symbol,
        ]);
	}

	/**
	* View Week Day Payout Details of Driver
	*
    * @return payout data json
	*/
	public function weekly_statement(Request $request)
	{
        $start_date = strtotime($request->date);
        $end_date = strtotime("+6 day", $start_date);
		$from_date = date('Y-m-d' . ' 00:00:00', $start_date);
		$to_date = date('Y-m-d' . ' 23:59:59', $end_date);
		$user_details = JWTAuth::parseToken()->authenticate();

        $to_currency = $user_details->currency_code;
        $to_currency = Currency::whereCode($to_currency)->first();
        $symbol = html_entity_decode($to_currency->symbol);

        $common = Trips::where('driver_id',$user_details->id)
                ->whereHas('payment')
                ->where('status','Completed')
                ->whereBetween('created_at', [$from_date, $to_date])
                ->select('*',DB::raw('DATE_FORMAT(created_at,"%Y-%m-%d") as created_at_date'))
                ->orderBy('created_at_date','DESC');

        $bank_deposits = Trips::where('payment_mode','<>','Cash')
            ->where('payment_status','Completed')
            ->where('driver_id',$user_details->id)
            ->whereHas('payment',function($q) use ($from_date, $to_date){
                $q->where('driver_payout_status','Pending')->whereBetween('updated_at', [$from_date, $to_date]);
            })
            ->get()
            ->sum('driver_payout');

        $trips = (clone $common)->get();

        $cash = (clone $common)->CashTripsOnly()->get();

        $trips_grouping = $trips->groupBy('created_at_date');

        $statement = $trips_grouping->map(function($trip,$date) use ($symbol) {
            return [
                'driver_earning' => $symbol.number_format($trip->sum('company_driver_earnings'),2),
                'day'            => date('l',strtotime($date)),
                'format'         => date('d/m',strtotime($date)),
                'date'           => $date,
            ];
        })->values();

        $header = array(
            "key" => date('d M', $start_date).' - '.date('d M', $end_date),
            "value" => $symbol.number_format($trips->sum('company_driver_earnings'),2),
            'colour' => 'green'
        );
        $title = __('messages.api.trip_earning');

        $total_fare = array(
            'key'   => __('messages.api.total_fare'),
            'value' => $symbol.number_format($trips->sum('company_driver_earnings') + $trips->sum('driver_or_company_commission'),2),
            'tooltip' => __('messages.api.total_fare_tooltip'),
        );
        $content[] = formatStatementItem($total_fare);

        $access_fee = array(
            "key" => __('messages.access_fee'),
            "value" => '-'.$symbol.number_format($trips->sum('driver_or_company_commission'),2),
        );
        $content[] = formatStatementItem($access_fee);

        $driver_earning = array(
            "key" => __('messages.api.driver_earnings'),
            "value" => $symbol.number_format($trips->sum('company_driver_earnings'),2),
            "bar"   => true,
            "colour"   => 'black',
        );
        $content[] = formatStatementItem($driver_earning);

        $bank_deposits = array(
            'key'   => __('messages.api.admin_remaining_amount'),
            'value' => $symbol.number_format($bank_deposits,2),
        );
        $content[] = formatStatementItem($bank_deposits);

        $footer = array(
            array(
                "key" => __('messages.driver_dashboard.completed_trips'),
                "value" => $trips->where('status','Completed')->count(),
            ),
        );

        $driver_statement = array(
            'header'    => $header,
            'title'     => $title,
            'content'   => $content,
            'footer'    => $footer,
        );

        return response()->json([
            'status_code'       => '1',
            'status_message'    => __('messages.api.listed_successfully'),
            'driver_statement'  => $driver_statement,
            'statement'         => $statement,
        ]);
	}

	/**
	* View Daily Payout Details of Driver
	*
    * @return payout data json
	*/
	public function daily_statement(Request $request)
	{
		$date = $request->date;
        $from_date = date('Y-m-d' . ' 00:00:00', strtotime($date));
        $to_date = date('Y-m-d' . ' 23:59:59', strtotime($date));
		$user_details = JWTAuth::parseToken()->authenticate();

        $to_currency = $this->getSessionOrDefaultCode();
        $to_currency = Currency::whereCode($to_currency)->first();
        $symbol = html_entity_decode($to_currency->symbol);
        
        $common = Trips::with('ride_request')->where('driver_id',$user_details->id)
                ->whereHas('payment',function($q){
                    // $q->where('driver_payout_status','Pending');
                })
                ->where('status','Completed')
                ->whereBetween('created_at', [$from_date, $to_date])
                ->orderBy('id','DESC');
        $trips = (clone $common)->get();

        $cash = (clone $common)->CashTripsOnly()->get();
        $timezone = $request->timezone ?? 'UTC';

        $bank_deposits = Trips::where('payment_mode','<>','Cash')
                        ->where('driver_id',$user_details->id)
                        ->where('payment_status','Completed')
                        ->whereHas('payment',function($q) use ($from_date, $to_date) {
                            $q->where('driver_payout_status','Pending');
                        })
                        ->whereBetween('created_at', [$from_date, $to_date])
                        ->get()
                        ->sum('driver_payout');

        $header = array(
            "key" => date('l', strtotime($date)).' - '.date('d/m', strtotime($date)),
            "value" => $symbol.number_format($trips->sum('company_driver_earnings'),2),
        );
        $title = __('messages.api.trip_earning');

        $total_fare = array(
            'key'   => __('messages.api.total_fare'),
            'value' => $symbol.number_format($trips->sum('company_driver_earnings') + $trips->sum('driver_or_company_commission'),2),
            'tooltip' => __('messages.api.total_fare_tooltip'),
        );
        $content[] = formatStatementItem($total_fare);

        $access_fee = array(
            "key" => __('messages.access_fee'),
            "value" => '-'.$symbol.number_format($trips->sum('driver_or_company_commission'),2),
        );
        $content[] = formatStatementItem($access_fee);

        $driver_earning = array(
            "key" => __('messages.api.driver_earnings'),
            "value" => $symbol.number_format($trips->sum('company_driver_earnings'),2),
            "bar"   => true,
            'colour' => 'black',
        );
        $content[] = formatStatementItem($driver_earning);

        $bank_deposits = array(
            'key'   => __('messages.api.admin_remaining_amount'),
            'value' => $symbol.number_format($bank_deposits,2),
        );
        $content[] = formatStatementItem($bank_deposits);

        $footer = array(
            array(
                "key" => __('messages.driver_dashboard.completed_trips'),
                "value" => $trips->where('status','Completed')->count(),
            ),
        );

        $driver_statement = array(
            'header'    => $header,
            'title'     => $title,
            'content'   => $content,
            'footer'    => $footer,
        );

		$statement = $trips->map(function($trip) use ($timezone,$symbol) {
            
            if(isset($trip->ride_request->schedule_ride)) {
                $trip_time = $trip->created_at->format('g:i A');
            }
            else {
                $trip_time = $trip->created_at->setTimezone($timezone)->format('g:i A');
            }
            
            return [
                'id' => $trip->id,
                'driver_earning' => $symbol.number_format($trip->company_driver_earnings,2),
                'time' => $trip_time,
            ];
        });
		
        return response()->json([
            'status_code' => '1',
            'status_message' => "successfully",
            'driver_statement'  => $driver_statement,
            'daily_statement' => $statement,
        ]);
	}
}