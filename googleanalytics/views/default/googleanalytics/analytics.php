<?php
	global $CONFIG;
	
	$ga_code = $CONFIG->google_analytics_code;
	
	if ($ga_code) {
		?>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("<?php echo $ga_code; ?>");
pageTracker._trackPageview();
} catch(err) {}</script>
		<?php
	}
	else if ($CONFIG->debug)
	{
?>
<!-- 
	<?php echo _echo('googleanalytics:nocode'); ?>
 -->
<?php
	}
?>