var css3_engine = {
	scaleOut: function(jqElement, speed) {
		console.log("scale out");
		return css3_engine.animator(jqElement, 'scale-1 '+(speed === 'fast' ?  't' : 't-fast'),'scale-0 '+(speed === 'fast' ?  't-fast' : 't'),speed === 'fast' ? 150 : 350);
	},
	scaleIn: function(jqElement, speed) {
		console.log('scaleIn');
		return css3_engine.animator(jqElement, (speed === 'fast' ?  't' : 't-fast')+' scale-0',(speed === 'fast' ?  't-fast' : 't')+' scale-1',speed === 'fast' ? 150 : 350);
	},
	vibrate: function(jqElement) {
		console.log('scaleIn');
		jqElement.addClass('t');
		return css3_engine.animator(jqElement,'slide-left','slide-right',40 ).done(function(){
			css3_engine.animator(jqElement,'slide-right','slide-left',50 ).done(function(){
				css3_engine.animator(jqElement,'slide-left','slide-right',40 ).done(function(){
					jqElement.removeClass('slide-right');
				});
			});
		});
	},
	flash: function(element, color, isFont)
	{
		var bg = isFont ? 'color' : 'backgroundColor';
		element.addClass('t').css(bg, color);
		var $d = $.Deferred();
		setTimeout(function() {
			element.css(bg, '');
			$d.resolve();
		}, 250);
		return $d.promise();
	},
	animator: function(jqElement,rc,ac,promiseTimeOut)
	{
		jqElement.removeClass(rc).addClass(ac);
		var $d = $.Deferred();
		setTimeout(function() {
			console.log("resolve");
			$d.resolve();
		}, promiseTimeOut);
		return $d.promise();
	}
}