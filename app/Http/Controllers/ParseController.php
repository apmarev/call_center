<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Revolution\Google\Sheets\Facades\Sheets;

class ParseController extends Controller {

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




            $values = Sheets::spreadsheet('160Fn0hUuonM4AvH1sdc92o1qOCv5NK2KNvhDAGUFR8I')->sheet('Лист1')->range('A1:B1000')->all();

            $insert = [];

            foreach($message as $device) {
                $isset = -1;
                $key = 0;
                foreach($values as $value) {
                    if($value[0] == $device['name']) {
                        $isset = $key;
                        break;
                    }
                    $key++;
                }

                if($isset > -1) $values[$isset] = [$device['name'], $device['price']];
                else $insert[] = [$device['name'], $device['price']];
            }

            if(sizeof($insert) > 0) {
                foreach($insert as $i) {
                    $values[] = $i;
                }
            }

            Sheets::spreadsheet('160Fn0hUuonM4AvH1sdc92o1qOCv5NK2KNvhDAGUFR8I')->sheet('Лист1')->range("A1")->update($values);

        }




        return "Ok";

    }

}
