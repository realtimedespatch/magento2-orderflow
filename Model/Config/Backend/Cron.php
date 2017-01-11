<?php

namespace RealtimeDespatch\OrderFlow\Model\Config\Backend;

/**
 * Class Cron
 * @package RealtimeDespatch\OrderFlow\Model\Config\Backend
 * @codeCoverageIgnore
 */
class Cron extends \Magento\Framework\App\Config\Value
{
    const CRON_REGEX = '/^(((([\*]{1}){1})|((\*\/){0,1}(([0-9]{1}){1}|(([1-5]{1}){1}([0-9]{1}){1}){1}))) ((([\*]{1}){1})|((\*\/){0,1}(([0-9]{1}){1}|(([1]{1}){1}([0-9]{1}){1}){1}|([2]{1}){1}([0-3]{1}){1}))) ((([\*]{1}){1})|((\*\/){0,1}(([1-9]{1}){1}|(([1-2]{1}){1}([0-9]{1}){1}){1}|([3]{1}){1}([0-1]{1}){1}))) ((([\*]{1}){1})|((\*\/){0,1}(([1-9]{1}){1}|(([1-2]{1}){1}([0-9]{1}){1}){1}|([3]{1}){1}([0-1]{1}){1}))|(jan|feb|mar|apr|may|jun|jul|aug|sep|okt|nov|dec)) ((([\*]{1}){1})|((\*\/){0,1}(([0-7]{1}){1}))|(sun|mon|tue|wed|thu|fri|sat)))$/';

    /**
     * {@inheritdoc}
     */
    public function beforeSave()
    {
        if (preg_match(self::CRON_REGEX, (string) $this->getValue()) !== 1) {
            throw new Exception(Mage::helper('cron')->__('Unable to parse the cron expression.'));
        }

        return parent::beforeSave();
    }
}