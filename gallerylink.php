<?php
/*
Plugin Name: GalleryLink
Plugin URI: https://wordpress.org/plugins/gallerylink/
Version: 10.05
Description: Output as a gallery by find the file extension and directory specified.
Author: Katsushi Kawamori
Author URI: http://riverforest-wp.info/
Text Domain: gallerylink
Domain Path: /languages
*/

/*  Copyright (c) 2013- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
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


	define("GALLERYLINK_PLUGIN_BASE_FILE", plugin_basename(__FILE__));
	define("GALLERYLINK_PLUGIN_BASE_DIR", dirname(__FILE__));
	define("GALLERYLINK_PLUGIN_URL", plugins_url($path='gallerylink',$scheme=null));

	load_plugin_textdomain('gallerylink');
//	load_plugin_textdomain('gallerylink', false, basename( GALLERYLINK_PLUGIN_BASE_DIR ) . '/languages' );

	require_once( GALLERYLINK_PLUGIN_BASE_DIR . '/req/GalleryLinkRegistAndHeader.php' );
	$gallerylinkregistandheader = new GalleryLinkRegistAndHeader();
	add_action('admin_init', array($gallerylinkregistandheader, 'register_settings'));
	add_action('wp_head', array($gallerylinkregistandheader, 'add_feedlink'));
	add_action('wp_print_styles', array($gallerylinkregistandheader, 'load_styles'));
	unset($gallerylinkregistandheader);

	require_once( GALLERYLINK_PLUGIN_BASE_DIR . '/req/GalleryLinkAdmin.php' );
	$gallerylinkadmin = new GalleryLinkAdmin();
	add_action( 'admin_menu', array($gallerylinkadmin, 'plugin_menu'));
	add_action( 'admin_enqueue_scripts', array($gallerylinkadmin, 'load_custom_wp_admin_style') );
	add_filter( 'plugin_action_links', array($gallerylinkadmin, 'settings_link'), 10, 2 );
	add_action( 'admin_notices', array($gallerylinkadmin, 'closed_plugin'));
	unset($gallerylinkadmin);

	add_shortcode( 'gallerylink', 'gallerylink_func' );

	require_once( GALLERYLINK_PLUGIN_BASE_DIR . '/req/GalleryLinkWidgetItem.php' );
	add_action('widgets_init', create_function('', 'return register_widget("GalleryLinkWidgetItem");'));

	require_once( GALLERYLINK_PLUGIN_BASE_DIR . '/req/GalleryLinkQuickTag.php' );
	$gallerylinkquicktag = new GalleryLinkQuickTag();
	add_action('media_buttons', array($gallerylinkquicktag, 'add_quicktag_select'));
	add_action('admin_print_footer_scripts', array($gallerylinkquicktag, 'add_quicktag_button_js'));
	unset($gallerylinkquicktag);

/* ==================================================
 * Main
 */
function gallerylink_func( $atts, $html = NULL ) {

	include_once GALLERYLINK_PLUGIN_BASE_DIR . '/inc/GalleryLink.php';
	$gallerylink = new GalleryLink();

	$gallerylink_character_code = get_option('gallerylink_character_code');
	$gallerylink->mb_initialize($gallerylink_character_code);

	extract(shortcode_atts(array(
        'set' => '',
        'sort' => '',
        'topurl' => '',
        'suffix' => '',
        'suffix_2' => '',
        'suffix_keitai' => '',
        'display' => '',
        'display_keitai' => '',
        'thumbnail'  => '',
        'exclude_file' => '',
        'exclude_dir' => '',
        'generate_rssfeed' => '',
		'rssname' => '',
        'rssmax'  => '',
        'filesize_show'  => '',
        'stamptime_show'  => '',
        'exif_show'  => '',
        'selectbox_show'  => '',
        'pagelinks_show'  => '',
        'sortlinks_show'  => '',
        'searchbox_show'  => '',
        'rssicon_show'  => '',
        'credit_show'  => ''
	), $atts));

	if ( empty($set) ){
		$set = 'all';
	}
	$gallerylink->set = $set;

	$gallerylink_album = get_option('gallerylink_album');
	$gallerylink_all = get_option('gallerylink_all');
	$gallerylink_document = get_option('gallerylink_document');
	$gallerylink_exclude = get_option('gallerylink_exclude');
	$gallerylink_movie = get_option('gallerylink_movie');
	$gallerylink_music = get_option('gallerylink_music');

	$rssdef = false;
	if ( $set === 'all' ){
		if( empty($sort) ) { $sort = $gallerylink_all['sort']; }
		if( empty($topurl) ) { $topurl = $gallerylink_all['topurl']; }
		$suffix_pattern = $gallerylink->extpattern();
		$suffix_pattern_sp = $gallerylink->extpattern();
		$suffix_pattern_keitai = $gallerylink->extpattern();
		$separator = '|';
		$suffix_pattern .= $separator.strtoupper($gallerylink_movie['suffix']).$separator.strtolower($gallerylink_movie['suffix']);
		$suffix_movie_pc2 = $gallerylink_movie['suffix_2'];
		$suffix_pattern_keitai .= $separator.strtoupper($gallerylink_movie['suffix_keitai']).$separator.strtolower($gallerylink_movie['suffix_keitai']);
		$suffix_pattern .= $separator.strtoupper($gallerylink_music['suffix']).$separator.strtolower($gallerylink_music['suffix']);
		$suffix_music_pc2 = $gallerylink_music['suffix_2'];
		$suffix_pattern_keitai .= $separator.strtoupper($gallerylink_music['suffix_keitai']).$separator.strtolower($gallerylink_music['suffix_keitai']);
		if( empty($display) ) { $display = intval($gallerylink_all['display']); }
		if( empty($display_keitai) ) { $display_keitai = intval($gallerylink_all['display_keitai']); }
		if( empty($thumbnail) ) {
			$thumbnail = '-'.get_option('thumbnail_size_w').'x'.get_option('thumbnail_size_h');
		}
		if( empty($generate_rssfeed) ) { $generate_rssfeed = $gallerylink_all['generate_rssfeed']; }
		if( empty($rssname) ) {
			$rssname = $gallerylink_all['rssname'];
			$rssdef = true;
		}
		if( empty($rssmax) ) { $rssmax = intval($gallerylink_all['rssmax']); }
		if( empty($filesize_show) ) { $filesize_show = $gallerylink_all['filesize_show']; }
		if( empty($stamptime_show) ) { $stamptime_show = $gallerylink_all['stamptime_show']; }
		if( empty($exif_show) && !empty($gallerylink_all['exif_show']) ) { $exif_show = $gallerylink_all['exif_show']; }
		if( empty($selectbox_show) ) { $selectbox_show = $gallerylink_all['selectbox_show']; }
		if( empty($pagelinks_show) ) { $pagelinks_show = $gallerylink_all['pagelinks_show']; }
		if( empty($sortlinks_show) ) { $sortlinks_show = $gallerylink_all['sortlinks_show']; }
		if( empty($searchbox_show) ) { $searchbox_show = $gallerylink_all['searchbox_show']; }
		if( empty($rssicon_show) ) { $rssicon_show = $gallerylink_all['rssicon_show']; }
		if( empty($credit_show) ) { $credit_show = $gallerylink_all['credit_show']; }

	} else if ( $set === 'album' ){
		if( empty($sort) ) { $sort = $gallerylink_album['sort']; }
		if( empty($topurl) ) { $topurl = $gallerylink_album['topurl']; }
		if( empty($suffix) ) {
			$separator = '|';
			if ( $gallerylink_album['suffix'] === 'all' ) {
				$suffix_pattern = $gallerylink->extpattern();
			} else {
				$suffix_pattern = strtoupper($gallerylink_album['suffix']).$separator.strtolower($gallerylink_album['suffix']);
			}
		} else {
			if ( $suffix === 'all' ) {
				$suffix_pattern = $gallerylink->extpattern();
			} else {
				$suffix_pattern = strtoupper($suffix).$separator.strtolower($suffix);
			}
		}
		if( empty($suffix_keitai) ) {
			if ( $gallerylink_album['suffix_keitai'] === 'all' ) {
				$suffix_pattern_keitai = $gallerylink->extpattern();
			} else {
				$suffix_pattern_keitai = strtoupper($gallerylink_album['suffix_keitai']).$separator.strtolower($gallerylink_album['suffix_keitai']);
			}
		} else {
			if ( $suffix_keitai === 'all' ) {
				$suffix_pattern_keitai = $gallerylink->extpattern();
			} else {
				$suffix_pattern_keitai = strtoupper($suffix_keitai).$separator.strtolower($suffix_keitai);
			}
		}
		if( empty($display) ) { $display = intval($gallerylink_album['display']); }
		if( empty($display_keitai) ) { $display_keitai = intval($gallerylink_album['display_keitai']); }
		if( empty($thumbnail) ) {
			$thumbnail = '-'.get_option('thumbnail_size_w').'x'.get_option('thumbnail_size_h');
		}
		if( empty($generate_rssfeed) ) { $generate_rssfeed = $gallerylink_album['generate_rssfeed']; }
		if( empty($rssname) ) {
			$rssname = $gallerylink_album['rssname'];
			$rssdef = true;
		}
		if( empty($rssmax) ) { $rssmax = intval($gallerylink_album['rssmax']); }
		if( empty($filesize_show) ) { $filesize_show = $gallerylink_album['filesize_show']; }
		if( empty($stamptime_show) ) { $stamptime_show = $gallerylink_album['stamptime_show']; }
		if( empty($exif_show) && !empty($gallerylink_album['exif_show']) ) { $exif_show = $gallerylink_album['exif_show']; }
		if( empty($selectbox_show) ) { $selectbox_show = $gallerylink_album['selectbox_show']; }
		if( empty($pagelinks_show) ) { $pagelinks_show = $gallerylink_album['pagelinks_show']; }
		if( empty($sortlinks_show) ) { $sortlinks_show = $gallerylink_album['sortlinks_show']; }
		if( empty($searchbox_show) ) { $searchbox_show = $gallerylink_album['searchbox_show']; }
		if( empty($rssicon_show) ) { $rssicon_show = $gallerylink_album['rssicon_show']; }
		if( empty($credit_show) ) { $credit_show = $gallerylink_album['credit_show']; }
	} else if ( $set === 'movie' ){
		if( empty($sort) ) { $sort = $gallerylink_movie['sort']; }
		if( empty($topurl) ) { $topurl = $gallerylink_movie['topurl']; }
		$separator = '|';
		if( empty($suffix) ) {
			$suffix_pattern = strtoupper($gallerylink_movie['suffix']).$separator.strtolower($gallerylink_movie['suffix']);
		} else {
			$suffix_pattern = strtoupper($suffix).$separator.strtolower($suffix);
		}
		if( empty($suffix_2) ) { $suffix_2 = $gallerylink_movie['suffix_2']; }
		if( empty($suffix_keitai) ) {
			$suffix_pattern_keitai = strtoupper($gallerylink_movie['suffix_keitai']).$separator.strtolower($gallerylink_movie['suffix_keitai']);
		} else {
			$suffix_pattern_keitai = strtoupper($suffix_keitai).$separator.strtolower($suffix_keitai);
		}
		if( empty($display) ) { $display = intval($gallerylink_movie['display']); }
		if( empty($display_keitai) ) { $display_keitai = intval($gallerylink_movie['display_keitai']); }
		if( empty($thumbnail) ) { $thumbnail = $gallerylink_movie['thumbnail']; }
		if( empty($generate_rssfeed) ) { $generate_rssfeed = $gallerylink_movie['generate_rssfeed']; }
		if( empty($rssname) ) {
			$rssname = $gallerylink_movie['rssname'];
			$rssdef = true;
		}
		if( empty($rssmax) ) { $rssmax = intval($gallerylink_movie['rssmax']); }
		if( empty($filesize_show) ) { $filesize_show = $gallerylink_movie['filesize_show']; }
		if( empty($stamptime_show) ) { $stamptime_show = $gallerylink_movie['stamptime_show']; }
		if( empty($selectbox_show) ) { $selectbox_show = $gallerylink_movie['selectbox_show']; }
		if( empty($pagelinks_show) ) { $pagelinks_show = $gallerylink_movie['pagelinks_show']; }
		if( empty($sortlinks_show) ) { $sortlinks_show = $gallerylink_movie['sortlinks_show']; }
		if( empty($searchbox_show) ) { $searchbox_show = $gallerylink_movie['searchbox_show']; }
		if( empty($rssicon_show) ) { $rssicon_show = $gallerylink_movie['rssicon_show']; }
		if( empty($credit_show) ) { $credit_show = $gallerylink_movie['credit_show']; }
	} else if ( $set === 'music' ){
		if( empty($sort) ) { $sort = $gallerylink_music['sort']; }
		if( empty($topurl) ) { $topurl = $gallerylink_music['topurl']; }
		$separator = '|';
		if( empty($suffix) ) {
			$suffix_pattern = strtoupper($gallerylink_music['suffix']).$separator.strtolower($gallerylink_music['suffix']);
		} else {
			$suffix_pattern = strtoupper($suffix).$separator.strtolower($suffix);
		}
		if( empty($suffix_2) ) { $suffix_2 = $gallerylink_music['suffix_2']; }
		if( empty($suffix_keitai) ) {
			$suffix_pattern_keitai = strtoupper($gallerylink_music['suffix_keitai']).$separator.strtolower($gallerylink_music['suffix_keitai']);
		} else {
			$suffix_pattern_keitai = strtoupper($suffix_keitai).$separator.strtolower($suffix_keitai);
		}
		if( empty($display) ) { $display = intval($gallerylink_music['display']); }
		if( empty($display_keitai) ) { $display_keitai = intval($gallerylink_music['display_keitai']); }
		if( empty($thumbnail) ) { $thumbnail = $gallerylink_music['thumbnail']; }
		if( empty($generate_rssfeed) ) { $generate_rssfeed = $gallerylink_music['generate_rssfeed']; }
		if( empty($rssname) ) {
			$rssname = $gallerylink_music['rssname'];
			$rssdef = true;
		}
		if( empty($rssmax) ) { $rssmax = intval($gallerylink_music['rssmax']); }
		if( empty($filesize_show) ) { $filesize_show = $gallerylink_music['filesize_show']; }
		if( empty($stamptime_show) ) { $stamptime_show = $gallerylink_music['stamptime_show']; }
		if( empty($selectbox_show) ) { $selectbox_show = $gallerylink_music['selectbox_show']; }
		if( empty($pagelinks_show) ) { $pagelinks_show = $gallerylink_music['pagelinks_show']; }
		if( empty($sortlinks_show) ) { $sortlinks_show = $gallerylink_music['sortlinks_show']; }
		if( empty($searchbox_show) ) { $searchbox_show = $gallerylink_music['searchbox_show']; }
		if( empty($rssicon_show) ) { $rssicon_show = $gallerylink_music['rssicon_show']; }
		if( empty($credit_show) ) { $credit_show = $gallerylink_music['credit_show']; }
	} else if ( $set === 'document' ){
		if( empty($sort) ) { $sort = $gallerylink_document['sort']; }
		if( empty($topurl) ) { $topurl = $gallerylink_document['topurl']; }
		if( empty($suffix) ) {
			$separator = '|';
			if ( $gallerylink_document['suffix'] === 'all' ) {
				$suffix_pattern = $gallerylink->extpattern();
			} else {
				$suffix_pattern = strtoupper($gallerylink_document['suffix']).$separator.strtolower($gallerylink_document['suffix']);
			}
		} else {
			if ( $suffix === 'all' ) {
				$suffix_pattern = $gallerylink->extpattern();
			} else {
				$suffix_pattern = strtoupper($suffix).$separator.strtolower($suffix);
			}
		}
		if( empty($suffix_keitai) ) {
			if ( $gallerylink_document['suffix_keitai'] === 'all' ) {
				$suffix_pattern_keitai = $gallerylink->extpattern();
			} else {
				$suffix_pattern_keitai = strtoupper($gallerylink_document['suffix_keitai']).$separator.strtolower($gallerylink_document['suffix_keitai']);
			}
		} else {
			if ( $suffix_keitai === 'all' ) {
				$suffix_pattern_keitai = $gallerylink->extpattern();
			} else {
				$suffix_pattern_keitai = strtoupper($suffix_keitai).$separator.strtolower($suffix_keitai);
			}
		}
		if( empty($display) ) { $display = intval($gallerylink_document['display']); }
		if( empty($display_keitai) ) { $display_keitai = intval($gallerylink_document['display_keitai']); }
		if( empty($thumbnail) ) { $thumbnail = $gallerylink_document['thumbnail']; }
		if( empty($generate_rssfeed) ) { $generate_rssfeed = $gallerylink_document['generate_rssfeed']; }
		if( empty($rssname) ) {
			$rssname = $gallerylink_document['rssname'];
			$rssdef = true;
		}
		if( empty($rssmax) ) { $rssmax = intval($gallerylink_document['rssmax']); }
		if( empty($filesize_show) ) { $filesize_show = $gallerylink_document['filesize_show']; }
		if( empty($stamptime_show) ) { $stamptime_show = $gallerylink_document['stamptime_show']; }
		if( empty($selectbox_show) ) { $selectbox_show = $gallerylink_document['selectbox_show']; }
		if( empty($pagelinks_show) ) { $pagelinks_show = $gallerylink_document['pagelinks_show']; }
		if( empty($sortlinks_show) ) { $sortlinks_show = $gallerylink_document['sortlinks_show']; }
		if( empty($searchbox_show) ) { $searchbox_show = $gallerylink_document['searchbox_show']; }
		if( empty($rssicon_show) ) { $rssicon_show = $gallerylink_document['rssicon_show']; }
		if( empty($credit_show) ) { $credit_show = $gallerylink_document['credit_show']; }
	}
	if ( empty($exclude_file) ) {
		$exclude_file = $gallerylink_exclude['file'];
	}
	if ( empty($exclude_dir) ) {
		$exclude_dir = $gallerylink_exclude['dir'];
	}

	$server_root = $_SERVER['DOCUMENT_ROOT'];
	$document_root = $server_root.$topurl;

	$mode = NULL;
	$mode = $gallerylink->agent_check();
	if ( $mode === 'mb' ) {
		$suffix_pattern = $suffix_pattern_keitai;
		$display = $display_keitai;
	}
	if ( $set === 'movie' || $set === 'music' || $set === 'all' ) {
		$suffix_2 =  '.'.$suffix_2;
	}

	$dparam = NULL;
	$fparam = NULL;
	$page = NULL;
	$search = NULL;
	if (!empty($_GET['d'])){
		$dparam = urldecode($_GET['d']);		//dirs
	}
	if (!empty($_GET['f'])){
		$fparam = urldecode($_GET['f']);		//files
	}
	if (!empty($_GET['glp'])){
		$page = $_GET['glp'];					//pages
	}
	if (!empty($_GET['gls'])){
		$search = urldecode($_GET['gls']);		//search word
	}
	if (!empty($_GET['sort'])){
		$sort = $_GET['sort'];					//sort
	}

	$dparam = $gallerylink->mb_encode_multibyte($dparam, $gallerylink_character_code);
	$search = $gallerylink->mb_encode_multibyte($search, $gallerylink_character_code);
	$document_root = $gallerylink->mb_encode_multibyte($document_root, $gallerylink_character_code);
	$exclude_file = $gallerylink->mb_encode_multibyte($exclude_file, $gallerylink_character_code);
	$exclude_dir = $gallerylink->mb_encode_multibyte($exclude_dir, $gallerylink_character_code);
	if (empty($dparam)){
		$dir = $document_root;
	}else{
		$dir = $document_root."/".$dparam;
	}

	$sortnamenew = __('New', 'gallerylink');
	$sortnameold = __('Old', 'gallerylink');
	$sortnamedes = __('Des', 'gallerylink');
	$sortnameasc = __('Asc', 'gallerylink');
	$searchbutton = __('Search', 'gallerylink');
	$dirselectall = __('all', 'gallerylink');
	$mbselectbutton = __('Select', 'gallerylink');

	$sortnamenew = $gallerylink->mb_utf8($sortnamenew, $gallerylink_character_code);
	$sortnameold = $gallerylink->mb_utf8($sortnameold, $gallerylink_character_code);
	$sortnamedes = $gallerylink->mb_utf8($sortnamedes, $gallerylink_character_code);
	$sortnameasc = $gallerylink->mb_utf8($sortnameasc, $gallerylink_character_code);
	$searchbutton = $gallerylink->mb_utf8($searchbutton, $gallerylink_character_code);
	$dirselectall = $gallerylink->mb_utf8($dirselectall, $gallerylink_character_code);
	$mbselectbutton = $gallerylink->mb_utf8($mbselectbutton, $gallerylink_character_code);

	$gallerylink->thumbnail = $thumbnail;
	$gallerylink->suffix_pattern = $suffix_pattern;
	$gallerylink->exclude_file = $exclude_file;
	$gallerylink->exclude_dir = $exclude_dir;
	$gallerylink->generate_rssfeed = $generate_rssfeed;
	$gallerylink->search = $search;
	$gallerylink->dparam = $dparam;
	$gallerylink->topurl = $topurl;
	$gallerylink->document_root = $document_root;
	$gallerylink->set = $set;
	$gallerylink->mode = $mode;
	$gallerylink->rssname = $rssname;
	$gallerylink->rssmax = $rssmax;
	$gallerylink->sort = $sort;
	$gallerylink->filesize_show = $filesize_show;
	$gallerylink->stamptime_show = $stamptime_show;
	$gallerylink->exif_show = $exif_show;

	$org_files = array();
	$files = array();
	$rssfiles = array();

	$dir = $gallerylink->mb_encode_multibyte($dir, $gallerylink_character_code);
	$org_files = $gallerylink->scan_file($dir);

	// time
	foreach ( $org_files as $org_file ){
		$org_file = $gallerylink->mb_encode_multibyte($org_file, $gallerylink_character_code);
		$time_list[] = @filemtime($org_file);
	}
	// sort for newer
	if (!empty($org_files)){
		array_multisort($time_list,SORT_DESC,$org_files); 
	}
	if ( $sort === "new" || empty($sort) ) {
	} else if ($sort === 'old') {
		array_multisort($time_list,SORT_ASC,$org_files); 
	} else if ($sort === 'des') {
		rsort($org_files, SORT_STRING);
	} else if ($sort === 'asc') {
		sort($org_files, SORT_STRING);
	}

	$maxpage = ceil(count($org_files) / $display);
	if(empty($page)){
		$page = 1;
	}
	$gallerylink->page = $page;
	$gallerylink->maxpage = $maxpage;

	add_action( 'wp_footer', array($gallerylink, 'add_js') );

	$beginfiles = 0;
	$endfiles = 0;
	if( $page == $maxpage){
		$beginfiles = $display * ( $page - 1 );
		$endfiles = count($org_files) - 1;
	}else{
		$beginfiles = $display * ( $page - 1 );
		$endfiles = ( $display * $page ) - 1;
	}

	list($files, $rssfiles) = $gallerylink->files_args($org_files, $gallerylink_character_code);
	unset($org_files);
	$dirs = $gallerylink->scan_dir($document_root);

	$linkfiles = NULL;
	$titlename = NULL;
	for ( $i = $beginfiles; $i <= $endfiles; $i++ ) {
		if (!empty($files)){
			$linkfile = $gallerylink->print_file($files[$i]['file'],$files[$i]['title'],$files[$i]['thumblink'],$files[$i]['metadata']);
			$linkfiles = $linkfiles.$linkfile;
			if ( $files[$i]['file'] === '/'.$fparam ) {
				$titlename = $files[$i]['title'];
			}
		}
	}

	$linkselectbox = NULL;
	foreach ($dirs as $linkdir) {
		$linkdirenc = $gallerylink->mb_utf8(str_replace($document_root."/", "", $linkdir), $gallerylink_character_code);
		if($document_root.'/'.$dparam === $linkdir){
			$linkdirs = '<option value="'.urlencode($linkdirenc).'" selected>'.$linkdirenc.'</option>';
		}else{
			$linkdirs = '<option value="'.urlencode($linkdirenc).'">'.$linkdirenc.'</option>';
		}
		$linkselectbox = $linkselectbox.$linkdirs;
	}
	$dirselectall = $gallerylink->mb_utf8($dirselectall, $gallerylink_character_code);
	if(empty($dparam)){
		$linkdirs = '<option value="" selected>'.$dirselectall.'</option>';
	}else{
		$linkdirs = '<option value="">'.$dirselectall.'</option>';
	}
	$linkselectbox = $linkselectbox.$linkdirs;

	$linkpages = NULL;
	$linkpages = $gallerylink->print_pages();

	$scriptname = get_permalink();

	$fparamexts = explode('.', $fparam);
	$fparamext = end($fparamexts);
	$currentfoldercategory = $gallerylink->mb_utf8($dparam, $gallerylink_character_code);
	$currentfoldercategory_encode = urlencode($currentfoldercategory);
	$selectedfilename = $gallerylink->mb_utf8(str_replace('.'.$fparamext, '', $fparam), $gallerylink_character_code);
	$fparam = $gallerylink->mb_utf8($fparam, $gallerylink_character_code);

	$prevfile = "";
	if (!empty($fparam)) {
		if (!empty($currentfoldercategory)) {
			$prevfile = $topurl.'/'.str_replace("%2F","/",$currentfoldercategory_encode).'/'.str_replace("%2F","/",urlencode($fparam));
		}else{
			$prevfile = $topurl.'/'.str_replace("%2F","/",urlencode($fparam));
		}
	}
	$prevfiles = explode('.', $prevfile);
	$prevfile_nosuffix = str_replace('.'.end($prevfiles), '', $prevfile);

	$sortlinks = $gallerylink->sort_pages();

	$permlinkstrform = $gallerylink->permlink_form();

	$mbselectbutton = $gallerylink->mb_utf8($mbselectbutton, $gallerylink_character_code);
	$str_submit = "";
	$str_onchange = "";
	if($mode === 'mb'){
		$str_submit = '<input type="submit" value="'.$mbselectbutton.'">';
	}else{
		$str_onchange = 'onchange="submit(this.form)"';
	}

$selectbox = <<<SELECTBOX
<form method="get" action="{$scriptname}">
{$permlinkstrform}
<select name="d" {$str_onchange}>
{$linkselectbox}
</select>
{$str_submit}
</form>
SELECTBOX;

	$searchbutton = $gallerylink->mb_utf8($searchbutton, $gallerylink_character_code);
	$search = $gallerylink->mb_utf8($search, $gallerylink_character_code);
$searchform = <<<SEARCHFORM
<form method="get" action="{$scriptname}">
{$permlinkstrform}
<input type="hidden" name="d" value="{$currentfoldercategory}">
<input type="text" name="gls" value="{$search}" size="10">
<input type="submit" class="button" value="{$searchbutton}">
</form>
SEARCHFORM;

//MoviePlayerContainer
$movieplayercontainer = <<<MOVIEPLAYERCONTAINER
<div id="PlayerContainer-gallerylink">
<video controls style="width: 100%;" autoplay>
<source src="{$prevfile}">
<source src="{$prevfile_nosuffix}{$suffix_2}">
</video>
</div>
MOVIEPLAYERCONTAINER;

//MusicPlayerContainer
$musicplayercontainer = <<<MUSICPLAYERCONTAINER
<div id="PlayerContainer-gallerylink">
<audio controls autoplay>
<source src="{$prevfile}">
<source src="{$prevfile_nosuffix}{$suffix_2}">
</audio>
</div>
MUSICPLAYERCONTAINER;

	if ( is_single() || is_page() ) {
		wp_enqueue_script('jquery');
		$gallerylink_infinite = get_option('gallerylink_infinite');
		$gallerylink_masonry = get_option('gallerylink_masonry');
		if ( $gallerylink_masonry['apply'] && $set === 'album' ) {
			wp_enqueue_script('imagesloaded', GALLERYLINK_PLUGIN_URL.'/js/imagesloaded.pkgd.min.js', null, '4.1.0');
			wp_enqueue_script('masonry');
		}
		if ( $gallerylink_infinite['apply'] ) {
			wp_enqueue_script('infinitescroll', GALLERYLINK_PLUGIN_URL.'/js/jquery.infinitescroll.min.js', null, '2.1.0');
		}
	}

	if ( $set === 'all' ){
		if( !empty($selectedfilename) ) { $html .= '<h2>'.$selectedfilename.'</h2>'; }
	} else {
		if ( $set <> 'document' && !empty($selectedfilename) ){
			$html .= '<h2>'.$selectedfilename.'</h2>';
		}
	}

	if ( !empty($fparam) ) {
		if ( wp_ext2type($fparamext) === 'video' ) {
			$html .= $movieplayercontainer;
		} else if ( wp_ext2type($fparamext) === 'audio' ) {
			$html .= $musicplayercontainer;
		}
	}

	$linkfiles_begin = NULL;
	$linkfiles_end = NULL;
	$selectbox_begin = NULL;
	$selectbox_end = NULL;
	$linkpages_begin = NULL;
	$linkpages_end = NULL;
	$sortlink_begin = NULL;
	$sortlink_end = NULL;
	$searchform_begin = NULL;
	$searchform_end = NULL;
	$rssfeeds_icon = NULL;

	if ( $mode === 'pc' ) {
		$selectbox_begin = '<div align="left" style="float: left;">';
		$selectbox_end = '</div>';
		$searchform_begin = '<div align="right">';
		$searchform_end = '</div><div style="clear: both;"></div>';
		$sortlink_begin = '<div style="width: 100%; text-align: center; margin: 15px 0px 20px;">';
		$sortlink_end = '</div>';
		$linkfiles_begin = '<div class="gallerylink">';
		$linkfiles_end = '</div><div style="clear: both;"></div>';
		$linkpages_begin = '<div style="width: 100%; text-align: center; margin: 15px 0px 20px;">';
		$linkpages_end = '</div>';
	} else if ( $mode === 'mb' ) {
		$selectbox_begin = '<div>';
		$selectbox_end = '</div>';
		$linkpages_begin = '<div align="center">';
		$linkpages_end = '</div>';
		$sortlink_begin = '<div>';
		$sortlink_end = '</div>';
		$searchform_begin = '<div>';
		$searchform_end = '</div>';
	}

	if ( $selectbox_show === 'Show' ) {
		$html .= $selectbox_begin;
		$html .= $selectbox;
		$html .= $selectbox_end;
	}

	if ( $searchbox_show === 'Show' ) {
		$html .= $searchform_begin;
		$html .= $searchform;
		$html .= $searchform_end;
	}

	if ( $sortlinks_show === 'Show' ) {
		$html .= $sortlink_begin;
		$html .= $sortlinks;
		$html .= $sortlink_end;
	}

	// RSS Feeds
	if ($generate_rssfeed === 'on') {
		$xml_title =  get_bloginfo('name').' | '.get_the_title();

		$rssfeed_url = $topurl.'/'.$rssname.'.xml';
		$rssfeeds_icon = '<div align="right"><a href="'.$rssfeed_url.'" class="dashicons dashicons-rss" style="text-decoration: none;"></a></div>';
		if ( $mode === "pc" ) {
			if ( $rssicon_show === 'Show' ) { $html .= $rssfeeds_icon; }
			if ( $rssdef === false ) {
				$html .= '<link rel="alternate" type="application/rss+xml" href="'.$rssfeed_url.'" title="'.$xml_title.'" />';
			}
		}
		if(!empty($rssfiles)){
			$gallerylink->rss_wirte($xml_title, $rssfiles);
		}
	}

	if ( $credit_show === 'Show' ) {
		$html .= '<div align = "right"><a href="https://wordpress.org/plugins/gallerylink/"><span style="font-size : xx-small">by GalleryLink</span></a></div>';
	}

	$html .= $linkfiles_begin;
	$html .= $linkfiles;
	$html .= $linkfiles_end;

	if ( $pagelinks_show === 'Show' ) {
		$html .= $linkpages_begin;
		$html .= $linkpages;
		$html .= $linkpages_end;
	}

	$html = apply_filters( 'post_gallerylink', $html );

	return $html;

}

?>