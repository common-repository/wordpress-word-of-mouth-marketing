<?php
/*
Plugin Name: WordPress Word of Mouth Marketing
Plugin URI: http://daverigotti.com/wordpress-word-of-mouth-marketing-plugin
Description: Adds a link to bookmark, tell a friend, and subscribe to rss feed in footer of posts, pages, and more.  Easily get your visitors to share your blog!  Built using the structure of <a href="http://www.dagondesign.com/articles/add-signature-plugin-for-wordpress/"> this plugin</a> and released with permission.
Author: Dave Rigotti
Version: 1.0
Author URI: http://daverigotti.com
*/

$womm_version = '1.0';


// Setup defaults if options do not exist

add_option('womm_data', '');	// Signature data
add_option('womm_sindex', FALSE);	// Show on index
add_option('womm_sposts', TRUE);	// Show on posts
add_option('womm_spages', TRUE);	// Show on pages
add_option('womm_sarc', FALSE);	// Show on archives
add_option('womm_ssearch', FALSE);	// Show on search





function womm_add_option_pages() {
	if (function_exists('add_options_page')) {
		add_options_page("Add Sig", 'WP-WOMM', 8, __FILE__, 'womm_options_page');
	}		
}

function womm_trim_sig($sig) {
	return trim($sig, "*");
}


function womm_options_page() {

	global $womm_version;


	if (isset($_POST['set_defaults'])) {
		echo '<div id="message" class="updated fade"><p><strong>';

		update_option('womm_data', '');	// Signature data
		update_option('womm_sindex', FALSE);	// Show on index
		update_option('womm_sposts', TRUE);	// Show on posts
		update_option('womm_spages', TRUE);	// Show on pages
		update_option('womm_sarc', FALSE);	// Show on archives
		update_option('womm_ssearch', FALSE);	// Show on search

		echo 'Default Options Loaded!';
		echo '</strong></p></div>';

	} else if (isset($_POST['info_update'])) {

		echo '<div id="message" class="updated fade"><p><strong>';

		update_option('womm_data', '*' . (string)$_POST["womm_data"] . '*');
		update_option('womm_sindex', (bool)$_POST["womm_sindex"]);
		update_option('womm_sposts', (bool)$_POST["womm_sposts"]);
		update_option('womm_spages', (bool)$_POST["womm_spages"]);
		update_option('womm_sarc', (bool)$_POST["womm_sarc"]);
		update_option('womm_ssearch', (bool)$_POST["womm_ssearch"]);

		echo 'Configuration Updated!';
		echo '</strong></p></div>';

	} ?>

	<div class=wrap>

	<h2>Word of Mouth Marketing v<?php echo $womm_version; ?></h2>


	<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
	<input type="hidden" name="info_update" id="info_update" value="true" />

	<fieldset class="options"> 
	<legend>WOMM plugin adds a link to the footer of each post and page (default) that allows visitors to bookmark, tell friends, and subscribe to the blog.</legend>

	<div style="padding: 0 0 0 0px;">


<br /><br /><center>	<input type="submit" name="info_update" value="<?php _e('CLICK HERE TO ACTIVATE PLUGIN'); ?> &raquo;" /></center><br /><br />
<img src="http://daverigotti.com/wp-includes/images/rss.png"> <a href="http://daverigotti.com/feed" target="_blank"><b>Subscribe To My RSS Feed</b></a> - I write about blogging, search, social media, word of mouth, and more.
<br /><br />
<p><b>The code and options:</b><br />
		<textarea name="womm_data" cols="110" rows="5"><b>Did you like this?</b>  If so, please <script type="text/javascript">addthis_pub  = '';</script><a href="http://www.addthis.com/bookmark.php" onmouseover="return addthis_open(this, '', '[URL]', '[TITLE]')" onmouseout="addthis_close()" onclick="return addthis_sendto()"><u>bookmark it</u></a><script type="text/javascript" src="http://s7.addthis.com/js/152/addthis_widget.js"></script>, <div id="st0000000000" class="st-taf"style="display: inline;"><script src="http://cdn.socialtwist.com/0000000000/script.js"></script></div><div id="st0000000000" class="st-taf"style="display: inline;"> <a href="http://cdn.socialtwist.com/0000000000/script.js"></script><img style="border:0;margin:0;padding:0;" src="" alt="tell a friend" onmouseout="hideHoverMap(this)" onmouseover="showHoverMap(this, '0000000000', window.location, document.title)" onclick="cw(this, {id:'0000000000',link: window.location, title: document.title })"/></a></div> about it, and subscribe to the blog <a href="%URL%/feed/">RSS feed</a>.</textarea>
		</p>


	</div>

	</fieldset>


	<fieldset class="options"> 

	<div style="padding: 0 0 0 30px">
		<input type="checkbox" name="womm_sindex" value="checkbox" <?php if (get_option('womm_sindex')) echo "checked='checked'"; ?>/>&nbsp;&nbsp;
		<strong>Display on index page</strong>
		<br />
		<input type="checkbox" name="womm_sposts" value="checkbox" <?php if (get_option('womm_sposts')) echo "checked='checked'"; ?>/>&nbsp;&nbsp;
		<strong>Display on posts</strong>
		<br />
		<input type="checkbox" name="womm_spages" value="checkbox" <?php if (get_option('womm_spages')) echo "checked='checked'"; ?>/>&nbsp;&nbsp;
		<strong>Display on static pages</strong>
		<br />
		<input type="checkbox" name="womm_sarc" value="checkbox" <?php if (get_option('womm_sarc')) echo "checked='checked'"; ?>/>&nbsp;&nbsp;
		<strong>Display on archive pages (includes cat archives)</strong>
		<br />
		<input type="checkbox" name="womm_ssearch" value="checkbox" <?php if (get_option('womm_ssearch')) echo "checked='checked'"; ?>/>&nbsp;&nbsp;
		<strong>Display on search pages</strong>
	</div>
	
	</fieldset>

	<div class="submit">
		<input type="submit" name="set_defaults" value="<?php _e('Load Default Options'); ?> &raquo;" />
		<input type="submit" name="info_update" value="<?php _e('Activate / Update options'); ?> &raquo;" />
	</div>

	</form>
	</div><?php
}



function womm_generate($content) {

	global $wpdb, $id, $authordata;

	// strip p tags around html comments

	$content = preg_replace('/<p>\s*<!--(.*)-->\s*<\/p>/i', "<!--$1-->", $content);

	// Load options

	$womm_data = get_option('womm_data');
	$womm_sindex = get_option('womm_sindex');
	$womm_sposts = get_option('womm_sposts');
	$womm_spages = get_option('womm_spages');
	$womm_sarc = get_option('womm_sarc');
	$womm_ssearch = get_option('womm_ssearch');

	// Check page type

	$show_sig = FALSE;

	if (is_home() && $womm_sindex) {
		$show_sig = TRUE;
	}

	if (is_single() && $womm_sposts) {
		$show_sig = TRUE;
	}

	if (is_page() && $womm_spages) {
		$show_sig = TRUE;
	}
	
	if (is_archive() && $womm_sarc) {
		$show_sig = TRUE;
	}	

	if (is_search() && $womm_ssearch) {
		$show_sig = TRUE;
	}

	$found = strpos ($content, '<!-- womm -->');

	if ($found) {
		$show_sig = TRUE;
	}

	if ($show_sig) {

		// Get author information
		$a_url = get_the_author_url();			// %URL%
	

		// Process signature

		$the_sig = stripslashes(nl2br(womm_trim_sig($womm_data)));
		$the_sig = str_replace("%URL%", $a_url, $the_sig);

		// Look for trigger

		if ($found_trigger) { 			// If trigger found, process
			$content = str_replace('<!-- womm -->', $the_sig, $content);
		} else {	
			$content .= $the_sig;
		}

	}



	return $content;

}


add_filter('the_content', 'womm_generate');
add_action('admin_menu', 'womm_add_option_pages');

?>