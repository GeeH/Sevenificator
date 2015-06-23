<?php
/**
 * Created by Gary Hockin.
 * Date: 20/06/2015
 * @GeeH
 */

namespace GeeH\SevenificatorTest\Asset;


class SinglePublicMethodWithDefaultClass
{
    /**
     * @param string $string
     * @param bool $bool
     * @param array $array
     * @param int $int
     * @param float $float
     * @return int
     */
    public function aPublicMethod($string = 'GlaDos)', $bool = false, array $array = ['Wheatley'], $int = 10, $float = null)
    {
        return 100;
    }
}