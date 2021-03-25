<?php
class Add
{
    public static function run($envelope, $queue) {
        // Handle response
        echo $envelope->getBody()."\n";
    }

}