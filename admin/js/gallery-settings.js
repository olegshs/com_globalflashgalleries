/**
 * @copyright   Copyright (c) 2010-2015 Mediaparts Interactive. All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/licenses/gpl.html
 */

var swf;

function flashAPI(swf, callback, timeout) {
	if (swf.parentNode.parentNode.style.display != 'none') {
		if (timeout == undefined || --timeout) {
			if (swf.setParameterValue == undefined) {
				setTimeout( function() { flashAPI(swf, callback, timeout); }, 1000 );
			} else {
				try {
					callback(swf);
				} catch (e) {
					// alert(e);
				}
			}
		}
	}
}

function getValue(e) {
	if (e.type == 'checkbox') {
		return e.value = e.checked ? 'true' : 'false';
	} else if (e.className.match(/\bcolor\b/)) {
		return '0x'+e.value.match(/[0-9A-Fa-f]+/);
	} else
		return e.value;
}

function getValuesByTagName(tagName, e) {
	var result = [];
	if (e) {
		var elements = e.getElementsByTagName(tagName);
		var m;
		for (var i = 0; i < elements.length; i++) {
			if (m = elements[i].id.match(/(.+)\.(.+)/))
				result.push(m[2] + ': "' + getValue(elements[i]) + '"');
		}
	}
	return result;
}

function applyChanges(e) {
	if (swf != undefined) {
		var m;
		if (m = e.id.match(/(.+)\.(.+)/)) {
			if (m[1] != 'lightbox') {
				var obj = document.getElementById(m[1]);

				var elements = [];

				elements = elements.concat(getValuesByTagName('input', obj));
				elements = elements.concat(getValuesByTagName('select', obj));
				elements = elements.concat(getValuesByTagName('textarea', obj));

				var objValues = '';
				for (var i = 0; i < elements.length; i++) {
					objValues += elements[i] + ', ';
				}
				objValues = objValues.substring(0, objValues.length-2);

				flashAPI(swf,
					function(swf) {
						eval('swf.setParameterValue( "'+ m[1] +'", { '+ objValues +' } );');

						if (dbg = document.getElementById('debug')) {
							dbg.innerHTML = 'swf.setParameterValue( "'+ m[1] +'", { '+ objValues +' } );';
							jQuery(dbg)
								.stop()
								.css({ opacity:1 })
								.animate({ opacity:0.3 }, 5000);
						}
					},
					30
				);
			}
		}
		else {
			flashAPI(swf,
				function(swf) {
					swf.setParameterValue( e.id, getValue(e) );

					if (dbg = document.getElementById('debug')) {
						dbg.innerHTML = 'swf.setParameterValue( "'+ e.id +'", "'+ getValue(e) +'" );';
						jQuery(dbg)
							.stop()
							.css({ opacity:1 })
							.animate({ opacity:0.3 }, 5000);
					}
				},
				30
			);
		}
	}
}

jQuery(document).ready(function($) {
	$('#flashSettings input, #flashSettings select, #flashSettings textarea').change(
		function() {
			applyChanges(this);
		}
	);

	$('#flashSettings input[type=checkbox]').click(
		function() {
			applyChanges(this);
		}
	);

	$('#flashSettings input').keypress(
		function(e) {
			if (e.which == 13) {
				applyChanges(this);
				return false;
			}
		}
	);

	$('#flashSettings input.int').keypress(
		function(e) {
			if (e.which != 0 && e.which != 8 && String.fromCharCode(e.which).match(/\D/))
				return false;
		}
	);

	$('#flashSettings').tabs({
		//event: 'mouseover',
		fx: { opacity: 'toggle', duration: 'fast' }
	}).css({ visibility: 'visible' });
});
