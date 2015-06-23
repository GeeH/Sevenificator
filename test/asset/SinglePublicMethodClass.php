<?php

namespace GeeH\SevenificatorTest\Asset;

/**
 * Class SinglePublicMethodClass
 * @package GeeH\SevenificatorTest\Asset
 */
class SinglePublicMethodClass
{

    /**
     * @param array $array
     * @param string $string
     * @param int $int
     * @return bool
     */
    public function aSingleMethod(array $array, $string, $int)
    {
        return true;
    }
}