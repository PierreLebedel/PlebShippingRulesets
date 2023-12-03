<?php

namespace PlebWooCommerceShippingRules;

class Dumper
{

    public static function dump(...$args)
    {
        foreach($args as $value){
            echo '<pre style="color:white;background:#222;border-radius:5px;margin:10px 0;padding:5px 10px;">'.print_r($value, true).'</pre>';
        }
    }

}