<?php

/**
 * Vehicle Type Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Vehicle Type
 * @author      Trioangle Product Team
 * @version     2.1
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\CarTypeDataTable;
use App\Models\CarType;
use App\Models\Currency;
use App\Models\DriverDocuments;
use App\Models\DriverLocation;
use App\Models\Vehicle;
use App\Http\Start\Helpers;
use Validator;

class VehicleTypeController extends Controller
{
    /**
     * Load Datatable for vehicle Type
     *
     * @param array $dataTable Instance of CarTypeDataTable
     * @return datatable
     */
    public function index(CarTypeDataTable $dataTable)
    {
        return $dataTable->render('admin.vehicle_type.view');
    }

    /**
     * Add a New vehicle Type
     *
     * @param array $request  Input values
     * @return redirect     to vehicle Type view
     */
    public function add(Request $request)
    {
        if($request->isMethod('GET')) {
            $data['currency']   = Currency::codeSelect();
            return view('admin.vehicle_type.add', $data);
        }
        else if($request->submit) {
            // add vehicle Type Validation Rules
            $rules = array(
                'car_name'      => 'required|unique:car_type,car_name,'.$request->id,
                'status'   => 'required',
                'vehicle_image'   => 'required|mimes:jpg,jpeg,png,gif',                
                'active_image'        => 'required|mimes:jpg,jpeg,png,gif'
            );

            // add vehicle Type Validation Custom Fields Name
            $attributes = array(
                'car_name'      => 'Name',                      
                'status'        => 'Status',
                'active_image'  =>'Active image',
                'vehicle_image'  =>'Vehicle image',
            );

            $validator = Validator::make($request->all(), $rules, [], $attributes);
            $validator->after(function ($validator) use($request) {
                $active_car = CarType::where('status','active')->count();
                if($active_car<=0 && $request->status=='Inactive') {
                   $validator->errors()->add('status',"Atleast one vehicle type should be in active status");
                }
            });

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            $icon = $request->file('active_image');
            $icon2 = $request->file('vehicle_image');

            $vehicle_type = new  CarType;
            $vehicle_type->car_name     = $request->car_name;
            $vehicle_type->description  = $request->description;

            $vehicle_type->status       = $request->status; 

            //Active Image
            if($icon) { 
                $icon_extension      =   $icon->getClientOriginalExtension();
                $icon_filename       =   'active_image' . time() . '.' . $icon_extension;

                $success = $icon->move('images/car_image/', $icon_filename);

                if(!$success) {
                    return back()->withError('Could not upload icon Image');
                }
                $vehicle_type->active_image = $icon_filename;
            }

            if($icon2) { 
                $icon2_extension      =   $icon2->getClientOriginalExtension();
                $icon2_filename       =   'vehicle_image' . time() . '.' . $icon2_extension;

                $success = $icon2->move('images/car_image/', $icon2_filename);

                if(!$success) {
                    return back()->withError('Could not upload icon2 Image');
                }
                $vehicle_type->vehicle_image     = $icon2_filename;
            }

            $vehicle_type->save();

            flashMessage('success', 'Added Successfully');
            return redirect('admin/vehicle_type');
        }
        return redirect('admin/vehicle_type');
    }

    /**
     * Update vehicle Type Details
     *
     * @param array $request    Input values
     * @return redirect     to vehicle Type View
     */
    public function update(Request $request)
    {
        if($request->isMethod('GET')) {
            $data['result'] = CarType::find($request->id);
            if($data['result']) {
                $data['currency']   = Currency::codeSelect();
                return view('admin.vehicle_type.edit', $data);  
            }
            flashMessage('danger', 'Invalid ID');
            return redirect('admin/vehicle_type');            
        }
        else if($request->submit)
        {
            // Edit vehicle Type Validation Rules
            $rules = array(
                'car_name'      => 'required|unique:car_type,car_name,'.$request->id,
                'status'        => 'required',
                'active_image'   => 'mimes:jpg,jpeg,png,gif',
                'vehicle_image'   => 'mimes:jpg,jpeg,png,gif',    
            );

            // add vehicle Type Validation Custom Fields Name
            $attributes = array(
                'car_name'      => 'Name',
                'status'        => 'Status',                       
            );

            $validator = Validator::make($request->all(), $rules,[], $attributes);
            $validator->after(function ($validator) use($request) {
                $active_car = CarType::where('status','active')->where('id','!=',$request->id)->count();
                if($active_car<=0 && $request->status=='Inactive') {
                   $validator->errors()->add('status',"Atleast one vehicle type should be in active status");
                }
            });
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
            }

            $icon = $request->file('active_image');
            $icon2 = $request->file('vehicle_image');

            $vehicle_type = CarType::find($request->id);              
            $vehicle_type->car_name     = $request->car_name;
            $vehicle_type->description  = $request->description;                
            $vehicle_type->status       = $request->status; 

            //Active Image
            if($icon) { 
                $icon_extension      =   $icon->getClientOriginalExtension();
                $icon_filename       =   'active_image' . time() . '.' . $icon_extension;

                $success = $icon->move('images/car_image/', $icon_filename);

                if(!$success) {
                    return back()->withError('Could not upload icon Image');
                }
                $vehicle_type->active_image     = $icon_filename;
            }     
            
            //Inctive Image
            if($icon2) {
                $icon2_extension      =   $icon2->getClientOriginalExtension();
                $icon2_filename       =   'vehicle_image' . time() . '.' . $icon2_extension;

                $success = $icon2->move('images/car_image/', $icon2_filename);

                if(!$success) {
                    return back()->withError('Could not upload icon2 Image');
                }
                $vehicle_type->vehicle_image     = $icon2_filename;
            } 

            $vehicle_type->save(); 

            flashMessage('success', 'Updated Successfully'); // Call flash message function

            return redirect('admin/vehicle_type');
        }
        return redirect('admin/vehicle_type');
    }

    /**
     * Delete vehicle Type
     *
     * @param array $request    Input values
     * @return redirect     to vehicle Type View
     */
    public function delete(Request $request)
    {
        $driver_location_id = DriverLocation::where('car_id',$request->id)->count();
        $find_vehicle_id = Vehicle::where('vehicle_id',$request->id)->count();
        $active_car = CarType::where('status','active')->where('id','!=',$request->id)->count();
        if($driver_location_id) {
            flashMessage('danger', "Driver using this Vehicle  type, So can't delete this"); // Call flash message function
        }
        elseif($find_vehicle_id) {
            flashMessage('danger', "vehicle using this Vehicle type, So can't delete this"); // Call flash message function
        }
        elseif($active_car<=0) {
            flashMessage('danger', "Atleast one vehicle type should be in active status, So can't delete this");
        }
        else { 
            CarType::find($request->id)->delete();
            flashMessage('success', 'Deleted Successfully');
        }
        return redirect('admin/vehicle_type');
    }
}
