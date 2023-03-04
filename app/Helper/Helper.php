<?php

namespace App\Helper;

class Helper
{
    public static function getUrl($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
			return $url;
		}

        $url = str_replace('public', 'storage', $url);

		return $url ? url($url) : null;
    }
}
