<?PHP

	// Google Analytics API setup:
	if($config['google_analytics'][0] == true AND isset($config['google_analytics'][1])) {
		$google_analytics_content = '
		<!--Google Analytics-->
		<script type="text/javascript">
			var _gaq = _gaq || [];
			_gaq.push([\'_setAccount\', \''.$config['google_analytics'][1].'\']);
			_gaq.push([\'_trackPageview\']);

			(function() {
				var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;
				ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';
				var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);
			})();
		</script>
		';
	}

?>