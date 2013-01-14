More/Locale
===========

Additional I18n module for AGL.

## Installation

Add the following package to the `require` section of your application's `composer.json` file:

	"agl/more-locale": "*"

## Configuration

### Configuration file

In your application, create a file `app/etc/config/more/locale.json` with the following content:

	{
		"default" : "en",
		"accepted": [
			"en"
		],
		"domains" : {

		},
		"urls"    : [

		],
		"params"  : [

		],
		"values"  : [

		]
	}

* *default*: default language of your application
* *accepted*: an array of languages accepted by your application
* *domains*: optional, associative array to automatically set language depending of the domain name
* *urls*: optional, list of urls (*module/view*) to translate (should be added to your Gettext files)
* *params*: optional, list of parameters names to translate (should be added to your Gettext files)
* *values*: optional, list of parameters values to translate (should be added to your Gettext files)

For example:

	{
		"default" : "en",
		"accepted": [
			"en",
			"fr"
		],
		"domains" : {
			"domain.com": "en",
			"domain.fr": "fr"
		},
		"urls"    : [
			"home/project"
		],
		"params"  : [
			"param_name"
		],
		"values"  : [
			"param_value"
		]
	}

### Gettext files

Create also your Gettext files for each language, for example:

`app/etc/locale/en_GB.utf8/LC_MESSAGES/default.mo`
`app/etc/locale/en_GB.utf8/LC_MESSAGES/default.po`

`app/etc/locale/fr_FR.utf8/LC_MESSAGES/default.mo`
`app/etc/locale/fr_FR.utf8/LC_MESSAGES/default.po`

## Usage

### Select language

If you don't use a domain name per language, start your URLs with the language code you want to use. For example: `http://domain.tld/en/` or  `http://domain.tld/fr/`.

### Display i18n string

	echo _("String");

### Get current language

	Agl::getSingleton('more/locale')->getLanguage();

### Get accepted languages

	Agl::app()->getConfig('more/locale]/accepted');
