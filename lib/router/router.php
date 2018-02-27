<?php

/**
 * Первый уровень понимания роутеров.
 * Намерено сделано через if и else
 * Пример урла: /?/{controller}/{action}/{param1}/{value1}/{param2}/{value2}/
 * /?/task/update/id/1/
 */

$params = [];
$pathList = preg_split('/\//', $_SERVER['REQUEST_URI'], -1, PREG_SPLIT_NO_EMPTY);
array_shift($pathList);


// Значение по умолчанию
if (!isset($_COOKIE['logged_in'])) {
    $pathList = ['user', 'getForm'];
} elseif (count($pathList) < 2) {
    $pathList = ['task', 'list'];
}
if (count($pathList) >= 2) {
    $controller = array_shift($pathList);
    $action = array_shift($pathList);
    foreach ($pathList as $i => $value) {
        if ($i % 2 == 0 && isset($pathList[$i + 1])) {
            $params[$pathList[$i]] = $pathList[$i + 1];
        }
    }
    if ($controller == 'task') {
        include 'controller/TaskController.php';
        $tasks = new TaskController($db, $twig);
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            if ($action == 'list') {
                $tasks->getList();
            } elseif ($action == 'add') {
                $tasks->getAdd();
            } elseif ($action == 'update') {
                $tasks->getUpdate($params);
            } elseif ($action == 'delete') {
                $tasks->getDelete($params);
            }
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($action == 'add') {
                $tasks->postAdd($_POST);
            } elseif ($action == 'update') {
                $tasks->postUpdate($params, $_POST);
            }
        }
    }
    if ($controller == 'user') {
        include 'controller/UserController.php';
        $user = new UserController($db, $twig);
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            if ($action == 'getForm') {
                $user->getForm();
            }
            if ($action == 'logout') {
                $user->logout();
            }
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($_POST['register']) {
                $user->register($_POST);
            } elseif ($_POST['log_in']) {
                $user->log_in();
            }
        }

    }
}

?>
