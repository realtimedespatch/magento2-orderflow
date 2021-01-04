<?php

namespace RealtimeDespatch\OrderFlow\Helper;

use DOMDocument;
use Exception;

class Xml
{
    /**
     * DOM Document Factory.
     *
     * @param $xml
     * @return DOMDocument|boolean
     */
    public function getDomDocument($xml)
    {
        try {
            $dom = new DOMDocument;
            $dom->preserveWhiteSpace = false;
            $dom->loadXML($xml);
            $dom->formatOutput = true;

            return $dom;
        } catch (Exception $ex) {
            return false;
        }
    }
}
