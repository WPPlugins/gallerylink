<?php
/**
 * GalleryLink
 * 
 * @package    GalleryLink
 * @subpackage GalleryLink registered in the database and generate header
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

class GalleryLinkRegistAndHeader {

	public $maxpage;

	/* ==================================================
	 * Settings register
	 * @since	2.0
	 */
	function register_settings(){

		if( strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && get_locale() === 'ja' ) { // Japanese Windows
			$character_code = 'CP932';
		} else {
			$character_code = 'UTF-8';
		}
		if ( !get_option('gallerylink_character_code') ) {
			update_option('gallerylink_character_code', $character_code);
		}

		if ( get_option('gallerylink_mb_language') ) { // delete old setting
			delete_option('gallerylink_mb_language');
		}

		if ( !get_option('gallerylink_all') ) {
			$all_tbl = array(
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
			update_option( 'gallerylink_all', $all_tbl );
		}

		if ( !get_option('gallerylink_album') ) {
			$album_tbl = array(
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
			update_option( 'gallerylink_album', $album_tbl );
		}

		if ( !get_option('gallerylink_movie') ) {
			$movie_tbl = array(
							'sort' => 'new',
							'topurl' => '',
							'suffix' => 'mp4',
							'suffix_2' => 'ogv',
							'suffix_keitai' => '3gp',
							'display' => 8,
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
			update_option( 'gallerylink_movie', $movie_tbl );
		}

		if ( !get_option('gallerylink_music') ) {
			$music_tbl = array(
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
			update_option( 'gallerylink_music', $music_tbl );
		}

		if ( !get_option('gallerylink_document') ) {
			$document_tbl = array(
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
			update_option( 'gallerylink_document', $document_tbl );
		}

		if ( !get_option('gallerylink_exclude') ) {
			$exclude_tbl = array(
								'file' => '',
								'dir' => ''
							);
			update_option( 'gallerylink_exclude', $exclude_tbl );
		}

		if ( !get_option('gallerylink_css') ) {
			$css_tbl = array(
							'listthumbsize' => '40x40',
							'linkstrcolor' => '#ffffff',
							'linkbackcolor' => '#f6efe2'
							);
			update_option( 'gallerylink_css', $css_tbl );
		}

		$loading_image = GALLERYLINK_PLUGIN_URL.'/img/ajax-loader.gif';
		if ( !get_option('gallerylink_infinite') ) {
			$infinite_tbl = array(
								'apply' => FALSE,
								'loading_image' => $loading_image
								);
			update_option( 'gallerylink_infinite', $infinite_tbl );
		}

		if ( !get_option('gallerylink_masonry') ) {
			$masonry_tbl = array(
								'apply' => FALSE,
								'width' => 100
								);
			update_option( 'gallerylink_masonry', $masonry_tbl );
		}

		if ( !get_option('gallerylink_useragent') ) {
			$useragent_tbl = array(
								'mb' => 'DoCoMo\/|KDDI-|UP\.Browser|SoftBank|Vodafone|J-PHONE|MOT-|WILLCOM|DDIPOCKET|PDXGW|emobile|ASTEL|L-mode'
							);
			update_option( 'gallerylink_useragent', $useragent_tbl );
		}

	}

	/* ==================================================
	 * Add FeedLink
	 * @since	2.9
	 */
	function add_feedlink(){

		$gallerylink_album = get_option('gallerylink_album');
		$gallerylink_all = get_option('gallerylink_all');
		$gallerylink_document = get_option('gallerylink_document');
		$gallerylink_movie = get_option('gallerylink_movie');
		$gallerylink_music = get_option('gallerylink_music');

		$wp_uploads = wp_upload_dir();
		$wp_uploads_path = str_replace('http://'.$_SERVER["SERVER_NAME"], '', $wp_uploads['baseurl']);

		$documentrootname = $_SERVER['DOCUMENT_ROOT'];
		$servername = 'http://'.$_SERVER['HTTP_HOST'];

		$xml_all_file = $gallerylink_all['topurl'].'/'.$gallerylink_all['rssname'].'.xml';
		$xml_all_media = $wp_uploads_path.'/'.$gallerylink_all['rssname'].'.xml';
		$xml_album_file = $gallerylink_album['topurl'].'/'.$gallerylink_album['rssname'].'.xml';
		$xml_album_media = $wp_uploads_path.'/'.$gallerylink_album['rssname'].'.xml';
		$xml_movie_file = $gallerylink_movie['topurl'].'/'.$gallerylink_movie['rssname'].'.xml';
		$xml_movie_media = $wp_uploads_path.'/'.$gallerylink_movie['rssname'].'.xml';
		$xml_music_file = $gallerylink_music['topurl'].'/'.$gallerylink_music['rssname'].'.xml';
		$xml_music_media = $wp_uploads_path.'/'.$gallerylink_music['rssname'].'.xml';
		$xml_document_file = $gallerylink_document['topurl'].'/'.$gallerylink_document['rssname'].'.xml';
		$xml_document_media = $wp_uploads_path.'/'.$gallerylink_document['rssname'].'.xml';

		include_once GALLERYLINK_PLUGIN_BASE_DIR .'/inc/GalleryLink.php';
		$gallerylink = new GalleryLink();
		$mode = $gallerylink->agent_check();

		if ( $mode === "pc" ) {
			echo '<!-- Start Gallerylink feed -->'."\n";
			if (file_exists($documentrootname.$xml_all_file)) {
				$xml_all_file_data = simplexml_load_file($documentrootname.$xml_all_file);
				echo '<link rel="alternate" type="application/rss+xml" href="'.$servername.$xml_all_file.'" title="'.$xml_all_file_data->channel->title.'" />'."\n";
			}
			if (file_exists($documentrootname.$xml_all_media)) {
				$xml_all_media_data = simplexml_load_file($documentrootname.$xml_all_media);
				echo '<link rel="alternate" type="application/rss+xml" href="'.$servername.$xml_all_media.'" title="'.$xml_all_media_data->channel->title.'" />'."\n";
			}
			if (file_exists($documentrootname.$xml_album_file)) {
				$xml_album_file_data = simplexml_load_file($documentrootname.$xml_album_file);
				echo '<link rel="alternate" type="application/rss+xml" href="'.$servername.$xml_album_file.'" title="'.$xml_album_file_data->channel->title.'" />'."\n";
			}
			if (file_exists($documentrootname.$xml_album_media)) {
				$xml_album_media_data = simplexml_load_file($documentrootname.$xml_album_media);
				echo '<link rel="alternate" type="application/rss+xml" href="'.$servername.$xml_album_media.'" title="'.$xml_album_media_data->channel->title.'" />'."\n";
			}
			if (file_exists($documentrootname.$xml_movie_file)) {
				$xml_movie_file_data = simplexml_load_file($documentrootname.$xml_movie_file);
				echo '<link rel="alternate" type="application/rss+xml" href="'.$servername.$xml_movie_file.'" title="'.$xml_movie_file_data->channel->title.'" />'."\n";
			}
			if (file_exists($documentrootname.$xml_movie_media)) {
				$xml_movie_media_data = simplexml_load_file($documentrootname.$xml_movie_media);
				echo '<link rel="alternate" type="application/rss+xml" href="'.$servername.$xml_movie_media.'" title="'.$xml_movie_media_data->channel->title.'" />'."\n";
			}
			if (file_exists($documentrootname.$xml_music_file)) {
				$xml_music_file_data = simplexml_load_file($documentrootname.$xml_music_file);
				echo '<link rel="alternate" type="application/rss+xml" href="'.$servername.$xml_music_file.'" title="'.$xml_music_file_data->channel->title.'" />'."\n";
			}
			if (file_exists($documentrootname.$xml_music_media)) {
				$xml_music_media_data = simplexml_load_file($documentrootname.$xml_music_media);
				echo '<link rel="alternate" type="application/rss+xml" href="'.$servername.$xml_music_media.'" title="'.$xml_music_media_data->channel->title.'" />'."\n";
			}
			if (file_exists($documentrootname.$xml_document_file)) {
				$xml_document_file_data = simplexml_load_file($documentrootname.$xml_document_file);
				echo '<link rel="alternate" type="application/rss+xml" href="'.$servername.$xml_document_file.'" title="'.$xml_document_file_data->channel->title.'" />'."\n";
			}
			if (file_exists($documentrootname.$xml_document_media)) {
				$xml_document_media_data = simplexml_load_file($documentrootname.$xml_document_media);
				echo '<link rel="alternate" type="application/rss+xml" href="'.$servername.$xml_document_media.'" title="'.$xml_document_media_data->channel->title.'" />'."\n";
			}
			echo '<!-- End Gallerylink feed -->'."\n";
		}

	}

	/* ==================================================
	 * Load Dashicons
	 * @since	9.91
	 */
	function load_styles() {
		wp_enqueue_style('dashicons');
	}

}

?>