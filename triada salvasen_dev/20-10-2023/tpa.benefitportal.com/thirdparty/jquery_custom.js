//Developed By HR
var req, reqVal;
var _ = $(document);

handler = function(url, dataString, startFunctionName, endFunctionName, async, ref) {
	('undefined' === typeof async) && (async = !0);
	try {
		return reqVal = $.ajax({
			url: url,
			beforeSend: function() {
				'function' === typeof startFunctionName ? 'undefined' === typeof ref ? startFunctionName() : startFunctionName(ref) : 'function' === typeof eval(startFunctionName) && ('undefined' === typeof ref ? window[startFunctionName]() : window[startFunctionName](ref));
			},
			method: 'post',
			dataType: 'json',
			data: dataString,
			async: async,
			success: function(data) {
				'function' === typeof endFunctionName ? 'undefined' === typeof ref ? endFunctionName(data) : endFunctionName(data, ref) : 'function' === typeof eval(endFunctionName) && ('undefined' === typeof ref ? window[endFunctionName](data) : window[endFunctionName](data, ref));
			}
		}), req;
	} catch (e) {
		console.log('ERR:: error in redirection - ' + e);
	}
};

formHandler = function(formRef, startFunctionName, endFunctionName) {
	var $inputs = $('input[type="file"]:not([disabled])', formRef)
	$inputs.each(function(_, input) {
		console.log(input.files.length)
		if (input.files.length > 0) return;
		$(input).prop('disabled', true)
	})
	var formURL = formRef.attr('action');
	var formData = new FormData(formRef[0]);
	$inputs.prop('disabled', false)
	return reqVal = $.ajax({
		url: formURL,
		type: 'post',
		dataType: 'json',
		data: formData,
		processData: !1,
		contentType: !1,
		enctype: 'multipart/form-data',
		mimeType: 'multipart/form-data',
		cache: !1,
		beforeSend: function() {
			'function' === typeof startFunctionName ? startFunctionName() : 'function' === typeof eval(startFunctionName) && window[startFunctionName]();
		},
		success: function(data, textStatus, jqXHR) {
			'function' === typeof endFunctionName ? endFunctionName(data) : 'function' === typeof eval(endFunctionName) && window[endFunctionName](data);
		},
		/*error: function(n, e, t) {
		console.error(n);
		}*/
	}), !1;
};

confirm_export_data = function(callback_fun){
	swal({
        text: '<br>Export Data: Are you sure?',
        showCancelButton: true,
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel',
    }).then(function() {
        'function' === typeof callback_fun ? callback_fun() : 'function' === typeof eval(callback_fun) && window[callback_fun]();
    }, function(dismiss) {
    });
};
function confirm_view_export_request(from_colorbox,portal){
	if(typeof(from_colorbox) === "undefined") {
		var from_colorbox = false;
	}
	if(from_colorbox == true) {
		parent.swal({
		    text: "<br>Export Data: Request Submitted",
		    showCancelButton: true,
		    confirmButtonText: "View Export",
		    cancelButtonText: 'Close',
		}).then(function() {
			setTimeout(function(portal){
				if(typeof(portal) === "undefined") {
					var portal = "admin";
				}
				if(portal == "admin") {
					parent.window.open($HOST + "/admin/report_export.php",'_blank');	
					parent.$.colorbox.close();

				} else if(portal == "agent") {
					parent.window.open($HOST + "/agents/report_export.php",'_blank');	
					parent.$.colorbox.close();

				} else if(portal == "group") {
					parent.window.open($HOST + "/groups/report_export.php",'_blank');	
					parent.$.colorbox.close();
				}	
			},300,portal);
		}, function(dismiss) {
			parent.$.colorbox.close();
		});
	} else {
		swal({
		    text: "<br>Export Data: Request Submitted",
		    showCancelButton: true,
		    confirmButtonText: "View Export",
		    cancelButtonText: 'Close',
		}).then(function() {
		  	setTimeout(function(portal){
		  		if(typeof(portal) === "undefined") {
		  			var portal = "admin";
		  		}
		  		if(portal == "admin") {
		  			window.open($HOST + "/admin/report_export.php",'_blank');	
		  		
		  		} else if(portal == "agent") {
					window.open($HOST + "/agents/report_export.php",'_blank');	

				} else if(portal == "group") {
					window.open($HOST + "/groups/report_export.php",'_blank');	
				}	      	
		  	},300,portal);
		}, function(dismiss) {
		});
	}
};
block_special_char = function(e){
	var k;
	document.all ? k = e.keyCode : k = e.which;
	return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57));
}
trigger = function(e, r, i) {
	'undefined' === typeof i && (i = 'click'), _.off(i, e), _.on(i, e, function(e) {
		r($(this), e);
	});
};
allowed_extesion = ['jpg', 'jpeg', 'png'];
/*code for check image validation when select file*/
(function($) {
	$.fn.checkFileType = function(options) {
		var defaults = {
			allowedExtensions: [],
			success: function() {},
			error: function() {}
		};
		options = $.extend(defaults, options);
		return this.each(function() {
			$(this).on('change', function() {
				var value = $(this).val(),
					file = value.toLowerCase(),
					extension = file.substring(file.lastIndexOf('.') + 1);
				if ($.inArray(extension, options.allowedExtensions) == -1) {
					options.error();
					$(this).focus();
				} else {
					options.success();
				}
			});
		});
	};

})(jQuery);
(function($) {
	$.fn.getStyle = function() {
		// var returns = {};
		returns.height = this.height();
		returns.width = this.width();
		returns.transform = this.css('transform');
		return returns;
	};

	$.fn.getSuffixClass = function($suffix) {
		var $className = false;
		$.each(this.attr('class').split(' '), function($k, $v) {
			($v.indexOf($suffix) > 0) && ($className = $v);
		});
		return $className.trim();
	};
})(jQuery);
$.fn.removeClassPrefix = function(prefix) {
	var c, regex = new RegExp('(^|\\s)' + prefix + '\\S+', 'g');
	return this.each(function() {
		c = this.getAttribute('class');
		this.setAttribute('class', c.replace(regex, ''));
	});
};

isGroup = function() {
	return $('.outer_selection').hasClass('t_group');
};

addLoader = function($this) {
	removeLoader($this);
	$this.append(" <i class='fa fa-spin fa-spinner myloaderforlivit'></i>").prop("disabled", true);
	setTimeout(function() {
		removeLoader($this);
	}, 5000);
};
removeLoader = function($this) {
	$this.prop('disabled', false);
	$('.myloaderforlivit').remove();
};

showLoader = function(ref, $class) {
	hideLoader();
	(typeof $class === 'undefined') && ($class = 'livituploader');
	$('<i class="fa fa-spin fa-spinner ' + $class + '"></i>').insertAfter(ref);
	setTimeout(function() {
		hideLoader();
	}, 5000);
};

hideLoader = function($class) {
	(typeof $class === 'undefined') && ($class = 'livituploader');
	$('.' + $class).remove();
};

base64Decode = function(str) {
	return decodeURIComponent(Array.prototype.map.call(atob(str), function(c) {
		return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
	}).join(''));
};
base64Encode = function(str) {
	return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function(match, p1) {
		return String.fromCharCode('0x' + p1);
	}));
};
src = function($src, $h, $w, $zc) {
	(typeof $h === 'undefined') && ($h = 100);
	(typeof $w === 'undefined') && ($w = 100);
	(typeof $zc === 'undefined') && ($zc = 1);
	var $pathname = $src.substring(0, $src.lastIndexOf('/') + 1);
	var $filename = $src.substring($src.lastIndexOf('/') + 1);
	return $HOST + '/thumb/' + $h + '/' + $w + '/' + $zc + '/' + base64Encode($pathname) + '/' + $filename;
};

url = function($object) {
	// return $.param($object);
	return Object.keys($object).map(function(key) {
		return ($object[key] != "") ? encodeURIComponent(key.trim()) + '=' + encodeURIComponent($object[key]) : null;
	}).join('&');
};
var redirect = function($url, $target) {
	(typeof $target === 'undefined') && ($target = '_blank');
	window.open($url, $target);
};
if (typeof isset != 'function') {
	isset = function(e) {
		return (e === 'undefined' || e === undefined) ? false : true;
	};
}
if (typeof issetor != 'function') {
	issetor = function(e, r) {
		return isset(e) ? e : (isset(r) ? r : '')
	};
}
if (typeof blankor != 'function') {
	blankor = function(e, r) {
		return isset(e) && e != '' ? e : (isset(r) ? r : '')
	};
}
Array.prototype.toObject = function() {
	var rv = {};
	for (var i = 0; i < this.length; ++i)
		rv[i] = this[i];
	return rv;
};
//custom drop area start
$.fn.applyDrop = function() {
	$curElement = $(this)[0];
	$hoverClassName = "custom-hover";
	var doc = $curElement;
	doc.ondragover = function(e) {
		$(this).addClass($hoverClassName);
		return false;
	};
	doc.ondragend = doc.ondragleave = function(e) {
		$(this).removeClass($hoverClassName);
		return false;
	};
	doc.ondrop = function(e) {
		//e.stopPropagation();
		//e.preventDefault && e.preventDefault();
		$(this).removeClass($hoverClassName);

		/*var files = e.dataTransfer.files;
		$($curElement).find("input[type='file']").prop("files",files);
		return false;*/
	};
};
/*function basename(path) {
   return path.split('/').reverse()[0];
}*/
trigger(".custom_drag_control .gui-file", function($this, e) {
	// console.log($this);
	if($this.val()!=''){
		$this.parent(".custom_drag_control").find('.gui-input').val($this.val().match(/\\([^\\]+)$/)[1]);
	}else{
		$this.parent(".custom_drag_control").find('.gui-input').val('');
	}
}, "change");
$(function() {
	isBrowserIE();
	//to apply drag and drop effect
	$(".custom_drag_control").each(function() {
		$(this).applyDrop();
	});
	/*$( ".custom_drag_control" ).droppable({
    hoverClass: "custom-hover",
    drop: function( event, ui ) {
    }
   });*/
});
getTwoCharCurrentTimezone = function() {
	$currentTimezone = getTimezoneName();
	if ($currentTimezone == "US/Pacific") {
		return "PT";
	} else if ($currentTimezone == "US/Mountain") {
		return "MT";
	} else if ($currentTimezone == "US/Central") {
		return "CT";
	} else if ($currentTimezone == "US/Eastern") {
		return "ET";
	} else
		return "ET";
};
getThreeCharCurrentTimezone = function() {
	$currentTimezone = getTimezoneName();
	if ($currentTimezone == "US/Pacific") {
		return "PST";
	} else if ($currentTimezone == "US/Mountain") {
		return "MST";
	} else if ($currentTimezone == "US/Central") {
		return "CST";
	} else if ($currentTimezone == "US/Eastern") {
		return "EST";
	} else
		return "EST";
};

function getTimezoneName() {
	tmSummer = new Date(Date.UTC(2005, 6, 30, 0, 0, 0, 0));
	so = -1 * tmSummer.getTimezoneOffset();
	tmWinter = new Date(Date.UTC(2005, 12, 30, 0, 0, 0, 0));
	wo = -1 * tmWinter.getTimezoneOffset();

	if (-660 == so && -660 == wo) return 'Pacific/Midway';
	if (-600 == so && -600 == wo) return 'Pacific/Tahiti';
	if (-570 == so && -570 == wo) return 'Pacific/Marquesas';
	if (-540 == so && -600 == wo) return 'America/Adak';
	if (-540 == so && -540 == wo) return 'Pacific/Gambier';
	if (-480 == so && -540 == wo) return 'US/Alaska';
	if (-480 == so && -480 == wo) return 'Pacific/Pitcairn';
	if (-420 == so && -480 == wo) return 'US/Pacific';
	if (-420 == so && -420 == wo) return 'US/Arizona';
	if (-360 == so && -420 == wo) return 'US/Mountain';
	if (-360 == so && -360 == wo) return 'America/Guatemala';
	if (-360 == so && -300 == wo) return 'Pacific/Easter';
	if (-300 == so && -360 == wo) return 'US/Central';
	if (-300 == so && -300 == wo) return 'America/Bogota';
	if (-240 == so && -300 == wo) return 'US/Eastern';
	if (-240 == so && -240 == wo) return 'America/Caracas';
	if (-240 == so && -180 == wo) return 'America/Santiago';
	if (-180 == so && -240 == wo) return 'Canada/Atlantic';
	if (-180 == so && -180 == wo) return 'America/Montevideo';
	if (-180 == so && -120 == wo) return 'America/Sao_Paulo';
	if (-150 == so && -210 == wo) return 'America/St_Johns';
	if (-120 == so && -180 == wo) return 'America/Godthab';
	if (-120 == so && -120 == wo) return 'America/Noronha';
	if (-60 == so && -60 == wo) return 'Atlantic/Cape_Verde';
	if (0 == so && -60 == wo) return 'Atlantic/Azores';
	if (0 == so && 0 == wo) return 'Africa/Casablanca';
	if (60 == so && 0 == wo) return 'Europe/London';
	if (60 == so && 60 == wo) return 'Africa/Algiers';
	if (60 == so && 120 == wo) return 'Africa/Windhoek';
	if (120 == so && 60 == wo) return 'Europe/Amsterdam';
	if (120 == so && 120 == wo) return 'Africa/Harare';
	if (180 == so && 120 == wo) return 'Europe/Athens';
	if (180 == so && 180 == wo) return 'Africa/Nairobi';
	if (240 == so && 180 == wo) return 'Europe/Moscow';
	if (240 == so && 240 == wo) return 'Asia/Dubai';
	if (270 == so && 210 == wo) return 'Asia/Tehran';
	if (270 == so && 270 == wo) return 'Asia/Kabul';
	if (300 == so && 240 == wo) return 'Asia/Baku';
	if (300 == so && 300 == wo) return 'Asia/Karachi';
	if (330 == so && 330 == wo) return 'Asia/Calcutta';
	if (345 == so && 345 == wo) return 'Asia/Katmandu';
	if (360 == so && 300 == wo) return 'Asia/Yekaterinburg';
	if (360 == so && 360 == wo) return 'Asia/Colombo';
	if (390 == so && 390 == wo) return 'Asia/Rangoon';
	if (420 == so && 360 == wo) return 'Asia/Almaty';
	if (420 == so && 420 == wo) return 'Asia/Bangkok';
	if (480 == so && 420 == wo) return 'Asia/Krasnoyarsk';
	if (480 == so && 480 == wo) return 'Australia/Perth';
	if (540 == so && 480 == wo) return 'Asia/Irkutsk';
	if (540 == so && 540 == wo) return 'Asia/Tokyo';
	if (570 == so && 570 == wo) return 'Australia/Darwin';
	if (570 == so && 630 == wo) return 'Australia/Adelaide';
	if (600 == so && 540 == wo) return 'Asia/Yakutsk';
	if (600 == so && 600 == wo) return 'Australia/Brisbane';
	if (600 == so && 660 == wo) return 'Australia/Sydney';
	if (630 == so && 660 == wo) return 'Australia/Lord_Howe';
	if (660 == so && 600 == wo) return 'Asia/Vladivostok';
	if (660 == so && 660 == wo) return 'Pacific/Guadalcanal';
	if (690 == so && 690 == wo) return 'Pacific/Norfolk';
	if (720 == so && 660 == wo) return 'Asia/Magadan';
	if (720 == so && 720 == wo) return 'Pacific/Fiji';
	if (720 == so && 780 == wo) return 'Pacific/Auckland';
	if (765 == so && 825 == wo) return 'Pacific/Chatham';
	if (780 == so && 780 == wo) return 'Pacific/Enderbury'
	if (840 == so && 840 == wo) return 'Pacific/Kiritimati';
	return 'US/Pacific';
}

//custom drop area end
//code for getting base64 image or any image height and width using js
(function() {
	'use strict';

	function toInt32(bytes) {
		return (bytes[0] << 24) | (bytes[1] << 16) | (bytes[2] << 8) | bytes[3];
	}

	function getDimensions(data) {
		return {
			width: toInt32(data.slice(16, 20)),
			height: toInt32(data.slice(20, 24))
		};
	}
	var base64Characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';

	function base64Decode(data) {
		var result = [];
		var current = 0;
		for (var i = 0, c; c = data.charAt(i); i++) {
			if (c === '=') {
				if (i !== data.length - 1 && (i !== data.length - 2 || data.charAt(i + 1) !== '=')) {
					throw new SyntaxError('Unexpected padding character.');
				}
				break;
			}
			var index = base64Characters.indexOf(c);
			if (index === -1) {
				throw new SyntaxError('Invalid Base64 character.');
			}
			current = (current << 6) | index;
			if (i % 4 === 3) {
				result.push(current >> 16, (current & 0xff00) >> 8, current & 0xff);
				current = 0;
			}
		}
		if (i % 4 === 1) {
			throw new SyntaxError('Invalid length for a Base64 string.');
		}
		if (i % 4 === 2) {
			result.push(current >> 4);
		} else if (i % 4 === 3) {
			current <<= 6;
			result.push(current >> 16, (current & 0xff00) >> 8);
		}
		return result;
	}
	window.getPngDimensions = function(dataUri) {
		return getDimensions(base64Decode(dataUri.substring(22)));
	};
})();

uploadImageOnServer = uos = function(dataUrl, successFunc, errorFunc) {
	handler(
		$HOST + "/ajax_savefile.php",
		$.param({
			action: "save",
			imageCode: dataUrl
		}),
		function() {
			ajax_loader(true);
		},
		function(data) {
			ajax_loader(false);
			if (data.code == 200) {
				("function" == typeof successFunc) ? successFunc(data): "function" == typeof eval(successFunc) && window[successFunc](data);
			} else {
				("function" == typeof errorFunc) ? errorFunc(data): "function" == typeof eval(errorFunc) && window[errorFunc](data);
			}
			$(".loaderPercentage").remove();
		}, !0,
		"",
		function(e, xhr) {
			//to handle progress by HR
			if (e.lengthComputable) {
				if ($(".loaderPercentage").length == 0) {
					$("#ajax_loader").append("<p class='loaderPercentage'></p>");
				}
				$(".loaderPercentage").html(Math.round((100 * e.loaded) / e.total) + "%");
			}
		},
		function(xhr, textStatus, errorThrown) {});
};

function getQueryStringParam(name, url) {
	if (!url) url = window.location.href;
	name = name.replace(/[\[\]]/g, "\\$&");
	var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
		results = regex.exec(url);
	if (!results) return null;
	if (!results[2]) return '';
	return decodeURIComponent(results[2].replace(/\+/g, " "));
}
imagePreview = function(input, container, h) {
	input = $(input)[0];
	if (input.files && input.files[0]) {
		t = input.files[0].name;
		var reader = new FileReader();
		reader.onload = function(e) {
			if (typeof(h) != "undefined") {
				$(container).attr("src", e.target.result).css({
					"height": h + "px"
				}).show();
			} else {
				$(container).attr("src", e.target.result).show();
			}
		};
		reader.readAsDataURL(input.files[0]);
	}
};

/************* Sweet alert IE hack start *****************/
function isBrowserIE() {
	var ua = window.navigator.userAgent;
	var msie = ua.indexOf("MSIE ");
	if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {


		$.getScript("https://cdnjs.cloudflare.com/ajax/libs/bluebird/3.3.5/bluebird.min.js", function(data, textStatus, jqxhr) {

		});

	}
	return false;
}
/************* Sweet alert IE hack end *****************/


// Numeric only control handler
jQuery.fn.numberOnlyAllowed =
	function() {
		return this.each(function() {
			$(this).keydown(function(e) {
				var key = e.charCode || e.keyCode || 0;
				// allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
				// home, end, period, and numpad decimal
				return (
					key == 8 ||
					key == 9 ||
					key == 13 ||
					key == 46 ||
					key == 110 ||
					key == 190 ||
					(key >= 35 && key <= 40) ||
					(key >= 48 && key <= 57) ||
					(key >= 96 && key <= 105));
			});
		});
	};
/******** Interaction ,Note And Claim start **************/
interactionUpdate = function(id,int_note_claim,url,location){
	var data = '';
	if(int_note_claim === 'claim'){
		data = {claim_ajax:'Y',id:id};
	}else if(int_note_claim === 'notes'){
		data = {note_ajax:'Y',id:id}
	}else{
		data = {interaction_ajax:'Y',id:id}
	}

	if(location === '' || location === undefined){
		location = 'admin';
	}
	$("#intrection_loader").show();
	$.ajax({
		url: $HOST+'/'+location+'/'+url+'?id='+id,
		data:data,
		method:'post',
		dataType: 'html',
		success:function(res){
			if(int_note_claim === 'claim'){
			$("#intrection_loader").hide();
			$("#claim_tab").html(res);
			$(".activity_wrap").mCustomScrollbar({
			theme:"dark"
			});
			}else if(int_note_claim === 'notes'){
			$("#intrection_loader").hide();
			$("#note_tab").html(res);
			$(".activity_wrap").mCustomScrollbar({
			theme:"dark"
			});
			}else{
			$("#intrection_loader").hide();
			$("#interactions_tab").html(res);
			$(".activity_wrap").mCustomScrollbar({
			theme:"dark"
			});
			}
		}
	});
};
/******** Interaction ,Note And Claim End **************/