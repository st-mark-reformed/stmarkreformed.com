<?php

namespace dev\services;

use Michelf\SmartyPants;

/**
 * Class TypesetService
 */
class TypesetService
{
    /**
     * Runs both functions
     * @param string $str
     * @return string
     */
    public function typeset(string $str) : string
    {
        return $this->widont($this->smartypants($str));
    }

    /**
     * Processes string to keep widows from happening
     * @param string $str
     * @return string
     */
    public function widont(string $str = '') : string
    {
        // This regex is a beast, tread lightly
        $widontTest = "/([^\s])\s+(((<(a|span|i|b|em|strong|acronym|caps|sub|sup|abbr|big|small|code|cite|tt)[^>]*>)*\s*[^\s<>]+)(<\/(a|span|i|b|em|strong|acronym|caps|sub|sup|abbr|big|small|code|cite|tt)>)*[^\s<>]*\s*(<\/(p|h[1-6]|li)>|$))/i";

        return preg_replace($widontTest, '$1&nbsp;$2', $str);
    }

    /**
     * Runs smartypants on string
     * @param string $str
     * @return string
     */
    public function smartypants(string $str = '') : string
    {
        return SmartyPants::defaultTransform($str);
    }
}
