<?php

namespace PHPUnit\Framework;

$class = 'PHPUnit\Framework\TestCase';

if (!class_exists($class)) {
    abstract class TestCase extends \PHPUnit_Framework_TestCase
    {
    }
}
