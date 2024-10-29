<?php
function wp_b16b_ajaxdecode($decStr) {
$formatedtext = wp_b16b_format_text($decStr);
return $formatedtext;
}
function wp_b16b_format_text($decStr) {

	$arrDelim = '^';
	$decArr = explode($arrDelim ,$decStr);
	$currChar = "0";
	while ($currChar <= count($decArr)) {
		$retval = $retval . html_entity_decode (chr($decArr[$currChar++]), ENT_QUOTES, "UTF-8");
	}
  return $retval;
}

echo wp_b16b_ajaxdecode($_REQUEST['string']);

?>
