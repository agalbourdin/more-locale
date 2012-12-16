<?php
namespace Agl\More\Locale;

/**
 * Observer for configured Locale events.
 *
 * @category Agl_More
 * @package Agl_More_Locale
 * @version 0.1.0
 */

class Observer
{
    /**
     * If an URL was requested, and a translation exists, redirect the user to
     * the translated version to avoid duplicate content.
     *
     * @var bool
     */
    private static $_redirectOrig = false;

    /**
     * Handle the request before its use by AGL to reverse/translate it.
     *
     * @param array $pObserver
     */
    public static function translateRequest(array $pObserver)
    {
        $origRequestUri = $pObserver['request_uri'];
        $requestUri     = &$pObserver['request_uri'];
        $locale         = \Agl::getSingleton(\Agl::AGL_MORE_POOL . '/locale/locale');

        if (preg_match('#^' . DS . '[a-z]{2}' . DS . '#', $requestUri, $matches)) {
            $requestUri = str_replace($matches[0], '', $requestUri);
            $lang       = str_replace(DS, '', $matches[0]);
            $locale->setLanguage($lang);
        } else {
            $locale->setLanguage();
        }

        $urls         = $locale->getUrls();
        $params       = $locale->getParams();
        $paramsValues = $locale->getParamsValues();

        if (substr($requestUri, 0, 1) !== DS) {
            $requestUri = DS . $requestUri;
        }

        if (substr($requestUri, -1) !== DS) {
            $requestUri .= DS;
        }

        preg_match_all('#([a-z0-9]+)' . DS . '([a-z0-9_-]+)#', $requestUri, $matches);

        if (! empty($urls) and isset($matches[0][0])) {
            if (isset($urls[$matches[0][0]])) {
                $requestUri = str_replace($matches[0][0], $urls[$matches[0][0]], $requestUri);
            } else if ($key = array_search($matches[0][0], $urls)) {
                self::$_redirectOrig = true;
                $origRequestUri      = str_replace($matches[0][0], $key, $origRequestUri);
            }
        }

        if (! empty($params) and isset($matches[1]) and is_array($matches[1]) and count($matches[1]) > 1) {
            array_shift($matches[1]);
            foreach ($matches[1] as $param) {
                if (isset($params[$param])) {
                    $requestUri = str_replace(DS . $param . DS, DS . $params[$param] . DS, $requestUri);
                } else if ($key = array_search($param, $params)) {
                    self::$_redirectOrig = true;
                    $origRequestUri      = str_replace(DS . $param . DS, DS . $key . DS, $origRequestUri);
                }
            }
        }

        if (! empty($paramsValues) and isset($matches[2]) and is_array($matches[2]) and count($matches[2]) > 1) {
            array_shift($matches[2]);
            foreach ($matches[2] as $value) {
                if (isset($paramsValues[$value])) {
                    $requestUri = str_replace(DS . $value . DS, DS . $paramsValues[$value] . DS, $requestUri);
                } else if ($key = array_search($value, $paramsValues)) {
                    self::$_redirectOrig = true;
                    $origRequestUri      = str_replace(DS . $value . DS, DS . $key . DS, $origRequestUri);
                }
            }
        }

        if (self::$_redirectOrig) {
            $requestUri = $origRequestUri;
        }
    }

    /**
     * If an URL was requested, and a translation exists, redirect the user to
     * the translated version to avoid duplicate content.
     *
     * @param array $pObserver
     */
    public static function redirectOrig(array $pObserver)
    {
        if (self::$_redirectOrig) {
            $pObserver['request']->redirect($pObserver['request']->getModule() . '/' . $pObserver['request']->getView(), $pObserver['request']->getParams(), 301);
        }
    }
}
