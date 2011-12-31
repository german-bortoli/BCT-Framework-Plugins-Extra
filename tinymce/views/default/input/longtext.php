<?php

	global $tinymce_js_loaded;
	
	$input = rand(0,9999);
	
	if (!isset($tinymce_js_loaded)) $tinymce_js_loaded = false;

	if (!$tinymce_js_loaded) {
		
		
		if ($vars['theme_advanced_buttons1']) 
			$vars['theme_advanced_buttons1'] = 'bold,italic,underline,justifycenter,separator,bullist,numlist,separator,spellchecker,undo,redo,link,unlink,image,media,blockquote,code,formatselect,fontselect,fontsizeselect';
	
		if ($vars['valid_elements']) 
			$vars['valid_elements'] = ""
				+"a[href|target],"
				+"b,"
				+"br,"
				+"img[src|id|width|height|align|hspace|vspace],"
				+"i,"
				+"li,"
				+"p[align|class],"
				+"h1,"
				+"h2,"
				+"h3,"
				+"h4,"
				+"h5,"
				+"h6,"
				+"strong,"
				+"u,"
				+"ul,"
				+"ol";
				
		if ($vars['extended_valid_elements']) 
			$vars['extended_valid_elements'] = "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|style],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]";
				
?>
<!-- include tinymce -->
<script language="javascript" type="text/javascript" src="<?php echo $vars['url']; ?>plugins/vendor/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<!-- intialise tinymce, you can find other configurations here http://wiki.moxiecode.com/examples/tinymce/installation_example_01.php -->
<script language="javascript" type="text/javascript">
   tinyMCE.init({
	mode : "textareas",
	theme : "advanced",
	<?php if ($vars['plugins']) { ?>plugins : "<?php echo $vars['plugins'];  ?>",<?php } ?>
	editor_deselector : "mceNoEditor",
	relative_urls : false,
	paste_auto_cleanup_on_paste : true,
	paste_convert_headers_to_strong : false,
	paste_strip_class_attributes : "all",
	paste_remove_spans : true,
	paste_remove_styles : true,
	<?php if ($vars['theme_advanced_buttons1']) { ?>theme_advanced_buttons1 : "<?php echo $vars['theme_advanced_buttons1'];  ?>",<?php } ?>
	<?php if ($vars['theme_advanced_buttons2']) { ?>theme_advanced_buttons2 : "<?php echo $vars['theme_advanced_buttons2'];  ?>",<?php } ?>
	<?php if ($vars['theme_advanced_buttons3']) { ?>theme_advanced_buttons3 : "<?php echo $vars['theme_advanced_buttons3'];  ?>",<?php } ?>
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	<?php if ($vars['valid_elements']) { ?>valid_elements : "<?php echo $vars['valid_elements'];  ?>",<?php } ?>
	<?php if ($vars['extended_valid_elements']) { ?>extended_valid_elements : "<?php echo $vars['extended_valid_elements'];  ?>",<?php } ?>
	content_css : "<?php echo $CONFIG->wwwroot; ?>style.css",
});
function toggleEditor(id) {
if (!tinyMCE.get(id))
	tinyMCE.execCommand('mceAddControl', false, id);
else
	tinyMCE.execCommand('mceRemoveControl', false, id);
}
</script>
<?php

		$tinymce_js_loaded = true;
	}

?>

<!-- show the textarea -->
<textarea class="input-textarea" name="<?php echo $vars['name']; ?>"><?php echo htmlentities($vars['value'], null, 'UTF-8'); ?></textarea> 
<div class="toggle_editor_container"><a class="toggle_editor" href="javascript:toggleEditor('<?php echo $vars['name']; ?>');"><?php echo elgg_echo('tinymce:remove'); ?></a></div>

<script type="text/javascript">
	$(document).ready(function() {
		$('textarea').parents('form').submit(function() {
			tinyMCE.triggerSave();
		});
	});
</script>