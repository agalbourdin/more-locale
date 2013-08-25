AGL Framework - More/Locale
===========================

Additional I18n module for [AGL Framework](https://github.com/agl-php/agl-app).

## Installation

Run the following command in the root of your AGL application:

	php composer.phar require agl/more-locale:*

## Configuration

### Configuration file

Edit `app/etc/config/more/locale/main.php` to configure the module.

### Gettext files

Create also Gettext files for each language you wish to use, for example:

`app/etc/locale/en_GB.utf8/LC_MESSAGES/default.mo`
`app/etc/locale/en_GB.utf8/LC_MESSAGES/default.po`

`app/etc/locale/fr_FR.utf8/LC_MESSAGES/default.mo`
`app/etc/locale/fr_FR.utf8/LC_MESSAGES/default.po`

## Usage

### Select language

If you don't use a domain name per language, start your URLs with the language code you want to use. For example: `http://domain.tld/en/` or `http://domain.tld/fr/`.

### Display i18n string (GetText syntax)

	echo _("String");

### Get current language

	Agl::getSingleton('more/locale')->getLanguage();

### Get accepted languages

	Agl::getSingleton('more/locale')->getAcceptedLanguages();

### Create an URL with a specific language

Instead of calling Agl::getUrl(), which uses the current language, call the following method with a $pLang parameter.

	Agl::getSingleton('more/locale')->getUrl($pPath, $pParams, $pRelative, $pLang);

For example:

	Agl::getSingleton('more/locale')->getUrl('game/upload', array(), true, 'fr');
