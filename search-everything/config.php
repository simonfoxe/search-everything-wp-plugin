<?php

global $wp_se_options, $wp_se_meta;
$wp_se_options = false;
$wp_se_meta = false;


function wp_se_get_options() {
	global $wp_se_options, $wp_se_meta;
	if($wp_se_options) {
		return $wp_se_options;
	}

	$wp_se_options = get_option('se_options', false);

	if(!$wp_se_options || $wp_se_meta['version'] !== WP_SE_VERSION) {
		wp_se_upgrade();
		$wp_se_meta = get_option('se_meta');
		$wp_se_options = get_option('se_options');
	}

	$wp_se_meta = new ArrayObject($wp_se_meta);
	$wp_se_options = new ArrayObject($wp_se_options);

	return $wp_se_options;
}


function wp_se_get_meta() {
	global $wp_se_meta;

	if (!$wp_se_meta) {
		wp_se_get_options();
	}
	return $wp_se_meta;
}

function wp_se_update_meta($new_meta) {
	global $wp_se_meta;

	$new_meta = (array) $new_meta;

	$r = update_option('se_meta', $new_meta);

	if($r && $wp_se_meta !== false) {
		$wp_se_meta->exchangeArray($new_meta);
	}

	return $r;
}

function wp_se_update_options($new_options) {
	global $wp_se_options;

	$new_options = (array) $new_options;
	$r = update_option('se_options', $new_options);
	if($r && $wp_se_options !== false) {
		$wp_se_options->exchangeArray($new_options);
	}

	return $r;
}

//we have to be careful, as previously version was not stored in the options!
function wp_se_upgrade() {
	$wp_se_meta = get_option('se_meta', false);
	$version = false;

	if($wp_se_meta) {
		$version = $wp_se_meta['version'];
	}

	if($version) {
		if(version_compare($version, WP_SE_VERSION, '<')) {
			call_user_func('wp_se_migrate_' . str_replace('.', '_', $version));
			wp_se_upgrade();
		}
	} else {
		//check if se_options exist
		$wp_se_options = get_option('se_options', false);
		if($wp_se_options) {
			wp_se_migrate_7_0_1(); //existing users don't have version stored in their db
		} else {
			wp_se_install();
		}
	}
}


function wp_se_migrate_7_0_1() {
	$wp_se_meta = array(
		'blog_id'			=> false,
		'auth_key'			=> false,
		'version'			=> '7.0.2',
		'first_version'			=> '7.0.1',
		'new_user'			=> false,
		'name'				=> '',
		'email'				=> '',
		'show_options_page_notice'	=> false
	);

	update_option('se_meta',$wp_se_meta);

	//get options and update values to boolean
	$old_options = get_option('se_options', false);

	if($old_options) {
		$new_options = wp_se_get_default_options();

		$boolean_keys = array(
			'se_use_page_search'		=> false,
			'se_use_comment_search' 	=> false,
			'se_use_tag_search'		=> false,
			'se_use_tax_search'		=> false,
			'se_use_category_search'	=> false,
			'se_approved_comments_only'=> false,
			'se_approved_pages_only'	=> false,
			'se_use_excerpt_search'	=> false,
			'se_use_draft_search'		=> false,
			'se_use_attachment_search'	=> false,
			'se_use_authors'		=> false,
			'se_use_cmt_authors'		=> false,
			'se_use_metadata_search'	=> false,
			'se_use_highlight'		=> false,
			);
		$text_keys = array(
			'se_exclude_categories' 	=> '',
			'se_exclude_categories_list'	=> '',
			'se_exclude_posts'		=> '',
			'se_exclude_posts_list'		=> '',
			'se_highlight_color'		=> '',
			'se_highlight_style'		=> ''
			);

		foreach ($boolean_keys as $k) {
			$new_options[$k] = ('Yes' === $old_options[$k]);
		}
		foreach ($text_keys as $t) {
			$new_options[$t] = $old_options[$t];
		}
		update_option($new_options);
	}

	//moved to meta
	$notice = get_option('se_show_we_tried', false);
	if($notice) {
		delete_option('se_show_we_tried');
	}
}


function wp_se_install() {
	$wp_se_meta = array(
		'blog_id' => false,
		'auth_key' => false,
		'version' => WP_SE_VERSION,
		'first_version' => WP_SE_VERSION,
		'new_user' => true,
		'name' => '',
		'email' => '',
		'show_options_page_notice'	=> true
	);
	$wp_se_options = wp_se_get_default_options();

	update_option('se_meta', $wp_se_meta);
	update_option('se_options', $wp_se_options);

}

function wp_se_get_default_options() {
	$wp_se_options = array(
				'se_exclude_categories'	=> '',
				'se_exclude_categories_list' 	=> '',
				'se_exclude_posts'		=> '',
				'se_exclude_posts_list'		=> '',
				'se_use_page_search'		=>false,
				'se_use_comment_search' 	=>false,
				'se_use_tag_search'		=> false,
				'se_use_tax_search'		=> false,
				'se_use_category_search'	=> false,
				'se_approved_comments_only'=> false,
				'se_approved_pages_only'	=> false,
				'se_use_excerpt_search'	=> false,
				'se_use_draft_search'		=> false,
				'se_use_attachment_search'	=> false,
				'se_use_authors'		=> false,
				'se_use_cmt_authors'		=> false,
				'se_use_metadata_search'	=> false,
				'se_use_highlight'		=> false,
				'se_highlight_color'		=> 'yellow',
				'se_highlight_style'		=> ''
			);

	return $wp_se_options;
}

?>