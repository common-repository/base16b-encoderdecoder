<?php
function wp_b16b_ajaxencode($decodedtext) {
$formatedtext = wp_b16b_format_text($decodedtext);
return $formatedtext;
}

function wp_b16b_format_text($decodedtext) {
  $retval =bin2hex($decodedtext);
  return $retval;
}

echo wp_b16b_ajaxencode($_REQUEST['string']);


?>
