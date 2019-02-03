<?php
namespace TestFromPizzaFabrica;
/*
Класс составлет маршруты - аргументом конструктора является очередь заявок,
сформированная в классе PizzaTurn
*/
class Routes{
    /*
    routesGraph - двухмерный массив. Ключам которого являются id заявок
                  (как на первом, так и на втором уровне), значениями - время
                  проезда между точками в минутах
    routes       -массив маршрутов(итоговый)
    inRoutes     -массив ключами которого являются id заявок, необходим
                   для отсутствия дублирования в создании маршрутов
    turn         -массив очереди
    speed        -Скорость единиц в час
    */
    public $routesGraph = array();
    public $routes = array();
    public $inRoutes = array();
    public $turn = array();
    public $speed = 60;
    function __construct($turn){
        $this->turn = $turn;
        $zeroPlace = array('x'=>0,'y'=>0);
        $zeroRequest = array('id'         => -1,
                             'coordinates'=>$zeroPlace);
        foreach ($turn as $k => $v) {
            $this->neibor($zeroRequest,$v);
            $this->makeNeibors($v,$turn);
        }
        $this->makeRoutS($this->sortByTimeOut($turn));
    }
    /*
    Сортируется массив заявок - на выходе массив времен изготовления пиццы
    отсортированный по времени изготовления по возрастанию
    */
    function sortByTimeOut($turn){
        $notSort= array();
        foreach ($turn as $key => $value) {
            $notSort[$key] = $value['timeOut'];
        }
        asort($notSort);
        return $notSort;
    }
    /*
    две функции ниже составляют массив соседей точки доставки
    глобальный массив класса routesGraph
    */
    function makeNeibors($request,$turn){
        foreach ($turn as $k => $v) {
            if($v['timeOut']>$request['timeOut']
               AND $v['timeOut']<$request['timeOut'] + 60){
                $this->neibor($request,$v);
            }
        }
    }
    function neibor($requestA,$requestB){
        $deltaTime = $this->driveTime($requestA['coordinates'],$requestB['coordinates']);
        $idA = $requestA['id'];
        $idB = $requestB['id'];
        if($deltaTime<60){
            $this->routesGraph[$idA][$idB]=$deltaTime;
            $this->routesGraph[$idB][$idA]=$deltaTime;
        }
    }
    //Время проезда между двумя точками пространства
    function driveTime($coordinatesA,$coordinatesB){
        $xA = $coordinatesA['x'];
        $yA = $coordinatesA['y'];
        $xB = $coordinatesB['x'];
        $yB = $coordinatesB['y'];
        $delta = sqrt(pow(($xB-$xA),2)+pow(($yB-$yA),2));
        return $delta/$this->speed;
    }
    //непосредственно создание маршрутов
    function makeRoutS($sorttime){
        foreach($sorttime as $key => $value){
            $this->makeRoutE(-1,$key);//-1 означает нулевую точку
            foreach($sorttime as $key2 => $value2){
                $this->makeRoutE($key,$key2);
            }
        }
    }
    /*
    создание маршрута -на входе 2 точки между которыми, предположительно можно проложить маршрут
    элементы массива routes - маршруты
    данные маршрута:
    maxEndTime - максимальное время завершения маршрута (старт самого раннего
    заказа + 60 минут)
    currentTime - текущее время - время последнего заказа
    currentDriveTime - общее время выезда (абсолютное)
    startTime - время начала выезда
    requests - заявки
    requestsDriveTime - время привоза пиццы клиенту
    currentPlace - точка текущей (последней) заявки
    */
    function makeRoutE($idA,$idB){
       if(isset($this->inRoutes[$idB])) return true;//проверяем входит ли точка в какой-либо маршрут
       if($idA == -1){
           $this->inRoutes[$idB] = 1;
           $maxBtime =  $this->MaxTime($idB);
           $currentTime = $this->routesGraph[-1][$idB] + $this->turn[$idB]['timeOut'];
           $this->routes[]=array('maxEndTime'       =>$maxBtime,
                                 'currentTime'      =>$currentTime,
                                 'currentPlace'     =>$idB,
                                 'currentDriveTime' =>$this->routesGraph[-1][$idB],
                                 'startTime'        =>$this->turn[$idB]['timeOut'],
                                 'requests'         =>array('0'=>$idB),
                                 'requestsTime'     =>array('0'=>$currentTime),
                                 'requestsDriveTime'=>array('0'=>$this->routesGraph[-1][$idB]));
       }
       else{
           foreach ($this->routes as $key => $value) {
               if(in_array($idA,$value['requests'])
                  AND $value['currentPlace'] == $idA
                  AND count($value['requests'])<3){
                   if(!isset($this->routesGraph[$idA][$idB])) return true;
                   $deltaCurrentTime=$value['currentTime'] + $this->routesGraph[$idA][$idB];
                   if($deltaCurrentTime < $value['maxEndTime']){
                       $this->inRoutes[$idB] = 1;
                       if($this->turn[$idB]['timeOut'] > $this->routes[$key]['startTime'])
                           $this->routes[$key]['startTime'] = $this->turn[$idB]['timeOut'];
                       $this->routes[$key]['currentDriveTime'] += $this->routesGraph[$idA][$idB];
                       $this->routes[$key]['requests'][] = $idB;
                       $this->routes[$key]['currentPlace'] = $idB;
                       $this->routes[$key]['currentTime'] = $deltaCurrentTime;
                       $this->routes[$key]['requestsTime'][] = $deltaCurrentTime;
                       $this->routes[$key]['requestsDriveTime'][] = $this->routes[$key]['currentDriveTime'];
                       if($this->MaxTime($idB)<$value['maxEndTime'])
                           $this->routes[$key]['maxEndTime'] = $this->MaxTime($idB);
                   }
               }
           }
       }
    }
    function MaxTime($numRequest){
        return $this->turn[$numRequest]['timeIn'] + 60;
    }
}
?>
