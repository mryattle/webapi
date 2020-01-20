<?php
namespace App\Helpers;

use Illuminate\Http\Request;

class i18n
{
    /**
     * Retrieve locale using user request
     *
     * @param value  the id of the object
     */
    static function locale(Request $request) {
        if($request->has('locale'))
            return $request->input('locale');
        // Accept language
        $lang = $request->header('Accept-Language');
        if(!is_null($lang))
            return $lang;
        return 'en';
    }
}