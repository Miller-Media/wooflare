<?php

if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

/**
 * Class WOOCF_Helpers
 *
 * Helper functions for Cloudflare IP Blacklist plugin
 *
 */
class WOOCF_Helpers
{
    /**
     * Function to parse domain from a URL.
     * Solution taken from: https://stackoverflow.com/a/45044051/975592
     *
     * @param $url
     * @return string
     */
    public function parseBaseDomainFromURL($url){
        $url = substr($url,0,4)=='http'? $url: 'http://'.$url;
        $d = parse_url($url);
        $tmp = explode('.',$d['host']);
        $n = count($tmp);
        if ($n>=2){
            if ($n==4 || ($n==3 && strlen($tmp[($n-2)])<=3)){
                $d['domain'] = $tmp[($n-3)].".".$tmp[($n-2)].".".$tmp[($n-1)];
                $d['domainX'] = $tmp[($n-3)];
            } else {
                $d['domain'] = $tmp[($n-2)].".".$tmp[($n-1)];
                $d['domainX'] = $tmp[($n-2)];
            }
        }
        return $d;
    }
}