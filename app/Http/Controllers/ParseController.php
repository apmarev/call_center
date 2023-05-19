<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Revolution\Google\Sheets\Facades\Sheets;

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

        if($request->has('message') && isset($request->input('message')['text'])) {
            $message = [];

            $lines = explode("\n", $request->input('message')['text']);

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
        }

        $values = Sheets::spreadsheet('160Fn0hUuonM4AvH1sdc92o1qOCv5NK2KNvhDAGUFR8I')->sheet('Лист1')->range('A1:B1000')->all();

        foreach($message as $device) {
            $isset = 0;
            $key = 0;
            foreach($values as $value) {
                if($value[0] == $device['name']) {
                    $isset = $key;
                    break;
                }
                $key++;
            }

            if($isset > 0) Sheets::spreadsheet('160Fn0hUuonM4AvH1sdc92o1qOCv5NK2KNvhDAGUFR8I')->sheet('Лист1')->range("A{$isset}")->update([[$device['name'], $device['price']]]);
            else Sheets::spreadsheet('160Fn0hUuonM4AvH1sdc92o1qOCv5NK2KNvhDAGUFR8I')->sheet('Лист1')->range("A{$isset}")->append([[$device['name'], $device['price']]]);
        }


        return "Ok";

    }

}
