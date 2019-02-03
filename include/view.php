<?php
namespace TestFromPizzaFabrica;
/*
вывод в cli результатов расчета
*/
class view{
  function printRoutes($routes){
     $printstr = "Маршруты:\n";
     foreach ($routes as $key => $value) {
        $printstr .= "Маршрут : ".$key."\n";
        foreach ($value['requests'] as $k => $v) {
            $time = $this->numToTime($value['requestsTime'][$k]);
            $printstr .= $v." ".$time."\n";
        }
     }
     print $printstr;
  }
  function printRequests($requests){
    $printstr = "Завки:\n";
    foreach ($requests as $key => $value) {
        $printstr .= $key." "
                     .$this->numToTime($value['timeIn'])." "
                     .$this->numToTime($value['timeProcess'])." "
                     .$value['coordinates']['x']." "
                     .$value['coordinates']['y']."\n";
    }
    print $printstr;
  }
  //Переводим число минут в минуты:секунды
  function numToTime($time){
     $min = floor($time);
     $sec = round(($time - $min)*60);
     return $min.":".$sec;
  }
}
?>
