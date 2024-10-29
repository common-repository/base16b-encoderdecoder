<?php
/*  
Plugin Name: Base16b Encoder/Decoder
Plugin URI: http://www.base16b.org/plugins/wordpress/
Description: Plugin for Base16b Encoding/Decoding in WordPress
Version: 0.1.3
Author: Andrew Henderson
Author URI: http://base16b.org
License: GPL

Version History:

0.1.3 (13 Aug 2009)
	* Basic version
	
Credits :

The code for the base16b plugin is largely taken from a base64 encoding WordPress plugin created by MrAnderson MD. Thanks to MrAnderson MD for providing a solid code base. 
http://www.mrandersonmd.com/wordpress-plugins/base16b-encoderdecoder-plugin-for-wordpress/
The 0.8.5 version of the base64 plugin was minimally modified to change encoding from base64 to base16b.
(credits by MrAnderson MD)
Most parts of the code are not my creation, they were borrowed from people smarter than me, so I must thank to them.
Thanks to aNieto2k's AntiTroll Plugin for part of the code, because that was my first source when I knew nothing about creating a Wordpress Plugin. (http://www.anieto2k.com/2006/02/08/plugin-antitroll/)
Thanks to Random Snippets for the Javascript replacement script. (http://www.randomsnippets.com/2008/03/07/how-to-find-and-replace-text-dynamically-via-javascript/)
Thanks to Lorelle's Blog for the info on how to search and replace inside a Wordpress database. (http://lorelle.wordpress.com/2005/12/01/search-and-replace-in-wordpress-mysql-database/)
Thanks to MyDigitalLife for the info on how to identify the postID, helping me to solve the bug related to multiple base64 blocks showing on different posts at same time. (http://www.mydigitallife.info/2006/06/24/retrieve-and-get-wordpress-post-id-outside-the-loop-as-php-variable/)
Thanks to Daniel Lorch for the info on how to use AJAX inside the plugin, it was a clarificating example. (http://daniel.lorch.cc/docs/ajax_simple/)
Thanks to Automatic Timezone Plugin for parts of script that adds "Settings" link to Admin Page in Installed Plugins Page. (http://wordpress.org/extend/plugins/automatic-timezone/)
Thanks to Famfamfam for the key icon used for the Admin page. (http://www.famfamfam.com/lab/icons/silk/)

License:

    Copyright 2009  Andrew Henderson  (email : webmaster@base16b.org)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


*/
load_plugin_textdomain('base16b-encoderdecoder', "/wp-content/plugins/base16b-encoderdecoder/");
include_once(ABSPATH . WPINC . '/class-snoopy.php');

wp_b16b_variables();
wp_b16b_init();

add_filter('the_content','wp_b16b_tag');
add_action('wp_head','wp_b16b_add_header');
add_filter('admin_footer', 'wp_b16b_add_js');
add_action('admin_menu','wp_b16b_admin_page');

// Define variables
function wp_b16b_variables(){
define('wp_b16b_wordwrap_default', '55', true);
define('wp_b16b_format_default', 'bq', true);
define('wp_b16b_button_default', __('Base16b Encode', 'base16b-encoderdecoder'), true);
define('wp_b16b_buttons_option_default', 'on', true);
define('wp_b16b_encoding_base_default', '16', true);

define('wp_b16b_wordwrap', 'wp_b16b_wordwrap', true);
define('wp_b16b_format', 'wp_b16b_format', true);
define('wp_b16b_button', 'wp_b16b_button', true);
define('wp_b16b_decode_button', 'wp_b16b_decode_button', true);
define('wp_b16b_buttons_option', 'wp_b16b_buttons_option', true);
define('wp_b16b_encoding_base', 'wp_b16b_encoding_base', true);

$wp_b16b_wordwrap;
$wp_b16b_format;
$wp_b16b_button;
$wp_b16b_decode_button;
$wp_b16b_buttons_option;
$wp_b16b_encoding_base;
}

// Initiates all variables
function wp_b16b_init() {
  $wp_b16b_wordwrap = get_option(wp_b16b_wordwrap);
  if (!$wp_b16b_wordwrap) {
    add_option(wp_b16b_wordwrap, wp_b16b_wordwrap_default);
    add_option(wp_b16b_format, wp_b16b_format_default);
    add_option(wp_b16b_button, wp_b16b_button_default);
    add_option(wp_b16b_title, wp_b16b_title_default);
    add_option(wp_b16b_buttons_option, wp_b16b_buttons_option_default);
    add_option(wp_b16b_encoding_base, wp_b16b_encoding_base_default);
  }
  $wp_b16b_wordwrap = get_option(wp_b16b_wordwrap);
}

// Main function
function wp_b16b_tag($content) {
  if ( strchr($content, '<base16b>') == null ) {
    return $content;
  } else {
    // checks for paired tag
    $retbase16b = '';
    $counta1 = substr_count($content, '<base16b>');
    $counta2 = substr_count($content, '</base16b>');
    if ($counta1 == $counta2) {
	  $arrContent[0] = $content;
	  $counter = $counta1;
	  $countera = $counta1;
	  $whilecount = "1";
     while ($whilecount <= $counter) {
     	while ($countera >= 1) {
        $arrRetVal = explode('<base16b>', $arrContent [0], 2);
        $retbase16b .= $arrRetVal[0];
        $arrRetVal = explode('</base16b>', $arrRetVal[1], 2);
        $retbase16b .= wp_b16b_encode($arrRetVal[0], $whilecount);
        $arrContent[0] = $arrRetVal[1];
        $whilecount++;
        $countera--;
			}
      }
		$retbase16b .= $arrRetVal[1];
      return $retbase16b;
    } else {
      return $content;
    }
  }
}

// Encodes part of the post
function wp_b16b_encode($string_to_encode, $i) {
  $encoded_data = htmlentities($string_to_encode, ENT_QUOTES);
  $wp_b16b_html = wp_b16b_html($encoded_data, $i);
  return $wp_b16b_html;
}

// Gives html format to the encoded text block
function wp_b16b_html($encoded_data, $i) {
  global $wp_query;
  $thePostID = $wp_query->post->ID;
  $wp_b16b_wordwrap = get_option('wp_b16b_wordwrap');
  $wp_b16b_format = get_option('wp_b16b_format');
  $wp_b16b_button = get_option('wp_b16b_button');
  $wp_b16b_decode_button = get_option('wp_b16b_decode_button');
  $wp_b16b_block = wordwrap($encoded_data, $wp_b16b_wordwrap, "<br />\n", 1);
  $retval = "<div id='b16bblock-" . $thePostID . "-" . $i . "'>";
  if ($wp_b16b_format=='bq') {
    $retval .= "<blockquote><p>" . $wp_b16b_block . "</p></blockquote>";
  } elseif ($wp_b16b_format=='cd') {
    $retval .= "<code><p>" . $wp_b16b_block . "</p></code>";
  } else {
    $retval .= "<p>" . $wp_b16b_block . "</p>";
  }
  $retval .= '<input type="button" value="' . $wp_b16b_button . '" id="theButton" name="send" onClick="replaceb16bEncodeText(\'b16bblock-' . $thePostID . '-' . $i . '\', \'' . $encoded_data . '\', \'' . get_option(wp_b16b_encoding_base) . '\', \'' . $wp_b16b_decode_button . '\');">';
  
  return $retval;
}

// Adds Javascript to the header
function wp_b16b_add_header() {
	echo "\n<!-- Start of script generated by WP-Base16b Plugin -->\n";
    echo "<script type=\"text/javascript\" src=\"http://base16b.org/lib/version/0.1/js/base16b.js\"></script>";
	echo "<script language=\"JavaScript\" type=\"text/javascript\">
  
var http = false;

if(navigator.appName == \"Microsoft Internet Explorer\") {
  http = new ActiveXObject(\"Microsoft.XMLHTTP\");
} else {
  http = new XMLHttpRequest();
}

function replaceb16bEncodeText(b16bblock, encstring, encodingBase, buttonText) {
	function h2d(h) {return parseInt(h,16);} ;
	http.abort();
	http.open(\"GET\", \"" . WP_PLUGIN_URL . "/" . plugin_basename(dirname(__FILE__) ) . "/base16b_encode.php?string=\" + encstring, true);
	http.onreadystatechange=function() {
	if(http.readyState == 4) {
		var hexString = http.responseText.replace(/5c22/ig,'22'); // strip off the added backslashes
		var binArr = [];
		for (i=0;i<hexString.length;i++) // prepare binary array as input for Base16b.encode
		{
			var currVal = h2d(hexString.slice(i,i+1));
			for (j=3;j>=0;j--) {
				binArr.push(Math.floor(currVal/Math.pow(2,j))%2);
			}
		}
		var base16bStr 			= Base16b.encode(binArr, encodingBase);
		var EncodeButtonText 	= document.getElementById('theButton').value;
		var DecodeButtonType 	= 'button';
		var DecodeButtonID 		= '\"'+'theButton'+'\"';;
		var DecodeButtonValue 	= '\"'+buttonText+'\"';
		var DecodeButtonTrig	= 'replaceb16bDecodeText('+'\''+b16bblock+'\''+', '+'\''+base16bStr+'\''+', '+'\''+EncodeButtonText+'\''+')';
		var DecodeButtonAppend	= ' ' + Base16b.trueLength(base16bStr) + ' characters';
		var DecodeButtonHTML 	= '<input type = '+DecodeButtonType+' value = '+DecodeButtonValue+' id = '+DecodeButtonID+' onclick= '+'\"'+DecodeButtonTrig+'\"'+'>';

		function align_chars (inStr) { // step through multi-byte character string. Build output string with line breaks
			var outStr = '';
			var i;
			for (i=0; i<= Math.floor(i<Base16b.trueLength(inStr)/20); i++) {
				outStr = outStr + base16bStr.slice(i*60,(i+1)*60) +'<br></br>';
			}
			return outStr;
		};
		document.getElementById(b16bblock).innerHTML = align_chars (base16bStr) + DecodeButtonHTML + DecodeButtonAppend;
	}
  }
  http.send(null);
}

function replaceb16bDecodeText(b16bblock, encstring, buttonText) {
	var binArr = Base16b.decode(encstring);
	var decStr = '';
	
	var decVal;
	for (i=0;i<binArr.length/8;i++) // prepare dec array 
	{
		decVal=0;
		for (j=0; j<8; j++) {
			if (binArr[i*8+j] === 1) decVal += Math.pow(2,7-j);
		}
		if (i>0) decStr = decStr+ '^' ; // delimiter for php explode
		decStr = decStr+ '0' + decVal; 
	}

	http.abort();
	http.open(\"GET\", \"" . WP_PLUGIN_URL . "/" . plugin_basename(dirname(__FILE__) ) . "/base16b_decode.php?string=\" + decStr , true);
	http.onreadystatechange=function() {
		if(http.readyState == 4) {
			var decodedStr 			= http.responseText;
			var DecodeButtonText 	= document.getElementById('theButton').value;
			var EncodeButtonType 	= 'button';
			var EncodeButtonID 		= '\"'+'theButton'+'\"';;
			var EncodeButtonValue 	= '\"'+buttonText+'\"';
			var EncodeButtonTrig	= 'replaceb16bEncodeText('+'\''+b16bblock+'\''+', '+'\''+decodedStr+'\''+', '+'\''+DecodeButtonText+'\''+')';
			var EncodeButtonHTML = '<input type = '+EncodeButtonType+' value = '+EncodeButtonValue+' id = '+EncodeButtonID+' onclick= '+'\"'+EncodeButtonTrig+'\"'+'>';
			document.getElementById(b16bblock).innerHTML = decodedStr + EncodeButtonHTML ;
		}
	}
	http.send(null);
}

</script>";
echo "\n<!-- End of script generated by WP-Base16b Plugin -->\n"; }

// Adds Javascript functions
function wp_b16b_add_js() {
  $b16b_button_option = get_option(wp_b16b_buttons_option);
  if ((strpos($_SERVER['REQUEST_URI'], 'post.php') || strpos($_SERVER['REQUEST_URI'], 'post-new.php') || strpos($_SERVER['REQUEST_URI'], 'page-new.php')) && $b16b_button_option == 'on') {
  	?>
  	<script language="JavaScript" type="text/javascript">
	<!--
          var toolbar = document.getElementById("ed_toolbar");

          if(toolbar) {
            var theDecodeButton = document.createElement('input');
            theDecodeButton.type = 'button';
            theDecodeButton.value = 'base16b';
            theDecodeButton.onclick = 'wp_b16b_add_decode_tag_button()';
            theDecodeButton.className = 'ed_button';
            theDecodeButton.title = 'Insert base16b encoded text block';
            theDecodeButton.id = 'ed_base16b_decode';
            toolbar.appendChild(theDecodeButton);

            var theEncodeButton = document.createElement('input');
            theEncodeButton.type = 'button';
            theEncodeButton.value = 'base16b';
            theEncodeButton.onclick = 'wp_b16b_add_encode_tag_button()';
            theEncodeButton.className = 'ed_button';
            theEncodeButton.title = 'Insert base16b encoded text block';
            theEncodeButton.id = 'ed_base16b_encode';
            toolbar.appendChild(theEncodeButton);
           }

	  function wp_b16b_add_encode_tag_button() {
	    edInsertContent(edCanvas, '<base16b>');
            var theDecodeButton = document.getElementById("ed_base16b_decode");
            theDecodeButton.value = '/base16b';
            theDecodeButton.onclick = wp_b16b_rem_encode_tag_button;
	  }

      function wp_b16b_rem_encode_tag_button() {
        edInsertContent(edCanvas, '</base16b>');
            var theDecodeButton = document.getElementById("ed_base16b_decode");
            theDecodeButton.value = 'base16b';
            theDecodeButton.onclick = wp_b16b_add_encode_tag_button;
          }
		  
	//--></script>
	<?php
  }
}

// Adds Admin page
function wp_b16b_admin_page() {
	global $wp_version;
	if ( current_user_can('manage_options') && function_exists('add_options_page') ) {
	
		$menutitle = '';
		if ( version_compare( $wp_version, '2.6.999', '>' ) ) {
	  		$menutitle = '<img src="'.plugins_url(dirname(plugin_basename(__FILE__))).'/key.png" style="margin-right:4px;" />';
		}

		$menutitle .= __('Base16b Enc/Dec', 'base16b-encoderdecoder');
		add_options_page(__('Base16b Enc/Dec Configuration', 'base16b-encoderdecoder'), $menutitle , 'manage_options', 'wp-b16b-config', 'wp_b16b_config');
		add_filter( 'plugin_action_links', 'wp_b16b_filter_plugin_actions', 10, 2 );
	}
}

// Options page
function wp_b16b_config() {
  if (isset($_POST['update'])) {
    update_option('wp_b16b_wordwrap', $_POST['wp_b16b_wordwrap']);
    update_option('wp_b16b_format', $_POST['wp_b16b_format']);
    update_option('wp_b16b_decode_button', $_POST['wp_b16b_decode_button']);
    update_option('wp_b16b_button', $_POST['wp_b16b_button']);
    update_option('wp_b16b_buttons_option', $_POST['wp_b16b_buttons_option']);
    update_option('wp_b16b_encoding_base', $_POST['wp_b16b_encoding_base']);
    echo "<div style=\"background-color: rgb(207, 235, 247);\" id=\"message\" class=\"updated fade\"><p><strong>".__('Options Updated', 'base16b-encoderdecoder')."</strong></p></div>";
  }
  if (isset($_POST['reset'])) {
    update_option('wp_b16b_wordwrap', wp_b16b_wordwrap_default);
    update_option('wp_b16b_format', wp_b16b_format_default);
    update_option('wp_b16b_button', wp_b16b_button_default);
    update_option('wp_b16b_buttons_option', wp_b16b_buttons_option_default);
    update_option('wp_b16b_encoding_base', wp_b16b_encoding_base_default);
    echo "<div style=\"background-color: rgb(207, 235, 247);\" id=\"message\" class=\"updated fade\"><p><strong>".__('Options Reseted', 'base16b-encoderdecoder')."</strong></p></div>";
  }
  if (($remote = b16b_remote_version_check()) == 1) {
	$b16b_homeurl = wp_b16b_info('homeurl');
	$b16b_homename = wp_b16b_info('homename');
	$b16b_downloadurl = wp_b16b_info('downloadurl') . wp_b16b_info('remoteversion');
  	printf("<div style=\"background-color: rgb(207, 235, 247);\" id=\"message\" class=\"updated fade\"><p>".__('There is a <strong><a href=\"%1$s\" title=\"%2$s\">NEW</a></strong> version available. You can download it <a href=\"%3$s.zip\">HERE</a>', 'base16b-encoderdecoder')."</p></div", $b16b_homeurl, $b16b_homename, $b16b_downloadurl);
  }
  $b16b_format = get_option(wp_b16b_format);
  $b16b_button_option = get_option(wp_b16b_buttons_option);
  $b16b_encoding_base = get_option(wp_b16b_encoding_base);
 ?>
  <div class="wrap">
<?php
echo "<h2>".__('Base16b Encoder/Decoder Options', 'base16b-encoderdecoder')."</h2>";
?>
    <br />
    <form name="wp_b16b_options" method="post">
<?php
echo "<h3>".__('Display Options', 'base16b-encoderdecoder')."</h3>";
?>
	 <table class="form-table">
	 <tr valign="top">
<?php
echo "<th scope=\"row\">".__('Button encode text', 'base16b-encoderdecoder')."</th>";
echo "<td><fieldset><legend class=\"hidden\">".__('Button encode text', 'base16b-encoderdecoder')."</legend><label for=\"button_text\"><input name=\"" . wp_b16b_button . "\" value=\"" . get_option(wp_b16b_button) . "\" size=\"40\" class=\"code\" type=\"text\" /> ";
echo __('Text of the button for encoding', 'base16b-encoderdecoder')."</label><br /></fieldset></td>";
?>
	 <tr valign="top">
<?php
echo "<th scope=\"row\">".__('Button decode text', 'base16b-encoderdecoder')."</th>";
echo "<td><fieldset><legend class=\"hidden\">".__('Button decode text', 'base16b-encoderdecoder')."</legend><label for=\"button_text\"><input name=\"" . wp_b16b_decode_button . "\" value=\"" . get_option(wp_b16b_decode_button) . "\" size=\"40\" class=\"code\" type=\"text\" /> ";
echo __('Text of the button for decoding', 'base16b-encoderdecoder')."</label><br /></fieldset></td>";
?>
    </tr><tr valign="top">
<?php
echo "<th scope=\"row\">".__('Wordwrap', 'base16b-encoderdecoder')."</th>";
echo "<td><fieldset><legend class=\"hidden\">".__('Wordwrap', 'base16b-encoderdecoder')."</legend><label for=\"wordwrap\"><input name=\"" . wp_b16b_wordwrap . "\" value=\"" . get_option(wp_b16b_wordwrap) . "\" size=\"40\" class=\"code\" type=\"text\" /> ";
echo __('How many characters per line you want', 'base16b-encoderdecoder')."</label><br /></fieldset></td>";
?>
    </tr><tr valign="top">
<?php
echo "<th scope=\"row\">".__('Encoding Base', 'base16b-encoderdecoder')."</th>";
echo "<td><fieldset><legend class=\"hidden\">".__('Encoding Base', 'base16b-encoderdecoder')."</legend><label for=\"wordwrap\"><input name=\"" . wp_b16b_encoding_base . "\" value=\"" . get_option(wp_b16b_encoding_base) . "\" size=\"40\" class=\"code\" type=\"text\" /> ";
echo __('Which base in the Base16b family to encode in (7 - 17)', 'base16b-encoderdecoder')."</label><br /></fieldset></td>";
?>
    </tr><tr valign="top">
<?php
echo "<th scope=\"row\">".__('Block Format', 'base16b-encoderdecoder')."</th>";
echo "<td><fieldset><legend class=\"hidden\">".__('Block Format', 'base16b-encoderdecoder')."</legend><label for=\"block_format\"><select name=\"" . wp_b16b_format . "\"><option value=\"bq\"";
if($b16b_format=='bq'){echo ' selected';}
echo ">".__('Blockquote', 'base16b-encoderdecoder')."</option>";
echo "<option value=\"cd\"";
if($b16b_format=='cd'){echo ' selected';}
echo ">".__('Code', 'base16b-encoderdecoder')."</option>";
echo "<option value=\"no\"";
if($b16b_format=='no'){echo ' selected';}
echo ">".__('None', 'base16b-encoderdecoder')."</option></select> ";
echo __('Choose the html format for the text block', 'base16b-encoderdecoder')."</label><br /></fieldset></td>";
?>
    </tr>
    </table>
<?php
echo "<h3>".__('Editing Options', 'base16b-encoderdecoder')."</h3>";
?>
	 <table class="form-table">
	 <tr valign="top">
<?php
echo "<th scope=\"row\">".__('Display Post Buttons', 'base16b-encoderdecoder')."</th>";
echo "<td><fieldset><legend class=\"hidden\">".__('Display Post Buttons', 'base16b-encoderdecoder')."</legend><label for=\"display_post_buttons\">";
echo "<input name=\"" . wp_b16b_buttons_option . "\" type=\"checkbox\" value=\"on\"";
if ($b16b_button_option=='on'){ echo ' checked';}
echo " /> ".__('Hide/unhide the buttons when you edit the post', 'base16b-encoderdecoder')."</label><br /></fieldset></td>";
?>
	</tr>
    </table>

    <p class="submit">
<?php
echo "<input type=\"submit\" name=\"update\" value=\"".__('Update Options', 'base16b-encoderdecoder')."\" />&nbsp; ";
echo "<input type=\"submit\" name=\"reset\" value=\"".__('Reset Options', 'base16b-encoderdecoder')."\" />";

?>  </p>
    </form>
  </div>
  <?php
}

function b16b_remote_version_check() {  
  	$remote = wp_b16b_info('remoteversion');
  	if (!$remote) {
  		return -1;
  	} else {
	  	return version_compare($remote, wp_b16b_info('localeversion'));
	}
}

// Information function
function wp_b16b_info($show = '') {
  switch($show) {
    case 'localeversion':
      $info = '0.1.1';
      break;
    case 'homeurl':
      $info = 'http://base16b.org/';
      break;
	 case 'downloadurl':
	 	$info = 'http://base16b.org/blog/wp-content/plugins/base16b-encoderdecoder.';
	 	break;
    case 'homename':
      $info = 'Andrew Henderson';
      break;
    case 'remoteversionfile':
      $info = 'http://base16b.org/blog/wp-content/plugins/wp-base16b-version.txt';
      break;
	 case 'remoteversion':
	   $info = b16b_remote_version();
	   break;
    default:
      $info = '';
      break;
    }
  return $info;
}

// Checks for new versions
function b16b_remote_version() {
  if (class_exists(snoopy)) {
  	$client = new Snoopy();
  	$client->_fp_timeout = 4;
  	if ($client->fetch(wp_b16b_info('remoteversionfile')) === false ) {
		return -1;
	}
	$remote = $client->results;
	return $remote;
	}
}

function wp_b16b_filter_plugin_actions($links, $file){
	static $this_plugin;

	if( !$this_plugin ) $this_plugin = plugin_basename(__FILE__);

	if( $file == $this_plugin ) {
		$settings_link = '<a href="admin.php?page=wp-b16b-config">' . __('Settings', 'base16b-encoderdecoder') . '</a>';
		$links = array_merge( array($settings_link), $links); // before other links
	}
	return $links;
}

?>
