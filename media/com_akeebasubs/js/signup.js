/**
 * @package		akeebasubs
 * @copyright	Copyright (c)2010-2013 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

/**
 * Setup (required for Joomla! 3)
 */
if(typeof(akeeba) == 'undefined') {
	var akeeba = {};
}
if(typeof(akeeba.jQuery) == 'undefined') {
	akeeba.jQuery = jQuery.noConflict();
}

var european_union_countries = ['AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK'];
var akeebasubs_business_state = '';
var akeebasubs_isbusiness = false;
var akeebasubs_blocked_gui = false;
var akeebasubs_run_validation_after_unblock = false;
var akeebasubs_cached_response = false;
var akeebasubs_valid_form = true;
var akeebasubs_personalinfo = 1;
var akeebasubs_validation_fetch_queue = [];
var akeebasubs_validation_queue = [];
var akeebasubs_sub_validation_fetch_queue = [];
var akeebasubs_sub_validation_queue = [];
var akeebasubs_level_id = 0;
var akeebasubs_submit_after_validation = false;
var akeebasubs_noneuvat = false;
var akeebasubs_fieldprefs = {
	'showregularfield'		: 1,
	'showdiscountfield'		: 1,
	'showtaxfield'			: 1
};
var akeebasubs_apply_validation = false;

function cacheSubmitAction(e)
{
	(function($) {
		e.preventDefault();
		akeebasubs_submit_after_validation = true;
		$('#subscribenow').attr('disabled','disabled');
	})(akeeba.jQuery);
}

function blockInterface()
{
	(function($) {
		$('#subscribenow').click(cacheSubmitAction);
		$('#ui-disable-spinner').css('display','inline-block');
		//$('#subscribenow').attr('disabled','disabled');
		akeebasubs_blocked_gui = true;
	})(akeeba.jQuery);
}

function enableInterface()
{
	(function($) {
		$('#subscribenow').unbind('click');
		$('#ui-disable-spinner').css('display','none');
		$('#subscribenow').removeAttr('disabled');
		akeebasubs_blocked_gui = false;
		if(akeebasubs_run_validation_after_unblock) {
			akeebasubs_run_validation_after_unblock = false;
			validateBusiness();
		} else {
			if(akeebasubs_submit_after_validation) {
				akeebasubs_submit_after_validation = false;
				setTimeout("(function($) {$('#subscribenow').click()})(akeeba.jQuery);", 100);
			}
		}
	})(akeeba.jQuery);
}

/**
 * Runs a form validation with the server and returns validation and, optionally,
 * price analysis information. If a callback_function is specified, it will be called
 * if the form is valid.
 * @param callback_function
 * @return
 */
function validateForm(callback_function)
{
	if(akeebasubs_blocked_gui) {
		akeebasubs_run_validation_after_unblock = true;
		return;
	}

	(function($) {
		if(akeebasubs_personalinfo == 1) {
			var data = {
				'action'	:	'read',
				'id'		:	akeebasubs_level_id,
				'username'	:	$('#username').val(),
				'name'		:	$('#name').val(),
				'email'		:	$('#email').val(),
				'email2'	:	$('#email2').val(),
				'address1'	:	$('#address1').val(),
				'address2'	:	$('#address2').val(),
				'country'	:	$('select[name$="country"]').val(),
				'state'		:	$('select[name$="state"]').val(),
				'city'		:	$('#city').val(),
				'zip'		:	$('#zip').val(),
				'isbusiness':	$('#isbusiness1').is(':checked') ? 1 : 0,
				'businessname':	$('#businessname').val(),
				'occupation':	$('#occupation').val(),
				'vatnumber'	:	$('#vatnumber').val(),
				'coupon'	:	($("#coupon").length > 0) ? $('#coupon').val() : '',
				'custom'	:	{},
				'subcustom'	:	{}
			};
		} else if(akeebasubs_personalinfo == 0) {
			var data = {
				'action'	:	'read',
				'id'		:	akeebasubs_level_id,
				'username'	:	$('#username').val(),
				'name'		:	$('#name').val(),
				'email'		:	$('#email').val(),
				'email2'	:	$('#email2').val(),
				'country'	:	$('select[name$="country"]').val(),
				'coupon'	:	($("#coupon").length > 0) ? $('#coupon').val() : '',
				'custom'	:	{},
				'subcustom'	:	{}
			};
		} else {
			var data = {
				'action'	:	'read',
				'id'		:	akeebasubs_level_id,
				'username'	:	$('#username').val(),
				'name'		:	$('#name').val(),
				'email'		:	$('#email').val(),
				'email2'	:	$('#email2').val(),
				'coupon'	:	($("#coupon").length > 0) ? $('#coupon').val() : '',
				'custom'	:	{},
				'subcustom'	:	{}
			};
		}
		
		if($('#password')) {
			data.password = $('#password').val();
			data.password2 = $('#password2').val();
		}
		
		// Fetch the custom fields
		$.each(akeebasubs_validation_fetch_queue, function(index, function_name){
			var result = function_name();
			if( (result !== null) && (typeof result == 'object') ) {
				// Merge the result with the data object
				$.extend(data.custom, result);
			}
		});
		
		// Fetch the per-subscription custom fields
		$.each(akeebasubs_sub_validation_fetch_queue, function(index, function_name){
			var result = function_name();
			if( (result !== null) && (typeof result == 'object') ) {
				// Merge the result with the data object
				$.extend(data.subcustom, result);
			}
		});
		
		blockInterface();
		
		$.ajax({
			type: 'POST',
			url: akeebasubs_validate_url+'?option=com_akeebasubs&view=validate&format=json',
			data: data,
			dataType: 'json',
			success: function(msg, textStatus, xhr) {
				if(msg.validation) {
					msg.validation.custom_validation = msg.custom_validation;
					msg.validation.custom_valid = msg.custom_valid;
					msg.validation.subcustom_validation = msg.subscription_custom_validation;
					msg.validation.subcustom_valid = msg.subscription_custom_valid;
					applyValidation(msg.validation, callback_function);
				}
				if(msg.price) applyPrice(msg.price);
				enableInterface();
			},
			error: function(jqXHR, textStatus, errorThrown) {
				enableInterface();
			}
		});		
		
	})(akeeba.jQuery);
}

/**
 * Validates the password fields
 * @return
 */
function validatePassword()
{
	(function($) {
		if(!$('#password')) return;
		var password = $('#password').val();
		var password2 = $('#password2').val();
		
		$('#password_invalid').css('display','none');
		$('#password2_invalid').css('display','none');
		
		if (!akeebasubs_apply_validation)
		{
			if ((password == '') && (password2 == ''))
			{
				$('#password').parent().parent().removeClass('error').removeClass('success');
				$('#password2').parent().parent().removeClass('error').removeClass('success');
				return;
			}
		}
		
		if(password == '') {
			$('#password').parent().parent().addClass('error').removeClass('success');
			$('#password2').parent().parent().removeClass('error').removeClass('success');
			$('#password_invalid').css('display','inline-block');
			akeebasubs_valid_form = false;
		} else {
			$('#password').parent().parent().removeClass('error').addClass('success');
			if(password2 != password) {
				$('#password2').parent().parent().addClass('error').removeClass('success');
				$('#password2_invalid').css('display','inline-block');
				akeebasubs_valid_form = false;
			} else {
				$('#password2').parent().parent().removeClass('error').addClass('success');
			}
		}		
	})(akeeba.jQuery);
}

/**
 * Validates the (real) name
 * @return
 */
function validateName()
{
	(function($) {
		$('#name_empty').css('display','none');
		var name = $('#name').val();
		
		if (!akeebasubs_apply_validation)
		{
			if (name == '')
			{
				$('#name').parent().parent().removeClass('error').removeClass('success');
				return;
			}
		}
		
		var invalidName = false;
		if(name == '') {
			invalidName = true;
		}
		/**
		else {
			name = ltrim(rtrim(name, " "), " ");
			var nameParts = name.split(' ');
			if(nameParts.length < 2) invalidName = true;
		}
		**/
		
		if(invalidName) {
			$('#name').parent().parent().addClass('error').removeClass('success');
			$('#name_empty').css('display','inline-block');
			akeebasubs_valid_form = false;
			return;
		} else {
			$('#name').parent().parent().removeClass('error').addClass('success');
		}
	})(akeeba.jQuery);
}

/**
 * DHTML email validation script. Courtesy of SmartWebby.com (http://www.smartwebby.com/dhtml/)
 */

function echeck(str) {
	var at="@";
	var dot=".";
	var lat=str.indexOf(at);
	var lstr=str.length;
	var ldot=str.indexOf(dot);
	if (str.indexOf(at)==-1){
	   return false;
	}

	if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){
	   return false;
	}

	if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){
	    return false;
	}

	 if (str.indexOf(at,(lat+1))!=-1){
	    return false;
	 }

	 if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){
	    return false;
	 }

	 if (str.indexOf(dot,(lat+2))==-1){
	    return false;
	 }
	
	 if (str.indexOf(" ")!=-1){
	    return false;
	 }

	 return true;				
}

/**
 * Validates the email address
 * @return
 */
function validateEmail()
{
	(function($) {
		$('#email_empty').css('display','none');
		$('#email_invalid').css('display','none');
		$('#email2_invalid').css('display','none');
		var email = $('#email').val();
		var email2 = $('#email2').val();
		
		if ((email == '') && (email2 == ''))
		{
			$('#email').parent().parent().removeClass('error').removeClass('success');
			$('#email2').parent().parent().removeClass('error').removeClass('success');
			$('#email_empty').css('display','none');
			$('#email_invalid').css('display','none');
			$('#email2_invalid').css('display','none');
			return;
		}
		
		if(email == '') {
			$('#email').parent().parent().addClass('error').removeClass('success');
			$('#email_empty').css('display','inline-block');
			akeebasubs_valid_form = false;
			return;
		} else if(!echeck(email)) {
			$('#email').parent().parent().addClass('error').removeClass('success');
			$('#email_invalid').css('display','inline-block');
			akeebasubs_valid_form = false;
			return;
		} else {
			validateForm();
		}
	})(akeeba.jQuery);
}

function validateAddress()
{
	if(akeebasubs_personalinfo == 0) return;

	(function($) {
		var address = $('#address1').val();
		var country = $('select[name$="country"]').val();
		var state = $('select[name$="state"]').val();
		var city = $('#city').val();
		var zip = $('#zip').val();
		
		var hasErrors = false;
		
		if(akeebasubs_personalinfo == 1)
		{
			if (!akeebasubs_apply_validation)
			{
				$('#address1').parent().parent().removeClass('error').removeClass('success');
				$('#country').parent().parent().removeClass('error').removeClass('success');
				$('#city').parent().parent().removeClass('error').removeClass('success');
				$('#state').parent().parent().removeClass('error').removeClass('success');
				$('#zip').parent().parent().removeClass('error').removeClass('success');
				
				$('#address1_empty').css('display','none');
				$('#country_empty').css('display','none');
				$('#city_empty').css('display','none');
				$('#state_empty').css('display','none');
				$('#zip_empty').css('display','none');
				
				return;
			}
			
			
			$('#address1').parent().parent().removeClass('error').removeClass('success');
			if(address == '') {
				$('#address1').parent().parent().addClass('error');
				$('#address1_empty').css('display','inline-block');
				hasErrors = true;
			} else {
				$('#address1').parent().parent().addClass('success');
				$('#address1_empty').css('display','none');
			}

			$('#country').parent().parent().removeClass('error').removeClass('success');
			if(country == '') {
				$('#country').parent().parent().addClass('error');
				$('#country_empty').css('display','inline-block');
				hasErrors = true;
			} else {
				$('#country').parent().parent().addClass('success');
				$('#country_empty').css('display','none');
				// If that's an EU country, show and update the VAT field
				if($('#vatfields')) {
					$('#vatfields').css('display','none');

					if(akeebasubs_noneuvat) {
						$('#vatfields').css('display','block');
						$('#vatcountry').css('display','none');
					}
					for(key in european_union_countries) {
						if(european_union_countries[key] == country) {
							$('#vatfields').css('display','block');
							$('#vatcountry').css('display','inline-block');
							var ccode = country;
							if(ccode == 'GR') {ccode = 'EL';}
							$('#vatcountry').text(ccode);
						}
					}
				}
			}

			/*
			if( (country == 'US') || (country == 'CA') ) {
				$('#stateField').css('display','block');
				$('#state').parent().parent().removeClass('error').removeClass('success');
				if(state == '') {
					$('#state').parent().parent().addClass('error');
					$('#state_empty').css('display','inline-block');
					hasErrors = true;
				} else {
					$('#state').parent().parent().addClass('success');
					$('#state_empty').css('display','none');
				}
			} else {
				$('#stateField').css('display','none');
			}
			*/

			$('#city').parent().parent().removeClass('error').removeClass('success');
			if(city == '') {
				$('#city').parent().parent().addClass('error');
				$('#city_empty').css('display','inline-block');
				hasErrors = true;
			} else {
				$('#city').parent().parent().addClass('success');
				$('#city_empty').css('display','none');
			}

			$('#zip').parent().parent().removeClass('error').removeClass('success');
			if(zip == '') {
				$('#zip').parent().parent().addClass('error');
				$('#zip_empty').css('display','inline-block');
				hasErrors = true;
			} else {
				$('#zip').parent().parent().addClass('success');
				$('#zip_empty').css('display','none');
			}
		}
		else
		{
			if (!akeebasubs_apply_validation)
			{
				$('#country').parent().parent().removeClass('error').removeClass('success');
				$('#country_empty').css('display','none');
				return;
			}
			
			$('#country').parent().parent().removeClass('error').removeClass('success');
			if(country == '') {
				$('#country').parent().parent().addClass('error');
				$('#country_empty').css('display','inline-block');
				hasErrors = true;
			} else {
				$('#country').parent().parent().addClass('success');
				$('#country_empty').css('display','none');
			}
		}
		
		if(hasErrors) {
			akeebasubs_valid_form = false;
			return;
		}
		
	})(akeeba.jQuery);
}

/**
 * Validates the business registration information and runs a price fetch
 * @return
 */
function validateBusiness()
{
	(function($) {
		if(akeebasubs_personalinfo == 1) {
			// Do I have to show the business fields?
			if($('#isbusiness1').is(':checked')) {
				$('#businessfields').show();
			} else {
				$('#businessfields').hide();
				// If it's not a business validation, chain an address validation
				if(akeebasubs_blocked_gui) {
					akeebasubs_run_validation_after_unblock = true;
					return;
				} else {
					akeebasubs_valid_form = true;
					validateForm();
				}
				return;
			}
		} else {
			if(akeebasubs_blocked_gui) {
				akeebasubs_run_validation_after_unblock = true;
				return;
			} else {
				akeebasubs_valid_form = true;
				validateForm();
			}
			return;
		}
		
		// Do I have to show VAT fields?
		var country = $('select[name$="country"]').val();
		$('#vatfields').css('display','none');
		if(akeebasubs_noneuvat) {
			$('#vatfields').css('display','block');
			$('#vatcountry').css('display','none');
		}
		for(key in european_union_countries) {
			if(european_union_countries[key] == country) {
				$('#vatfields').css('display','block');
				$('#vatcountry').css('display','inline-block');
				var ccode = country;
				if(ccode == 'GR') {ccode = 'EL';}
				$('#vatcountry').text(ccode);
			}
		}
		
		if(akeebasubs_personalinfo == 1) {
			// Make sure we don't do business validation / price check unless something's changed
			var vatnumber = '';
			if($('#vatnumber')) vatnumber = $('#vatnumber').val();
			
			var data = {
				country: $('select[name$="country"]').val(),
				state: $('select[name$="state"]').val(),
				city: $('#city').val(),
				zip: $('#zip').val(),
				isbusiness: $('#isbusiness1').is(':checked') ? 1 : 0,
				businessname: $('#businessname').val(),
				occupation: $('#occupation').val(),
				vatnumber: vatnumber,
				coupon: ($("#coupon").length > 0) ? $('#coupon').val() : ''
			};
		} else {
			var data = {
				coupon: ($("#coupon").length > 0) ? $('#coupon').val() : ''
			};
		}
		
		var hash = '';
		for(key in data) {
			hash += '|' + key + '|' + data[key];
		}
		hash += '|';
		
		if(akeebasubs_business_state == hash) {
			if(akeebasubs_isbusiness) return;
			akeebasubs_isbusiness = true;
		}
		
		akeebasubs_business_state = hash;

		validateForm();
	})(akeeba.jQuery);
}

function validateIsNotBusiness(e) {
	(function($) {
		$('#businessfields').hide();
		akeebasubs_cached_response.businessname = true;
		akeebasubs_cached_response.novatrequired = true;
		applyValidation(akeebasubs_cached_response);
		akeebasubs_isbusiness = false;
	})(akeeba.jQuery);
}

function applyValidation(response, callback)
{
	akeebasubs_cached_response = response;

	(function($) {
		akeebasubs_valid_form = true;
		if (akeebasubs_apply_validation)
		{
			$('#username').parent().parent().removeClass('error').removeClass('success');
			if($('#username_valid')) {
				if(response.username) {
					$('#username').parent().parent().addClass('success');
					$('#username_valid').css('display','inline-block');
					$('#username_invalid').css('display','none');
				} else {
					if( !$('#username').attr('disabled') || $('#username').attr('disabled') != 'disabled' ) {
						akeebasubs_valid_form = false;
					}

					$('#username_valid').css('display','none');
					$('#username_invalid').css('display','none');

					if($('#username').val() != '') {
						$('#username').parent().parent().addClass('error');
						$('#username_invalid').css('display','inline-block');
					}
				}
			}

			$('#name').parent().parent().removeClass('error').removeClass('success');
			if(response.name) {
				$('#name').parent().parent().addClass('success');
				$('#name_empty').css('display','none');
			} else {
				$('#name').parent().parent().addClass('error');
				akeebasubs_valid_form = false;
				$('#name_empty').css('display','inline-block');
			}

			$('#email').parent().parent().removeClass('error').removeClass('success');
			if(response.email) {
				$('#email').parent().parent().addClass('success');
				$('#email_invalid').css('display','none');
			} else {
				$('#email').parent().parent().addClass('error');
				akeebasubs_valid_form = false;
				$('#email_invalid').css('display','inline-block');
			}

			$('#email2').parent().parent().removeClass('error').removeClass('success');
			if(response.email2) {
				$('#email2').parent().parent().addClass('success');
				$('#email2_invalid').css('display','none');
			} else {
				$('#email2').parent().parent().addClass('error');
				akeebasubs_valid_form = false;
				$('#email2_invalid').css('display','inline-block');
			}

			if(akeebasubs_personalinfo == -1)
			{
				$('#country').parent().parent().removeClass('error').removeClass('success');
				if(response.country) {
					$('#country').parent().parent().addClass('success');
					$('#country_empty').css('display','none');
				} else {
					akeebasubs_valid_form = false;
					$('#country').parent().parent().addClass('error');
					$('#country_empty').css('display','inline-block');
				}
			}
			else if(akeebasubs_personalinfo == 1) {
				$('#address1').parent().parent().removeClass('error').removeClass('success');
				if(response.address1) {
					$('#address1').parent().parent().addClass('success');
					$('#address1_empty').css('display','none');
				} else {
					$('#address1').parent().parent().addClass('error');
					akeebasubs_valid_form = false;
					$('#address1_empty').css('display','inline-block');
				}

				$('#country').parent().parent().removeClass('error').removeClass('success');
				if(response.country) {
					$('#country').parent().parent().addClass('success');
					$('#country_empty').css('display','none');
				} else {
					akeebasubs_valid_form = false;
					$('#country').parent().parent().addClass('error');
					$('#country_empty').css('display','inline-block');
				}

				$('#state').parent().parent().removeClass('error').removeClass('success');
				if(response.state) {
					$('#state').parent().parent().addClass('success');
					$('#state_empty').css('display','none');
				} else {
					$('#state').parent().parent().addClass('error');
					if($('#state_empty').css('display') != 'none') {
						akeebasubs_valid_form = false;
					}
					$('#state_empty').css('display','inline-block');
				}

				$('#city').parent().parent().removeClass('error').removeClass('success');
				if(response.city) {
					$('#city').parent().parent().addClass('success');
					$('#city_empty').css('display','none');
				} else {
					$('#city').parent().parent().addClass('error');
					akeebasubs_valid_form = false;
					$('#city_empty').css('display','inline-block');
				}

				$('#zip').parent().parent().removeClass('error').removeClass('success');
				if(response.zip) {
					$('#zip').parent().parent().addClass('success');
					$('#zip_empty').css('display','none');
				} else {
					$('#zip').parent().parent().addClass('error');
					akeebasubs_valid_form = false;
					$('#zip_empty').css('display','inline-block');
				}

				$('#businessname').parent().parent().removeClass('error').removeClass('success');
				if(response.businessname) {
					$('#businessname').parent().parent().addClass('success');
					$('#businessname_empty').css('display','none');
				} else {
					$('#businessname').parent().parent().addClass('error');
					if($('#isbusiness1').is(':checked')) {
						akeebasubs_valid_form = false;
					}
					$('#businessname_empty').css('display','inline-block');
				}

				$('#occupation').parent().parent().removeClass('error').removeClass('success');
				if($('#occupation').val()) {
					$('#occupation').parent().parent().addClass('success');
					$('#occupation_empty').css('display','none');
				} else {
					$('#occupation').parent().parent().addClass('error');
					if($('#isbusiness1').is(':checked')) {
						akeebasubs_valid_form = false;
					}
					$('#occupation_empty').css('display','inline-block');
				}
			}
		}
		else
		{
			// Apply validation is false
			$('#businessname').parent().parent().removeClass('error').removeClass('success');
			$('#occupation').parent().parent().removeClass('error').removeClass('success');
			$('#businessname_empty').css('display','none');
			$('#occupation_empty').css('display','none');
		}
		
		if(akeebasubs_personalinfo == 1)
		{
			$('#vatnumber').parent().parent().parent().removeClass('warning').removeClass('success');
				if(response.vatnumber) {
					$('#vatnumber').parent().parent().parent().addClass('success');
					$('#vat-status-invalid').css('display','none');
					$('#vat-status-valid').css('display','inline-block');
				} else {
					$('#vatnumber').parent().parent().parent().addClass('warning');
					$('#vat-status-invalid').css('display','inline-block');
					$('#vat-status-valid').css('display','none');
				}

				if(response.novatrequired) {
					$('#vatnumber').parent().parent().parent().removeClass('warning').removeClass('success');
					$('#vat-status-invalid').css('display','none');
					$('#vat-status-valid').css('display','none');
				}
		}
		
		// Finally, apply the custom validation
		$.each(akeebasubs_validation_queue, function(index, function_name){
			var isValid = function_name(response);
			akeebasubs_valid_form = akeebasubs_valid_form & isValid;
		});
		$.each(akeebasubs_sub_validation_queue, function(index, function_name){
			var isValid = function_name(response);
			akeebasubs_valid_form = akeebasubs_valid_form & isValid;
		});
		
		if (!akeebasubs_apply_validation)
		{
			akeebasubs_valid_form = true;
		}
	})(akeeba.jQuery);
}

function applyPrice(response)
{
	(function($) {
		if($('#akeebasubs-sum-net').length > 0) {
			$('#akeebasubs-sum-net').val(response.net);
			$('#akeebasubs-sum-discount').val(response.discount);
			$('#akeebasubs-sum-vat').val(response.tax);
			$('#akeebasubs-sum-total').val(response.gross);
			
			$('#akeebasubs-sum-net-container').css('display', 'none');
			$('#akeebasubs-sum-discount-container').css('display', 'none');
			$('#akeebasubs-sum-vat-container').css('display', 'none');
			
			if (akeebasubs_fieldprefs.showregularfield == 1)
			{
				$('#akeebasubs-sum-net-container').css('display', 'block');
			}
			else if (akeebasubs_fieldprefs.showregularfield == -1)
			{
				if ((response.discount * 1 + response.tax * 1) >= 0.01)
				{
					$('#akeebasubs-sum-net-container').css('display', 'block');
				}
			}
			
			if (akeebasubs_fieldprefs.showdiscountfield == 1)
			{
				$('#akeebasubs-sum-discount-container').css('display', 'block');
			}
			else if (akeebasubs_fieldprefs.showdiscountfield == -1)
			{
				if (Math.abs(response.discount * 1) >= 0.01)
				{
					$('#akeebasubs-sum-discount-container').css('display', 'block');
				}
			}
			
			if (akeebasubs_fieldprefs.showtaxfield == 1)
			{
				$('#akeebasubs-sum-vat-container').css('display', 'block');
			}
			else if (akeebasubs_fieldprefs.showtaxfield == -1)
			{
				if (Math.abs(response.tax * 1) >= 0.01)
				{
					$('#akeebasubs-sum-vat-container').css('display', 'block');
				}
			}
			
			if(response.gross * 1 <= 0) {
				$('#paymentmethod-container').css('display','none');
			} else {
				$('#paymentmethod-container').css('display','inline');
			}
		}
	})(akeeba.jQuery);
}

/**
 * Adds a function to the validation fetch queue
 */
function addToValidationFetchQueue(myfunction)
{
	if(typeof myfunction != 'function') return false;
	akeebasubs_validation_fetch_queue.push(myfunction);
}

/**
 * Adds a function to the validation queue
 */
function addToValidationQueue(myfunction)
{
	if(typeof myfunction != 'function') return false;
	akeebasubs_validation_queue.push(myfunction);
}

/**
 * Adds a function to the per-subscription validation fetch queue
 */
function addToSubValidationFetchQueue(myfunction)
{
	if(typeof myfunction != 'function') {
		return false;
	}
	akeebasubs_sub_validation_fetch_queue.push(myfunction);
}

/**
 * Adds a function to the per-subscription validation queue
 */
function addToSubValidationQueue(myfunction)
{
	if(typeof myfunction != 'function') return false;
	akeebasubs_sub_validation_queue.push(myfunction);
}


(function($) {
	$(document).ready(function(){
		$('#username').blur(validateForm);
		if($('#password')) {
			$('#password').blur(validatePassword);
			$('#password2').blur(validatePassword);
		}
		$('#name').blur(validateName);
		$('#email').blur(validateEmail);
		$('#email2').blur(validateEmail);
		if(akeebasubs_personalinfo) {
			$('select[name$="country"]').change(validateBusiness);
			$('select[name$="state"]').change(validateBusiness);
			$('#address1').blur(validateAddress);
			$('#city').blur(validateBusiness);
			$('#zip').blur(validateBusiness);
			$('#businessname').blur(validateBusiness);
			$('#isbusiness0').click(validateIsNotBusiness);
			$('#isbusiness1').click(validateBusiness);
			$('#vatnumber').blur(validateBusiness);
		}
		if($('#coupon').length > 0) {
			$('#coupon').blur(validateBusiness);
		}
		// Attach onBlur events to custom fields
		$('#signupForm *[name]').filter(function(index){
			if($(this).is('input')) return $(this).attr('name').substr(0, 7) == 'custom[';
			return false;
		}).blur(validateForm);
		$('#signupForm *[name]').filter(function(index){
			if($(this).is('input')) return $(this).attr('name').substr(0, 10) == 'subcustom[';
			return false;
		}).blur(validateForm);
		// Attach onChange events to custom checkboxes
		$('#signupForm *[name]').filter(function(index){
			if($(this).attr('type') == 'checkbox') return true;
			if($(this).attr('type') == 'radio') return true;
			if($(this).is('select')) return true;
			return false;
		}).change(validateForm);
		
		// Workaround for RocketTheme's fancy option hider
		var rokkedLabel = $('label[for="isbusiness1"]');
		if(rokkedLabel) {
			rokkedLabel.removeClass('rokradios');
			$('#isbusiness0').attr('style','');
		}
		
		if(akeebasubs_personalinfo) {
			if($('#isbusiness1').is(':checked')) {
				$('#isbusiness1').click();
			} else {
				$('#isbusiness0').click();
			}
		}
		
		// Disable form submit when ENTER is hit in the coupon field
		$('input#coupon').keypress(function(e){
			if ( e.which == 13 ) {
				validateForm();
				return false;
			}
		});
		
		validateForm();
	});
})(akeeba.jQuery);

/**
(function($) {
})(akeeba.jQuery);
/**/

function rtrim (str, charlist) {
    charlist = !charlist ? ' \\s\u00A0' : (charlist + '').replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '\\$1');
    var re = new RegExp('[' + charlist + ']+$', 'g');
    return (str + '').replace(re, '');
}

function ltrim (str, charlist) {
    charlist = !charlist ? ' \\s\u00A0' : (charlist + '').replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
    var re = new RegExp('^[' + charlist + ']+', 'g');
    return (str + '').replace(re, '');
}