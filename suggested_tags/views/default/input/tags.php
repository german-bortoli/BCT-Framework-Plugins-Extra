<?php

	if (!$vars['class']) $vars['class'] = "input-tags";
	
	// If endpoint specified then override control
	if ($vars['endpoint']) {
		
		static $suggested_tag_ctl;
		$suggested_tag_ctl++;
		
		if (!$vars['id']) $vars['id'] = "suggested_tag_ctl_$suggested_tag_ctl";
?>		
		<script type="text/javascript">
			$(function() {
				function split(val) {
					return val.split(/,\s*/);
				}
				function extractLast(term) {
					return split(term).pop();
				}
				
				$("#<?php echo $vars['id'] ?>").autocomplete({
					source: function(request, response) {
						$.getJSON("<?php echo $vars['endpoint']; ?>", {
							term: extractLast(request.term)
						}, response);
					},
					search: function() {
						// custom minLength
						var term = extractLast(this.value);
						if (term.length < 2) {
							return false;
						}
					},
					focus: function() {
						// prevent value inserted on focus
						return false;
					},
					select: function(event, ui) {
						var terms = split( this.value );
						// remove the current input
						terms.pop();
						// add the selected item
						terms.push( ui.item.value );
						// add placeholder to get the comma-and-space at the end
						terms.push("");
						this.value = terms.join(", ");
						return false;
					}
				});
			});
		</script>
	
<?php		
	}

	echo view('input/text', $vars);
