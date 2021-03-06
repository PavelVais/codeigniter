/**
 * @name Message plugin
 * @contains: jquery.timer plugin
 * @author Pavel Vais
 * @version 1.0
 * Toto je balicek se vsim, co je potreba k informovani uzivatele
 * Tento balicek obsahuje jquery plugin timer, ke kteremu se 
 * pristupuje pomoci funkci "everyTime" , "oneTime" , "stopTime"
 */

/**
 * Plugin Jquery Timer
 * @name jquery.timer.min.js
 */
jQuery.extend({
	timer: {
		guid: 1,
		global: {},
		regex: /^([0-9]+)\s*(.*s)?$/,
		powers: {
			ms: 1,
			cs: 10,
			ds: 100,
			s: 1000,
			das: 10000,
			hs: 100000,
			ks: 1000000
		},
		timeParse: function(c) {
			if (c == undefined || c == null) {
				return null
			}
			var a = this.regex.exec(jQuery.trim(c.toString()));
			if (a[2]) {
				var b = parseInt(a[1], 10);
				var d = this.powers[a[2]] || 1;
				return b * d
			} else {
				return c
			}
		},
		add: function(e, c, d, g, h, b) {
			var a = 0;
			if (jQuery.isFunction(d)) {
				if (!h) {
					h = g
				}
				g = d;
				d = c
			}
			c = jQuery.timer.timeParse(c);
			if (typeof c != "number" || isNaN(c) || c <= 0) {
				return
			}
			if (h && h.constructor != Number) {
				b = !!h;
				h = 0
			}
			h = h || 0;
			b = b || false;
			if (!e.$timers) {
				e.$timers = {}
			}
			if (!e.$timers[d]) {
				e.$timers[d] = {}
			}
			g.$timerID = g.$timerID || this.guid++;
			var f = function() {
				if (b && this.inProgress) {
					return
				}
				this.inProgress = true;
				if ((++a > h && h !== 0) || g.call(e, a) === false) {
					jQuery.timer.remove(e, d, g)
				}
				this.inProgress = false
			};

			f.$timerID = g.$timerID;
			if (!e.$timers[d][g.$timerID]) {
				e.$timers[d][g.$timerID] = window.setInterval(f, c)
			}
			if (!this.global[d]) {
				this.global[d] = []
			}
			this.global[d].push(e)
		},
		remove: function(c, b, d) {
			var e = c.$timers, a;
			if (e) {
				if (!b) {
					for (b in e) {
						this.remove(c, b, d)
					}
				} else {
					if (e[b]) {
						if (d) {
							if (d.$timerID) {
								window.clearInterval(e[b][d.$timerID]);
								delete e[b][d.$timerID]
							}
						} else {
							for (var d in e[b]) {
								window.clearInterval(e[b][d]);
								delete e[b][d]
							}
						}
						for (a in e[b]) {
							break
						}
						if (!a) {
							a = null;
							delete e[b]
						}
					}
				}
				for (a in e) {
					break
				}
				if (!a) {
					c.$timers = null
				}
			}
		}
	}
});
if (jQuery.browser.msie) {
	jQuery(window).one("unload", function() {
		var d = jQuery.timer.global;
		for (var a in d) {
			var c = d[a], b = c.length;
			while (--b) {
				jQuery.timer.remove(c[b], a)
			}
		}
	})
}
;

jQuery.fn.extend({
	everyTime: function(interval, label, fn, times, belay) {
		return this.each(function() {
			jQuery.timer.add(this, interval, label, fn, times, belay);
		});
	},
	oneTime: function(interval, label, fn) {
		return this.each(function() {
			jQuery.timer.add(this, interval, label, fn, 1);
		});
	},
	stopTime: function(label, fn) {
		return this.each(function() {
			jQuery.timer.remove(this, label, fn);
		});
	}
});

jQuery.extend({
	showMessage: function(message, type, duration)
	{
		if (!duration)
			var duration = 5;
		var panel_obj = $('#jqeasypanel');

		if (panel_obj.length == 0)
		{
			$('body').prepend('<div id="jqeasypanel"><div id="message-content"></div></div>');
			panel_obj = $('#jqeasypanel');
		}
		var string = '<div class="' + type + '" style="margin: 0;">' + message + '</div>';
		if (panel_obj.css('top') == '0px')
		{
			$('#jqeasypanel #message-content div').stop().fadeOut(300, function() {
				$(this).parent().empty().delay(150).append(string).find('div').hide().stop().fadeIn(300);
				$('#jqeasypanel').stopTime().oneTime(duration + "s", function() {
					panel_obj.slideUp(300);
				});
			});
		} else {
			$('#jqeasypanel #message-content').empty().append(string);
			panel_obj.slideDown(300);
			$('a.openpanel').trigger('click');


			$('#jqeasypanel').oneTime(duration + "s", function() {
				//$('a.closepanel').trigger('click');
				panel_obj.slideUp(300);
			});
		}
	},
	closeMessage: function()
	{
		$('#jqeasypanel').slideUp(300).stopTime();
	}

})

