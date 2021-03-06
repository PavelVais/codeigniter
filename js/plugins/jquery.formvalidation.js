/**
 * @author Pavel Vais
 * tento javascript se stara o automatickou validaci u kazdeho formu
 * -> pokud ma jakykoli form prvek na sobe dany data-validation-XY tak se zvaliduje
 */

$(document).ready(function() {
	var cachedValues = new Array;
	var errors = new Array;

	$('body').on("submit", "form", function(e) {
		//return form_validation($(this), e);
	});
});

function form_validation(t, e) {
	cachedValues = [];
	errors = [];

	elements = t.find(':input').get();

	if (t.data("validationrender") !== undefined)
		$(t.data("validationrender")).find('.flash').remove();
	$('form').find('.flash').remove();

	if (elements.length == 0)
		return false;

	prepareData(elements);

	if (cachedValues.length == 0)
		return true;

	jQuery.each(cachedValues, function(name, value) {
		v = value.value;
		ref = value.ref;
		n = name;

		delete value.value;
		delete value.ref;

		//= Kdyz je vse pripraveno - zacne validovat
		jQuery.each(value, function(name, value) {
			arg = (value != null && value != "") ? value.split('::') : '';
			arg = arg.length > 1 ? arg[1] : null;
			if (!validate(name, v, ref, arg))
			{
				var error = new Array();
				value = value != null ? value.split('::') : '';
				value = value.length > 1 ? value[0] : value;
				error["value"] = value;
				error["ref"] = ref;
				errors.push(error);
			}
		});
	});
	if (t.data('validationcallback') !== undefined && errors.length > 0)
	{

		window[t.data('validationcallback')](errors);
		if (e !== undefined)
			e.stopImmediatePropagation()
		return false;

	} else
	if (errors.length > 0 && errors.length < 3)
	{
		jQuery.each(errors, function(name, value) {

			if (t.data("validationrender") !== undefined)
			{
				t = $(t.data("validationrender"));
			}


			t.prepend('<div class="flash error">' + value.value + "</div>");
			value.ref.parent().prev().find('label').css({
				color: '#D8000C'
			});
		});
		goToByScroll(t);
		if (e !== undefined)
			e.stopImmediatePropagation();
		return false;
	} else if (errors.length >= 3)
	{
		string = '<div class="flash error"><ul class="error">';

		jQuery.each(errors, function(name, value) {
			string += "<li>" + value.value + "</li>";
			value.ref.parent().prev().find('label').css({
				color: '#D8000C'
			});
		});
		string += "</ul></div>";

		if (t.data("validationrender") !== undefined)
		{
			t = $(t.data("validationrender"));
		}

		t.prepend(string);
		goToByScroll(t);
		if (e !== undefined)
			e.stopImmediatePropagation();
		return false;
	} else {
		return true;
	}

}
;

function prepareData(objs)
{
	for (i = 0; i < objs.length; i++)
	{
		e = $(elements[i]);
		data = e.data();

		e.attr('style', ''); // vymazani predchozich erroru
		e.parent().prev().find('label').attr('style', '');

		if (jQuery.isEmptyObject(data))
			continue;

		var e = {
			value: e.val(),
			ref: e
		};

		jQuery.each(data, function(name, value) {
			if (name.substring(0, 10) != 'validation')
				return;
			e[name.substring(10).toLowerCase()] = value;
		});
		saveToCache(e);
	}
}
function validate(type, value, references, arguments)
{
	switch (type) {
		case 'filled':
			return value == '' ? false : true;
			break;
		case 'same':
			return $('input[name=' + arguments + ']').val() == value ? true : false;
			break;
		case 'number':
			for (var i = 0; i < value.length; ++i)
			{
				var new_key = value.charAt(i); //cycle through characters

				if (((new_key < "0") || (new_key > "9")) &&
					   !(new_key == ""))
				{
					return false
				}
			}
			return true;
			break;
		case 'email':
			if (value == '')
				return true;
			var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			return re.test(value);
			break;
		case 'checked':
			return (references.attr('checked'))
			break;
		case 'phone':
			for (var i = 0; i < value.length; ++i)
			{
				var new_key = value.charAt(i); //cycle through characters
				if (i == 0 && new_key == "+")
					continue;

				if (((new_key < "0") || (new_key > "9")) &&
					   !(new_key == ""))
				{
					return false
				}
			}
			return true;
			break;
		case 'min_chars':
			if (arg == null)
				arg = 4;
			return value.length >= arg ? true : false;
			break;

	}
}

function saveToCache(arguments)
{
	cachedValues.push(arguments);
}

function goToByScroll(jquery_object,offset) {
	// Remove "link" from the ID
	// Scroll
	$('html,body').animate({
		scrollTop: $(jquery_object).offset().top - (offset !== undefined ? offset : 40)
	},
	'slow');
}
