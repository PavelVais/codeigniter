var notifier = {
	jq: undefined,
	timeout: undefined,
	text: function(text,error) {
		if (!$('#notifier').length)
			notifier.inject();
		
		
		error = error === undefined || error === true ? 'alert-success' : 'alert-danger';
		console.log(error);
		notifier.changeClass(error);
		notifier.jq.empty().html('<p><i class="fa '+(error == 'alert-danger' ? 'fa-danger' : 'fa-thumbs-o-up')+'"></i> '+text+'</p>');
		
		if (notifier.timeout !== undefined)
		{
			clearTimeout(notifier.timeout);
			css3_engine.scaleOut(notifier.jq).done(function(){
				notifier.fadeIn();
			});
		} else {
			notifier.fadeIn();
		}
		
	},
	fadeIn: function()
	{
		css3_engine.scaleIn(notifier.jq).done(function(){
			notifier.timeout = setTimeout(function(){
				css3_engine.scaleOut(notifier.jq);
				notifier.timeout = undefined;
			},6000);
		});
	},
	changeClass: function(className)
	{
		notifier.jq.removeClass('alert-success alert-danger').addClass(className);
	},
	inject: function() {
		$('#wrapper nav').append('<div id="notifier" class="scale-0"></div>');
		notifier.jq = $('#notifier');
	}
}

$(document).ready(function() {
	notifier.inject();	
});