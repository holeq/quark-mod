=== BO-модификация Quark ===

Базируется на Quark v1.3 и Options Framework v1.9.1.

!!! Для корректного функционирования необходимы следующие плагины:
WP Session Manager
	https://wordpress.org/plugins/wp-session-manager/
BO Lessify Wordpress
WP Better Attachments
	https://wordpress.org/plugins/wp-better-attachments/
Add From Server (Добавить с сервера)
	https://wordpress.org/plugins/add-from-server/



	

=== Данные с оригинального README ===

=== Quark ===

Contributors: ahortin
Donate link: http://quarktheme.com
Requires at least: 3.6
Tested up to: 4.1
Stable tag: 1.3
License: GPLv2 or later

== License ==

Quark is licensed under the [GNU General Public License version 2](http://www.gnu.org/licenses/old-licenses/gpl-2.0.html).

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your option) any later version.

== Credits ==

Quark utilises the following awesomeness:

[Options Framework](http://wptheming.com/options-framework-theme), which is licensed under the GPLv2 License
[Modernizr](http://modernizr.com), which is licensed under the MIT license
[Normalize.css](https://github.com/necolas/normalize.css), which is licensed under the MIT license
[jQuery Validation](http://bassistance.de/jquery-plugins/jquery-plugin-validation) which is dual licensed under the MIT license and GPL licenses
[Font Awesome](http://fortawesome.github.io/Font-Awesome) icon font, which is licensed under SIL Open Font License and MIT License
[PT Sans font](http://www.google.com/fonts/specimen/PT+Sans), which is licensed under SIL Open Font License 1.1
[Arvo font](http://www.google.com/fonts/specimen/Arvo), which is licensed under SIL Open Font License 1.1


== Changelog ==

= 1.3 =
- Updated normalize.css to v3.0.2
- Updated Options Framework to v1.9.1
- Fixed focus on footer links so they're visible
- Added French translation. Props @arpinfo
- Added support for new title-tag
- Added support for WooCommerce

= 1.2.12 =
- Updated Modernizr to v2.8.3
- Updated Font Awesome icon font to v4.2
- Updated jQuery Validation to v1.13.0
- Added SlideShare icon to the theme options

= 1.2.11 =
- Updated Modernizr to v2.8.2
- Updated Font Awesome icon font to v4.1
- Added German translation. Props Tino Groteloh
- Added WPML compatibility

= 1.2.10 =
- Updated normalize.css to v3.0.1
- Updated Modernizr to v2.7.2
- Fixed grid percentages
- Added RSS icon to the theme options
- Added Spanish translation. Props @amieiro
- Updated img element to add vertical-align so images are better aligned

= 1.2.9 =
- Removed Google Analytics script as requested by theme reviewer. This is best left for plugins so please ensure you add one if you were using this feature

= 1.2.8 =
- Fixed undefined function error on sanitization methods that were introduced due to Options Framework changing to class based code

= 1.2.7 =
- Updated Font Awesome icon font to v4.0.3
- Updated Options Framework to v1.7.1
- Updated Modernizr to v2.7.1
- Updated comments to be enclosed in <section> rather than <div>. Props @gnotaras
- Removed pubdate from post/comment meta. Replaced with itemprop
- Removed invalid attribute from email input box. Props @gnotaras

= 1.2.6 =
- Updated normalize.css to v2.1.3
- Updated Font Awesome icon font to v4.0.0 (incl. renaming font classes as per their new naming convention)
- Removed Font Awesome More font as it's now outdated and no longer needed 
- Removed minimum-scale & maximum-scale from viewport meta tag
- Fixed extra period in blockquote style. Props @angeliquejw
- Fixed 'Skip to main content' accessibility link
- Added extra theme option to allow social media links to open in another browser tab/window
- Added extra social media profiles in the theme options for Dribbble, Tumblr, Bitbucket and Foursquare
- Added check for 'Comment author must fill out name and e-mail' setting when validating comments

= 1.2.5 =
- Updated normalize.css to v2.1.2
- Updated Font Awesome icon font to v3.2.1
- Updated theme short description
- Updated Post Format templates to contain Author bio
- Updated _e() references to esc_html_e() to ensure any html added into translation file is neutralised
- Updated __() references to esc_html__() to ensure any html added into translation file is neutralised
- Added template for displaying Author bios
- Added extra use of wp_kses() to ensure only authorised HTML is allowed in translations
- Added loading of Google Fonts in TinyMCE Editor
- Added display of Featured Image on Pages, if used
- Added extra styling to make sure non-breaking text in the title, content, and comments doesn't break layout
- Removed login_errors filter. This is best left for plugins
- Removed audio.js since audio functionality is now part of core
- Removed use of clearfix class as containers will now automatically clear

= 1.2.4 =
- Updated strings that weren't wrapped in gettext functions for translation purposes
- Updated Text Domain in Options Framework
- Added esc_url() when using site URL in header
- Added sanitation when outputting theme options
- Fixed bottom margin on blog articles on homepage
- Fixed text colour in homepage banner

= 1.2.2 =
- Updated blockquote.pull-right style
- Updated footer smallprint link colour
- Fixed display of site name in header area if no Custom Header is specified (ie. no logo image)
- Removed wp_head hook that removes the WP version number. This is best left for plugins
- Updated enqueing of scripts. Scripts that are being depended on, will automatically get enqueued so no need to enqueue them manually.
- Added max-with of 100% to select form fields. Field no longer extends past container in sidebar
- Fixed padding in main content area when homepage is a blog, so pagination doesn't touch footer
- Changed fonts so they're called from Google Fonts rather than local
- Removed unrequired font files from fonts folder

= 1.2.1 =
- Fixed sidebars
- Updated description in stylesheet
- Updated IE filters in btn class 
- Added extra class when styling frontpage widgets

= 1.2 =
- Updated Options Framework to version 1.5.2
- Replaced Museo font with Arvo font
- Replaced background images
- Replaced Responsive Grid System with own custom grid
- Replaced IcoMoon icon font with Font Awesome icon font
- Added GitHub social icon theme option

= 1.1 =
- Changed margin and removed padding on .row class and consolidated html to remove extra container elements from templates
- Removed unnecessary comments from style.css
- Updated navigation margins in media queries
- Updated margin, padding & font-size with matching rem values, where missing
- Updated readme.txt with Getting Started information
- Removed Google Analytics code from footer and enqueued with other scripts
- Initial Repository Release

= 1.0 =
- Initial version
