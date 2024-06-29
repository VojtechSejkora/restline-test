<?php

namespace Test;

use App\TestedClass;
use PHPUnit\Framework\TestCase;

class TestedClassTest extends TestCase {
    public function testEvenIsCalledWhenNumberIsEven() {
        // Create a partial mock for the TestedClass class,
        // only mock the even method.
        $mock = $this->createPartialMock(TestedClass::class, ['even']);

        // Set up the expectation for the even method
        // to be called once.
        $mock->expects($this->once())
            ->method('even');

        // Call the testThis method with an even number
        $mock->testThis(2);
    }

    public function testEvenIsNotCalledWhenNumberIsOdd() {
        // Create a partial mock for the TestedClass class,
        // only mock the even method.
        $mock = $this->createPartialMock(TestedClass::class, ['even']);

        // Set up the expectation for the even method
        // to not be called.
        $mock->expects($this->never())
            ->method('even');

        // Call the testThis method with an odd number
        $mock->testThis(3);
    }
}
