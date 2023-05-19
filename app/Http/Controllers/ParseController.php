<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ParseController extends Controller {

    protected static function removeEmoji($string): string {

        $regex_emoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $clear_string = preg_replace($regex_emoticons, '', $string);

        $regex_symbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $clear_string = preg_replace($regex_symbols, '', $clear_string);

        $regex_transport = '/[\x{1F680}-\x{1F6FF}]/u';
        $clear_string = preg_replace($regex_transport, '', $clear_string);

        $regex_misc = '/[\x{2600}-\x{26FF}]/u';
        $clear_string = preg_replace($regex_misc, '', $clear_string);

        $regex_dingbats = '/[\x{2700}-\x{27BF}]/u';
        $clear_string = preg_replace($regex_dingbats, '', $clear_string);

        return $clear_string;
    }

    public function getMessage(Request $request) {

        Log::alert(json_encode($request->input('message')));

        return true;

        $message = [];

        $lines = explode("\n", $request->input('message'));

        foreach($lines as $line) {
            if($line == '' || $line == ' ') continue;
            if(strripos($line, '[') === false) {

            } else continue;

            $string = trim(preg_replace("/[^a-zA-ZА-Яа-яЁё0-9\s]/u","", $line));

            $string_array = explode(" ", $string);

            $price = end($string_array);

            array_pop($string_array);

            $name = implode(' ', $string_array);

            if($name != '' && $price != '' && is_numeric($price))
                $message[] = [
                    'name' => implode(' ', $string_array),
                    'price' => $price,
                ];
        }

        return $message;

    }

}
