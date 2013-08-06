(function($) {

	$.confirm = function(params) {

		if ($('#confirmOverlay').length) {
			// A confirm is already shown on the page:
			return false;
		}

		var buttonHTML = '';
		$.each(params.buttons, function(name, obj) {

			// Generating the markup for the buttons:

			buttonHTML += '<a href="#" class="btn btn-primary ' + obj['class'] + '">' + obj['label'] + '<span></span></a>';

			if (!obj.action) {
				obj.action = function() {
				};
			}
		});

		var markup = [
			'<div id="confirmOverlay">',
			'<div id="confirmBox">',
			'<h1>', params.title, '</h1>',
			'<p>', params.message, '</p>',
			'<div class="clear"></div>',
			'<div id="confirmButtons">',
			buttonHTML,
			'<div class="clearfix"></div>',
			'</div></div></div>'
		].join('');

		$(markup).hide().appendTo('body').fadeIn();

		var buttons = $('#confirmBox .btn'),
			   i = 0;

		$.each(params.buttons, function(name, obj) {
			buttons.eq(i++).click(function() {

				// Calling the action attribute when a
				// click occurs, and hiding the confirm.

				obj.action();
				$.confirm.hide();
				return false;
			});
		});
	}

	$.confirm.hide = function() {
		$('#confirmOverlay').fadeOut(function() {
			$(this).remove();
		});
	}

})(jQuery);
(function($) {
	$.fn.confirm = function(params) {
		/* params:
		 * ajax : true / false
		 * onSucess : fcn
		 * onSubmit : fcn
		 * parent : jquery element = NON-ajax rodic
		 * message : string
		 * title : string
		 */
		t = $(this);
		defaults = {
			ajax: t.hasClass("ajax"),
			title: "Potvrzovací dialog",
			parent: t,
			okLabel: "Ano",
			cancelLabel: "Ne"
		}

		params = jQuery.extend({}, defaults, params);
		if (params.parent.selector === t.selector)
			t.selector = null;

		params.parent.on('click', t.selector, params, function(event) {
			t = $(this);
			url = t.attr("href") === undefined ? t.data("url") : t.attr("href");
			p = event.data;
			ajax = p.ajax;
			message = p.message === undefined ? t.data("message") : p.message;
			onSuccess = p.onSuccess;
			onSubmit = p.onSubmit;
			event.preventDefault();
			$.confirm({
				'title': p.title,
				'message': message,
				'buttons': {
					'Ano': {
						label: p.okLabel,
						'class': 'btn ok',
						'action': function() {
							if (onSubmit !== undefined)
							{
								onSubmit(t);
							} else
							if (ajax)
							{
								$.post(url, function(data) {
									if (onSuccess === undefined)
										try {
											data = jQuery.parseJSON(data);
											ci.showNotice(data.response, data.status);
										} catch (e) {
										}
									onSuccess(jQuery.parseJSON(data), t);
								});
							} else
							if (t && t.attr("href")) {
								var length = String(t.attr("href")).length;
								if (t.attr("href").substring(length - 1, length) != '#')
									document.location = t.attr("href");
							} else {
								t.closest("form").trigger("submit");
							}
						}
					},
					'Ne': {
						label: p.cancelLabel,
						'class': 'btn cancel',
						'action': function() {
						}	// Nothing to do in this case. You can as well omit the action property.
					}
				}
			});

		});

	}
})(jQuery);