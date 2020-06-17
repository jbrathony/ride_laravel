app.controller('facebook_account_kit', ['$scope', '$http','fileUploadService', function($scope, $http,fileUploadService) {
	$scope.selectFile = function() {
		$("#file").click();
	};
	$scope.select_image = function() {
		$("#file").click();
	};

	$scope.fileNameChanged = function(element) {
		files = element.files;
		if(files) {
			file = files[0];
			if(file) {
				$('.profile_update-loader').addClass('loading');
				url = APP_URL+'/'+'profile_upload';
				upload = fileUploadService.uploadFileToUrl(file, url);
				upload.then(function(response) {
					if(response.success == 'true') {
						$('.profile_picture').attr('src',response.profile_url);
						$('.flash-container').html('<div class="alert alert-success text-center col-ssm-12" style="background: #2ec5e1 !important;border-color: #2ec5e1 !important;color: #fff !important;" >' + response.status_message + '</div>');
						$(".flash-container").fadeIn(3000);
						$(".flash-container").fadeOut(3000);
						$('.profile_update-loader').removeClass('loading');

					}
					else {
						$('.flash-container').html('<div class="alert alert-danger text-center col-ssm-12" >' + response.status_message + '</div>');
						$(".flash-container").fadeIn(3000);
						$(".flash-container").fadeOut(3000);
						$('.profile_update-loader').removeClass('loading');
					}
				});
			}
		}
	};

	function loginCallback(response) {
		if (response.status === "PARTIALLY_AUTHENTICATED") {
			document.getElementById('code').value = response.code;
			document.getElementById('_token').value = response.state;
			document.getElementById('submit-btn').setAttribute("ng-click", "");
			document.getElementById('submit-btn').setAttribute("type", "submit");
			document.getElementById('form').submit();
		}
		else if (response.status === "NOT_AUTHENTICATED") {
			// handle authentication failure
		}
		else if (response.status === "BAD_PARAMS") {
			// handle bad parameters
		}
	}

	$scope.showPopup = function(submit_method) {
		url = $('#form').attr('action')
		if (url.includes('update_profile')) {
			$('.mobile-text-danger').hide()
			smsLogin();
		}
		else {
			$('.text-danger').hide()
			$('#request_type').val(submit_method)
			$('#otp').val($('#otp_input').val())
			$.post(url,$('#form').serialize(),function(data) {
				data = $.parseJSON(data)
				if(submit_method=='send_otp') {
					if(data.status_code==0) {
						$.each(data.messages, function( index, value ) {
							$('.'+index+'_error').show()
							$('.'+index+'_error').html(value[0])
						});
					}
					else {
						$('#otp_popup').modal('show');
					}
				}
				else if (submit_method=='resend_otp') {
					$('#otp_resended_flash').html(data.message);
					$('#otp_resended_flash').removeClass('success_msg');
					$('#otp_resended_flash').removeClass('error_msg');

					if (data.status_code==1) {
						$('#otp_resended_flash').addClass('success_msg');
					}
					else {
						$('#otp_resended_flash').addClass('error_msg');
					}

					$('#otp_resended_flash').show();
					setTimeout(function() {
						$('#otp_resended_flash').fadeOut('slow');
					}, 2000);
				}
				else if (submit_method=='check_otp') {
					if (data.status_code==0) {
						$('.otp_error').show()
						$('.otp_error').html(data.message)
					}
					else {
						$('#request_type').val('submit')
						document.getElementById('form').submit();
					}
				}
			});
		}
	};

	$scope.changeNumberPopup = function(submit_method) {
		url = $('#form').attr('action')
		if(submit_method == 'show_popup') {
			$('#otp_popup').modal('show');
		}
		else {
			request_data = {};
			request_data.country_code = $('#mobile_country').val();
			request_data.mobile_number = $('#mobile_input').val();
			request_data.otp = $('#otp_input').val();
			request_data.request_type = submit_method
			url = APP_URL+'/change_mobile_number';
			$('.text-danger').hide()
			$.post(url,request_data,function(data) {
				data = $.parseJSON(data)
				if (submit_method=='send_otp') {
					if (data.status_code==0) {
						$.each(data.messages, function( index, value ) {
							$('.'+index+'_error').show()
							$('.'+index+'_error').html(value[0])
						});
					}
					else {
						$('#otp_resended_flash').show();
						setTimeout(function() {
							$('#otp_resended_flash').fadeOut('slow');
						}, 2000);
					}
				}
				else if (submit_method=='check_otp') {
					if (data.status_code==0) {
						$('.otp_error').show()
						$('.otp_error').html(data.message)
					}
					else {
						location.reload();
					}
				}
			});
		}
	};

	$(document).ready(function() {
		$('.resend_otp').click(function() {
			$('.otp_resended_flash').show();
			setTimeout(function() {
				$('.otp_resended_flash').fadeOut('slow');
			}, 1000);
		});
	});

	function smsLogin() {

	}

	// Call Google Autocomplete Initialize Function
	initAutocomplete();
	// Google Place Autocomplete Code
	$scope.location_found = false;
	$scope.autocomplete_used = false;
	var autocomplete;
	function initAutocomplete() {
		var home_address = document.getElementById('home_address');
		if(!home_address) {
			return false;
		}
		autocomplete = new google.maps.places.Autocomplete(home_address);
		autocomplete.addListener('place_changed', fillInAddress);
	}

	function fillInAddress() {
		$scope.autocomplete_used = true;
		fetchMapAddress(autocomplete.getPlace());
	}

	function fetchMapAddress(data) {
		if(data['types'] == 'street_address')
			$scope.location_found = true;
		var componentForm = {
			street_number: 'short_name',
			route: 'long_name',
			sublocality_level_1: 'long_name',
			sublocality: 'long_name',
			locality: 'long_name',
			administrative_area_level_1: 'long_name',
			country: 'short_name',
			postal_code: 'short_name'
		};
		$('#address_line1').val('');
		$('#address_line2').val('');
		$('#city').val('');
		$('#state').val('');
		$('#postal_code').val('');
		var place = data;
		$scope.street_number = '';
		for (var i = 0; i < place.address_components.length; i++) {
			var addressType = place.address_components[i].types[0];
			if (componentForm[addressType]) {
				var val = place.address_components[i][componentForm[addressType]];
				if(addressType       == 'street_number')
					$scope.street_number = val;
				if(addressType       == 'route')
					var street_address = $scope.street_number+' '+val;
				$('#address_line1').val($.trim(street_address));
				if(addressType == 'sublocality_level_1')
					$('#address_line2').val(val);
				if(addressType       == 'postal_code')
					$('#postal_code').val(val);
				if(addressType       == 'locality')
					$('#city').val(val);
				if(addressType       == 'administrative_area_level_1')
					$('#state').val(val);
				if(addressType       == 'country')
					$('#country').val(val);
			}
		}

		var latitude  = place.geometry.location.lat();
		var longitude = place.geometry.location.lng();
		$('#latitude').val(latitude);
		$('#longitude').val(longitude);
	}
	$('#mobile_country').click(function() {
		$('#select-title-stage').text($(this).find(':selected').attr('data-value'));
	});
}]);