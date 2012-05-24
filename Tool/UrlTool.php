<?php

namespace Kitpages\DataGridBundle\Tool;
/**
 * Created by JetBrains PhpStorm.
 * User: levan
 * Date: 25/04/12
 * Time: 16:01
 * To change this template use File | Settings | File Templates.
 */
class UrlTool
{
    /**
     * @param $url
     * @param  array  $mixedKey
     * @param  null   $value
     * @return string
     */
    public function changeRequestQueryString($url, $mixedKey = array(), $value = null)
    {
        if (is_string($mixedKey)) {
            $changeTab = array("$mixedKey" => $value);
        } else {
            $changeTab = $mixedKey;
        }

        $parseTab = parse_url($url);
        $queryString = "";
        if (array_key_exists("query", $parseTab)) {
            $queryString = $parseTab["query"];
        }
        parse_str($queryString, $query);
        foreach ($changeTab as $key => $val) {
            $query[$key] = $val;
        }
        // build new query string
        $newQueryString = http_build_query($query);
        $parseTab["query"] = $newQueryString;

        // change
        return
            ((isset($parseTab['scheme'])) ? $parseTab['scheme'] . '://' : '')
            .((isset($parseTab['user'])) ? $parseTab['user'] . ((isset($parseTab['pass'])) ? ':' . $parseTab['pass'] : '') .'@' : '')
            .((isset($parseTab['host'])) ? $parseTab['host'] : '')
            .((isset($parseTab['port'])) ? ':' . $parseTab['port'] : '')
            .((isset($parseTab['path'])) ? $parseTab['path'] : '')
            .((isset($parseTab['query'])) ? '?' . $parseTab['query'] : '')
            .((isset($parseTab['fragment'])) ? '#' . $parseTab['fragment'] : '');
    }

}
