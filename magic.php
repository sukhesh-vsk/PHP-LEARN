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

        $dir = __DIR__ . '/api/apis/';
        $methods = scandir($dir);

        foreach($methods as $m) {
            if($m == '.' || $m == '..') {
                continue;
            } 
            
            $basem = basename($m, '.php');
            if($basem == $method) {
                include $dir . $m;
                $func = Closure::bind(${$basem}, $this, get_class());
                echo "Closure :" . "\n";
                var_dump($func);
                if(is_callable($func)) {
                    return call_user_func_array($func, $args);
                } else {
                    return "NO Method Found";
                }
                // echo ${$basem}();
            }
            
        }
    }
}

$obj = new MagicMethods();
$obj->getName();
$obj->getFrndName();
echo $obj->getPowers();



?>
</pre>