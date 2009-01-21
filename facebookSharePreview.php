<?php
/**
 * @package facebook-share-preview
 * @author Sandro Padin
 * @version 0.4.5
 */
/*
Plugin Name: Facebook Share Preview
Plugin URI: http://www.digitalpadin.com/
Description: This plugin adds Facebook-specific meta tags to the header of a post. This allows you to share content, such as: video and audio, directly on Facebook.
Author: Sandro Padin
Version: 0.4.5
Author URI: http://www.digitalpadin.com/
*/
/*  Copyright 2009  Sandro Padin  (email : sandro@digitalpadin.com)

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
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/

// Hook for adding admin menus
add_action('admin_menu', 'fbsp_add_admin_pages');
add_action('wp_head', 'fbsp_add_meta_to_post');

// action function for above hook
function fbsp_add_admin_pages() {
    // Add a new submenu under Options:
    add_options_page('Facebook Share Preview Options', 'Facebook Share Preview', 8, 'fbSharePreviewoptions', 'fbsp_options_page');
}

// mt_options_page() displays the page content for the Test Options submenu
function fbsp_options_page() {
	$hidden_field_name = 'fbsp_submit_hidden';
	
	$audio_tag_opt_name    = 'fbsp_audio_tag';
	$audio_artist_opt_name = 'fbsp_audio_artist';
	$audio_title_opt_name  = 'fbsp_audio_title';
	$image_src_opt_name    = 'fbsp_image_src';
	
	$audio_tag_data_field_name    = 'fbsp_audio_tag_field';
	$audio_artist_data_field_name = 'fbsp_audio_artist_field';
	$audio_title_data_field_name  = 'fbsp_audio_title_field';
	$image_src_data_field_name    = 'fbsp_image_src_field';

	// Read in existing option value from database
	$audio_tag_opt_val    = get_option( $audio_tag_opt_name    );
	$audio_artist_opt_val = get_option( $audio_artist_opt_name );
	$audio_title_opt_val  = get_option( $audio_title_opt_name  );
	$image_src_opt_val    = get_option( $image_src_opt_name    );

	// See if the user has posted us some information
	// If they did, this hidden field will be set to 'Y'
	if( $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
		$audio_tag_opt_val    = $_POST[ $audio_tag_data_field_name    ];
		$audio_artist_opt_val = $_POST[ $audio_artist_data_field_name ];
		$audio_title_opt_val  = $_POST[ $audio_title_data_field_name  ];
		$image_src_opt_val    = $_POST[ $image_src_data_field_name    ];

		// Save the posted value in the database
		update_option( $audio_tag_opt_name,    $audio_tag_opt_val    );
		update_option( $audio_artist_opt_name, $audio_artist_opt_val );
		update_option( $audio_title_opt_name,  $audio_title_opt_val  );
		update_option( $image_src_opt_name,    $image_src_opt_val    );

		// Put an options updated message on the screen
		echo <<<EOF
<div class="updated"><p><strong>Options saved.</strong></p></div>	

EOF;
	}
	
	// This section uses PHP Heredoc syntax.
	// http://www.php.net/manual/en/language.types.string.php#language.types.string.syntax.heredoc
	$mt_form_action = str_replace( '%7E', '~', $_SERVER['REQUEST_URI']);
	echo <<<EOF
<div class="wrap">
	<h2>Facebook Share Preview Options</h2>
	<p>The following settings are required to use the Facebook Share Preview Plugin.</p>
	<p>Modify the settings below to begin seamlessly sharing your content with Facebook users.</p>
	<form name="form1" method="post" action="{$mt_form_action}">
		<h2>Audio Sharing Settings</h2>
		<p>In order to have your audio posts prepared for Facebook sharing, you need to tag the post with a tag of your choosing.</p>
		<p>Tag name: A tag that will denote this post has audio. <em>Recommended: <strong>audio</strong> or <strong>mp3</strong></em></p>
		<input type="text" name="{$audio_tag_data_field_name}" value="{$audio_tag_opt_val}" id="{$audio_tag_data_field_name}">
		
		<p>Default Artist: The default artist name for audio. <em>Note: If blank, the post author's first and last name will be used.</em></p>
		<input type="text" size="50" name="{$audio_artist_data_field_name}" value="{$audio_artist_opt_val}" id="{$audio_artist_data_field_name}">
		
		<p>Default Title: The default title for the audio. <em>Note: If blank, the post title will be used.</em></p>
		<input type="text" size="50" name="{$audio_title_data_field_name}" value="{$audio_title_opt_val}" id="{$audio_title_data_field_name}">
		
		<p>Default Album Art: An image that will be used as the album art. <em><strong>Required by Facebook.</strong></em></p>
		<input type="text" size="80" name="{$image_src_data_field_name}" value="{$image_src_opt_val}" id="{$image_src_data_field_name}">
		
		<input type="hidden" name="{$hidden_field_name}" value="Y">
	
		<p class="submit">
			<input type="submit" name="Submit" value="Update Options" />
		</p>
	</form>
</div>

EOF;

}

function fbsp_add_meta_to_post() {
	
	$audio_tag_opt_name    = 'fbsp_audio_tag';
	$audio_artist_opt_name = 'fbsp_audio_artist';
	$audio_title_opt_name  = 'fbsp_audio_title';
	$image_src_opt_name    = 'fbsp_image_src';
	
	// Get option values from the database.
	$audio_tag_opt_val    = get_option( $audio_tag_opt_name    );
	$audio_artist_opt_val = get_option( $audio_artist_opt_name );
	$audio_title_opt_val  = get_option( $audio_title_opt_name  );
	$image_src_opt_val    = get_option( $image_src_opt_name    );
	
	// If an option is blank, set to default values.
	$audio_title_opt_val  = ($audio_title_opt_val == "")?"post_title":$audio_title_opt_val;
	$audio_artist_opt_val = ($audio_artist_opt_val == "")?"post_author":$audio_artist_opt_val;
	
	if(is_single() && has_tag($audio_tag_opt_val)) {
		// Get current post id. Cannot use the_ID() because this is outside of The Loop?
		global $wp_query;
		$postID         = $wp_query->post->ID;
		$post_title     = $wp_query->post->post_title;
		$post_author_id = $wp_query->post->post_author;
		$post_author_ln = get_usermeta($post_author_id,'last_name');
		$post_author_fn = get_usermeta($post_author_id,'first_name');
		$post_author    = $post_author_fn . " " . $post_author_ln;
		
		// Get enclosure field and prepare for printing. Enclosure field is multiline, but URL is on first line.
		$enclosure    = get_post_meta($postID, 'enclosure', true);
		$audio_src    = split("\n",$enclosure);
		$audio_src    = trim($audio_src[0]);
		
		// Get audio_title custom field. If blank, use default audio_title option from settings, if that's blank, use post_title
		$audio_title  = get_post_meta($postID, 'audio_title', true);
		$audio_title  = ($audio_title <> "")?$audio_title:$audio_title_opt_val;
		$audio_title  = ($audio_title <> "post_title")?$audio_title:$post_title;
		
		// Get audio_artist custom field. If blank, use audio_artist option from settings, if that's blank, use post_author first/last name
		$audio_artist = get_post_meta($postID, 'audio_artist', true);
		$audio_artist = ($audio_artist <> "")?$audio_artist:$audio_artist_opt_val;
		$audio_artist = ($audio_artist <> "post_author")?$audio_author:$post_author;
		
		$audio_album  = get_post_meta($postID, 'audio_album', true);
		
		// Get image_src custom field, If blank, use image_src option from settings.
		$image_src    = get_post_meta($postID, 'image_src', true);
		$image_src    = ($image_src <> "")?$image_src:$image_src_opt_val;
		
		echo '<!-- Facebook Share Preview Plugin Start -->'."\n";
		echo '<link rel="image_src" href="'.$image_src.'" />'."\n";
		echo '<link rel="audio_src" href="'.$audio_src.'" />'."\n";
		echo '<meta name="audio_type" content="audio/mpeg3" />'."\n";
		echo '<meta name="audio_title" content="'.$audio_title.'" />'."\n";
		echo '<meta name="audio_artist" content="'.$audio_artist.'" />'."\n";
		
		if($audio_album <> "")
			echo '<meta name="audio_album" content="'.$audio_album.'" />'."\n";
		
		echo '<!-- Facebook Share Preview Plugin End -->'."\n\n";

	}
}



?>