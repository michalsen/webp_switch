<?php
/*
Plugin Name: Webp Switch
Plugin URI: http://localhost
Description: Switching Webp images in Wordpress content since 1975.
Version:     1
Author: Eric L. Michalsen
Author URI:
Text Domain: wporg
Domain Path: /languages
License:     GPL2

{Plugin Name} is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

{Plugin Name} is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with {Plugin Name}. If not, see {License URI}.
*/

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}


function callback($buffer) {

    $doc = new DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML($buffer);
    $xpath = new DOMXPath($doc);
    $imagelist = $xpath->query("//img");


   foreach ($imagelist as $key => $value) {
     $node = $imagelist->item($key);
     $src = $node->attributes->getNamedItem('src')->nodeValue;
     $alt = $node->attributes->getNamedItem('alt')->nodeValue;
     $class = $node->attributes->getNamedItem('class')->nodeValue;
     $path = pathinfo($src);

       switch ($path['extension']) {
         case 'jpg':
             $type = 'image/jpeg';
           break;
         case 'jpeg':
             $type = 'image/jpeg';
           break;
         case 'png':
             $type = 'image/png';
           break;
       }

    $picture = '<picture>
                  <source srcset="' . $src . '.webp" type="image/webp">
                  <source srcset="' . $src . '" type="' . $type . '">
                  <img src="' . $src . '" alt="' . $alt . '" class="' . $class . '">
                </picture>';


     $buffer = preg_replace("#<img[^>](.*?)$src(.*?)+>#", $picture, $buffer);
   }


    return $buffer;
}

function buffer_start() { ob_start("callback"); }
function buffer_end() { ob_end_flush(); }

add_action('after_setup_theme', 'buffer_start');
add_action('shutdown', 'buffer_end');



