<pre>
<?php

class Temp {
    private $frnd = "John";

    public function getFrndName() {
        echo "Temp Frnd Name :  $this->frnd" . "\n";
    }
}


class MagicMethods {
    private $frnd = "Azar";
    public function getName() {
        echo "hello" . "\n";
    }

    private function getFrndName() {
        echo "Frnd Name :  $this->frnd" . "\n";
    }

    public function __call($method, $args) {
        if(method_exists(new Temp(), $method)) {
            call_user_func_array([new Temp(), $method], $args);
            return;
        } 

        $methods = scandir(__DIR__ . '/api/apis');

        foreach($methods as $m) {
            if($m == '.' || $m == '..') {
                continue;
            } 
            $basem = basename($m, '.php');
            require_once(__DIR__ . '/api/apis/' . $m);
            echo $user; 
            
        }
    }
}

$obj = new MagicMethods();
$obj->getName();
$obj->getFrndName();
$obj->helloGuys();
$hero = new SuperHero();
echo $hero->getPowers();

?>
</pre>