<?php

Class se_admin {

//	var $version = '7.0.1';

	function se_admin() {

		// Load language file
		$locale = get_locale();
		if ( !empty($locale) )
			load_textdomain('SearchEverything', dirname(__FILE__) .'lang/se-'.$locale.'.mo');

		add_action('admin_menu', array(&$this, 'se_add_options_panel'));
	}

	function se_add_options_panel() {
		add_options_page('Search', 'Search Everything', 'manage_options', 'extend_search', array(&$this, 'se_option_page'));
	}
	

	//build admin interface
	function se_option_page() {
		global $wpdb, $table_prefix, $wp_version;

			$new_options = array(
				'se_exclude_categories'			=> (isset($_POST['exclude_categories']) && !empty($_POST['exclude_categories'])) ? $_POST['exclude_categories'] : '',
				'se_exclude_categories_list'	=> (isset($_POST['exclude_categories_list']) && !empty($_POST['exclude_categories_list'])) ? $_POST['exclude_categories_list'] : '',
				'se_exclude_posts'				=> (isset($_POST['exclude_posts'])) ? $_POST['exclude_posts'] : '',
				'se_exclude_posts_list'			=> (isset($_POST['exclude_posts_list']) && !empty($_POST['exclude_posts_list'])) ? $_POST['exclude_posts_list'] : '',
				'se_use_page_search'			=> (isset($_POST['search_pages']) && $_POST['search_pages'] == 'Yes') ? 'Yes' : 'No',
				'se_use_comment_search'			=> (isset($_POST['search_comments']) && $_POST['search_comments'] == 'Yes') ? 'Yes' : 'No',
				'se_use_tag_search'				=> (isset($_POST['search_tags']) && $_POST['search_tags'] == 'Yes') ? 'Yes' : 'No',
				'se_use_tax_search'				=> (isset($_POST['search_taxonomies']) && $_POST['search_taxonomies'] == 'Yes') ? 'Yes' : 'No',
				'se_use_category_search'		=> (isset($_POST['search_categories']) && $_POST['search_categories'] == 'Yes') ? 'Yes' : 'No',
				'se_approved_comments_only'		=> (isset($_POST['appvd_comments']) && $_POST['appvd_comments'] == 'Yes') ? 'Yes' : 'No',
				'se_approved_pages_only'		=> (isset($_POST['appvd_pages']) && $_POST['appvd_pages'] == 'Yes') ? 'Yes' : 'No',
				'se_use_excerpt_search'			=> (isset($_POST['search_excerpt']) && $_POST['search_excerpt'] == 'Yes') ? 'Yes' : 'No',
				'se_use_draft_search'			=> (isset($_POST['search_drafts']) && $_POST['search_drafts'] == 'Yes') ? 'Yes' : 'No',
				'se_use_attachment_search'		=> (isset($_POST['search_attachments']) && $_POST['search_attachments'] == 'Yes') ? 'Yes' : 'No',
				'se_use_authors'				=> (isset($_POST['search_authors']) && $_POST['search_authors'] == 'Yes') ? 'Yes' : 'No',
				'se_use_cmt_authors'			=> (isset($_POST['search_cmt_authors']) && $_POST['search_cmt_authors'] == 'Yes') ? 'Yes' : 'No',
				'se_use_metadata_search'		=> (isset($_POST['search_metadata']) && $_POST['search_metadata'] == 'Yes') ? 'Yes' : 'No',
				'se_use_highlight'				=> (isset($_POST['search_highlight']) && $_POST['search_highlight'] == 'Yes') ? 'Yes' : 'No',
				'se_highlight_color'			=> (isset($_POST['highlight_color'])) ? $_POST['highlight_color'] : '',
				'se_highlight_style'			=> (isset($_POST['highlight_style'])) ? $_POST['highlight_style'] : ''

			);

		if(isset($_POST['action']) && $_POST['action'] == "save") {
			echo "<div class=\"updated fade\" id=\"limitcatsupdatenotice\"><p>" . __('Your default search settings have been <strong>updated</strong> by Search Everything. </p><p> What are you waiting for? Go check out the new search results!', 'SearchEverything') . "</p></div>";
			//update_option("se_options", $new_options);
			wp_se_update_options($new_options);
		}

		if(isset($_POST['action']) && $_POST['action'] == "reset") {
			echo "<div class=\"updated fade\" id=\"limitcatsupdatenotice\"><p>" . __('Your default search settings have been <strong>updated</strong> by Search Everything. </p><p> What are you waiting for? Go check out the new search results!', 'SearchEverything') . "</p></div>";
			$default_options = wp_se_get_default_options();
			wp_se_update_options($default_options);
		}

		$options = wp_se_get_options();
		$meta = wp_se_get_meta();

		?>

	<div class="wrap">
		<h2><?php _e('Search Everything Version:', 'SearchEverything'); ?> <?php echo $meta['version']?></h2>
		<form method="post">
			<table id="se-basic-settings" class="widefat">
				<thead>
					<tr class="title">
						<th scope="col" class="manage-column se-col"><?php _e('Basic Configuration', 'SearchEverything'); ?></th>
						<th scope="col" class="manage-column"></th>
					</tr>
				</thead>
				<tbody> <?php
				// Show options for 2.5 and below
				if ($wp_version <= '2.5') : ?>
				<tr valign="middle">
					<th class="titledesc"><?php _e('Search every page','SearchEverything'); ?>:<br/><small></small></td>
					<td class="forminp">
					<select id="search_pages" name="search_pages">
						<option <?php selected($options['se_use_page_search'], 'No'); ?> value="No"><?php _e('No','SearchEverything')?></option>
						<option <?php selected($options['se_use_page_search'], 'Yes'); ?> value="Yes"><?php _e('Yes', 'SearchEverything'); ?></option>
					</select>
					</td>
				</tr>
				<tr valign="middle">
					<th scope="row"><label for="appvd_pages">&nbsp;&nbsp;&nbsp;&nbsp;<?php _e('Search approved pages only','SearchEverything'); ?>:</label></th>
					<td class="forminp">
					<select id="appvd_pages" name="appvd_pages">
						<option <?php selected($options['se_approved_pages_only'], 'No'); ?> value="No"><?php _e('No','SearchEverything')?></option>
						<option<?php selected($options['se_approved_pages_only'], 'Yes'); ?> value="Yes"><?php _e('Yes', 'SearchEverything'); ?></option>
					</select>
					</td>
				</tr><?php endif; ?> <?php 
				// Show tags only for WP 2.3+
				if ($wp_version >= '2.3') : ?>
				<tr valign="middle">
					<th scope="row"><label for="search_tags"><?php _e('Search every tag name','SearchEverything'); ?>:</label></th>
					<td class="forminp">
						<select id="search_tags" name="search_tags" >
							<option<?php selected($options['se_use_tag_search'], 'No'); ?> value="No"><?php _e('No','SearchEverything')?></option>
							<option<?php selected($options['se_use_tag_search'], 'Yes'); ?> value="Yes"><?php _e('Yes', 'SearchEverything'); ?></option>
						</select>
						<br/><small></small>
					</td>
				</tr><?php endif; ?> <?php 
				// Show taxonomies only for WP 2.3+
				if ($wp_version >= '2.3') : ?>
				<tr valign="middle">
					<th scope="row"><label for="search_tags"><?php _e('Search custom taxonomies','SearchEverything'); ?>:</label></th>
					<td class="forminp">
						<select id="search_tags" name="search_taxonomies" >
							<option<?php selected($options['se_use_tax_search'], 'No'); ?> value="No">&nbsp;&nbsp;</option>
							<option<?php selected($options['se_use_tax_search'], 'Yes'); ?> value="Yes"><?php _e('Yes', 'SearchEverything'); ?></option>
						</select>
						<br/><small></small>
					</td>
				</tr><?php endif; ?>
				<?php 
				if ($wp_version >= '2.5') : ?>
				<tr valign="middle">
					<th scope="row"><label for="search_categories"><?php _e('Search every category name and description','SearchEverything'); ?>:</label></th>
					<td class="forminp">
						<select id="search_categories" name="search_categories">
							<option<?php selected($options['se_use_category_search'], 'No'); ?> value="No">&nbsp;&nbsp;</option>
							<option<?php selected($options['se_use_category_search'], 'Yes'); ?> value="Yes"><?php _e('Yes', 'SearchEverything'); ?></option>
						</select>
						<br/><small></small>
					</td>
				</tr><?php endif; ?>
				<tr valign="middle">
					<th scope="row"><label for="search_comments"><?php _e('Search every comment','SearchEverything'); ?>:</label></th>
					<td class="forminp">
						<select name="search_comments" id="search_comments">
							<option<?php selected($options['se_use_comment_search'], 'No'); ?> value="No">&nbsp;&nbsp;</option>
							<option<?php selected($options['se_use_comment_search'], 'Yes'); ?> value="Yes"><?php _e('Yes', 'SearchEverything'); ?></option>
						</select>
						<br/><small></small>
					</td>
				</tr>
				<tr valign="middle">
					<th scope="row"><label for="search_cmt_authors">&nbsp;&nbsp;&nbsp;&nbsp;<?php _e('Search comment authors','SearchEverything'); ?>:</label></th>
					<td class="forminp">
						<select id="search_cmt_authors" name="search_cmt_authors">
							<option<?php selected($options['se_use_cmt_authors'], 'No'); ?> value="No">&nbsp;&nbsp;</option>
							<option<?php selected($options['se_use_cmt_authors'], 'Yes'); ?> value="Yes"><?php _e('Yes', 'SearchEverything'); ?></option>
						</select>
						<br/><small></small>
					</td>
				</tr>
				<tr valign="middle">
					<th scope="row"><label for="appvd_comments">&nbsp;&nbsp;&nbsp;&nbsp;<?php _e('Search approved comments only','SearchEverything'); ?>:</label></th>
					<td class="forminp">
						<select id="appvd_comments" name="appvd_comments">
							<option<?php selected($options['se_approved_comments_only'], 'No'); ?> value="No">&nbsp;&nbsp;</option>
							<option<?php selected($options['se_approved_comments_only'], 'Yes'); ?> value="Yes"><?php _e('Yes', 'SearchEverything'); ?></option>
						</select>
						<br/><small></small>
					</td>
				</tr>
				<tr valign="middle">
					<th scope="row"><label for="search_excerpt"><?php _e('Search every excerpt','SearchEverything'); ?>:</label></th>
					<td class="forminp">
						<select id="search_excerpt" name="search_excerpt">
							<option<?php selected($options['se_use_excerpt_search'], 'No'); ?> value="No">&nbsp;&nbsp;</option>
							<option<?php selected($options['se_use_excerpt_search'], 'Yes'); ?> value="Yes"><?php _e('Yes', 'SearchEverything'); ?></option>
						</select>
						<br/><small></small>
					</td>
				</tr>
				<?php
				// Show categories only for WP 2.5+
				if ($wp_version >= '2.5') : ?>
				<tr valign="middle">
					<th scope="row"><label for="search_drafts"><?php _e('Search every draft','SearchEverything'); ?></label></th>
					<td>
					<select id="search_drafts" name="search_drafts">
						<option<?php selected($options['se_use_draft_search'], 'No'); ?> value="No"><?php _e('No','SearchEverything')?></option>
						<option<?php selected($options['se_use_draft_search'], 'Yes'); ?> value="Yes"><?php _e('Yes', 'SearchEverything'); ?></option>
					</select>
					</td>
				</tr>
				<?php endif; ?>
				<tr valign="middle">
					<th><label for="search_attachments"><?php _e('Search every attachment','SearchEverything'); ?></label>:<br/><small><?php _e('(post type = attachment)','SearchEverything'); ?></small></td>
					<td>
					<select id="search_attachments" name="search_attachments">
						<option <?php selected($options['se_use_attachment_search'], 'No'); ?> value="No"><?php _e('No','SearchEverything')?></option>
						<option <?php selected($options['se_use_attachment_search'], 'Yes'); ?> value="Yes"><?php _e('Yes', 'SearchEverything'); ?></option>
					</select>
					</td>
				</tr>
				<tr valign="middle">
					<th scope="row"><label for="search_metadata"><?php _e('Search every custom field','SearchEverything'); ?>:</label><br/><small><?php _e('(metadata)','SearchEverything'); ?></small></tth>
					<td class="forminp">
					<select id="search_metadata" name="search_metadata">
						<option<?php selected($options['se_use_metadata_search'], 'No'); ?> value="No"><?php _e('No','SearchEverything')?></option>
						<option<?php selected($options['se_use_metadata_search'], 'Yes'); ?> value="Yes"><?php _e('Yes', 'SearchEverything'); ?></option>
					</select>
					</td>
				</tr>
				<tr valign="middle">
					<th scope="row"><label for="search_authors"><?php _e('Search every author','SearchEverything'); ?>:</label></th>
					<td class="forminp">
					<select id="search_authors" name="search_authors">
					<option<?php selected($options['se_use_authors'],'No'); ?> value="No"><?php _e('No','SearchEverything')?></option>
					<option<?php selected($options['se_use_authors'], 'Yes'); ?> value="Yes"><?php _e('Yes', 'SearchEverything'); ?></option>
					</select>
					</td>
				</tr>
				<tr valign="middle">
					<th scope="row"><label for="search_highlight"><?php _e('Highlight Search Terms','SearchEverything'); ?>:</label></th>
					<td class="forminp">
					<select id="search_highlight" name="search_highlight">
						<option<?php selected($options['se_use_highlight'], 'No'); ?> value="No"><?php _e('No','SearchEverything')?></option>
						<option<?php selected($options['se_use_highlight'], 'Yes'); ?> value="Yes"><?php _e('Yes', 'SearchEverything'); ?></option>
					</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="highlight_color">&nbsp;&nbsp;&nbsp;&nbsp;<?php _e('Highlight Background Color','SearchEverything'); ?>:</label></th>
					<td class="forminp">
					<input type="text" id="highlight_color" name="highlight_color" value="<?php echo $options['se_highlight_color'];?>" /><br/>
					<small> <?php _e('Examples:\'#FFF984\' or \'red\'','SearchEverything'); ?></small>
					</td>
				</tr>
			</tbody>
			</table>
		<table id="se-advanced-settings" class="widefat">
			<thead>
				<tr class="title">
					<th scope="col" class="manage-column se-col"><?php _e('Advanced Configuration - Exclusion', 'SearchEverything'); ?></th>
					<th scope="col" class="manage-column"></th>
				</tr>
			</thead>
			<tbody>
			<tr valign="middle">
				<th scope="row"><label for="exclude_posts_list"><?php _e('Exclude some post or page IDs','SearchEverything'); ?>:</label></th>
				<td class="forminp">
					<input type="text" id="exclude_posts_list" name="exclude_posts_list" value="<?php echo $options['se_exclude_posts_list'];?>" />
					<br/><small><?php _e('Comma separated Post IDs (example: 1, 5, 9)','SearchEverything'); ?></small>
				</td>
			</tr>
			<tr valign="middle">
				<th scope="row"><label for="exclude_categories_list"><?php _e('Exclude Categories','SearchEverything'); ?>:</label></th>
				<td class="forminp">
					<input type="text" id="exclude_categories_list" name="exclude_categories_list" value="<?php echo $options['se_exclude_categories_list'];?>" />
					<br/><small><?php _e('Comma separated category IDs (example: 1, 4)','SearchEverything'); ?></small>
				</td>
			</tr>
			<tr valign="middle">
				<th scope="row"><label for="highlight_style"><?php _e('Full Highlight Style','SearchEverything'); ?>:</label></th>
				<td class="forminp">
					<input type="text" id="highlight_style" name="highlight_style" value="<?php echo $options['se_highlight_style'];?>" /> <br/>
					<small><?php _e('Important: \'Highlight Background Color\' must be blank to use this advanced styling.', 'SearchEverything') ?></small><br/>
					<small><?php _e('Example: background-color: #FFF984; font-weight: bold; color: #000; padding: 0 1px;','SearchEverything'); ?></small>
				</td>
			</tr>
		</tbody>
		</table>
		<p class="submit">
			<input type="hidden" name="action" value="save" />
			<input type="submit" class="button" value="<?php _e('Update Options', 'SearchEverything') ?>" />
		</p>
	</form>
	<table id="se-test-search" class="widefat">
		<thead>
			<tr class="title">
				<th scope="col" class="manage-column se-col"><?php _e('Test Search Form', 'SearchEverything'); ?></th>
				<th scope="col" class="manage-column"></th>
			</tr>
		</thead>
		<tbody>
		<tr valign="middle">
			<th >
				<?php _e('Use this search form to run a live search test.', 'SearchEverything'); ?>
			</th>
			<td>
				<form method="get" id="searchform" action="<?php bloginfo($cap = version_compare('2.2', $wp_version, '<') ? 'url' : 'home'); ?>">
				<p class="srch submit">
					<input type="text" class="srch-txt" value="<?php echo (isset($S)) ? wp_specialchars($s, 1) : ''; ?>" name="s" id="s" size="30" />
					<input type="submit" class="button"class="SE5_btn" id="searchsubmit" value="<?php _e('Run Test Search', 'SearchEverything'); ?>" />
				</p>
				</form>
			</td>
		</tr>
		</tbody>
	</table>
	<table id="se-info" class="widefat">
		<thead>
			<tr class="title">
				<th scope="col" class="manage-column se-col"><?php _e('News', 'SearchEverything'); ?></th>
				<th scope="col" class="manage-column"><?php _e('Development Support', 'SearchEverything'); ?></th>
				<th scope="col" class="manage-column"><?php _e('Localization Support', 'SearchEverything'); ?></th>
			</tr>
		</thead>
		<tr valign="middle">
			<td class="thanks">
			<p><strong><?php _e('LOCALIZATION SUPPORT:', 'SearchEverything'); ?></strong><br/><?php _e('Version 6 was a major update and a few areas need new localization support. If you can help send us your translations by posting them as a new issue, ', 'SearchEverything') ?><a href="https://github.com/Zemanta/search-everything-wordpress-plugin/issues?sort=created&direction=desc&state=open&page=1" target="blank"><strong><?php _e('here','SearchEverything')?></strong></a>.</p>
			<p><strong><?php _e('Thank You!', 'SearchEverything'); ?></strong><br/><?php _e('The development of Search Everything since Version one has primarily come from the WordPress community, We&#8217;m grateful for their dedicated and continued support.', 'SearchEverything'); ?></p>
			</td>
			<td>
				<ul class="SE_lists">
					<li><a href="https://github.com/ninnypants"><strong>Tyrel Kelsey</strong></a></li>
				</ul>
			</td>
			<td>
				<ul class="SE_lists">
					<li><a href="#" target="blank">minjae kim (KR) - v.6</a></li>
					<li><a href="http://www.r-sn.com/wp" target="blank">Anonymous (AR) - v.6</a></li>
					<li><a href="http://www.doctorley.pl" target="blank">Karol Manikowski (PL) - v.6</a></li>
					<li><a href="http://www.paulwicking.com" target="blank">Paul Wicking (NO)- v.6</a></li>
					<li><a href="#">Bilei Radu (RO)- v.6</a></li>
					<li><a href="http://www.fatcow.com" target="blank">Fat Cow (BY) - v.6</a></li>
					<li><a href="http://gidibao.net/" target="blank">Gianni Diurno (IT) - v.6</a></li>
					<li><a href="#">Maris Svirksts (LV) - v.6</a></li>
					<li><a href="#">Simon Hansen (NN) - v.6</a></li>
					<li><a href="http://beyn.org/" target="blank">jean Pierre Gavoille (FR) - v.6</a></li>
					<li><a href="#">hit1205 (CN and TW)</a></li>
					<li><a href="http://www.alohastone.com" target="blank">alohastone (DE)</a></li>
					<li><a href="http://gidibao.net/" target="blank">Gianni Diurno (ES)</a></li>
					<li><a href="#">János Csárdi-Braunstein (HU)</a></li>
					<li><a href="http://idimensie.nl" target="blank">Joeke-Remkus de Vries (NL)</a></li>
					<li><a href="#">Silver Ghost (RU)</a></li>
					<li><a href="http://mishkin.se" target="blank">Mikael Jorhult (RU)</a></li>
					<li><a href="#">Baris Unver (TR)</a></li>
				</ul>
			</td>
		</tr>
	</table>
	</div>

		<?php
	}	//end se_option_page
}
?>