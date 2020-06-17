<?php

/**
 * Payment Gateway Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Payment Gateway
 * @author      Trioangle Product Team
 * @version     2.1
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;

class PaymentGatewayController extends Controller
{
    /**
     * Load View and Update Payment Gateway Data
     *
     * @return redirect to payment_gateway
     */
    public function index(Request $request)
    {
        if($request->isMethod('GET')) {
            return view('admin.payment_gateway');
        }

        // Payment Gateway Validation Rules
        $rules = array(
            'bt_mode'           => 'required',
            'bt_merchant_id'    => 'required',
            'bt_public_key'     => 'required',
            'bt_private_key'    => 'required',
        );

        // Payment Gateway Validation Custom Names
        $attributes = array(
            'bt_mode'        => 'Payment Mode',
            'bt_merchant_id' => 'Merchant ID',
            'bt_public_key'  => 'Public Key',
            'bt_private_key' => 'Private Key',
        );

        $this->validate($request, $rules, [], $attributes);

        PaymentGateway::where(['name' => 'mode', 'site' => 'Braintree'])->update(['value' => $request->bt_mode]);
        PaymentGateway::where(['name' => 'merchant_id', 'site' => 'Braintree'])->update(['value' => $request->bt_merchant_id]);
        PaymentGateway::where(['name' => 'public_key', 'site' => 'Braintree'])->update(['value' => $request->bt_public_key]);
        PaymentGateway::where(['name' => 'private_key', 'site' => 'Braintree'])->update(['value' => $request->bt_private_key]);

        flashMessage('success', 'Updated Successfully');
    
        return redirect('admin/payment_gateway');
    }
}