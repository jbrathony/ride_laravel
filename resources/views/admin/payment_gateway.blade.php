@extends('admin.template')
@section('main')
<div class="content-wrapper">
	<section class="content-header">
		<h1> Payment Gateway </h1>
		<ol class="breadcrumb">
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"> <i class="fa fa-dashboard"></i> Home </a>
			</li>
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/payment_gateway') }}"> Payment Gateway </a>
			</li>
			<li class="active"> Edit </li>
		</ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-8 col-sm-offset-2">
				<div class="box box-info">
					<div class="box-header with-border">
						<h3 class="box-title"> Payment Gateway Form </h3>
					</div>
					{!! Form::open(['url' => 'admin/payment_gateway', 'class' => 'form-horizontal']) !!}
					<div class="box-body">
						<span class="text-danger">(*)Fields are Mandatory</span>
						<!-- Braintree Section Start -->
						<div class="box-body">
							<div class="form-group">
								<label for="input_mode" class="col-sm-3 control-label"> Payment Mode</label>
								<div class="col-sm-6">
									{!! Form::select('bt_mode', array('sandbox' => 'Sandbox', 'production' => 'Production'), old('bt_mode'
									,payment_gateway('mode','Braintree')), ['class' => 'form-control', 'id' => 'input_mode']) !!}
									<span class="text-danger">{{ $errors->first('mode') }}</span>
								</div>
							</div>
							<div class="form-group">
								<label for="input_merchant_id" class="col-sm-3 control-label"> Braintree Merchant ID <em class="text-danger">*</em></label>
								<div class="col-sm-6">
									{!! Form::text('bt_merchant_id', old('bt_merchant_id',payment_gateway('merchant_id','Braintree')), ['class' => 'form-control', 'id' => 'input_merchant_id', 'placeholder' => 'Merchant ID']) !!}
									<span class="text-danger">{{ $errors->first('bt_merchant_id') }}</span>
								</div>
							</div>
							<div class="form-group">
								<label for="input_bt_public" class="col-sm-3 control-label"> Braintree Public Key<em class="text-danger">*</em></label>
								<div class="col-sm-6">
									{!! Form::text('bt_public_key', old('bt_public_key',payment_gateway('public_key','Braintree')), ['class' => 'form-control', 'id' => 'input_bt_public', 'placeholder' => 'Public Key']) !!}
									<span class="text-danger">{{ $errors->first('bt_public_key') }}</span>
								</div>
							</div>
							<div class="form-group">
								<label for="input_bt_private_key" class="col-sm-3 control-label"> Braintree Private Key<em class="text-danger">*</em></label>
								<div class="col-sm-6">
									{!! Form::text('bt_private_key', old('bt_private_key',payment_gateway('private_key','Braintree')), ['class' => 'form-control', 'id' => 'input_bt_private_key', 'placeholder' => 'Private Key']) !!}
									<span class="text-danger">{{ $errors->first('bt_private_key') }}</span>
								</div>
							</div>
						</div>
						<!-- Braintree Section End -->
					</div>
					<div class="box-footer">
						<button type="submit" class="btn btn-info pull-right" name="submit" value="submit">Submit</button>
						<button type="reset" class="btn btn-default pull-left"> Reset </button>
					</div>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</section>
</div>
@endsection