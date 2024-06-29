<?php
declare(strict_types=1);

namespace App;

class TestedClass {

    public function testThis(int $number) : void {
        if ($number % 2 == 0) {
            $this->even();
        } else {
            return;
        }
    }

    public function even() : void {
        // Some logic here
    }
}
