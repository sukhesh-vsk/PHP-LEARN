<?php

class MagicMethods {
    public function getName() {
        echo "hello";
    }
}

echo "hello";
$obj = new MagicMethods();
$obj->getName();

