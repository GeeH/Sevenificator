<?php

namespace GeeH\SevenificatorTest\Asset;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag\AuthorTag;

/**
 * Class SinglePublicMethodClass
 * @package GeeH\SevenificatorTest\Asset
 */
class MultiplePublicMethodClass
{

    /**
     * @param array $array
     * @param string $string
     * @param int $int
     * @return bool
     */
    public function oneMethod(array $array, $string, $int)
    {
        return true;
    }

    /**
     * @param MultiplePublicMethodClass $self
     * @param float $float
     * @param bool $bool
     * @return string
     */
    public function twoMethod(MultiplePublicMethodClass $self, $float, $bool)
    {
        return 'Hi';
    }

    /**
     * @param $one
     * @param int $two
     * @param $three
     * @param array $four
     * @param $five
     * @return \stdClass
     */
    public function threeMethod($one, $two, $three, $four, $five)
    {
        return new \stdClass();
    }

    /**
     * @param AuthorTag $tag
     * @return AuthorTag
     */
    public function fourMethod(AuthorTag $tag)
    {
        return $tag;
    }

    /**
     * @param string $one
     * @param string $two
     * @param string $three
     * @return bool
     */
    public function fiveMethod($one = 'Orange Portal', $two = 'Blue Portal', $three = null)
    {
        return true;
    }
}