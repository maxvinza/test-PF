<?php
//тесты
namespace TestFromPizzaFabrica;

include '../main.php';

$error = false;

foreach($routes->routes as $key=>$value){
    foreach($value['requests'] as $k=>$v){
        $startTime = $turn->inputTurn[$v]['timeIn'];
        if($startTime + 60 < $value['maxEndTime']) {
            print "request ".$v." error_time ".$startTime." \n";
            $error = true;
        }
        else{
           $delta = $value['maxEndTime'] - $startTime;
           print "Tic: ".$key.", num:".$v." [ ".$startTime." - ".$value['maxEndTime']." ] delta time ".$delta."\n";
        }
    }
}

if($error) print "Problems! \n";
else print "All test ok \n";
?>
