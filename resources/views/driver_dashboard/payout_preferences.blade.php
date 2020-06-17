@extends('template_driver_dashboard')
@section('main')
<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content">
	<div class="page-lead separated--bottom  text--center text--uppercase">
		<h1 class="flush-h1 flush"> @lang('messages.account.payout_methods') </h1>
	</div>
	<main id="site-content" role="main" ng-controller="payout_preferences">
		<div class=" row-space-top-4 row-space-4">
			<div class="row">
				<div class="col-md-12">
					<div class="payout_setup">
						<div class="panel row-space-4">
							<div class="panel-header">
								{{trans('messages.account.bank_detail')}}
							</div>
							<div class="panel-body">
								<div class="scroll_table">
									{!! Form::open(['url' => route('driver_payout_preference'), 'class' => 'form-horizontal']) !!}
									<div class="form-group">
										<div class="col-sm-3 control-label"> @lang('messages.account.holder_name') </div>
										<div class="col-sm-6">
											{!! Form::text('holder_name',old('holder_name',$bank_details->holder_name), ['class' => 'form-control', 'id' => 'account_holder_name', 'placeholder' => trans('messages.account.holder_name')]) !!}
											<span class="text-danger">{{ $errors->first('holder_name') }}</span>
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-3 control-label"> @lang('messages.account.account_number') </div>
										<div class="col-sm-6">
											
											{!! Form::text('account_number',old('account_number',$bank_details->account_number), ['class' => 'form-control', 'id' => 'account_number', 'placeholder' => trans('messages.account.account_number')]) !!}
											<span class="text-danger">{{ $errors->first('account_number') }}</span>
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-3 control-label"> @lang('messages.account.bank_name') </div>
										<div class="col-sm-6">
											
											{!! Form::text('bank_name',old('bank_name',$bank_details->bank_name), ['class' => 'form-control', 'id' => 'bank_name', 'placeholder' => trans('messages.account.bank_name')]) !!}
											<span class="text-danger">{{ $errors->first('bank_name') }}</span>
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-3 control-label"> @lang('messages.account.bank_location') </div>
										<div class="col-sm-6">
											
											{!! Form::text('bank_location',old('bank_location',$bank_details->bank_location), ['class' => 'form-control', 'id' => 'bank_location', 'placeholder' => trans('messages.account.bank_location')]) !!}
											<span class="text-danger">{{ $errors->first('bank_location') }}</span>
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-3 control-label"> @lang('messages.account.bank_code') </div>
										<div class="col-sm-6">
											{!! Form::text('bank_code',old('code',$bank_details->code), ['class' => 'form-control', 'id' => 'bank_code', 'placeholder' => trans('messages.account.bank_code')]) !!}
											<span class="text-danger">{{ $errors->first('bank_code') }}</span>
										</div>
									</div>
									<div class="form-group" align="center">
										<input class="btn btn-primary" type="Submit" value="{{trans('messages.account.submit')}}">
									</div>
									{!! Form::close() !!}
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
	</div>
	@endsection