<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly      
/*---------------------------------------------------------------------------------------*/
/*
Internationalization (i18n) - Preparing your plugin for translation. Make it possible to someone else to translate it.

	1. Using GetText functions for strings 
	2. Unique Text Domain (define which theme/plugin owns the translated text) - DON'T USE a VARIABLE (must be hardcoded)

	<?php _e("string to translate", "text-domain"); ?>

	Don't place PHP variables inside a translation function! In that case, you can use springf:
	springf( __("I have %d places to go", "text-domain"), $number)

	Handle plurals with variables using the _n function:
	
	$string = sprintf( _n('You have %d taco.', 'You have %d tacos.', $number, 'plugin-domain'), $number );

	http://ottopress.com/2012/internationalization-youre-probably-doing-it-wrong/

	How does the translation system import strings? Do I need .po/.pot files?

	The translation system looks through all PHP files in a plugin and finds each gettext call, including __() and _e(). 
	For this reason, .po/.pot files are not needed to import, however they’re a great way of ensuring your entire plugin is completely internationalized.

	3. Once the plugin is fully internationalized --> create a .pot file and include it in your /languages folder.

	How to generate POT file for my plugin?

	1. Install Loco Translate 
	2. Go to Settings --> increase the "Skip PHP files larger than:" threshold 
	3. Go to Settings --> Scan JavaScript files with extensions: -> add "js"
	3. Go to Plugins --> plugin name --> Create Template 


Internationalization is the process to provide multiple language support to software, in this case WordPress. 
Internationalization is often abbreviated as i18n, where 18 stands for the number of letters between the first i and the last n.

Providing i18n support to your plugin and theme allows it to reach the largest possible audience, 
even without requiring you to provide the additional language translations. 
When you upload your software to WordPress.org, all JS and PHP files will automatically be parsed. Any detected translation strings are added to translate.
Wordpress.org to allow the community to translate, ensuring WordPress plugins and themes are available in as many languages as possible.

Internationalization is the process of developing a plugin so it can easily be translated into other languages. 
Internationalization is often abbreviated as i18n (because there are 18 letters between the letters i and n).
/*---------------------------------------------------------------------------------------*/
/*

Localization describes the subsequent process of translating an internationalized plugin. 
Localization is often abbreviated as l10n (because there are 10 letters between the l and the n.)
* POT - This file contains the original strings (in English) in your plugin.
The POT file is the one you need to hand to translators, so that they can do their work. 

* PO - Every translator will take the POT file and translate the msgstr sections into their own language. 
The result is a PO file with the same format as a POT, but with translations and some specific headers. 
There is one PO file per language.
* MO - From every translated PO file a MO file is built. 
These are machine-readable, binary files that the gettext functions actually use (they don’t care about .POT or .PO files), and are a “compiled” version of the PO file. 
The conversion is done using the msgfmt command line tool. 
In general, an application may use more than one large logical translatable module and a different MO file accordingly. 
A text domain is a handle of each module, which has a different MO file.

Loads and defines the internationalization files for this plugin so that it is ready for translation.
WordPress uses the GNU gettext localization framework for translation. In this framework, there are three types of files:

	1. Portable Object Template (POT)
	2. Portable Object (PO)
	3. language files (.mo)

/*---------------------------------------------------------------------------------------*/
/*
	How to generate POT file for my plugin?

	1. Install Loco Translate 
	2. Go to Settings --> increase the "Skip PHP files larger than:" threshold 
	3. Go to Settings --> Scan JavaScript files with extensions: -> add "js"
	3. Go to Plugins --> plugin name --> Create Template 
*/
/*---------------------------------------------------------------------------------------*/
/* 
	Test translations

	You will need to set your WordPress installation to Esperanto language. Go to Settings > General and change your site language to Esperanto.
	With the language set, create a new post, add the block, and you will see the translations used.

*/
/*---------------------------------------------------------------------------------------*/
class IDOCS_i18n {

	// For plugins hosted on WordPress Repo, the text domain on the plugin is already declared from the plugin slug
	/*

		Since WordPress 4.6 translations now take translate.wordpress.org as priority 
		so plugins that are translated via translate.wordpress.org do not necessary require load_plugin_textdomain() anymore. 
		If you still want to load your own translations and not the ones from translate, you will have to use a hook filter named load_textdomain_mofile.
	*/
	function load_plugin_textdomain( $mofile, $domain ) {

		if ( 'incredibledocs' === $domain && false !== strpos( $mofile, WP_LANG_DIR . '/plugins/' ) ) {
			
			$locale = apply_filters( 'plugin_locale', determine_locale(), $domain );
			$mofile = WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) . '/languages/' . $domain . '-' . $locale . '.mo';
			
		}
		/*-----------------------------------------------*/
		
		return $mofile;
	}
	/*---------------------------------------------------------------------------------------*/
	// tell WordPress that a specific JavaScript contains translation that should be loaded 
	public function javascript_set_script_translations( ) {

		// When you set script translations for a handle WordPress will automatically figure out if a translations file exists on translate.wordpress.org, 
		// and if so ensure that it's loaded into wp.i18n before your script runs. 

		// This function takes three arguments: the registered/enqueued script handle, the text domain, and optionally a path to the directory containing translation files. 
		// The latter is only needed if your plugin or theme is not hosted on WordPress.org, which provides these translation files automatically.
		wp_set_script_translations( 'class-idocs-custom-sidebar-js', 'incredibledocs' );

	}
	/*---------------------------------------------------------------------------------------*/
}
/*---------------------------------------------------------------------------------------*/
// https://daext.com/blog/how-to-make-a-wordpress-plugin-translatable/
// https://developer.wordpress.org/plugins/internationalization/
// https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/
// https://developer.wordpress.org/apis/internationalization/#internationalizing-javascript
// https://github.com/WordPress/gutenberg/blob/trunk/docs/how-to-guides/internationalization.md
// https://pexetothemes.com/wordpress-functions/is_rtl/

