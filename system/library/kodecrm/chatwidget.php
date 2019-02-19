<?php

/**
 * Helpers for rendering the chat widget from a
 * bunch of settings
 *
 * @param String $appid The KodeCRM appid
 * @param String $custom_settings An array of settings
 *   Example: $custom_settings = 'color:#000;text:Chat with us;bg:#000099'
 */
function kodecrm_chatwidget_render($appid) {
    $snippet = "var _kcrm = {};";
    $snippet .= "_kcrm['app_id'] = '$appid';";
    $snippet .= "(function (w, d, undefined) {";
    $snippet .= "    var k = document.createElement(\"script\"),";
    $snippet .= "    r = document.getElementsByTagName('script')[0],";
    $snippet .= "    p = ('https:' == document.location.protocol ? 'https://' : 'http://');";
    $snippet .= "    k.type = \"text/javascript\";";
    $snippet .= "    k.src =  p + 'kodecrm.com/static/javascript/widget.js';";
    $snippet .= "    r.parentNode.appendChild(k);";
    $snippet .= "}) (window, document);";
    return $snippet;
}

/**
 * Function to convert the custom settings string to a key-value array
 */
function kodecrm_chatwidget_settings($custom_settings) {
    $settings = array();
    $parts = array_map('trim', explode(';', $custom_settings));
    foreach ($parts as $part) {
        if (!$part) continue;
        list($k, $v) = array_map('trim', explode(':', $part));
        $settings[$k] = $v;
    }
    return $settings;
}
