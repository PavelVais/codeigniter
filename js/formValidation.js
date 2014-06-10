/*
 FormValidator.js
 Copyright (c) 2014 Pavel Vais (http://www.pavelvais.cz) 
 
 Permission is hereby granted, free of charge, to any person
 obtaining a copy of this software and associated documentation
 files (the "Software"), to deal in the Software without
 restriction, including without limitation the rights to use,
 copy, modify, merge, publish, distribute, sublicense, and/or sell
 copies of the Software, and to permit persons to whom the
 Software is furnished to do so, subject to the following
 conditions:
 
 The above copyright notice and this permission notice shall be
 included in all copies or substantial portions of the Software.
 
 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 OTHER DEALINGS IN THE SOFTWARE.
 */
(function($) {
	$.fn.validate = function(options) {

		var settings = $.extend({
			showTooltip: true,
			tooltipPosition: 'right',
			ajax: false, // Pokud je ajax, vzdy vraci FALSE, serializuje form a odesle na adresu uvedenou ve formulari
			ajaxText: "Odesílám...",
			validateMsg: "Validuji formulář...",
			defaultSubmitOnSuccess: "Uloženo",
			customValidation: undefined,
			onShowError: undefined,
			onBeforeCheck: undefined,
			onSuccess: undefined,
			onFail: undefined
		}, options);

		var ruleCache = {
			container: {},
			add: function(inputName, ruleName, value, message, ref, arg)
			{
				if (typeof this.container[inputName] === 'undefined')
					this.container[inputName] = {};
				ruleName = ruleName.substring(10).toLowerCase();
				this.container[inputName][ruleName] = {
					value: value,
					message: message,
					ref: ref,
					arg: arg
				};
			}
		};

		var validator = {
			filled: function(value) {
				return value == '' ? false : true;
			},
			same: function(value, ref, arg) {
				return $('input[name=' + arg + ']').val() == value ? true : false;
			},
			number: function(value) {
				for (var i = 0; i < value.length; ++i) {
					var new_key = value.charAt(i);
					if (((new_key < "0") || (new_key > "9")) &&
						   !(new_key == ""))
						return false;
				}
				return true;
			},
			email: function(value) {
				if (value == '')
					return true;
				var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				return re.test(value);
			},
			checked: function(value, ref) {
				return ref.attr('checked') === undefined ? false : true;
			},
			phone: function(value) {
				for (var i = 0; i < value.length; ++i)
				{
					var new_key = value.charAt(i); //cycle through characters
					if (i == 0 && new_key === "+")
						continue;
					if (((new_key < "0") || (new_key > "9")) &&
						   !(new_key === ""))
						return false;
				}
				return true;
			},
			min_chars: function(value, ref, arg) {
				return value.length >= arg ? true : false;
			},
			ajax: function(value, ref, arg)
			{
				return $.ajax({
					url: arg[0],
					data: {value: value},
					type: "post",
					timeout: 8000
				});
			}
		};

		if (settings.customValidation !== undefined)
			validator = jQuery.extend(validator, settings.customValidation);


		var methods = {
			//= Metoda na vycisteni promennych
			clear: function() {
				ruleCache.container = {};
			},
			//= Ziska z data atributu vse potrebne pro urceni pravidel
			stringFilter: function(string, value)
			{
				if (string.substring(0, 10) != 'validation')
					return false;
				string = string.substring(10).toLowerCase();
				var message = (value != null && value != "") ? value.split('::') : '';
				arg = message.length > 1 ? message.splice(1, message.length) : undefined;
				return [message[0], arg];
			},
			/**
			 * Pripravi formular a vyzjisti vsechny potrebna pravidla
			 * @param {jquery element} e (form element)
			 * @returns {Boolean} - FALSE = neobsahuje zadne validacni prvky
			 */
			formPrepare: function(e) {
				var notEmpty = false;
				var form_objects = e.find(':input').get();
				for (i = 0; i < form_objects.length; i++)
				{
					var e = $(form_objects[i]);
					var ename = e.attr('name');
					var data = e.data();
					if (jQuery.isEmptyObject(data))
						continue;

					jQuery.each(data, function(name, value) {
						var result = methods.stringFilter(name, value);
						if (result !== false)
						{
							notEmpty = true;
							ruleCache.add(ename, name, e.val(), result[0], e, result[1]);
						}
					});
				}
				return notEmpty;
			},
			/**
			 * 
			 * @param {jquery element} formElement
			 * @returns {Deferred}
			 */
			validate: function(formElement) {
				var cache = ruleCache.container;
				formElement.addClass('val-disabled');
				methods.changeSubmitText(settings.validateMsg)

				var $deferreds = [];

				for (var rules in cache) {
					for (var rule in cache[rules]) {
						$deferreds.push(methods.validateSingleElement(rule, cache[rules][rule]));
					}
				}

				$finalDeferred = $.Deferred();

				$.when.apply($, $deferreds).then(function() {
					$finalDeferred.resolve();
				}, function(e, a) {
					$finalDeferred.reject();
				}).always(function() {
					formElement.removeClass('val-disabled');
					methods.changeSubmitText('');
				});
				return $finalDeferred.promise();
			},
			//= Validace jednotliveho elementu
			validateSingleElement: function(ruleName, ruleArgs) {
				var drd = new $.Deferred();
				if (validator[ruleName] === undefined)
				{
					console.error('Validation rule "' + ruleName + '" is not defined!');
					return drd.resolve();
				} else {

					var status = validator[ruleName](ruleArgs.value, ruleArgs.ref, ruleArgs.arg);

					//= is status deffered object?
					if (status.promise)
					{
						methods.addLoading(ruleArgs.ref);
						status.always(function() {
							methods.removeLoading(ruleArgs.ref);
						});
						return status.promise();
					} else {
						if (!status)
						{
							methods.showError(ruleArgs.ref, ruleArgs.message);
							return drd.reject(ruleArgs.ref);
						}
						return drd.resolve();
					}
				}
			},
			showError: function(element, message)
			{
				if (settings.onShowError !== undefined)
				{
					settings.onShowError(element, message);
					return;
				}
				var p = element.closest('.form-group');
				p.addClass('has-error has-feedback');
				element.after('<span class="fa fa-warning form-control-feedback" data-toggle="tooltip" data-placement="' + settings.tooltipPosition + '" title="' + message + '"></span>');
				if ($.fn.tooltip)
				{
					p.find('.form-control-feedback').tooltip('show');
				}

			},
			addLoading: function(element)
			{
				var p = element.closest('.form-group');
				p.addClass('has-feedback');
				element.after('<span class="fa fa-spinner fa-spin form-control-feedback"></span>');
			},
			removeLoading: function(element)
			{
				element.closest('.form-group').find('.fa-spinner').remove();
			},
			removeErrors: function(formElement)
			{
				var e = formElement.find('.has-error').removeClass('has-error has-feedback').find('span.form-control-feedback')
				if ($.fn.tooltip)
				{
					e.tooltip('destroy');
					e.remove();
				}
			},
			changeSubmitText: function(text)
			{
				methods.submitEl.val(text ? text : methods.submitEl.data('prevVal'));
			}
		}

		/**
		 * Hlavni funkce ktera ...
		 * @param {type} el
		 * @returns {undefined} */
		var process = function(el)
		{
			//= Odstranime predesle errory
			methods.removeErrors(el);

			// Vyparsujeme vsechny pravidla
			if (!methods.formPrepare(el))
			{
				//= Formular nema zadna pravidla, tak ho spustime
				submit(el);
				return;
			}

			if (typeof settings.onBeforeCheck === 'function')
				if (settings.onBeforeCheck(ruleCache.container) === false)
					return false;


			//= Submit button cache init
			methods.submitEl = el.find('input[type="submit"]');
			methods.submitEl.data('prevVal', methods.submitEl.val());

			//= Main magic is here>>
			var dfd = methods.validate(el);

			dfd.done(function() {
				submit(el);
			}).fail(function() {
				if (settings.onFail !== undefined)
					settings.onFail();
			});
			console.timeEnd('init');
		}

		var submit = function(el)
		{
			if (settings.ajax)
			{
				$.ajax({
					url: el.attr("action"),
					type: "post",
					data: el.serialize(),
					beforeSend: function() {
						methods.changeSubmitText(settings.ajaxText);
						el.addClass('val-disabled');
					},
					success: function(data)
					{
						try {
							methods.changeSubmitText(settings.defaultSubmitOnSuccess);
							if (settings.onSuccess !== undefined)
							{
								data = jQuery.parseJSON(data);
								if (settings.onSuccess(data) === false)
									return false;
							}
							el.trigger('reset');

						}
						catch (e) {
						}
					},
					error: function(e, text) {
						methods.changeSubmitText('');
						if (settings.onFail !== undefined)
							settings.onFail(text);
					},
					complete: function() {
						el.removeClass('val-disabled');
					}
				});
			} else {
				el.off('validationSubmit').data('canSubmit', true);
				el.submit();
			}
		}

		this.each(function() {
			var t = $(this);
			console.log(t);
			t.on("validationSubmit", function(e) {
				if (t.hasClass('val-disabled'))
					return false;
				e.preventDefault();
				process(t);
			}).on('submit', function(e) {
				t.trigger('validationSubmit');
				if (!t.data('canSubmit'))
					e.preventDefault();
			});

		});
		return this;
	};
}(jQuery));