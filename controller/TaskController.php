<?php

class TaskController {
    private $taskModel = null;
    private $userModel = null;
    private $twig;
    function __construct($db, $twig)
    {
        include 'model/Task.php';
        $this->taskModel = new Task($db);
        include 'model/User.php';
        $this->userModel = new User($db);
        $this->twig = $twig;
    }

    /**
     * Отображаем шаблон
     * @param $template
     * @param $params
     */
    private function render($template, $params = [])
    {
        $fileTemplate = 'template/'.$template;
        if (is_file($fileTemplate)) {
            ob_start();
            if (count($params) > 0) {
                extract($params);
            }
            echo $this->twig->render($template, ['params' => $params, 'currentUser' => $_COOKIE['logged_in']]);
            return ob_get_clean();
        }
    }

    /**
     * Форма добавления задачи
     * @param $params array
     * @return mixed
     */
    function getAdd()
    {
        echo $this->render('task/add.html');
    }

    /**
     * Добавление задачи
     * @param $params array
     * @return mixed
     */
    function postAdd($post)
    {
        if (isset($post['description'])) {
            $idAdd = $this->taskModel->add([
                'description' => $post['description'],
            ]);
            if ($idAdd) {
                header('Location: /');
            }
        }
    }

    /**
     * Удаление задачи
     * @param $id
     */
    public function getDelete($params)
    {
        if (isset($params['id']) && is_numeric($params['id'])) {
            $isDelete = $this->taskModel->delete($params['id']);
            if ($isDelete) {
                header('Location: /');
            }
        }
    }

    /**
     * Форма редактирования данных
     * @param $id
     */
    public function getUpdate($params)
    {
        if (isset($params['id']) && is_numeric($params['id'])) {
            $task = $this->taskModel->find($params['id']);
            $users = $this->userModel->getUsers();
            echo $this->render('task/update.html', ['task' => $task, 'users' => $users]);
        }
    }

    /**
     * Изменение данных о книге
     * @param $id
     */
    public function postUpdate($params, $post)
    {
        if (isset($params['id']) && is_numeric($params['id'])) {
            $updateParam = [];
            if (isset($post['description'])) {
                $updateParam['description'] = $post['description'];
            }
            if (isset($post['is_done'])) {
                $updateParam['is_done'] = $post['is_done'];
            }
            if (isset($post['assign_to']) && is_numeric($post['assign_to'])) {
                $updateParam['assigned_user_id'] = $post['assign_to'];
            }
            $isUpdate = $this->taskModel->update($params['id'], $updateParam);
            if ($isUpdate) {
                header('Location: /');
            }
        }
    }
    /**
     * Получение всех задач
     * @return array
     */
    public function getList()
    {
        $tasks = $this->taskModel->findAll();
        echo $this->render('task/list.html', ['tasks' => $tasks]);
    }
}

?>