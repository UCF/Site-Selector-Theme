/******************************************************************************\
 Global Namespace
 the only variable exposed to the window should be Webcom
\******************************************************************************/
var Webcom = {};

if(!window.console ) { window.console = { log: function() { return; } }; }

// for jslint validation
/*global window, document, Image, google, $, jQuery */

Webcom.analytics = function($){
	if ((typeof GA_ACCOUNT !== 'undefined') && Boolean(GA_ACCOUNT)){
		(function(){
			var ga   = document.createElement('script');
			ga.type  = 'text/javascript';
			ga.async = true;
			ga.src   = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s    = document.getElementsByTagName('script')[0];
			s.parentNode.insertBefore(ga, s);
		})();
	}
};

Webcom.handleExternalLinks = function($){
	$('a:not(.ignore-external)').each(function(){
		var url  = $(this).attr('href');
		var host = window.location.host.toLowerCase();

		if (url && url.search(host) < 0 && url.search('http') > -1){
			$(this).attr('target', '_blank');
			$(this).addClass('external');
		}
	});
};
