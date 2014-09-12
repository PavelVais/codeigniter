
/*
 pava_loader.js
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
 
 Mozne varianty uziti:
 - Na button odesilani formulare,
 - Na jakykoli jiny prvek
 - Jako maska na cely prvek (vyuziva effect.js (ale nemusi!))
 
 
 */
(function($) {
	$.fn.loader = function(options) {

		if (typeof options === 'string')
		{
			options = {
				type: options
			};
		}
		var settings = $.extend({
			icon: 'fa-spinner fa-spin',
			loadingText: 'načítám',
			disabled: true,
			disabledClass: 'disabled',
			maskClass: '', // Dodatecna loading mask trida (pro jine stylizovani)
			type: 'inline', // inline - misto textu, mask - vlozi se cela loadovaci maska
			isEffectSupported: css3_engine !== undefined
		}, options);
		var start = function(t) {

			if (t.data('pavaloader') !== true)
			{
				init(t);
				if (settings.type === 'inline')
					start_inline(t);

				if (settings.type === 'mask')
					start_mask(t);

			} else {
				destroy(t);
			}


		}
		var start_inline = function(t)
		{
			t.data('pavahistory', t.html());
			t.html('<i class="fa ' + settings.icon + '"></i> ' + settings.loadingText);
			if (settings.disabled)
				t.addClass(settings.disabledClass);
		}

		var start_mask = function(t)
		{
			var elem = '<div class="pava-loader-mask"><span class="pava-loader-holder"><i class="fa ' + settings.icon + '"></i> ' + settings.loadingText + '</span></div>';
			t.addClass('masked');
			t.append(elem);
			t = t.find('.pava-loader-mask');

			$(window).on('resize.pavaloader', {parent: t}, mask_center).trigger('resize');

			if (settings.isEffectSupported)
			{
				t.addClass('scale-0');
				setTimeout(function() {
					css3_engine.scaleIn(t);
				}, 10);

			}
			else
				t.hide().fadeIn();
		}
		var init = function(t)
		{
			t.data('pavaloader', true).data('pavatype', settings.type);
		}

		var mask_center = function(event)
		{
			console.log("res");
			parent = event.data.parent;
			var pw = parent.outerWidth();
			var ph = parent.outerHeight();
			var h = parent.find('.pava-loader-holder');
			h.css({
				left: (pw / 2) - (h.outerWidth() / 2),
				top: (ph / 2) - (h.outerHeight() / 2)
			});

		}

		var destroy = function(t)
		{
			$('#element').loader = null;
			t.data('pavaloader', false);
			if (t.data('pavatype') === 'inline')
			{
				if (settings.disabled)
					t.removeClass(settings.disabledClass);
				t.html(t.data('pavahistory'));
			} else {

				$(window).off('resize.pavaloader');
				t = t.removeClass('masked').find('.pava-loader-mask');
				if (settings.isEffectSupported)
				{
					setTimeout(function() {
						css3_engine.scaleOut(t).done(function() {
							t.remove();
						});
					}, 10);
				}
				else
					t.fadeOut(function() {
						t.remove();
					});
			}

		}

		this.each(function() {
			var t = $(this);
			console.log("nacitam start");
			start(t);
		});
		return this;
	};
}(jQuery));