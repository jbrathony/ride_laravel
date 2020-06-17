@extends('admin.template')
@section('main')
<div class="content-wrapper">
	<section class="content-header">
		<h1> Payout <small>Control panel</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="active">Payout</li>
		</ol>
	</section>
	<section class="content" ng-controller="payout_preferences">
		<div class="payout_setup" id="payout_setup">
			<div class="panel row-space-4 clearfix">
				<div class="box-header">
					{{ trans('messages.account.payout_methods') }}
				</div>
				<div class="panel-body col-md-5" id="payout_intro">
					<p class="payout_intro">
						{{ trans('messages.account.payout_methods_desc') }}
					</p>
					{!! Form::open(['url' => route('company_payout_preference'), 'class' => 'modal-add-payout-pref', 'method' => 'POST']) !!}
					<div class="panel-body p-0">
						<div class="payout_popup_view">
							<label for="holder_name">
								@lang('messages.account.holder_name')
							</label>
							<div class="payout_input_field">
								{!! Form::text('holder_name',old('holder_name', $bank_details->holder_name), ['id' => 'holder_name']) !!}
							</div>
							<p class="text-danger" > {{ $errors->first('holder_name') }} </p>
						</div>
						<div class="payout_popup_view">
							<label for="account_number">
								@lang('messages.account.account_number')
							</label>
							<div class="payout_input_field">
								{!! Form::text('account_number', old('account_number',$bank_details->account_number), ['id' => 'account_number']) !!}
							</div>
							<p class="text-danger" >{{$errors->first('account_number')}}</p>
						</div>
						<div class="payout_popup_view">
							<label for="bank_name">
								@lang('messages.account.bank_name')
							</label>
							<div class="payout_input_field">
								{!! Form::text('bank_name', old('bank_name',$bank_details->bank_name), ['id' => 'bank_name']) !!}
							</div>
							<p class="text-danger" >{{$errors->first('bank_name')}}</p>
						</div>
						<div class="payout_popup_view">
							<label for="bank_location">
								@lang('messages.account.bank_location')
							</label>
							<div class="payout_input_field">
								{!! Form::text('bank_location', old('bank_location',$bank_details->bank_location), ['id' => 'bank_location']) !!}
							</div>
							<p class="text-danger" >{{$errors->first('bank_location')}}</p>
						</div>
						<div class="payout_popup_view">
							<label for="bank_code">
								@lang('messages.account.bank_code')
							</label>
							<div class="payout_input_field">
								{!! Form::text('bank_code', old('bank_code',$bank_details->code), ['id' => 'bank_code']) !!}
							</div>
							<p class="text-danger" >{{$errors->first('bank_code')}}</p>
						</div>
					</div>
					<div class="panel-footer payout_footer clearfix">
						<input type="submit" value="{{ trans('messages.account.submit') }}" class="btn btn-primary pull-right" id="payout-next">
					</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</section>
</div>
@endsection