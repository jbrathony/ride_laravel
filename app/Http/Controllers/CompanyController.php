<?php

namespace App\Http\Controllers;

use App\Http\Controllers\EmailController;
use App\Models\CompanyBankDetail;
use App\Models\PaymentGateway;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App;
use Session;
use Validator;

class CompanyController extends Controller
{
	public function __construct()
    {
        $this->helper = resolve('App\Http\Start\Helpers');
        $this->request_helper = resolve('App\Http\Helper\RequestHelper');
	}

	/**
     * Add a Payout Method and Load Payout Preferences File
     *
     * @param array $request Input values
     * @return redirect to Payout Preferences page and load payout preferences view file
     */
    public function payout_preferences(Request $request)
    {
        $company_id = auth('company')->id();
        if($request->isMethod('GET')) {
            $bank_details = CompanyBankDetail::firstOrNew(['company_id' => $company_id]);
            return view('company_payout', compact('bank_details'));
        }

        $rules = array(
            'holder_name'       => 'required',
            'account_number'    => 'required',
            'bank_name'         => 'required',
            'bank_location'     => 'required',
            'bank_code'         => 'required',
        );

        $messages   = array('required'=> ':attribute '.trans('messages.home.field_is_required'));

        $attributes = array(
            'holder_name'       => __('messages.account.holder_name'),
            'account_number'    => __('messages.account.account_number'),
            'bank_name'         => __('messages.account.bank_name'),
            'bank_location'     => __('messages.account.bank_location'),
            'bank_code'         => __('messages.account.bank_code'),
        );

        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        if ($validator->fails()) {
            // Form calling with Errors and Input values and error_code 1 for show Payout preference popup
            return back()->withErrors($validator)->withInput()->with('error_code', 1);
        }
        
        $bank_detail = CompanyBankDetail::firstOrNew(['company_id' => $company_id]);

        $bank_detail->company_id = $company_id;
        $bank_detail->holder_name = $request->holder_name;
        $bank_detail->account_number = $request->account_number;
        $bank_detail->bank_name = $request->bank_name;
        $bank_detail->bank_location = $request->bank_location;
        $bank_detail->code = $request->bank_code;
        $bank_detail->save();

        flashMessage('success', trans('messages.account.payout_updated'));
        return redirect()->route('company_payout_preference');
    }
}