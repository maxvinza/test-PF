<?php
namespace TestFromPizzaFabrica;
/*
Класс формирет очередь заявок
*/
class PizzaTurn{
    public $inputTurn = array();
    public $inputTimeMin = 1;
    public $inputTimeMax = 30;
    /*
    * Поля элемента очереди:
    * timeIn - время возникновения заявки
    * timeOut -время приготовления заявки (относительно 0)
    * timeProcess - время приготовления пиццы(абсолютное)
    */
    function makeTurn($num){
        //Если число заявок не указано - берем случаной число в промежутке от 10 до 100
        if($num == 0) $num = rand(10,100);
        $last = $this->inputTimeMin;
        $lambda = sqrt(1/($this->inputTimeMax-$this->inputTimeMin));
        for($i=0;$i<$num;++$i){
            $delta =0;
            while ($delta > $this->inputTimeMax OR $delta < $this->inputTimeMin){
              $delta = $this->puassonTime($lambda);
            }
            $current = $last + $delta;
            $process = $this->outTime();
            $outTime = $process + $current;
            $this->inputTurn[] = array('id'          => $i,
                                       'timeIn'      => $current,
                                       'timeOut'     => $outTime,
                                       'timeProcess' => $process,
                                       'coordinates' => $this->coordinates());
            $last = $current;
        }
    }
    //расчет интервалов между двумя событиями
    function puassonTime($lambda){
       return -1/$lambda*log(rand()/getrandmax());
    }
    //Делаем рандомные координаты
    function coordinates(){
        $x = rand(-1000,1000);
        $y = rand(-1000,1000);
        return compact("x","y");
    }
    function outTime(){
        return rand(10,30);
    }
}
?>
