=== GalleryLink ===
Contributors: Katsushi Kawamori
Donate link: https://pledgie.com/campaigns/28307
Tags: audio,feed,feeds,gallery,html5,image,images,list,music,photo,photos,picture,pictures,rss,shortcode,video,xml
Requires at least: 3.0.1
Tested up to: 4.7
Stable tag: 10.05
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Output as a gallery from the directory.

== Description ==

GalleryLink outputs as a gallery from the directory.

* Directory name and file name support for multi-byte character.

(Photos, music, videos, documents) data that is supported.

You write and use short codes to page.

Bundled software and function

*   HTML5 player (video, music)
*   Create RSS feeds of data (XML). It support to the podcast.
*   Works with [Masonry](http://masonry.desandro.com/).
*   Works with [Infinite Scroll](http://www.infinite-scroll.com/).

    It support to the japanese mobile phone. Themes [Garake](http://riverforest-wp.info/garake/) is required. Garake is unofficial theme.

== Installation ==

1. Upload `gallerylink` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add a new Page
4. Write a short code. The following text field. `[gallerylink]`
5. For all data `[gallerylink set='all']`. For pictures `[gallerylink set='album']`. For video `[gallerylink set='movie']`. For music `[gallerylink set='music']`. For document `[gallerylink set='document']`.
6. Please set. (Settings > Gallerylink)

    [Settings](https://wordpress.org/plugins/gallerylink/other_notes/)

7. Navigate to the appearance section and select widgets, select wordpress GalleryLinkRssFeed and configure from here.

== Frequently Asked Questions ==

none

== Screenshots ==

1. Settings 1
2. Settings 2

== Changelog ==

= 10.05 =
Closed plugin

= 10.04 =
Deleted slideshow mode.

= 10.03 =
Fixed problem of Javascript.

= 10.02 =
Fixed problem of masonry.
Fixed problem of file extension.

= 10.01 =
Fixed problem of pagination for japanese mobile phone.
Fixed problem of sort for japanese mobile phone.
Fixed problem of images displayed for japanese mobile phone.
Fixed problem of character code.

= 10.0 =
Fixed problem of settings screen.
Fixed problem of css.
Fixed problem of character code.

= 9.99 =
Fixed problem of settings screen.
Fixed problem of enter key.
Add progress display.

= 9.98 =
Add character encodings for server.
Fixed problem of settings screen.

= 9.97 =
Delete unnecessary code.

= 9.96 =
Supported Infinite Scroll.
Supported Masonry.
Change the position of the navigation.
Fixed problem of uninstall.

= 9.95 =
Supported GlotPress.
/languages directory is deleted.

= 9.94 =
Fixed problem of simplexml_load_file parser error.

= 9.93 =
Javascript and CSS will be loaded only to the required page.

= 9.92 =
Fixed problem of feed icon.

= 9.91 =
Change feed icon.

= 9.9 =
Fixed problem of Widgets.
Change /languages.

== Upgrade Notice ==

= 10.05 =
= 10.04 =
= 10.03 =
= 10.02 =
= 10.01 =
= 10.0 =
= 9.99 =
= 9.98 =
= 9.97 =
= 9.96 =
= 9.95 =
= 9.94 =
= 9.93 =
= 9.92 =
= 9.91 =
= 9.9 =

== Settings ==

How to use
Please set the default value in the setting page.

* Please upload the data to the data directory (topurl) by the FTP software.

Please add new Page. Please write a short code in the text field of the Page. Please go in Text mode this task.

In the case of all data

* [gallerylink set='all']

In the case of image

* [gallerylink set='album']

In the case of video

* [gallerylink set='movie']

In the case of music

* [gallerylink set='music']

In the case of document

* [gallerylink set='document']

Customization

GalleryLink can be used to specify various attributes to the short code. It will override the default settings.

All data Example

* [gallerylink set='all']

Image Example

* [gallerylink set='album' topurl='/wordpress/wp-content/uploads' exclude_file='(.ktai.)|(-[0-9]*x[0-9]*.)' exclude_dir='ps_auto_sitemap|backwpup.*|wpcf7_captcha' rssname='album']

Video Example

* [gallerylink set='movie' topurl='/gallery/video' rssmax=5]

Music Example

* [gallerylink set='music' topurl='/gallery/music']

Document Example

* [gallerylink set='document' topurl='/gallery/document' suffix='doc']

Caution

* If you want to use MULTI-BYTE CHARACTER SETS to the display of the directory name and the file name. In this case, please upload the file after UTF-8 character code setting of the FTP software.

* Please set to 777 or 757 the attributes of topurl directory. Because GalleryLink create thumbnail and RSS feed to the directory.

* (WordPress > Settings > General Timezone) Please specify your area other than UTC. For accurate time display of RSS feed.

* When you move to (WordPress > Appearance > Widgets), there is a widget GalleryLinkRssFeed. If you place you can set this to display the sidebar link the RSS feed.
