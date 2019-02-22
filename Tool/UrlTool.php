<?php

namespace Kitpages\DataGridBundle\Tool;

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

        $parseTab["query"] = http_build_query($query);

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