$(document).ready(function() {
	$(document).ready(function(){
		$('.sub_menu_header').click(function() {
			$('.sub_menu_header').toggleClass('open');
		});
	});

	$('.pay_close').click(function(){
		$('body').removeClass('new_fix');
	});

	$('.field__input').on('input', function() {
		var $field = $(this).closest('.field');
		if (this.value) {
			$field.addClass('field--not-empty');
		}
		else {
			$field.removeClass('field--not-empty');
		}
	});
});

$http = angular.injector(["ng"]).get("$http");

$('#js-currency-select').on('change', function(){
	currency_code = $(this).val();
	$http.post(APP_URL+'/set_session', {currency: currency_code}).then(function(response){
		location.reload();
	});
});

$('#js-language-select').on('change', function(){
	language_code = $(this).val();
	$http.post(APP_URL+'/set_session', {language: language_code
	}).then(function(response){
		location.reload();
	});
});

//Payout Preferences
app.controller('payout_preferences', ['$scope', '$http', function($scope, $http) {

}]);

app.controller('help', ['$scope', '$http', function($scope, $http) {
	$('.help-nav .navtree-list .navtree-next').click(function() {
		var id = $(this).data('id');
		var name = $(this).data('name');
		$('.help-nav #navtree').addClass('active');    
		$('.help-nav #navtree').removeClass('not-active');
		$('.help-nav .subnav-list li:first-child a').attr('aria-selected', 'false');
		$('.help-nav .subnav-list').append('<li> <a class="subnav-item" href="#" data-node-id="0" aria-selected="true"> ' + name + ' </a> </li>');
		$('.help-nav #navtree-'+id).css({
			'display': 'block'
		});
	});

	$('.help-nav .navtree-list .navtree-back').click(function() {
		var id = $(this).data('id');
		var name = $(this).data('name');
		$('.help-nav #navtree').removeClass('active');
		$('.help-nav #navtree').addClass('not-active');
		$('.help-nav .subnav-list li:first-child a').attr('aria-selected', 'true');
		$('.help-nav .subnav-list li').last().remove();
		$('.help-nav #navtree-' + id).css({
			'display': 'none'
		});
	});

	$('#help_search').autocomplete({
		source: function(request, response) {
			$.ajax({
				url: APP_URL + "/ajax_help_search",
				type: "GET",
				dataType: "json",
				data: {
					term: request.term
				},
				success: function(data) {
					response(data);
					$(this).removeClass('ui-autocomplete-loading');
				}
			});
		},
		search: function() {
			$(this).addClass('loading');
		},
		open: function() {
			$(this).removeClass('loading');
		}
	})
	.autocomplete("instance")._renderItem = function(ul, item) {
		if (item.id != 0) {
			$('#help_search').removeClass('ui-autocomplete-loading');
			return $("<li>")
			.append("<a href='" + APP_URL + "/help/article/" + item.id + "/" + item.question + "' class='article-link article-link-panel link-reset'><div class='hover-item__content'><div class='col-middle-alt article-link-left'><i class='icon icon-light-gray icon-size-2 article-link-icon icon-description'></i></div><div class='col-middle-alt article-link-right'>" + item.value + "</div></div></a>")
			.appendTo(ul);
		}
		else {
			$('#help_search').removeClass('ui-autocomplete-loading');
			return $("<li style='pointer-events: none;'>")
			.append("<span class='article-link article-link-panel link-reset'><div class='hover-item__content'><div class='col-middle-alt article-link-left'><i class='icon icon-light-gray icon-size-2 article-link-icon icon-description'></i></div><div class='col-middle-alt article-link-right'>" + item.value + "</div></div></span>")
			.appendTo(ul);
		}
	};

}]);