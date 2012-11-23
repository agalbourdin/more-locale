<?php
namespace Agl\More\Locale;

/**
 * Setting the locale.
 *
 * @category Agl_More
 * @package Agl_More_Locale
 * @version 0.1.0
 */

class Locale
{
    /**
     * Default encoding
     */
    const ENCODING = 'utf8';

    /**
     * Current language.
     *
     * @var string
     */
    private $_language = NULL;

    /**
     * Default language.
     *
     * @var string
     */
    private $_defaultLanguage = NULL;

    /**
     * List of accepted languages.
     *
     * @var array
     */
    private $_acceptedLanguages = array();

    /**
     * List of domain names with their corresponding language.
     *
     * @var array Associative array
     */
    private $_domainsLanguages = array();

    /**
     * List of supported locales.
     *
     * @var array Associative array
     */
    private $_locales = array(
        'fr' => 'fr_FR',
        'en' => 'en_US',
        'es' => 'es_ES',
        'de' => 'de_DE',
        'it' => 'it_IT'
    );

    /**
     * Register the translated URLs.
     */
    private $_urls = array();

    /**
     * Register the translated parameters names.
     */
    private $_params = array();

    /**
     * Register the translated parameters values.
     */
    private $_paramsValues = array();

    /**
     * Initialization.
     *
     * We get the defaultLanguage, acceptedLanguages and domainsLanguages from the configuration.
     * The current language is determined by a call to the _getRequestedLanguage method.
     */
    public function __construct()
    {
        $this->_defaultLanguage = \Agl::app()->getConfig('@module[' . \Agl::AGL_MORE_POOL . '/locale]/default');
        if (! is_string($this->_defaultLanguage)or ! isset($this->_locales[$this->_defaultLanguage])) {
            throw new \Agl\Exception("Incorrect default language code");
        }

        $this->_acceptedLanguages = \Agl::app()->getConfig('@module[' . \Agl::AGL_MORE_POOL . '/locale]/accepted');
        if (! in_array($this->_defaultLanguage, $this->_acceptedLanguages)) {
            throw new \Agl\Exception("Incorrect accepted languages configuration");
        }

        $domainsLanguages = \Agl::app()->getConfig('@module[' . \Agl::AGL_MORE_POOL . '/locale]/domain');
        if (is_array($domainsLanguages)) {
            $this->_domainsLanguages = $domainsLanguages;
        }
    }

    /**
     * Return the path to the current application's locale directory.
     *
     * @return string
     */
    private function _getAppLocalePath()
    {
        return \Agl::app()->getPath()
               . \Agl::APP_ETC_DIR
               . DS
               . 'locale';
    }

    /**
     * Apply a locale to the server configuration.
     *
     * @param string $pLocale
     */
    private function _setLocale($pLocale)
    {
        setlocale(LC_ALL, $pLocale);
        putenv('LANG=' . $pLocale);
        putenv('LC_ALL=' . $pLocale);
    }

    /**
     * Add a text domain to the server configuration.
     *
     * @param string $pDomainName The getText filename to use
     * @param string $pDir The getText directory
     */
    private function _addDomain($pDomainName, $pDir) {
        bindtextdomain($pDomainName, $pDir);
        bind_textdomain_codeset($pDomainName, self::ENCODING);
    }

    /**
     * Set the text domain.
     *
     * @param string $pDomainName
     */
    private function _setDomain($pDomainName) {
        textdomain($pDomainName);
    }

    /**
     * Get the language to use.
     *
     * Use the HTTP HOST if domains are set, or the $pLang parameter.
     *
     * @param null|string $pLang Language code to use
     * @return string
     */
    public function setLanguage($pLang = NULL)
    {
        if (! empty($this->_domainsLanguages)) {
            $key = \Agl\Core\Data\Arr::arraySearch($_SERVER['HTTP_HOST'], $this->_domainsLanguages);
            if ($key !== false) {
                $this->_language = $this->_domainsLanguages[$key]['lang'];
            }
        }

        if ($this->_language === NULL and preg_match('/^[a-z]{2}$/', $pLang) and $this->_isLanguageAccepted($pLang)) {
            $this->_language = $pLang;
        } else {
            $this->_language = $this->_defaultLanguage;
        }

        if (! isset($this->_locales[$this->_language])) {
            throw new \Agl\Exception("Invalid locale");
        }

        $this->_setLocale($this->_locales[$this->_language] . '.' . self::ENCODING);
        $this->_addDomain('default', $this->_getAppLocalePath());
        $this->_setDomain('default');

        $this->_translateUrls();

        return $this;
    }

    /**
     * Check if the language is accepted in the module configuration.
     *
     * @param string $pLang
     * @return bool
     */
    private function _isLanguageAccepted($pLang)
    {
        if (in_array($pLang, $this->_acceptedLanguages)) {
            return true;
        }

        return false;
    }

    /**
     * Translate the URLs with the key/value configured in the module XML
     * configuration.
     *
     * @return Locale
     */
    private function _translateUrls()
    {
        $urls = \Agl::app()->getConfig('@module[' . \Agl::AGL_MORE_POOL . '/locale]/urls', true);
        foreach ($urls as $url) {
            if (is_string($url)) {
                $this->_urls[_($url)] = $url;
            }
        }

        $params = \Agl::app()->getConfig('@module[' . \Agl::AGL_MORE_POOL . '/locale]/params', true);
        foreach ($params as $param) {
            if (is_string($param)) {
                $this->_params[_($param)] = $param;
            }
        }

        $values = \Agl::app()->getConfig('@module[' . \Agl::AGL_MORE_POOL . '/locale]/params', true);
        foreach ($values as $value) {
            if (is_string($value)) {
                $this->_paramsValues[_($value)] = $value;
            }
        }

        return $this;
    }

    /**
     * Return the current language.
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->_language;
    }

    /**
     * Return a formated URL with module, view, action and parameters.
     *
     * @param string $pUrl URL to get (module/view)
     * @param array $pParams Parameters to include into the request
     * @param bool $pRelative Create a relative URL
     * @return string
     */
    public function getUrl($pUrl, array $pParams = array(), $pRelative = true)
    {
        if (! $pUrl) {
            return ROOT;
        }

        $translatedUrl = _($pUrl);

        if (strpos($pUrl, \Agl::APP_PUBLIC_DIR) === false) {
            if (! empty($pParams)) {
                $params = array();
                foreach ($pParams as $key => $value) {
                    $params[] = _($key) . DS . $value;
                }

                $url = $translatedUrl . DS . implode(DS, $params) . DS;
                if ($pRelative) {
                    return ROOT . $url;
                }
                return self::getHostUrl($url);
            }

            $url = $translatedUrl . DS;
            if ($pRelative) {
                return ROOT . $url;
            }
            return \Agl\Core\Url\Url::getHostUrl($url);
        }

        if ($pRelative) {
            return ROOT . $translatedUrl;
        }
        return \Agl\Core\Url\Url::getHostUrl($translatedUrl);
    }

    /**
     * Return the translated URLs array.
     *
     * @return array
     */
    public function getUrls()
    {
        return $this->_urls;
    }

    /**
     * Return the translated parameters names array.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Return the translated parameters values array.
     *
     * @return array
     */
    public function getParamsValues()
    {
        return $this->_paramsValues;
    }
}