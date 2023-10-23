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
	var formURL = formRef.attr('action');
	var formData = new FormData(formRef[0]);
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
		error: function(n, e, t) {
			console.error(n);
		}
	}), !1;
};

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