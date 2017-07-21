<?php
/**
 * GalleryLink
 * 
 * @package    GalleryLink
 * @subpackage GalleryLink Management screen
    Copyright (c) 2013- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

class GalleryLinkAdmin {

	/* ==================================================
	 * Add a "Settings" link to the plugins page
	 * @since	1.0.18
	 */
	function settings_link($links, $file) {
		static $this_plugin;
		if ( empty($this_plugin) ) {
			$this_plugin = GALLERYLINK_PLUGIN_BASE_FILE;
		}
		if ( $file == $this_plugin ) {
			$links[] = '<a href="'.admin_url('options-general.php?page=GalleryLink').'">'.__( 'Settings').'</a>';
		}
		return $links;
	}

	/* ==================================================
	 * Settings page
	 * @since	1.0.6
	 */
	function plugin_menu() {
		add_options_page( 'GalleryLink Options', 'GalleryLink', 'manage_options', 'GalleryLink', array($this, 'plugin_options') );
	}

	/* ==================================================
	 * Add Css and Script
	 * @since	2.0
	 */
	function load_custom_wp_admin_style() {
		if ($this->is_my_plugin_screen()) {
			wp_enqueue_style( 'jquery-responsiveTabs', GALLERYLINK_PLUGIN_URL.'/css/responsive-tabs.css' );
			wp_enqueue_style( 'jquery-responsiveTabs-style', GALLERYLINK_PLUGIN_URL.'/css/style.css' );
			wp_enqueue_style( 'stacktable', GALLERYLINK_PLUGIN_URL.'/css/stacktable.css' );
			wp_enqueue_script('jquery');
			wp_enqueue_script( 'jquery-responsiveTabs', GALLERYLINK_PLUGIN_URL.'/js/jquery.responsiveTabs.min.js' );
			wp_enqueue_script( 'stacktable', GALLERYLINK_PLUGIN_URL.'/js/stacktable.js' );
			wp_enqueue_script( 'gallerylink-js', GALLERYLINK_PLUGIN_URL.'/js/jquery.gallerylink.js', array('jquery') );
		}
	}

	/* ==================================================
	 * For only admin style
	 * @since	9.93
	 */
	function is_my_plugin_screen() {
		$screen = get_current_screen();
		if (is_object($screen) && $screen->id == 'settings_page_GalleryLink') {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/* ==================================================
	 * Settings page
	 * @since	1.0.6
	 */
	function plugin_options() {

		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		if( !empty($_POST) ) {
			$this->options_updated(intval($_POST['gallerylink_admin_tabs']));
		}

		$scriptname = admin_url('options-general.php?page=GalleryLink');

		$gallerylink_character_code = get_option('gallerylink_character_code');
		$gallerylink_album = get_option('gallerylink_album');
		$gallerylink_all = get_option('gallerylink_all');
		$gallerylink_colorbox = get_option('gallerylink_colorbox');
		$gallerylink_css = get_option('gallerylink_css');
		$gallerylink_infinite = get_option('gallerylink_infinite');
		$gallerylink_masonry = get_option('gallerylink_masonry');
		$gallerylink_document = get_option('gallerylink_document');
		$gallerylink_exclude = get_option('gallerylink_exclude');
		$gallerylink_movie = get_option('gallerylink_movie');
		$gallerylink_music = get_option('gallerylink_music');
		$gallerylink_useragent = get_option('gallerylink_useragent');

		include_once GALLERYLINK_PLUGIN_BASE_DIR . '/inc/GalleryLink.php';
		$gallerylink = new GalleryLink();

		$server_root = $_SERVER['DOCUMENT_ROOT'];
		$dirs = $gallerylink->scan_dir($server_root);
		$linkdirs_all = NULL;
		$linkdirs_album = NULL;
		$linkdirs_movie = NULL;
		$linkdirs_music = NULL;
		$linkdirs_document = NULL;
		$linkselectbox_all = NULL;
		$linkselectbox_album = NULL;
		$linkselectbox_movie = NULL;
		$linkselectbox_music = NULL;
		$linkselectbox_document = NULL;

		$gallerylink->mb_initialize($gallerylink_character_code);

		foreach ($dirs as $linkdir) {
			$linkdirenc = $gallerylink->mb_utf8(str_replace($server_root, "", $linkdir), $gallerylink_character_code);
			if( $gallerylink_all['topurl'] === $linkdirenc ){
				$linkdirs_all = '<option value="'.$linkdirenc.'" selected>'.$linkdirenc.'</option>';
			} else if( $gallerylink_album['topurl'] === $linkdirenc ){
				$linkdirs_album = '<option value="'.$linkdirenc.'" selected>'.$linkdirenc.'</option>';
			} else if( $gallerylink_movie['topurl'] === $linkdirenc ){
				$linkdirs_movie = '<option value="'.$linkdirenc.'" selected>'.$linkdirenc.'</option>';
			} else if( $gallerylink_music['topurl'] === $linkdirenc ){
				$linkdirs_music = '<option value="'.$linkdirenc.'" selected>'.$linkdirenc.'</option>';
			} else if( $gallerylink_document['topurl'] === $linkdirenc ){
				$linkdirs_document = '<option value="'.$linkdirenc.'" selected>'.$linkdirenc.'</option>';
			}else{
				$linkdirs_all = '<option value="'.$linkdirenc.'">'.$linkdirenc.'</option>';
				$linkdirs_album = '<option value="'.$linkdirenc.'">'.$linkdirenc.'</option>';
				$linkdirs_movie = '<option value="'.$linkdirenc.'">'.$linkdirenc.'</option>';
				$linkdirs_music = '<option value="'.$linkdirenc.'">'.$linkdirenc.'</option>';
				$linkdirs_document = '<option value="'.$linkdirenc.'">'.$linkdirenc.'</option>';
			}
			$linkselectbox_all .= $linkdirs_all;
			$linkselectbox_album .= $linkdirs_album;
			$linkselectbox_movie .= $linkdirs_movie;
			$linkselectbox_music .= $linkdirs_music;
			$linkselectbox_document .= $linkdirs_document;
		}
		if( empty($gallerylink_all['topurl']) ){
			$linkdirs_all = '<option value="" selected>'.__('Select').'</option>';
			$linkselectbox_all .= $linkdirs_all;
		}
		if( empty($gallerylink_album['topurl']) ){
			$linkdirs_album = '<option value="" selected>'.__('Select').'</option>';
			$linkselectbox_album .= $linkdirs_album;
		}
		if( empty($gallerylink_movie['topurl']) ){
			$linkdirs_movie = '<option value="" selected>'.__('Select').'</option>';
			$linkselectbox_movie .= $linkdirs_movie;
		}
		if( empty($gallerylink_music['topurl']) ){
			$linkdirs_music = '<option value="" selected>'.__('Select').'</option>';
			$linkselectbox_music .= $linkdirs_music;
		}
		if( empty($gallerylink_document['topurl']) ){
			$linkdirs_document = '<option value="" selected>'.__('Select').'</option>';
			$linkselectbox_document .= $linkdirs_document;
		}

		?>

		<div id="gallerylink-loading" style="position: relative; left: 40%; top: 10%;"><img src="<?php echo GALLERYLINK_PLUGIN_URL; ?>/css/loading.gif"></div>
		<div class="wrap" id="gallerylink-loading-container">

		<h2>GalleryLink</h2>

	<div id="gallerylink-admin-tabs">
	  <ul>
	    <li><a href="#gallerylink-admin-tabs-1"><?php _e('How to use', 'gallerylink'); ?></a></li>
	    <li><a href="#gallerylink-admin-tabs-2"><?php _e('Settings'); ?> <?php _e('AllData', 'gallerylink'); ?></a></li>
	    <li><a href="#gallerylink-admin-tabs-3"><?php _e('Settings'); ?> <?php _e('Album', 'gallerylink'); ?></a></li>
	    <li><a href="#gallerylink-admin-tabs-4"><?php _e('Settings'); ?> <?php _e('Video', 'gallerylink'); ?></a></li>
	    <li><a href="#gallerylink-admin-tabs-5"><?php _e('Settings'); ?> <?php _e('Music', 'gallerylink'); ?></a></li>
	    <li><a href="#gallerylink-admin-tabs-6"><?php _e('Settings'); ?> <?php _e('Document', 'gallerylink'); ?></a></li>
		<li><a href="#gallerylink-admin-tabs-7"><?php _e('Settings'); ?> <?php _e('Other', 'gallerylink'); ?></a></li>
		<li><a href="#gallerylink-admin-tabs-8"><?php _e('Effect of Images', 'gallerylink'); ?></a></li>
		<li><a href="#gallerylink-admin-tabs-9"><?php _e('Caution:'); ?></a></li>
		<li><a href="#gallerylink-admin-tabs-10"><?php _e('Donate to this plugin &#187;'); ?></a></li>
	<!--
		<li><a href="#gallerylink-admin-tabs-11">FAQ</a></li>
	 -->
	  </ul>
	  <div id="gallerylink-admin-tabs-1">
		<h2><?php _e('How to use', 'gallerylink'); ?></h2>
		<div style="padding:10px;"><?php _e('Please upload the data to the data directory (topurl) by the FTP software.', 'gallerylink'); ?></div>

		<div style="padding:10px;"><?php _e('Please add new Page. Please write a short code in the text field of the Page. Please go in Text mode this task.', 'gallerylink'); ?></div>
		<div style="padding:10px;">
		<div><?php _e('In the case of all data', 'gallerylink'); ?>
		<code>&#91;gallerylink set='all'&#93;</code></div>
		<div><?php _e('In the case of image', 'gallerylink'); ?>
		<code>&#91;gallerylink set='album'&#93;</code></div>
		<div><?php _e('In the case of video', 'gallerylink'); ?>
		<code>&#91;gallerylink set='movie'&#93;</code></div>
		<div><?php _e('In the case of music', 'gallerylink'); ?>
		<code>&#91;gallerylink set='music'&#93;</code></div>
		<div><?php _e('In the case of document', 'gallerylink'); ?>
		<code>&#91;gallerylink set='document'&#93;</code></div>
		</div>
		<div style="padding:10px;">
		<div><?php _e('Customization', 'gallerylink'); ?></div>
		</div>
		<div style="padding:10px;">
		<div><?php _e('GalleryLink can be used to specify the attributes of the table below to short code. It will override the default settings.', 'gallerylink'); ?></div>
		</div>
		<div style="padding:10px;">
		<div><?php _e('All data Example', 'gallerylink'); ?>
		<code>&#91;gallerylink set='all'&#93;</code></div>
		<div><?php _e('Image Example', 'gallerylink'); ?>
		<code>&#91;gallerylink set='album' topurl='/wordpress/wp-content/uploads' exclude_file='(.ktai.)|(-[0-9]*x[0-9]*.)' exclude_dir='ps_auto_sitemap|backwpup.*|wpcf7_captcha' rssname='album'&#93;</code></div>
		<div><?php _e('Video Example', 'gallerylink'); ?>
		<code>&#91;gallerylink set='movie' topurl='/gallery/video' rssmax=5&#93;</code></div>
		<div><?php _e('Music Example', 'gallerylink'); ?>
		<code>&#91;gallerylink set='music' topurl='/gallery/music'&#93;</code></div>
		<div><?php _e('Document Example', 'gallerylink'); ?>
		<code>&#91;gallerylink set='document' topurl='/gallery/document' suffix='doc'&#93;</code></div>
		</div>
		<div style="padding:10px;">
		<div><?php _e('* If you want to use MULTI-BYTE CHARACTER SETS to the display of the directory name and the file name. In this case, please upload the file after UTF-8 character code setting of the FTP software.', 'gallerylink'); ?></div>
		<div><?php _e('* Please set to 777 or 757 the attributes of topurl directory. Because GalleryLink create thumbnail and RSS feed to the directory.', 'gallerylink'); ?></div>
		<div><?php _e('* (WordPress > Settings > General Timezone) Please specify your area other than UTC. For accurate time display of RSS feed.', 'gallerylink'); ?></div>
		<div><?php _e('* When you move to (WordPress > Appearance > Widgets), there is a widget GalleryLinkRssFeed. If you place you can set this to display the sidebar link the RSS feed.', 'gallerylink'); ?></div>
		</div>

		<form method="post" action="<?php echo $scriptname; ?>">
			<input type="hidden" name="gallerylink_admin_tabs" value="1" />
			<p class="submit">
				<input type="submit" class="button" name="Default" value="<?php _e('Default all settings', 'gallerylink') ?>" />
			</p>
		</form>

	  </div>

	  <div id="gallerylink-admin-tabs-2">
		<div class="wrap">

			<form method="post" action="<?php echo $scriptname.'#gallerylink-admin-tabs-2'; ?>">

			<p class="submit">
			  <input type="submit" class="button" name="Submit" value="<?php _e('Save Changes') ?>" />
			  <input type="submit" class="button" name="Default" value="<?php _e('Default') ?>" />
			</p>

			<h2><?php _e('Settings'); ?> <?php _e('AllData', 'gallerylink'); ?></h2>
			<table id="gallerylink-table2" border="1">
			<tbody>
				<tr>
					<th align="center" valign="middle"><?php _e('Attribute', 'gallerylink'); ?></th>
					<th align="center" valign="middle"><?php _e('Value'); ?></th>
					<th align="center" valign="middle"><?php _e('Description'); ?></th>
				</tr>
				<tr>
					<td align="center" valign="middle">set</td>
					<td align="center" valign="middle">all</td>
					<td align="left" valign="middle">
					<?php _e('Next only five. all(all data), album(image), movie(video), music(music), document(document)', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">sort</td>
					<td align="center" valign="middle">
					<?php $target_all_sort = $gallerylink_all['sort']; ?>
					<select id="gallerylink_all_sort" name="gallerylink_all_sort">
						<option <?php if ('new' == $target_all_sort)echo 'selected="selected"'; ?>>new</option>
						<option <?php if ('old' == $target_all_sort)echo 'selected="selected"'; ?>>old</option>
						<option <?php if ('des' == $target_all_sort)echo 'selected="selected"'; ?>>des</option>
						<option <?php if ('asc' == $target_all_sort)echo 'selected="selected"'; ?>>asc</option>
					</select>
					</td>
					<td align="left" valign="middle">
						<?php _e('Type of Sort', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">topurl</td>
					<td align="center" valign="middle">
						<select id="gallerylink_all_topurl" name="gallerylink_all_topurl" style="width: 100%;" />
							<?php echo $linkselectbox_all; ?>
						</select>
					</td>
					<td align="left" valign="middle">
						<?php _e('Full path to the top directory containing the data. Example:In the case of http://www.mysite.xxx/wordpress/wp-content/uploads is /wordpress/wp-content/uploads.', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">suffix</td>
					<td align="left" valign="top" rowspan="2"><?php _e("Audio's suffix and Video's suffix is following to the setting(set='music',set='movie'). Other than that, read all the data.", 'gallerylink'); ?></td>
					<td align="left" valign="middle">
						<?php _e('extension', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">suffix_2</td>
					<td align="left" valign="middle">
						<?php _e('second extension. Second candidate when working with html5', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">display</td>
					<td align="center" valign="middle">
						<input type="text" style="width: 100%;" id="gallerylink_all_display" name="gallerylink_all_display" value="<?php echo intval($gallerylink_all['display']) ?>" />
					</td>
					<td align="left" valign="middle">
						<?php _e('File Display per page', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">display_keitai</td>
					<td align="center" valign="middle">
						<input type="text" id="gallerylink_all_display_keitai" name="gallerylink_all_display_keitai" value="<?php echo intval($gallerylink_all['display_keitai']) ?>" style="width: 100%;" />
					</td>
					<td align="left" valign="middle">
						<?php _e('File Display per page(Japanese mobile phone)', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">thumbnail</td>
					<td align="center" valign="middle">
						-<?php echo get_option('thumbnail_size_w') ?>x<?php echo get_option('thumbnail_size_h') ?>
					</td>
					<td align="left" valign="middle">
						<?php _e('(album) thumbnail suffix name. (movie, music, document) The icon is displayed if you specify icon. The thumbnail no display if you do not specify anything.', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">generate_rssfeed</td>
					<td align="center" valign="middle">
					<?php $target_all_generate_rssfeed = $gallerylink_all['generate_rssfeed']; ?>
					<select id="gallerylink_all_generate_rssfeed" name="gallerylink_all_generate_rssfeed">
						<option <?php if ('on' == $target_all_generate_rssfeed)echo 'selected="selected"'; ?>>on</option>
						<option <?php if ('off' == $target_all_generate_rssfeed)echo 'selected="selected"'; ?>>off</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Generation of RSS feed.', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">rssname</td>
					<td align="center" valign="middle">
						<input type="text" style="width: 100%;" id="gallerylink_all_rssname" name="gallerylink_all_rssname" value="<?php echo $gallerylink_all['rssname'] ?>" />
					</td>
					<td align="left" valign="middle">
						<?php _e('The name of the RSS feed file (Use to widget)', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">rssmax</td>
					<td align="center" valign="middle">
						<input type="text" style="width: 100%;" id="gallerylink_all_rssmax" name="gallerylink_all_rssmax" value="<?php echo intval($gallerylink_all['rssmax']) ?>" />
					</td>
					<td align="left" valign="middle">
						<?php _e('Syndication feeds show the most recent (Use to widget)', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">filesize_show</td>
					<td align="center" valign="middle">
					<?php $target_all_filesize_show = $gallerylink_all['filesize_show']; ?>
					<select id="gallerylink_all_filesize_show" name="gallerylink_all_filesize_show">
						<option <?php if ('Show' == $target_all_filesize_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_all_filesize_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('File size', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">stamptime_show</td>
					<td align="center" valign="middle">
					<?php $target_all_stamptime_show = $gallerylink_all['stamptime_show']; ?>
					<select id="gallerylink_all_stamptime_show" name="gallerylink_all_stamptime_show">
						<option <?php if ('Show' == $target_all_stamptime_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_all_stamptime_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Date Time', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">exif_show</td>
					<td align="center" valign="middle">
					<?php
					if ( empty($gallerylink_all['exif_show']) ) {
						$target_all_exif_show = 'Hide';
					} else {
						$target_all_exif_show = $gallerylink_all['exif_show'];
					}
					?>
					<select id="gallerylink_all_exif_show" name="gallerylink_all_exif_show">
						<option <?php if ('Show' == $target_all_exif_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_all_exif_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">Exif</td>
				</tr>
				<tr>
					<td align="center" valign="middle">selectbox_show</td>
					<td align="center" valign="middle">
					<?php $target_all_selectbox_show = $gallerylink_all['selectbox_show']; ?>
					<select id="gallerylink_all_selectbox_show" name="gallerylink_all_selectbox_show">
						<option <?php if ('Show' == $target_all_selectbox_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_all_selectbox_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Selectbox of directories.', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">pagelinks_show</td>
					<td align="center" valign="middle">
					<?php $target_all_pagelinks_show = $gallerylink_all['pagelinks_show']; ?>
					<select id="gallerylink_all_pagelinks_show" name="gallerylink_all_pagelinks_show">
						<option <?php if ('Show' == $target_all_pagelinks_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_all_pagelinks_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Navigation of page.', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">sortlinks_show</td>
					<td align="center" valign="middle">
					<?php $target_all_sortlinks_show = $gallerylink_all['sortlinks_show']; ?>
					<select id="gallerylink_all_sortlinks_show" name="gallerylink_all_sortlinks_show">
						<option <?php if ('Show' == $target_all_sortlinks_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_all_sortlinks_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Navigation of sort.', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">searchbox_show</td>
					<td align="center" valign="middle">
					<?php $target_all_searchbox_show = $gallerylink_all['searchbox_show']; ?>
					<select id="gallerylink_all_searchbox_show" name="gallerylink_all_searchbox_show">
						<option <?php if ('Show' == $target_all_searchbox_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_all_searchbox_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Search box', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">rssicon_show</td>
					<td align="center" valign="middle">
					<?php $target_all_rssicon_show = $gallerylink_all['rssicon_show']; ?>
					<select id="gallerylink_all_rssicon_show" name="gallerylink_all_rssicon_show">
						<option <?php if ('Show' == $target_all_rssicon_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_all_rssicon_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('RSS Icon', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">credit_show</td>
					<td align="center" valign="middle">
					<?php $target_all_credit_show = $gallerylink_all['credit_show']; ?>
					<select id="gallerylink_all_credit_show" name="gallerylink_all_credit_show">
						<option <?php if ('Show' == $target_all_credit_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_all_credit_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Credit', 'gallerylink') ?>
					</td>
				</tr>
			</tbody>
			</table>

			<input type="hidden" name="gallerylink_admin_tabs" value="2" />
			<p class="submit">
			  <input type="submit" class="button" name="Submit" value="<?php _e('Save Changes') ?>" />
			</p>

			</form>

		</div>
	  </div>

	  <div id="gallerylink-admin-tabs-3">
		<div class="wrap">

			<form method="post" action="<?php echo $scriptname.'#gallerylink-admin-tabs-3'; ?>">

			<p class="submit">
			  <input type="submit" class="button" name="Submit" value="<?php _e('Save Changes') ?>" />
			  <input type="submit" class="button" name="Default" value="<?php _e('Default') ?>" />
			</p>

			<h2><?php _e('Settings'); ?> <?php _e('Album', 'gallerylink'); ?></h2>	
			<table id="gallerylink-table3" border="1">
			<tbody>
				<tr>
					<th align="center" valign="middle"><?php _e('Attribute', 'gallerylink'); ?></th>
					<th align="center" valign="middle"><?php _e('Value'); ?></th>
					<th align="center" valign="middle"><?php _e('Description'); ?></th>
				</tr>
				<tr>
					<td align="center" valign="middle">set</td>
					<td align="center" valign="middle">album</td>
					<td align="left" valign="middle">
					<?php _e('Next only five. all(all data), album(image), movie(video), music(music), document(document)', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">sort</td>
					<td align="center" valign="middle">
					<?php $target_album_sort = $gallerylink_album['sort']; ?>
					<select id="gallerylink_album_sort" name="gallerylink_album_sort">
						<option <?php if ('new' == $target_album_sort)echo 'selected="selected"'; ?>>new</option>
						<option <?php if ('old' == $target_album_sort)echo 'selected="selected"'; ?>>old</option>
						<option <?php if ('des' == $target_album_sort)echo 'selected="selected"'; ?>>des</option>
						<option <?php if ('asc' == $target_album_sort)echo 'selected="selected"'; ?>>asc</option>
					</select>
					</td>
					<td align="left" valign="middle">
						<?php _e('Type of Sort', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">topurl</td>
					<td align="center" valign="middle">
						<select id="gallerylink_album_topurl" name="gallerylink_album_topurl" style="width: 100%;" />
							<?php echo $linkselectbox_album; ?>
						</select>
					</td>
					<td align="left" valign="middle">
						<?php _e('Full path to the top directory containing the data. Example:In the case of http://www.mysite.xxx/wordpress/wp-content/uploads is /wordpress/wp-content/uploads.', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">suffix</td>
					<td align="center" valign="middle">
					<?php $target_album_suffix = $gallerylink_album['suffix']; ?>
					<select id="gallerylink_album_suffix" name="gallerylink_album_suffix">
						<option <?php if ('all' == $target_album_suffix)echo 'selected="selected"'; ?>>all</option>
						<?php
							$exts = $this->exts('image');
							foreach ( $exts as $ext ) {
								?>
								<option <?php if ($ext == $target_album_suffix)echo 'selected="selected"'; ?>><?php echo $ext ?></option>
								<?php
							}
						?>
					</select>
					</td>
					<td align="left" valign="middle">
						<?php _e('extension', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">suffix_keitai</td>
					<td align="center" valign="middle">
					<?php $target_album_suffix_keitai = $gallerylink_album['suffix_keitai']; ?>
					<select id="gallerylink_album_suffix_keitai" name="gallerylink_album_suffix_keitai">
						<option <?php if ('all' == $target_album_suffix_keitai)echo 'selected="selected"'; ?>>all</option>
						<?php
							$exts = $this->exts('image');
							foreach ( $exts as $ext ) {
								?>
								<option <?php if ($ext == $target_album_suffix_keitai)echo 'selected="selected"'; ?>><?php echo $ext ?></option>
								<?php
							}
						?>
					</select>
					</td>
					<td align="left" valign="middle">
						<?php _e('extension of Japanese mobile phone', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">display</td>
					<td align="center" valign="middle">
						<input type="text" style="width: 100%;" id="gallerylink_album_display" name="gallerylink_album_display" value="<?php echo intval($gallerylink_album['display']) ?>" />
					</td>
					<td align="left" valign="middle">
						<?php _e('File Display per page', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">display_keitai</td>
					<td align="center" valign="middle">
						<input type="text" id="gallerylink_album_display_keitai" name="gallerylink_album_display_keitai" value="<?php echo intval($gallerylink_album['display_keitai']) ?>" style="width: 100%;" />
					</td>
					<td align="left" valign="middle">
						<?php _e('File Display per page(Japanese mobile phone)', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">thumbnail</td>
					<td align="center" valign="middle">
						-<?php echo get_option('thumbnail_size_w') ?>x<?php echo get_option('thumbnail_size_h') ?>
					</td>
					<td align="left" valign="middle">
						<?php _e('(album) thumbnail suffix name. (movie, music, document) The icon is displayed if you specify icon. The thumbnail no display if you do not specify anything.', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">generate_rssfeed</td>
					<td align="center" valign="middle">
					<?php $target_album_generate_rssfeed = $gallerylink_album['generate_rssfeed']; ?>
					<select id="gallerylink_album_generate_rssfeed" name="gallerylink_album_generate_rssfeed">
						<option <?php if ('on' == $target_album_generate_rssfeed)echo 'selected="selected"'; ?>>on</option>
						<option <?php if ('off' == $target_album_generate_rssfeed)echo 'selected="selected"'; ?>>off</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Generation of RSS feed.', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">rssname</td>
					<td align="center" valign="middle">
						<input type="text" style="width: 100%;" style="width: 100%;" id="gallerylink_album_rssname" name="gallerylink_album_rssname" value="<?php echo $gallerylink_album['rssname'] ?>" />
					</td>
					<td align="left" valign="middle">
						<?php _e('The name of the RSS feed file (Use to widget)', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">rssmax</td>
					<td align="center" valign="middle">
						<input type="text" style="width: 100%;" id="gallerylink_album_rssmax" name="gallerylink_album_rssmax" value="<?php echo intval($gallerylink_album['rssmax']) ?>" />
					</td>
					<td align="left" valign="middle">
						<?php _e('Syndication feeds show the most recent (Use to widget)', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">filesize_show</td>
					<td align="center" valign="middle">
					<?php $target_album_filesize_show = $gallerylink_album['filesize_show']; ?>
					<select id="gallerylink_album_filesize_show" name="gallerylink_album_filesize_show">
						<option <?php if ('Show' == $target_album_filesize_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_album_filesize_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('File size', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">stamptime_show</td>
					<td align="center" valign="middle">
					<?php $target_album_stamptime_show = $gallerylink_album['stamptime_show']; ?>
					<select id="gallerylink_album_stamptime_show" name="gallerylink_album_stamptime_show">
						<option <?php if ('Show' == $target_album_stamptime_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_album_stamptime_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Date Time', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">exif_show</td>
					<td align="center" valign="middle">
					<?php
					if ( empty($gallerylink_album['exif_show']) ) {
						$target_album_exif_show = 'Hide';
					} else {
						$target_album_exif_show = $gallerylink_album['exif_show'];
					}
					?>
					<select id="gallerylink_album_exif_show" name="gallerylink_album_exif_show">
						<option <?php if ('Show' == $target_album_exif_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_album_exif_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">Exif</td>
				</tr>
				<tr>
					<td align="center" valign="middle">selectbox_show</td>
					<td align="center" valign="middle">
					<?php $target_album_selectbox_show = $gallerylink_album['selectbox_show']; ?>
					<select id="gallerylink_album_selectbox_show" name="gallerylink_album_selectbox_show">
						<option <?php if ('Show' == $target_album_selectbox_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_album_selectbox_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Selectbox of directories.', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">pagelinks_show</td>
					<td align="center" valign="middle">
					<?php $target_album_pagelinks_show = $gallerylink_album['pagelinks_show']; ?>
					<select id="gallerylink_album_pagelinks_show" name="gallerylink_album_pagelinks_show">
						<option <?php if ('Show' == $target_album_pagelinks_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_album_pagelinks_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Navigation of page.', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">sortlinks_show</td>
					<td align="center" valign="middle">
					<?php $target_album_sortlinks_show = $gallerylink_album['sortlinks_show']; ?>
					<select id="gallerylink_album_sortlinks_show" name="gallerylink_album_sortlinks_show">
						<option <?php if ('Show' == $target_album_sortlinks_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_album_sortlinks_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Navigation of sort.', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">searchbox_show</td>
					<td align="center" valign="middle">
					<?php $target_album_searchbox_show = $gallerylink_album['searchbox_show']; ?>
					<select id="gallerylink_album_searchbox_show" name="gallerylink_album_searchbox_show">
						<option <?php if ('Show' == $target_album_searchbox_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_album_searchbox_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Search box', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">rssicon_show</td>
					<td align="center" valign="middle">
					<?php $target_album_rssicon_show = $gallerylink_album['rssicon_show']; ?>
					<select id="gallerylink_album_rssicon_show" name="gallerylink_album_rssicon_show">
						<option <?php if ('Show' == $target_album_rssicon_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_album_rssicon_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('RSS Icon', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">credit_show</td>
					<td align="center" valign="middle">
					<?php $target_album_credit_show = $gallerylink_album['credit_show']; ?>
					<select id="gallerylink_album_credit_show" name="gallerylink_album_credit_show">
						<option <?php if ('Show' == $target_album_credit_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_album_credit_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Credit', 'gallerylink') ?>
					</td>
				</tr>
			</tbody>
			</table>

			<input type="hidden" name="gallerylink_admin_tabs" value="3" />
			<p class="submit">
			  <input type="submit" class="button" name="Submit" value="<?php _e('Save Changes') ?>" />
			</p>

			</form>

		</div>
	  </div>

	  <div id="gallerylink-admin-tabs-4">
		<div class="wrap">

			<form method="post" action="<?php echo $scriptname.'#gallerylink-admin-tabs-4'; ?>">

			<p class="submit">
			  <input type="submit" class="button" name="Submit" value="<?php _e('Save Changes') ?>" />
			  <input type="submit" class="button" name="Default" value="<?php _e('Default') ?>" />
			</p>

			<h2><?php _e('Settings'); ?> <?php _e('Video', 'gallerylink'); ?></h2>	
			<table id="gallerylink-table4" border="1">
			<tbody>
				<tr>
					<th align="center" valign="middle"><?php _e('Attribute', 'gallerylink'); ?></th>
					<th align="center" valign="middle"><?php _e('Value'); ?></th>
					<th align="center" valign="middle"><?php _e('Description'); ?></th>
				</tr>
				<tr>
					<td align="center" valign="middle">set</td>
					<td align="center" valign="middle">movie</td>
					<td align="left" valign="middle">
					<?php _e('Next only five. all(all data), album(image), movie(video), music(music), document(document)', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">sort</td>
					<td align="center" valign="middle">
					<?php $target_movie_sort = $gallerylink_movie['sort']; ?>
					<select id="gallerylink_movie_sort" name="gallerylink_movie_sort">
						<option <?php if ('new' == $target_movie_sort)echo 'selected="selected"'; ?>>new</option>
						<option <?php if ('old' == $target_movie_sort)echo 'selected="selected"'; ?>>old</option>
						<option <?php if ('des' == $target_movie_sort)echo 'selected="selected"'; ?>>des</option>
						<option <?php if ('asc' == $target_movie_sort)echo 'selected="selected"'; ?>>asc</option>
					</select>
					</td>
					<td align="left" valign="middle">
						<?php _e('Type of Sort', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">topurl</td>
					<td align="center" valign="middle">
						<select id="gallerylink_movie_topurl" name="gallerylink_movie_topurl" style="width: 100%;" />
							<?php echo $linkselectbox_movie; ?>
						</select>
					</td>
					<td align="left" valign="middle">
						<?php _e('Full path to the top directory containing the data. Example:In the case of http://www.mysite.xxx/wordpress/wp-content/uploads is /wordpress/wp-content/uploads.', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">suffix</td>
					<td align="center" valign="middle">
					<?php $target_movie_suffix = $gallerylink_movie['suffix']; ?>
					<select id="gallerylink_movie_suffix" name="gallerylink_movie_suffix">
						<?php
							$exts = $this->exts('video');
							foreach ( $exts as $ext ) {
								?>
								<option <?php if ($ext == $target_movie_suffix)echo 'selected="selected"'; ?>><?php echo $ext ?></option>
								<?php
							}
						?>
					</select>
					</td>
					<td align="left" valign="middle">
						<?php _e('extension', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">suffix_2</td>
					<td align="center" valign="middle">
					<?php $target_movie_suffix_2 = $gallerylink_movie['suffix_2']; ?>
					<select id="gallerylink_movie_suffix_2" name="gallerylink_movie_suffix_2">
						<?php
							$exts = $this->exts('video');
							foreach ( $exts as $ext ) {
								?>
								<option <?php if ($ext == $target_movie_suffix_2)echo 'selected="selected"'; ?>><?php echo $ext ?></option>
								<?php
							}
						?>
					</select>
					</td>
					<td align="left" valign="middle">
						<?php _e('second extension. Second candidate when working with html5', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">suffix_keitai</td>
					<td align="center" valign="middle">
					<?php $target_movie_suffix_keitai = $gallerylink_movie['suffix_keitai']; ?>
					<select id="gallerylink_movie_suffix_keitai" name="gallerylink_movie_suffix_keitai">
						<option <?php if ('3gp' == $target_movie_suffix_keitai)echo 'selected="selected"'; ?>>3gp</option>
						<option <?php if ('3g2' == $target_movie_suffix_keitai)echo 'selected="selected"'; ?>>3g2</option>
					</select>
					</td>
					<td align="left" valign="middle">
						<?php _e('extension of Japanese mobile phone', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">display</td>
					<td align="center" valign="middle">
						<input type="text" style="width: 100%;" id="gallerylink_movie_display" name="gallerylink_movie_display" value="<?php echo intval($gallerylink_movie['display']) ?>" />
					</td>
					<td align="left" valign="middle">
						<?php _e('File Display per page', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">display_keitai</td>
					<td align="center" valign="middle">
						<input type="text" id="gallerylink_movie_display_keitai" name="gallerylink_movie_display_keitai" value="<?php echo intval($gallerylink_movie['display_keitai']) ?>" style="width: 100%;" />
					</td>
					<td align="left" valign="middle">
						<?php _e('File Display per page(Japanese mobile phone)', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">thumbnail</td>
					<td align="center" valign="middle">
					<?php $target_movie_thumbnail = $gallerylink_movie['thumbnail']; ?>
					<select id="gallerylink_movie_thumbnail" name="gallerylink_movie_thumbnail">
						<option <?php if ('' == $target_movie_thumbnail)echo 'selected="selected"'; ?>></option>
						<option <?php if ('icon' == $target_movie_thumbnail)echo 'selected="selected"'; ?>>icon</option>
					</select>
					</td>
					<td align="left" valign="middle">
						<?php _e('(album) thumbnail suffix name. (movie, music, document) The icon is displayed if you specify icon. The thumbnail no display if you do not specify anything.', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">generate_rssfeed</td>
					<td align="center" valign="middle">
					<?php $target_movie_generate_rssfeed = $gallerylink_movie['generate_rssfeed']; ?>
					<select id="gallerylink_movie_generate_rssfeed" name="gallerylink_movie_generate_rssfeed">
						<option <?php if ('on' == $target_movie_generate_rssfeed)echo 'selected="selected"'; ?>>on</option>
						<option <?php if ('off' == $target_movie_generate_rssfeed)echo 'selected="selected"'; ?>>off</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Generation of RSS feed.', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">rssname</td>
					<td align="center" valign="middle">
						<input type="text" style="width: 100%;" id="gallerylink_movie_rssname" name="gallerylink_movie_rssname" value="<?php echo $gallerylink_movie['rssname'] ?>" />
					</td>
					<td align="left" valign="middle">
						<?php _e('The name of the RSS feed file (Use to widget)', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">rssmax</td>
					<td align="center" valign="middle">
						<input type="text" style="width: 100%;" id="gallerylink_movie_rssmax" name="gallerylink_movie_rssmax" value="<?php echo intval($gallerylink_movie['rssmax']) ?>" />
					</td>
					<td align="left" valign="middle">
						<?php _e('Syndication feeds show the most recent (Use to widget)', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">filesize_show</td>
					<td align="center" valign="middle">
					<?php $target_movie_filesize_show = $gallerylink_movie['filesize_show']; ?>
					<select id="gallerylink_movie_filesize_show" name="gallerylink_movie_filesize_show">
						<option <?php if ('Show' == $target_movie_filesize_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_movie_filesize_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('File size', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">stamptime_show</td>
					<td align="center" valign="middle">
					<?php $target_movie_stamptime_show = $gallerylink_movie['stamptime_show']; ?>
					<select id="gallerylink_movie_stamptime_show" name="gallerylink_movie_stamptime_show">
						<option <?php if ('Show' == $target_movie_stamptime_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_movie_stamptime_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Date Time', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">selectbox_show</td>
					<td align="center" valign="middle">
					<?php $target_movie_selectbox_show = $gallerylink_movie['selectbox_show']; ?>
					<select id="gallerylink_movie_selectbox_show" name="gallerylink_movie_selectbox_show">
						<option <?php if ('Show' == $target_movie_selectbox_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_movie_selectbox_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					</td>
					<td align="left" valign="middle">
					<?php _e('Selectbox of directories.', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">pagelinks_show</td>
					<td align="center" valign="middle">
					<?php $target_movie_pagelinks_show = $gallerylink_movie['pagelinks_show']; ?>
					<select id="gallerylink_movie_pagelinks_show" name="gallerylink_movie_pagelinks_show">
						<option <?php if ('Show' == $target_movie_pagelinks_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_movie_pagelinks_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Navigation of page.', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">sortlinks_show</td>
					<td align="center" valign="middle">
					<?php $target_movie_sortlinks_show = $gallerylink_movie['sortlinks_show']; ?>
					<select id="gallerylink_movie_sortlinks_show" name="gallerylink_movie_sortlinks_show">
						<option <?php if ('Show' == $target_movie_sortlinks_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_movie_sortlinks_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Navigation of sort.', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">searchbox_show</td>
					<td align="center" valign="middle">
					<?php $target_movie_searchbox_show = $gallerylink_movie['searchbox_show']; ?>
					<select id="gallerylink_movie_searchbox_show" name="gallerylink_movie_searchbox_show">
						<option <?php if ('Show' == $target_movie_searchbox_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_movie_searchbox_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Search box', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">rssicon_show</td>
					<td align="center" valign="middle">
					<?php $target_movie_rssicon_show = $gallerylink_movie['rssicon_show']; ?>
					<select id="gallerylink_movie_rssicon_show" name="gallerylink_movie_rssicon_show">
						<option <?php if ('Show' == $target_movie_rssicon_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_movie_rssicon_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('RSS Icon', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">credit_show</td>
					<td align="center" valign="middle">
					<?php $target_movie_credit_show = $gallerylink_movie['credit_show']; ?>
					<select id="gallerylink_movie_credit_show" name="gallerylink_movie_credit_show">
						<option <?php if ('Show' == $target_movie_credit_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_movie_credit_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Credit', 'gallerylink') ?>
					</td>
				</tr>
			</tbody>
			</table>

			<input type="hidden" name="gallerylink_admin_tabs" value="4" />
			<p class="submit">
			  <input type="submit" class="button" name="Submit" value="<?php _e('Save Changes') ?>" />
			</p>

			</form>

		</div>
	  </div>

	  <div id="gallerylink-admin-tabs-5">
		<div class="wrap">

			<form method="post" action="<?php echo $scriptname.'#gallerylink-admin-tabs-5'; ?>">

			<p class="submit">
			  <input type="submit" class="button" name="Submit" value="<?php _e('Save Changes') ?>" />
			  <input type="submit" class="button" name="Default" value="<?php _e('Default') ?>" />
			</p>

			<h2><?php _e('Settings'); ?> <?php _e('Music', 'gallerylink'); ?></h2>	
			<table id="gallerylink-table5" border="1">
			<tbody>
				<tr>
					<th align="center" valign="middle"><?php _e('Attribute', 'gallerylink'); ?></th>
					<th align="center" valign="middle"><?php _e('Value'); ?></th>
					<th align="center" valign="middle"><?php _e('Description'); ?></th>
				</tr>
				<tr>
					<td align="center" valign="middle">set</td>
					<td align="center" valign="middle">music</td>
					<td align="left" valign="middle">
					<?php _e('Next only five. all(all data), album(image), movie(video), music(music), document(document)', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">sort</td>
					<td align="center" valign="middle">
					<?php $target_music_sort = $gallerylink_music['sort']; ?>
					<select id="gallerylink_music_sort" name="gallerylink_music_sort">
						<option <?php if ('new' == $target_music_sort)echo 'selected="selected"'; ?>>new</option>
						<option <?php if ('old' == $target_music_sort)echo 'selected="selected"'; ?>>old</option>
						<option <?php if ('des' == $target_music_sort)echo 'selected="selected"'; ?>>des</option>
						<option <?php if ('asc' == $target_music_sort)echo 'selected="selected"'; ?>>asc</option>
					</select>
					</td>
					<td align="left" valign="middle">
						<?php _e('Type of Sort', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">topurl</td>
					<td align="center" valign="middle">
						<select id="gallerylink_music_topurl" name="gallerylink_music_topurl" style="width: 100%;" />
							<?php echo $linkselectbox_music; ?>
						</select>
					</td>
					<td align="left" valign="middle">
						<?php _e('Full path to the top directory containing the data. Example:In the case of http://www.mysite.xxx/wordpress/wp-content/uploads is /wordpress/wp-content/uploads.', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">suffix</td>
					<td align="center" valign="middle">
					<?php $target_music_suffix = $gallerylink_music['suffix']; ?>
					<select id="gallerylink_music_suffix" name="gallerylink_music_suffix">
						<?php
							$exts = $this->exts('audio');
							foreach ( $exts as $ext ) {
								?>
								<option <?php if ($ext == $target_music_suffix)echo 'selected="selected"'; ?>><?php echo $ext ?></option>
								<?php
							}
						?>
					</select>
					</td>
					<td align="left" valign="middle">
						<?php _e('extension', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">suffix_2</td>
					<td align="center" valign="middle">
					<?php $target_music_suffix_2 = $gallerylink_music['suffix_2']; ?>
					<select id="gallerylink_music_suffix_2" name="gallerylink_music_suffix_2">
						<?php
							$exts = $this->exts('audio');
							foreach ( $exts as $ext ) {
								?>
								<option <?php if ($ext == $target_music_suffix_2)echo 'selected="selected"'; ?>><?php echo $ext ?></option>
								<?php
							}
						?>
					</select>
					</td>
					<td align="left" valign="middle">
						<?php _e('second extension. Second candidate when working with html5', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">suffix_keitai</td>
					<td align="center" valign="middle">
					<?php $target_music_suffix_keitai = $gallerylink_music['suffix_keitai']; ?>
					<select id="gallerylink_music_suffix_keitai" name="gallerylink_music_suffix_keitai">
						<option <?php if ('3gp' == $target_music_suffix_keitai)echo 'selected="selected"'; ?>>3gp</option>
						<option <?php if ('3g2' == $target_music_suffix_keitai)echo 'selected="selected"'; ?>>3g2</option>
					</select>
					</td>
					<td align="left" valign="middle">
						<?php _e('extension of Japanese mobile phone', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">display</td>
					<td align="center" valign="middle">
						<input type="text" style="width: 100%;" id="gallerylink_music_display" name="gallerylink_music_display" value="<?php echo intval($gallerylink_music['display']) ?>" />
					</td>
					<td align="left" valign="middle">
						<?php _e('File Display per page', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">display_keitai</td>
					<td align="center" valign="middle">
						<input type="text" id="gallerylink_music_display_keitai" name="gallerylink_music_display_keitai" value="<?php echo intval($gallerylink_music['display_keitai']) ?>" style="width: 100%;" />
					</td>
					<td align="left" valign="middle">
						<?php _e('File Display per page(Japanese mobile phone)', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">thumbnail</td>
					<td align="center" valign="middle">
					<?php $target_music_thumbnail = $gallerylink_music['thumbnail']; ?>
					<select id="gallerylink_music_thumbnail" name="gallerylink_music_thumbnail">
						<option <?php if ('' == $target_music_thumbnail)echo 'selected="selected"'; ?>></option>
						<option <?php if ('icon' == $target_music_thumbnail)echo 'selected="selected"'; ?>>icon</option>
					</select>
					</td>
					<td align="left" valign="middle">
						<?php _e('(album) thumbnail suffix name. (movie, music, document) The icon is displayed if you specify icon. The thumbnail no display if you do not specify anything.', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">generate_rssfeed</td>
					<td align="center" valign="middle">
					<?php $target_music_generate_rssfeed = $gallerylink_music['generate_rssfeed']; ?>
					<select id="gallerylink_music_generate_rssfeed" name="gallerylink_music_generate_rssfeed">
						<option <?php if ('on' == $target_music_generate_rssfeed)echo 'selected="selected"'; ?>>on</option>
						<option <?php if ('off' == $target_music_generate_rssfeed)echo 'selected="selected"'; ?>>off</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Generation of RSS feed.', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">rssname</td>
					<td align="center" valign="middle">
						<input type="text" style="width: 100%;" id="gallerylink_music_rssname" name="gallerylink_music_rssname" value="<?php echo $gallerylink_music['rssname'] ?>" />
					</td>
					<td align="left" valign="middle">
						<?php _e('The name of the RSS feed file (Use to widget)', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">rssmax</td>
					<td align="center" valign="middle">
						<input type="text" style="width: 100%;" id="gallerylink_music_rssmax" name="gallerylink_music_rssmax" value="<?php echo intval($gallerylink_music['rssmax']) ?>" />
					</td>
					<td align="left" valign="middle">
						<?php _e('Syndication feeds show the most recent (Use to widget)', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">filesize_show</td>
					<td align="center" valign="middle">
					<?php $target_music_filesize_show = $gallerylink_music['filesize_show']; ?>
					<select id="gallerylink_music_filesize_show" name="gallerylink_music_filesize_show">
						<option <?php if ('Show' == $target_music_filesize_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_music_filesize_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('File size', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">stamptime_show</td>
					<td align="center" valign="middle">
					<?php $target_music_stamptime_show = $gallerylink_music['stamptime_show']; ?>
					<select id="gallerylink_music_stamptime_show" name="gallerylink_music_stamptime_show">
						<option <?php if ('Show' == $target_music_stamptime_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_music_stamptime_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Date Time', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">selectbox_show</td>
					<td align="center" valign="middle">
					<?php $target_music_selectbox_show = $gallerylink_music['selectbox_show']; ?>
					<select id="gallerylink_music_selectbox_show" name="gallerylink_music_selectbox_show">
						<option <?php if ('Show' == $target_music_selectbox_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_music_selectbox_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Selectbox of directories.', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">pagelinks_show</td>
					<td align="center" valign="middle">
					<?php $target_music_pagelinks_show = $gallerylink_music['pagelinks_show']; ?>
					<select id="gallerylink_music_pagelinks_show" name="gallerylink_music_pagelinks_show">
						<option <?php if ('Show' == $target_music_pagelinks_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_music_pagelinks_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Navigation of page.', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">sortlinks_show</td>
					<td align="center" valign="middle">
					<?php $target_music_sortlinks_show = $gallerylink_music['sortlinks_show']; ?>
					<select id="gallerylink_music_sortlinks_show" name="gallerylink_music_sortlinks_show">
						<option <?php if ('Show' == $target_music_sortlinks_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_music_sortlinks_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Navigation of sort.', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">searchbox_show</td>
					<td align="center" valign="middle">
					<?php $target_music_searchbox_show = $gallerylink_music['searchbox_show']; ?>
					<select id="gallerylink_music_searchbox_show" name="gallerylink_music_searchbox_show">
						<option <?php if ('Show' == $target_music_searchbox_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_music_searchbox_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Search box', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">rssicon_show</td>
					<td align="center" valign="middle">
					<?php $target_music_rssicon_show = $gallerylink_music['rssicon_show']; ?>
					<select id="gallerylink_music_rssicon_show" name="gallerylink_music_rssicon_show">
						<option <?php if ('Show' == $target_music_rssicon_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_music_rssicon_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('RSS Icon', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">credit_show</td>
					<td align="center" valign="middle">
					<?php $target_music_credit_show = $gallerylink_music['credit_show']; ?>
					<select id="gallerylink_music_credit_show" name="gallerylink_music_credit_show">
						<option <?php if ('Show' == $target_music_credit_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_music_credit_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Credit', 'gallerylink') ?>
					</td>
				</tr>
			</tbody>
			</table>

			<input type="hidden" name="gallerylink_admin_tabs" value="5" />
			<p class="submit">
			  <input type="submit" class="button" name="Submit" value="<?php _e('Save Changes') ?>" />
			</p>

			</form>

		</div>
	  </div>

	  <div id="gallerylink-admin-tabs-6">
		<div class="wrap">

			<form method="post" action="<?php echo $scriptname.'#gallerylink-admin-tabs-6'; ?>">

			<p class="submit">
			  <input type="submit" class="button" name="Submit" value="<?php _e('Save Changes') ?>" />
			  <input type="submit" class="button" name="Default" value="<?php _e('Default') ?>" />
			</p>

			<h2><?php _e('Settings'); ?> <?php _e('Document', 'gallerylink'); ?></h2>	
			<table id="gallerylink-table6" border="1">
			<tbody>
				<tr>
					<th align="center" valign="middle"><?php _e('Attribute', 'gallerylink'); ?></th>
					<th align="center" valign="middle"><?php _e('Value'); ?></th>
					<th align="center" valign="middle"><?php _e('Description'); ?></th>
				</tr>
				<tr>
					<td align="center" valign="middle">set</td>
					<td align="center" valign="middle">document</td>
					<td align="left" valign="middle">
					<?php _e('Next only five. all(all data), album(image), movie(video), music(music), document(document)', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">sort</td>
					<td align="center" valign="middle">
					<?php $target_document_sort = $gallerylink_document['sort']; ?>
					<select id="gallerylink_document_sort" name="gallerylink_document_sort">
						<option <?php if ('new' == $target_document_sort)echo 'selected="selected"'; ?>>new</option>
						<option <?php if ('old' == $target_document_sort)echo 'selected="selected"'; ?>>old</option>
						<option <?php if ('des' == $target_document_sort)echo 'selected="selected"'; ?>>des</option>
						<option <?php if ('asc' == $target_document_sort)echo 'selected="selected"'; ?>>asc</option>
					</select>
					</td>
					<td align="left" valign="middle">
						<?php _e('Type of Sort', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">topurl</td>
					<td align="center" valign="middle">
						<select id="gallerylink_document_topurl" name="gallerylink_document_topurl" style="width: 100%;" />
							<?php echo $linkselectbox_document; ?>
						</select>
					</td>
					<td align="left" valign="middle">
						<?php _e('Full path to the top directory containing the data. Example:In the case of http://www.mysite.xxx/wordpress/wp-content/uploads is /wordpress/wp-content/uploads.', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">suffix</td>
					<td align="center" valign="middle">
					<?php $target_document_suffix = $gallerylink_document['suffix']; ?>
					<select id="gallerylink_document_suffix" name="gallerylink_document_suffix">
						<option <?php if ('all' == $target_document_suffix)echo 'selected="selected"'; ?>>all</option>
						<?php
							$exts = $this->exts('document');
							foreach ( $exts as $ext ) {
								?>
								<option <?php if ($ext == $target_document_suffix)echo 'selected="selected"'; ?>><?php echo $ext ?></option>
								<?php
							}
							$exts = $this->exts('spreadsheet');
							foreach ( $exts as $ext ) {
								?>
								<option <?php if ($ext == $target_document_suffix)echo 'selected="selected"'; ?>><?php echo $ext ?></option>
								<?php
							}
							$exts = $this->exts('interactive');
							foreach ( $exts as $ext ) {
								?>
								<option <?php if ($ext == $target_document_suffix)echo 'selected="selected"'; ?>><?php echo $ext ?></option>
								<?php
							}
							$exts = $this->exts('text');
							foreach ( $exts as $ext ) {
								?>
								<option <?php if ($ext == $target_document_suffix)echo 'selected="selected"'; ?>><?php echo $ext ?></option>
								<?php
							}
							$exts = $this->exts('archive');
							foreach ( $exts as $ext ) {
								?>
								<option <?php if ($ext == $target_document_suffix)echo 'selected="selected"'; ?>><?php echo $ext ?></option>
								<?php
							}
							$exts = $this->exts('code');
							foreach ( $exts as $ext ) {
								?>
								<option <?php if ($ext == $target_document_suffix)echo 'selected="selected"'; ?>><?php echo $ext ?></option>
								<?php
							}
						?>
					</select>
					</td>
					<td align="left" valign="middle">
						<?php _e('extension', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">suffix_keitai</td>
					<td align="center" valign="middle">
					<?php $target_document_suffix_keitai = $gallerylink_document['suffix_keitai']; ?>
					<select id="gallerylink_document_suffix_keitai" name="gallerylink_document_suffix_keitai">
						<option <?php if ('all' == $target_document_suffix_keitai)echo 'selected="selected"'; ?>>all</option>
						<?php
							$exts = $this->exts('document');
							foreach ( $exts as $ext ) {
								?>
								<option <?php if ($ext == $target_document_suffix_keitai)echo 'selected="selected"'; ?>><?php echo $ext ?></option>
								<?php
							}
							$exts = $this->exts('spreadsheet');
							foreach ( $exts as $ext ) {
								?>
								<option <?php if ($ext == $target_document_suffix_keitai)echo 'selected="selected"'; ?>><?php echo $ext ?></option>
								<?php
							}
							$exts = $this->exts('interactive');
							foreach ( $exts as $ext ) {
								?>
								<option <?php if ($ext == $target_document_suffix_keitai)echo 'selected="selected"'; ?>><?php echo $ext ?></option>
								<?php
							}
							$exts = $this->exts('text');
							foreach ( $exts as $ext ) {
								?>
								<option <?php if ($ext == $target_document_suffix_keitai)echo 'selected="selected"'; ?>><?php echo $ext ?></option>
								<?php
							}
							$exts = $this->exts('archive');
							foreach ( $exts as $ext ) {
								?>
								<option <?php if ($ext == $target_document_suffix_keitai)echo 'selected="selected"'; ?>><?php echo $ext ?></option>
								<?php
							}
							$exts = $this->exts('code');
							foreach ( $exts as $ext ) {
								?>
								<option <?php if ($ext == $target_document_suffix_keitai)echo 'selected="selected"'; ?>><?php echo $ext ?></option>
								<?php
							}
						?>
					</select>
					</td>
					<td align="left" valign="middle">
						<?php _e('extension of Japanese mobile phone', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">display</td>
					<td align="center" valign="middle">
						<input type="text" style="width: 100%;" id="gallerylink_document_display" name="gallerylink_document_display" value="<?php echo intval($gallerylink_document['display']) ?>" />
					</td>
					<td align="left" valign="middle">
						<?php _e('File Display per page', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">display_keitai</td>
					<td align="center" valign="middle">
						<input type="text" id="gallerylink_document_display_keitai" name="gallerylink_document_display_keitai" value="<?php echo intval($gallerylink_document['display_keitai']) ?>" style="width: 100%;" />
					</td>
					<td align="left" valign="middle">
						<?php _e('File Display per page(Japanese mobile phone)', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">thumbnail</td>
					<td align="center" valign="middle">
					<?php $target_document_thumbnail = $gallerylink_document['thumbnail']; ?>
					<select id="gallerylink_document_thumbnail" name="gallerylink_document_thumbnail">
						<option <?php if ('' == $target_document_thumbnail)echo 'selected="selected"'; ?>></option>
						<option <?php if ('icon' == $target_document_thumbnail)echo 'selected="selected"'; ?>>icon</option>
					</select>
					</td>
					<td align="left" valign="middle">
						<?php _e('(album) thumbnail suffix name. (movie, music, document) The icon is displayed if you specify icon. The thumbnail no display if you do not specify anything.', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">generate_rssfeed</td>
					<td align="center" valign="middle">
					<?php $target_document_generate_rssfeed = $gallerylink_document['generate_rssfeed']; ?>
					<select id="gallerylink_document_generate_rssfeed" name="gallerylink_document_generate_rssfeed">
						<option <?php if ('on' == $target_document_generate_rssfeed)echo 'selected="selected"'; ?>>on</option>
						<option <?php if ('off' == $target_document_generate_rssfeed)echo 'selected="selected"'; ?>>off</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Generation of RSS feed.', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">rssname</td>
					<td align="center" valign="middle">
						<input type="text" style="width: 100%;" id="gallerylink_document_rssname" name="gallerylink_document_rssname" value="<?php echo $gallerylink_document['rssname'] ?>" />
					</td>
					<td align="left" valign="middle">
						<?php _e('The name of the RSS feed file (Use to widget)', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">rssmax</td>
					<td align="center" valign="middle">
						<input type="text" style="width: 100%;" id="gallerylink_document_rssmax" name="gallerylink_document_rssmax" value="<?php echo intval($gallerylink_document['rssmax']) ?>" />
					</td>
					<td align="left" valign="middle">
						<?php _e('Syndication feeds show the most recent (Use to widget)', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">filesize_show</td>
					<td align="center" valign="middle">
					<?php $target_document_filesize_show = $gallerylink_document['filesize_show']; ?>
					<select id="gallerylink_document_filesize_show" name="gallerylink_document_filesize_show">
						<option <?php if ('Show' == $target_document_filesize_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_document_filesize_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('File size', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">stamptime_show</td>
					<td align="center" valign="middle">
					<?php $target_document_stamptime_show = $gallerylink_document['stamptime_show']; ?>
					<select id="gallerylink_document_stamptime_show" name="gallerylink_document_stamptime_show">
						<option <?php if ('Show' == $target_document_stamptime_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_document_stamptime_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Date Time', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">selectbox_show</td>
					<td align="center" valign="middle">
					<?php $target_document_selectbox_show = $gallerylink_document['selectbox_show']; ?>
					<select id="gallerylink_document_selectbox_show" name="gallerylink_document_selectbox_show">
						<option <?php if ('Show' == $target_document_selectbox_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_document_selectbox_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Selectbox of directories.', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">pagelinks_show</td>
					<td align="center" valign="middle">
					<?php $target_document_pagelinks_show = $gallerylink_document['pagelinks_show']; ?>
					<select id="gallerylink_document_pagelinks_show" name="gallerylink_document_pagelinks_show">
						<option <?php if ('Show' == $target_document_pagelinks_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_document_pagelinks_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Navigation of page.', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">sortlinks_show</td>
					<td align="center" valign="middle">
					<?php $target_document_sortlinks_show = $gallerylink_document['sortlinks_show']; ?>
					<select id="gallerylink_document_sortlinks_show" name="gallerylink_document_sortlinks_show">
						<option <?php if ('Show' == $target_document_sortlinks_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_document_sortlinks_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Navigation of sort.', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">searchbox_show</td>
					<td align="center" valign="middle">
					<?php $target_document_searchbox_show = $gallerylink_document['searchbox_show']; ?>
					<select id="gallerylink_document_searchbox_show" name="gallerylink_document_searchbox_show">
						<option <?php if ('Show' == $target_document_searchbox_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_document_searchbox_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Search box', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">rssicon_show</td>
					<td align="center" valign="middle">
					<?php $target_document_rssicon_show = $gallerylink_document['rssicon_show']; ?>
					<select id="gallerylink_document_rssicon_show" name="gallerylink_document_rssicon_show">
						<option <?php if ('Show' == $target_document_rssicon_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_document_rssicon_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('RSS Icon', 'gallerylink') ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">credit_show</td>
					<td align="center" valign="middle">
					<?php $target_document_credit_show = $gallerylink_document['credit_show']; ?>
					<select id="gallerylink_document_credit_show" name="gallerylink_document_credit_show">
						<option <?php if ('Show' == $target_document_credit_show)echo 'selected="selected"'; ?>>Show</option>
						<option <?php if ('Hide' == $target_document_credit_show)echo 'selected="selected"'; ?>>Hide</option>
					</select>
					</td>
					<td align="left" valign="middle">
					<?php _e('Credit', 'gallerylink') ?>
					</td>
				</tr>
			</tbody>
			</table>

			<input type="hidden" name="gallerylink_admin_tabs" value="7" />
			<p class="submit">
			  <input type="submit" class="button" name="Submit" value="<?php _e('Save Changes') ?>" />
			</p>

			</form>

		</div>
	  </div>

	  <div id="gallerylink-admin-tabs-7">
		<div class="wrap">

			<h2><?php _e('Settings'); ?> <?php _e('Other', 'gallerylink'); ?></h2>	

			<form method="post" action="<?php echo $scriptname.'#gallerylink-admin-tabs-7'; ?>">

			<p class="submit">
			  <input type="submit" class="button" name="Submit" value="<?php _e('Save Changes') ?>" />
			  <input type="submit" class="button" name="Default" value="<?php _e('Default') ?>" />
			</p>

			<div style="padding:10px;border:#CCC 2px solid; margin:0 0 20px 0">
				<h3><?php _e('Size and color', 'gallerylink') ?></h3>
				<div style="display: block; padding:5px 20px;">
					<?php _e('The size of the thumbnail in listview.', 'gallerylink') ?>
					<?php $target_css_listthumbsize = $gallerylink_css['listthumbsize']; ?>
					<select id="gallerylink_css_listthumbsize" name="gallerylink_css_listthumbsize">
						<option <?php if ('40x40' == $target_css_listthumbsize)echo 'selected="selected"'; ?>>40x40</option>
						<option <?php if ('60x60' == $target_css_listthumbsize)echo 'selected="selected"'; ?>>60x60</option>
						<option <?php if ('80x80' == $target_css_listthumbsize)echo 'selected="selected"'; ?>>80x80</option>
					</select>
				</div>
				<div style="display: block; padding:5px 20px;">
					<?php _e('Background color', 'gallerylink') ?>
					<input type="text" id="gallerylink_css_linkbackcolor" name="gallerylink_css_linkbackcolor" value="<?php echo $gallerylink_css['linkbackcolor'] ?>" />
				</div>
				<div style="display: block; padding:5px 20px;">
					<?php _e('Hover color', 'gallerylink') ?>
					<input type="text" id="gallerylink_css_linkstrcolor" name="gallerylink_css_linkstrcolor" value="<?php echo $gallerylink_css['linkstrcolor'] ?>" />
				</div>
				<div style="display: block; padding:5px 35px;">
					<?php _e('* Color of sorting and pagination. List view is the opposite.', 'gallerylink') ?>
				</div>
			</div>
			<div style="clear:both"></div>

			<?php
			if ( function_exists('mb_check_encoding') ) {
			?>
			<div style="padding:10px;border:#CCC 2px solid; margin:0 0 20px 0">
				<h3><?php _e('Character Encodings for Server', 'gallerylink'); ?></h3>
				<div style="display: block; padding:5px 20px;">
				<?php _e('It may receive an error occurs if you are using a multi-byte name in the file name or folder name. In that case, please change.', 'gallerylink');
				$characterencodings_none_html = '<a href="'.__('https://en.wikipedia.org/wiki/Variable-width_encoding', 'media-from-ftp').'" target="_blank" style="text-decoration: none; word-break: break-all;">'.__('variable-width encoding', 'gallerylink').'</a>';
				echo sprintf(__('If you do not use the filename or directory name of %1$s, please choose "%2$s".','gallerylink'), $characterencodings_none_html, '<font color="red">none</font>');
				?>
				</div>
				<div style="display: block; padding:5px 20px;">
				<select name="gallerylink_character_code" style="width: 210px">
				<?php
				if ( 'none' === $gallerylink_character_code ) {
					?>
					<option value="none" selected>none</option>
					<?php
				} else {
					?>
					<option value="none">none</option>
					<?php
				}
				foreach (mb_list_encodings() as $chrcode) {
					if ( $chrcode <> 'pass' && $chrcode <> 'auto' ) {
						if ( $chrcode === $gallerylink_character_code ) {
							?>
							<option value="<?php echo $chrcode; ?>" selected><?php echo $chrcode; ?></option>
							<?php
						} else {
							?>
							<option value="<?php echo $chrcode; ?>"><?php echo $chrcode; ?></option>
							<?php
						}
					}
				}
				?>
				</select>
				</div>
				<div style="clear: both;"></div>
			</div>
			<?php
			}
			?>

			<div style="padding:10px;border:#CCC 2px solid; margin:0 0 20px 0">
			<h3><?php _e('Exclude', 'gallerylink') ?></h3>
			<table id="gallerylink-table7" border="1">
				<tbody>
				<tr>
					<th align="center" valign="middle"><?php _e('Attribute', 'gallerylink'); ?></th>
					<th align="center" valign="middle"><?php _e('Value'); ?></th>
					<th align="center" valign="middle"><?php _e('Description'); ?></th>
				</tr>
				<tr>
					<td align="center" valign="middle">exclude_file</td>
					<td align="center" valign="middle">
						<input type="text" id="gallerylink_exclude_file" name="gallerylink_exclude_file" value="<?php echo $gallerylink_exclude['file'] ?>" style="width: 100%;" />
					</td>
					<td align="left" valign="middle">
						<?php _e('File you want to exclude.', 'gallerylink'); ?> <?php _e('Regular expression is possible.', 'gallerylink'); ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="middle">exclude_dir</td>
					<td align="center" valign="middle">
						<input type="text" id="gallerylink_exclude_dir" name="gallerylink_exclude_dir" value="<?php echo $gallerylink_exclude['dir'] ?>" style="width: 100%;" />
					</td>
					<td align="left" valign="middle">
						<?php _e('Directory you want to exclude.', 'gallerylink'); ?> <?php _e('Regular expression is possible.', 'gallerylink'); ?>
					</td>
				</tr>
				</tbody>
			</table>
			</div>
			<div style="clear:both"></div>

			<div style="padding:10px;border:#CCC 2px solid; margin:0 0 20px 0">
			<h3><?php _e('User Agent', 'gallerylink') ?></h3>	
					<textarea id="gallerylink_useragent_mb" name="gallerylink_useragent_mb" rows="4" style="width: 100%;"><?php echo $gallerylink_useragent['mb'] ?></textarea>
					 <?php _e('Regular expression is possible.', 'gallerylink'); ?>
			</div>
			<div style="clear:both"></div>

			<input type="hidden" name="gallerylink_admin_tabs" value="8" />
			<p class="submit">
			  <input type="submit" class="button" name="Submit" value="<?php _e('Save Changes') ?>" />
			</p>
			</form>

		</div>
	  </div>

	  <div id="gallerylink-admin-tabs-8">
		<div class="wrap">
		<h2><?php _e('Effect of Images', 'gallerylink'); ?></h2>

			<form method="post" action="<?php echo $scriptname.'#gallerylink-admin-tabs-8'; ?>">

			<p class="submit">
			  <input type="submit" class="button" name="Submit" value="<?php _e('Save Changes') ?>" />
			  <input type="submit" class="button" name="Default" value="<?php _e('Default') ?>" />
			</p>

				<div style="margin: 20px 0; padding: 10px; border: #CCC 2px solid;">
					<h3><?php _e('Infinite Scroll', 'gallerylink') ?></h3>	
					<h4><?php _e('Can apply all.', 'gallerylink'); ?></h4>
					<div style="display:block; padding: 10px 0;">
						<?php _e('Apply') ?>
					    <input type="checkbox" name="gallerylink_infinite_apply" value="1" <?php if ( $gallerylink_infinite['apply'] == TRUE ) { echo 'checked'; }?>>
					</div>
					<div style="display:block; padding: 10px 0;">
						<?php _e('loading_image', 'gallerylink') ?>
						<input type="text" style="width: 80%;"id="gallerylink_infinite_loading_image" name="gallerylink_infinite_loading_image" value="<?php echo $gallerylink_infinite['loading_image'] ?>" />
					</div>
				</div>
				<div style="clear:both"></div>

				<div style="margin: 20px 0; padding: 10px; border: #CCC 2px solid;">
					<h3><?php _e('Masonry', 'gallerylink') ?></h3>
					<h4><?php _e('Can apply album only.', 'gallerylink'); ?></h4>
					<div style="display:block; padding: 10px 0;">
						<?php _e('Apply') ?>
				    	<input type="checkbox" name="gallerylink_masonry_apply" value="1" <?php if ( $gallerylink_masonry['apply'] == TRUE ) { echo 'checked'; }?>>
					</div>
					<div style="display:block; padding: 10px 0;">
						<?php _e('Width') ?>
						<input type="text" size=3 id="gallerylink_masonry_width" name="gallerylink_masonry_width" value="<?php echo $gallerylink_masonry['width'] ?>" />px
					</div>
				</div>
				<div style="clear:both"></div>

				<div style="margin: 20px 0; padding: 10px; border: #CCC 2px solid;">
					<h3><?php _e('Filter', 'gallerylink') ?></h3>
					<?php
						if ( is_multisite() ) {
							$boxersandswipers_install_url = network_admin_url('plugin-install.php?tab=plugin-information&plugin=Boxers+and+Swipers');
						} else {
							$boxersandswipers_install_url = admin_url('plugin-install.php?tab=plugin-information&plugin=Boxers+and+Swipers');
						}
						$boxersandswipers_install_html = '<a href="'.$boxersandswipers_install_url.'" target="_blank" style="text-decoration: none; word-break: break-all;">Boxers and Swipers</a>';
					?>
					<div style="padding: 5px 20px; font-weight: bold;"><?php echo sprintf(__('If you want to use %1$s, add the following sentence to boxersandswipers.php on line 62.', 'gallerylink'), $boxersandswipers_install_html); ?></div>
					<div style="padding: 5px 35px;">
					<code>add_filter('post_gallerylink', array($boxersandswipers, 'add_anchor_tag'));</code>
					</div>
					<div style="padding: 5px 20px; font-weight: bold;"><?php _e('In addition, offer the following filters. This filter passes the html that is generated.', 'gallerylink'); ?></div>
					<div style="padding: 5px 35px;">
					<code>post_gallerylink</code>
					</div>
				</div>
				<div style="clear:both"></div>

			<input type="hidden" name="gallerylink_admin_tabs" value="9" />
			<p class="submit">
			  <input type="submit" class="button" name="Submit" value="<?php _e('Save Changes') ?>" />
			</p>

			</form>

		</div>
	  </div>

	  <div id="gallerylink-admin-tabs-9">
		<div class="wrap">
	<h3><?php _e('The to playback of video and music, that such as the next, .htaccess may be required to the directory containing the data file by the environment.', 'gallerylink') ?></h3>
	<textarea rows="25" style="width: 100%;" readonly>
AddType video/mp4 mp4 m4v
AddType video/webm webm
AddType video/ogg ogv
AddType video/x-flv flv
AddType video/3gpp 3gp
AddType video/3gpp2 3g2
AddType audio/mpeg mp3 m4a m4b
AddType audio/ogg ogg oga
AddType audio/midi mid midi
AddType application/pdf pdf
AddType application/msword doc
AddType application/vnd.ms-excel xla xls xlt xlw
AddType application/vnd.openxmlformats-officedocument.wordprocessingml.document docx
AddType application/vnd.openxmlformats-officedocument.spreadsheetml.sheet xlsx
AddType application/vnd.ms-powerpoint pot pps ppt
AddType application/vnd.openxmlformats-officedocument.presentationml.presentation pptx
AddType application/vnd.ms-powerpoint.presentation.macroEnabled.12 pptm
AddType application/vnd.openxmlformats-officedocument.presentationml.slideshow ppsx
AddType application/vnd.ms-powerpoint.slideshow.macroEnabled.12 ppsm
AddType application/vnd.openxmlformats-officedocument.presentationml.template potx
AddType application/vnd.ms-powerpoint.template.macroEnabled.12 potm
AddType application/vnd.ms-powerpoint.addin.macroEnabled.12 ppam
AddType application/vnd.openxmlformats-officedocument.presentationml.slide sldx
AddType application/vnd.ms-powerpoint.slide.macroEnabled.12 sldm
	</textarea>

		</div>
	  </div>

		<div id="gallerylink-admin-tabs-10">
		<div class="wrap">
			<?php
			$plugin_datas = get_file_data( GALLERYLINK_PLUGIN_BASE_DIR.'/gallerylink.php', array('version' => 'Version') );
			$plugin_version = __('Version:').' '.$plugin_datas['version'];
			?>
			<h4 style="margin: 5px; padding: 5px;">
			<?php echo $plugin_version; ?> |
			</h4>
			<div style="width: 250px; height: 170px; margin: 5px; padding: 5px; border: #CCC 2px solid;">
			<h3><?php _e('Please make a donation if you like my work or would like to further the development of this plugin.', 'gallerylink'); ?></h3>
			<div style="text-align: right; margin: 5px; padding: 5px;"><span style="padding: 3px; color: #ffffff; background-color: #008000">Plugin Author</span> <span style="font-weight: bold;">Katsushi Kawamori</span></div>
	<a style="margin: 5px; padding: 5px;" href='https://pledgie.com/campaigns/28307' target="_blank"><img alt='Click here to lend your support to: Various Plugins for WordPress and make a donation at pledgie.com !' src='https://pledgie.com/campaigns/28307.png?skin_name=chrome' border='0' ></a>
			</div>
		</div>
		</div>

	<!--
	  <div id="gallerylink-admin-tabs-11">
		<div class="wrap">
		<h2>FAQ</h2>

		</div>
	  </div>
	-->

	</div>

		</div>

		<?php
	}


	/* ==================================================
	 * @param	string	$ext2type
	 * @return	array	$exts
	 * @since	5.5
	 */
	function exts($ext2type){

		$mimes = wp_get_mime_types();

		foreach ($mimes as $ext => $mime) {
			if( strpos($ext,  '|') <> FALSE ) {
				$extstmp = explode('|', $ext );
				foreach ( $extstmp as $exttmp ) {
					if ( wp_ext2type($exttmp) === $ext2type ) {
						$exts[] = $exttmp;
					}
				}
			} else {
				if ( wp_ext2type($ext) === $ext2type ) {
					$exts[] = $ext;
				}
			}
		}

		return $exts;

	}

	/* ==================================================
	 * Update wp_options table.
	 * @since	6.3
	 */
	function options_updated($tabs){

		if( strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && get_locale() === 'ja' ) { // Japanese Windows
			$gallerylink_character_code_reset = 'CP932';
		} else {
			$gallerylink_character_code_reset = 'UTF-8';
		}

		$all_reset_tbl = array(
							'sort' => 'new',
							'topurl' => '',
							'display' => 8, 	
							'display_keitai' => 6,
							'generate_rssfeed' => 'on',
							'rssname' => 'gallerylink_all_feed',
							'rssmax' => 10,
							'filesize_show' => 'Show',
							'stamptime_show' => 'Show',
							'exif_show' => 'Show',
							'selectbox_show' => 'Show',
							'pagelinks_show' => 'Show',
							'sortlinks_show' => 'Show',
							'searchbox_show' => 'Show',
							'rssicon_show' => 'Show',
							'credit_show' => 'Show'
					);
		$album_reset_tbl = array(
							'sort' => 'new',
							'topurl' => '',
							'suffix' => 'jpg',
							'suffix_keitai' => 'jpg',
							'display' => 20, 	
							'display_keitai' => 6,
							'generate_rssfeed' => 'on',
							'rssname' => 'gallerylink_album_feed',
							'rssmax' => 10,
							'filesize_show' => 'Show',
							'stamptime_show' => 'Show',
							'exif_show' => 'Show',
							'selectbox_show' => 'Show',
							'pagelinks_show' => 'Show',
							'sortlinks_show' => 'Show',
							'searchbox_show' => 'Show',
							'rssicon_show' => 'Show',
							'credit_show' => 'Show'
					);
		$movie_reset_tbl = array(
							'sort' => 'new',
							'topurl' => '',
							'suffix' => 'mp4',
							'suffix_2' => 'ogv',
							'suffix_keitai' => '3gp',
							'display' => 8,
							'display_keitai' => 6,
							'thumbnail' => '',
							'generate_rssfeed' => 'on',
							'rssname' => 'gallerylink_movie_feed',
							'rssmax' => 10,
							'filesize_show' => 'Show',
							'stamptime_show' => 'Show',
							'selectbox_show' => 'Show',
							'pagelinks_show' => 'Show',
							'sortlinks_show' => 'Show',
							'searchbox_show' => 'Show',
							'rssicon_show' => 'Show',
							'credit_show' => 'Show'
					);
		$music_reset_tbl = array(
							'sort' => 'new',
							'topurl' => '',
							'suffix' => 'mp3',
							'suffix_2' => 'ogg',
							'suffix_keitai' => '3gp',
							'display' => 8,
							'display_keitai' => 6,
							'thumbnail' => '',
							'generate_rssfeed' => 'on',
							'rssname' => 'gallerylink_music_feed',
							'rssmax' => 10,
							'filesize_show' => 'Show',
							'stamptime_show' => 'Show',
							'selectbox_show' => 'Show',
							'pagelinks_show' => 'Show',
							'sortlinks_show' => 'Show',
							'searchbox_show' => 'Show',
							'rssicon_show' => 'Show',
							'credit_show' => 'Show'
					);
		$document_reset_tbl = array(
								'sort' => 'new',
								'topurl' => '',
								'suffix' => 'all',
								'suffix_keitai' => 'all',
								'display' => 20,
								'display_keitai' => 6,
								'thumbnail' => 'icon',
								'generate_rssfeed' => 'on',
								'rssname' => 'gallerylink_document_feed',
								'rssmax' => 10,
								'filesize_show' => 'Show',
								'stamptime_show' => 'Show',
								'selectbox_show' => 'Show',
								'pagelinks_show' => 'Show',
								'sortlinks_show' => 'Show',
								'searchbox_show' => 'Show',
								'rssicon_show' => 'Show',
								'credit_show' => 'Show'
					);
		$exclude_reset_tbl = array(
							'file' => '',
							'dir' => ''
						);
		$css_reset_tbl = array(
						'listthumbsize' => '40x40',
						'linkstrcolor' => '#ffffff',
						'linkbackcolor' => '#f6efe2'
					);
		$useragent_reset_tbl = array(
							'mb' => 'DoCoMo\/|KDDI-|UP\.Browser|SoftBank|Vodafone|J-PHONE|MOT-|WILLCOM|DDIPOCKET|PDXGW|emobile|ASTEL|L-mode'
					);
		$loading_image = GALLERYLINK_PLUGIN_URL.'/img/ajax-loader.gif';
		$infinite_reset_tbl = array(
							'apply' => FALSE,
							'loading_image' => $loading_image
							);
		$masonry_reset_tbl = array(
							'apply' => FALSE,
							'width' => 100
							);

		switch ($tabs) {
			case 1:
				if ( !empty($_POST['Default']) ) {
					update_option( 'gallerylink_character_code', $gallerylink_character_code_reset );
					update_option( 'gallerylink_all', $all_reset_tbl );
					update_option( 'gallerylink_album', $album_reset_tbl );
					update_option( 'gallerylink_movie', $movie_reset_tbl );
					update_option( 'gallerylink_music', $music_reset_tbl );
					update_option( 'gallerylink_document', $document_reset_tbl );
					update_option( 'gallerylink_css', $css_reset_tbl );
					update_option( 'gallerylink_useragent', $useragent_reset_tbl );
					update_option( 'gallerylink_exclude', $exclude_reset_tbl );
					update_option( 'gallerylink_infinite', $infinite_reset_tbl );
					update_option( 'gallerylink_masonry', $masonry_reset_tbl );
					echo '<div class="updated"><ul><li>'.__('All Settings').' --> '.__('Default').' --> '.__('Changes saved.').'</li></ul></div>';
				}
				break;
			case 2:
				if ( !empty($_POST['Default']) ) {
					update_option( 'gallerylink_all', $all_reset_tbl );
					echo '<div class="updated"><ul><li>'.__('Settings').__('AllData', 'gallerylink').' --> '.__('Default').' --> '.__('Changes saved.').'</li></ul></div>';
				} else {
					$all_tbl = array(
									'sort' => $_POST['gallerylink_all_sort'],
									'topurl' => $_POST['gallerylink_all_topurl'],
									'display' => $_POST['gallerylink_all_display'],
									'display_keitai' => $_POST['gallerylink_all_display_keitai'],
									'generate_rssfeed' => $_POST['gallerylink_all_generate_rssfeed'],
									'rssname' => $_POST['gallerylink_all_rssname'],
									'rssmax' => $_POST['gallerylink_all_rssmax'],
									'filesize_show' => $_POST['gallerylink_all_filesize_show'],
									'stamptime_show' => $_POST['gallerylink_all_stamptime_show'],
									'exif_show' => $_POST['gallerylink_all_exif_show'],
									'selectbox_show' => $_POST['gallerylink_all_selectbox_show'],
									'pagelinks_show' => $_POST['gallerylink_all_pagelinks_show'],
									'sortlinks_show' => $_POST['gallerylink_all_sortlinks_show'],
									'searchbox_show' => $_POST['gallerylink_all_searchbox_show'],
									'rssicon_show' => $_POST['gallerylink_all_rssicon_show'],
									'credit_show' => $_POST['gallerylink_all_credit_show']
								);
					update_option( 'gallerylink_all', $all_tbl );
					echo '<div class="updated"><ul><li>'.__('Settings').__('AllData', 'gallerylink').' --> '.__('Changes saved.').'</li></ul></div>';
				}
				break;
			case 3:
				if ( !empty($_POST['Default']) ) {
					update_option( 'gallerylink_album', $album_reset_tbl );
					echo '<div class="updated"><ul><li>'.__('Settings').__('Album', 'gallerylink').' --> '.__('Default').' --> '.__('Changes saved.').'</li></ul></div>';
				} else {
					$album_tbl = array(
									'sort' => $_POST['gallerylink_album_sort'],
									'topurl' => $_POST['gallerylink_album_topurl'],
									'suffix' => $_POST['gallerylink_album_suffix'],
									'suffix_keitai' => $_POST['gallerylink_album_suffix_keitai'],
									'display' => $_POST['gallerylink_album_display'],
									'display_keitai' => $_POST['gallerylink_album_display_keitai'],
									'generate_rssfeed' => $_POST['gallerylink_album_generate_rssfeed'],
									'rssname' => $_POST['gallerylink_album_rssname'],
									'rssmax' => $_POST['gallerylink_album_rssmax'],
									'filesize_show' => $_POST['gallerylink_album_filesize_show'],
									'stamptime_show' => $_POST['gallerylink_album_stamptime_show'],
									'exif_show' => $_POST['gallerylink_album_exif_show'],
									'selectbox_show' => $_POST['gallerylink_album_selectbox_show'],
									'pagelinks_show' => $_POST['gallerylink_album_pagelinks_show'],
									'sortlinks_show' => $_POST['gallerylink_album_sortlinks_show'],
									'searchbox_show' => $_POST['gallerylink_album_searchbox_show'],
									'rssicon_show' => $_POST['gallerylink_album_rssicon_show'],
									'credit_show' => $_POST['gallerylink_album_credit_show']
								);
					update_option( 'gallerylink_album', $album_tbl );
					echo '<div class="updated"><ul><li>'.__('Settings').__('Album', 'gallerylink').' --> '.__('Changes saved.').'</li></ul></div>';
				}
				break;
			case 4:
				if ( !empty($_POST['Default']) ) {
					update_option( 'gallerylink_movie', $movie_reset_tbl );
					echo '<div class="updated"><ul><li>'.__('Settings').__('Video', 'gallerylink').' --> '.__('Default').' --> '.__('Changes saved.').'</li></ul></div>';
				} else {
					$movie_tbl = array(
									'sort' => $_POST['gallerylink_movie_sort'],
									'topurl' => $_POST['gallerylink_movie_topurl'],
									'suffix' => $_POST['gallerylink_movie_suffix'],
									'suffix_2' => $_POST['gallerylink_movie_suffix_2'],
									'suffix_keitai' => $_POST['gallerylink_movie_suffix_keitai'],
									'display' => $_POST['gallerylink_movie_display'],
									'display_keitai' => $_POST['gallerylink_movie_display_keitai'],
									'thumbnail' => $_POST['gallerylink_movie_thumbnail'],
									'generate_rssfeed' => $_POST['gallerylink_movie_generate_rssfeed'],
									'rssname' => $_POST['gallerylink_movie_rssname'],
									'rssmax' => $_POST['gallerylink_movie_rssmax'],
									'filesize_show' => $_POST['gallerylink_movie_filesize_show'],
									'stamptime_show' => $_POST['gallerylink_movie_stamptime_show'],
									'selectbox_show' => $_POST['gallerylink_movie_selectbox_show'],
									'pagelinks_show' => $_POST['gallerylink_movie_pagelinks_show'],
									'sortlinks_show' => $_POST['gallerylink_movie_sortlinks_show'],
									'searchbox_show' => $_POST['gallerylink_movie_searchbox_show'],
									'rssicon_show' => $_POST['gallerylink_movie_rssicon_show'],
									'credit_show' => $_POST['gallerylink_movie_credit_show']
									);
					update_option( 'gallerylink_movie', $movie_tbl );
					echo '<div class="updated"><ul><li>'.__('Settings').__('Video', 'gallerylink').' --> '.__('Changes saved.').'</li></ul></div>';
				}
				break;
			case 5:
				if ( !empty($_POST['Default']) ) {
					update_option( 'gallerylink_music', $music_reset_tbl );
					echo '<div class="updated"><ul><li>'.__('Settings').__('Music', 'gallerylink').' --> '.__('Default').' --> '.__('Changes saved.').'</li></ul></div>';
				} else {
					$music_tbl = array(
									'sort' => $_POST['gallerylink_music_sort'],
									'topurl' => $_POST['gallerylink_music_topurl'],
									'suffix' => $_POST['gallerylink_music_suffix'],
									'suffix_2' => $_POST['gallerylink_music_suffix_2'],
									'suffix_keitai' => $_POST['gallerylink_music_suffix_keitai'],
									'display' => $_POST['gallerylink_music_display'],
									'display_keitai' => $_POST['gallerylink_music_display_keitai'],
									'thumbnail' => $_POST['gallerylink_music_thumbnail'],
									'generate_rssfeed' => $_POST['gallerylink_music_generate_rssfeed'],
									'rssname' => $_POST['gallerylink_music_rssname'],
									'rssmax' => $_POST['gallerylink_music_rssmax'],
									'filesize_show' => $_POST['gallerylink_music_filesize_show'],
									'stamptime_show' => $_POST['gallerylink_music_stamptime_show'],
									'selectbox_show' => $_POST['gallerylink_music_selectbox_show'],
									'pagelinks_show' => $_POST['gallerylink_music_pagelinks_show'],
									'sortlinks_show' => $_POST['gallerylink_music_sortlinks_show'],
									'searchbox_show' => $_POST['gallerylink_music_searchbox_show'],
									'rssicon_show' => $_POST['gallerylink_music_rssicon_show'],
									'credit_show' => $_POST['gallerylink_music_credit_show']
									);
					update_option( 'gallerylink_music', $music_tbl );
					echo '<div class="updated"><ul><li>'.__('Settings').__('Music', 'gallerylink').' --> '.__('Changes saved.').'</li></ul></div>';
				}
				break;
			case 6:
				if ( !empty($_POST['Default']) ) {
					update_option( 'gallerylink_document', $document_reset_tbl );
					echo '<div class="updated"><ul><li>'.__('Settings').__('Document', 'gallerylink').' --> '.__('Default').' --> '.__('Changes saved.').'</li></ul></div>';
				} else {
					$document_tbl = array(
									'sort' => $_POST['gallerylink_document_sort'],
									'topurl' => $_POST['gallerylink_document_topurl'],
									'suffix' => $_POST['gallerylink_document_suffix'],
									'suffix_keitai' => $_POST['gallerylink_document_suffix_keitai'],
									'display' => $_POST['gallerylink_document_display'],
									'display_keitai' => $_POST['gallerylink_document_display_keitai'],
									'thumbnail' => $_POST['gallerylink_document_thumbnail'],
									'generate_rssfeed' => $_POST['gallerylink_document_generate_rssfeed'],
									'rssname' => $_POST['gallerylink_document_rssname'],
									'rssmax' => $_POST['gallerylink_document_rssmax'],
									'filesize_show' => $_POST['gallerylink_document_filesize_show'],
									'stamptime_show' => $_POST['gallerylink_document_stamptime_show'],
									'selectbox_show' => $_POST['gallerylink_document_selectbox_show'],
									'pagelinks_show' => $_POST['gallerylink_document_pagelinks_show'],
									'sortlinks_show' => $_POST['gallerylink_document_sortlinks_show'],
									'searchbox_show' => $_POST['gallerylink_document_searchbox_show'],
									'rssicon_show' => $_POST['gallerylink_document_rssicon_show'],
									'credit_show' => $_POST['gallerylink_document_credit_show']
									);
					update_option( 'gallerylink_document', $document_tbl );
					echo '<div class="updated"><ul><li>'.__('Settings').__('Document', 'gallerylink').' --> '.__('Changes saved.').'</li></ul></div>';
				}
				break;
			case 7:
				if ( !empty($_POST['Default']) ) {
					update_option( 'gallerylink_character_code', $gallerylink_character_code_reset );
					update_option( 'gallerylink_css', $css_reset_tbl );
					update_option( 'gallerylink_useragent', $useragent_reset_tbl );
					update_option( 'gallerylink_exclude', $exclude_reset_tbl );
					echo '<div class="updated"><ul><li>'.__('Settings').__('Other', 'gallerylink').' --> '.__('Default').' --> '.__('Changes saved.').'</li></ul></div>';
				} else {
					update_option( 'gallerylink_character_code', $_POST['gallerylink_character_code'] );
					$css_tbl = array(
									'listthumbsize' => $_POST['gallerylink_css_listthumbsize'],
									'linkstrcolor' => $_POST['gallerylink_css_linkstrcolor'],
									'linkbackcolor' => $_POST['gallerylink_css_linkbackcolor']
									);
					update_option( 'gallerylink_css', $css_tbl );
					$useragent_tbl = array(
									'mb' => stripslashes($_POST['gallerylink_useragent_mb'])
									);
					update_option( 'gallerylink_useragent', $useragent_tbl );
					$exclude_tbl = array(
									'file' => stripslashes($_POST['gallerylink_exclude_file']),
									'dir' => stripslashes($_POST['gallerylink_exclude_dir'])
									);
					update_option( 'gallerylink_exclude', $exclude_tbl );
					echo '<div class="updated"><ul><li>'.__('Settings').__('Other', 'gallerylink').' --> '.__('Changes saved.').'</li></ul></div>';
				}
				break;
			case 8:
				if ( !empty($_POST['Default']) ) {
					update_option( 'gallerylink_infinite', $infinite_reset_tbl );
					update_option( 'gallerylink_masonry', $masonry_reset_tbl );
					echo '<div class="updated"><ul><li>'.__('Settings').__('Effect of Images', 'gallerylink').' --> '.__('Default').' --> '.__('Changes saved.').'</li></ul></div>';
				} else {
					if ( !empty($_POST['gallerylink_infinite_apply']) ) {
						$gallerylink_infinite_apply = intval($_POST['gallerylink_infinite_apply']);
					} else {
						$gallerylink_infinite_apply = 0;
					}
					$infinite_tbl = array(
									'apply' => $gallerylink_infinite_apply,
									'loading_image' => $_POST['gallerylink_infinite_loading_image']
									);
					update_option( 'gallerylink_infinite', $infinite_tbl );
					if ( !empty($_POST['gallerylink_masonry_apply']) ) {
						$gallerylink_masonry_apply = intval($_POST['gallerylink_masonry_apply']);
					} else {
						$gallerylink_masonry_apply = 0;
					}
					$masonry_tbl = array(
										'apply' => $gallerylink_masonry_apply,
										'width' => intval($_POST['gallerylink_masonry_width'])
										);
					update_option( 'gallerylink_masonry', $masonry_tbl );
					echo '<div class="updated"><ul><li>'.__('Settings').__('Effect of Images', 'gallerylink').' --> '.__('Changes saved.').'</li></ul></div>';
				}
				break;
		}

	}

	/* ==================================================
	 * Closed Plugin
	 */
	function closed_plugin() {

		if ($this->is_my_plugin_screen()) {
			echo '<div class="notice notice-error is-dismissible"><ul><li>'.__('I decided to close this plugin. Because code is complicated and it feels difficult to maintain. Thanks for using it until now. It will be coming soon. Please use it at your own risk after closing.', 'gallerylink').'</li></ul></div>';
		}

	}

}

?>