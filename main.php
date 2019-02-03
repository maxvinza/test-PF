<?php
namespace TestFromPizzaFabrica;

include_once 'include/pizzaturn.php';
include_once 'include/routes.php';
include_once 'include/view.php';

$turn= new PizzaTurn();
$turn->makeTurn(0);
$routes = new Routes($turn->inputTurn);
$view = new view;
$view->printRequests($turn->inputTurn);
$view->printRoutes($routes->routes);
?>
