<?php

namespace RealtimeDespatch\OrderFlow\Test\Unit\Helper;

use PHPUnit\Framework\TestCase;
use RealtimeDespatch\OrderFlow\Helper\Xml;

class XmlTest extends TestCase
{
    /**
     * @var Xml
     */
    protected $helper;

    public function setUp()
    {
        $this->helper = new Xml();
    }

    public function testGetDomDocumentWithValidXml()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                        <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
                          <SOAP-ENV:Body>
                          </SOAP-ENV:Body>
                        </SOAP-ENV:Envelope>';

        $this->assertInstanceOf('DOMDocument', $this->helper->getDomDocument($xml));
    }

    public function testGetDomDocumentWithInvalidXml()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                        <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
                          </SOAP-ENV:Body>
                        </SOAP-ENV:Envelope>';

        $this->assertFalse($this->helper->getDomDocument($xml));
    }
}
