<?php

require_once(dirname(__FILE__) . '/lib.php');

/**
 * Create xml feed from an associative array
 *
 * @param Array $feedarr
 * format:
 * 
 *    array(
 *       'title' => 'This is the name of my store',
 *       'link' => 'http://mystore.com',
 *       'item' => array(
 *           array(
 *               'title' => 'Title of an example product',
 *               'brand' => 'Brand name of an example product',
 *               'description' => 'Some description here',
 *               'pid' => '1',
 *               'link' => 'http://mystore.com/?pid=1',
 *               'image_link' => 'http://mystore.com/image/?pid=1',
 *               'price' => '200',
 *               'currency' => 'USD',
 *               'availability' => '1',
 *               'category' => array(
 *                   'Phones',
 *                   'Guns',
 *                   'Mirrors',
 *               )
 *           ),
 *           array(
 *               'title' => 'Title of another example product',
 *               'brand' => 'Brand name of another example product',
 *               'description' => 'Some description here',
 *               'pid' => '1',
 *               'link' => 'http://mystore.com/?pid=1',
 *               'image_link' => 'http://mystore.com/image/?pid=1',
 *               'price' => '200',
 *               'currency' => 'USD',
 *               'availability' => '0',
 *               'category' => array(
 *                   'Code Readers',
 *                   'Editors'                
 *               )
 *           )
 *       )
 *   );
 * 
 * @return String xml
 * 
 */
function kodecrm_feed_create($feedarr) {
    $feedarr = array(
        'channel' => $feedarr,
    );
    $dom = new XmlDomConstruct('1.0', 'utf-8');
    $dom->fromMixed($feedarr);
    $dom->formatOutput = true;
    $feed = $dom->saveXML();
    $feed = str_replace('<channel>','<rss version="2.0"><channel>', $feed);
    $feed = str_replace('</channel>','</channel></rss>', $feed);
    return $feed;
}

