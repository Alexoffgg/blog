<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 10.02.2017
 * Time: 15:45
 */

class Router
{
    private $routes;

    public function __construct()
    {
//        Подключаем и присваиваем свойтву класса файл с массивом правил для роутинга

        $routesPath = ROOT.'/config/routes.php';
        $this->routes = include($routesPath);
    }

//            Получаем строку запроса

    private function getURI()
    {
        if (!empty($_SERVER['REQUEST_URI'])) {
            return trim($_SERVER['REQUEST_URI'], '/');
        }
    }

    public function run()
    {
        $uri = $this->getURI();

//        Если имеются совпадения то определяем какой контроллер и экшн обрабатывают запрос

        foreach($this->routes as $uri_pattern => $path){

            if(preg_match("~$uri_pattern~", $uri)){

//              Извлекаем внутренние параметры из маршрута

                $internalRoute = preg_replace('~$uri_pattern~', $path, $uri);

//                Определяем какой экшн и контроллер должен быть вызван

                $segments = explode('/', $internalRoute);

                $controllerName = ucfirst(array_shift($segments)). 'Controller';
                $actionName = 'action' .ucfirst(array_shift($segments));
                $params = $segments; //массив данных включающий в себя дополнительные параметры получаемые из адресной строки

//                Ищем и подключаем файл требуемого контроллера

                $controllerFile = ROOT . '/controllers/' . $controllerName . '.php';
                if(file_exists($controllerFile)) {
                    include_once($controllerFile); //Если файл существуем то подключаем его однократно
                }

//                Создаем экземпляр класса-контроллера и вызываем соответствующий метод

                    $controllerObj = new $controllerName;
                    $method = call_user_func_array(array($controllerObj, $actionName), $params);

//                Осуществляем проверку. Если метод класса уже вызван уже используется, то тогда мы выходим из цикла

                if($method != null){
                    break;
                }

            }else{

//                если запрос пуст - переходим на главную страницу

                require_once(ROOT.'/controllers/NewsController.php');
                $startPage = new NewsController();
                $startPage->actionIndex();
            }
        }
    }

//    public function run()
//    {
//        $uri = $this->getURI();
//
//        foreach ($this->routes as $uriPattern => $path) {
//
//            if(preg_match("~$uriPattern~", $uri)) {
//
//                /*				echo "<br>Где ищем (запрос, который набрал пользователь): ".$uri;
//                                echo "<br>Что ищем (совпадение из правила): ".$uriPattern;
//                                echo "<br>Кто обрабатывает: ".$path; */
//
//                // Получаем внутренний путь из внешнего согласно правилу.
//
//                $internalRoute = preg_replace("~$uriPattern~", $path, $uri);
//
//                /*				echo '<br>Нужно сформулировать: '.$internalRoute.'<br>'; */
//
//                $segments = explode('/', $internalRoute);
//
//                $controllerName = array_shift($segments).'Controller';
//                $controllerName = ucfirst($controllerName);
//
//
//                $actionName = 'action'.ucfirst(array_shift($segments));
//
//                $parameters = $segments;
//
//
//                $controllerFile = ROOT . '/controllers/' .$controllerName. '.php';
//                if (file_exists($controllerFile)) {
//                    include_once($controllerFile);
//                }
//
//                $controllerObject = new $controllerName;
//                /*$result = $controllerObject->$actionName($parameters); - OLD VERSION */
//                /*$result = call_user_func(array($controllerObject, $actionName), $parameters);*/
//                $result = call_user_func_array(array($controllerObject, $actionName), $parameters);
//
//                if ($result != null) {
//                    break;
//                }
//            }
//
//        }
//    }
}