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
    private static $_redirectOrig = false;

    /**
     * Handle the request before its use by AGL to reverse/translate it.
     *
     * @param array $pObserver
     */
    public static function translateRequest(array $pObserver)
    {
        $requestUri = &$pObserver['request_uri'];
        if (! $requestUri) {
            return false;
        }

        $locale = \Agl::getSingleton(\Agl::AGL_MORE_POOL . '/locale/locale');

        if (preg_match('#^/[a-z]{2}/#', $requestUri, $matches)) {
            $requestUri = str_replace($matches[0], '', $requestUri);
            $lang       = str_replace('/', '', $matches[0]);
            $locale->setLanguage($lang);
        } else {
            $locale->setLanguage();
        }

        $urls         = $locale->getUrls();
        $params       = $locale->getParams();
        $paramsValues = $locale->getParamsValues();

        $request = preg_replace('#(^/)|(/$)#', '', $requestUri);
        preg_match_all('#([a-z0-9]+)/([a-z0-9_-]+)#', $request, $matches);

        if (! empty($urls) and isset($matches[0][0])) {
            if (isset($urls[$matches[0][0]])) {
                $requestUri = str_replace($matches[0][0], $urls[$matches[0][0]], $requestUri);
            } else if (in_array($matches[0][0], $urls) and isset($pObserver['request']) and $pObserver['request'] instanceof \Agl\Core\Request\Request) {
                self::$_redirectOrig = true;
            }
        }

        if (! empty($params) and isset($matches[1]) and is_array($matches[1]) and count($matches[1]) > 1) {
            array_shift($matches[1]);
            foreach ($matches[1] as $param) {
                if (isset($params[$param])) {
                    $requestUri = str_replace("/$param/", '/' . $params[$param] . '/', $requestUri);
                }
            }
        }

        if (! empty($paramsValues) and isset($matches[2]) and is_array($matches[2]) and count($matches[2]) > 1) {
            array_shift($matches[2]);
            foreach ($matches[2] as $value) {
                if (isset($paramsValues[$value])) {
                    $requestUri = str_replace("/$value/", '/' . $paramsValues[$value] . '/', $requestUri);
                }
            }
        }
    }

    public static function redirectOrig(array $pObserver)
    {
        if (self::$_redirectOrig) {
            $pObserver['request']->redirect($pObserver['request']->getModule() . '/' . $pObserver['request']->getView(), $pObserver['request']->getParams(), 301);
        }
    }
}
