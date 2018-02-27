<?php

class UserController
{
    private $userModel = null;
    private $twig;

    function __construct($db, $twig)
    {
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
            echo $this->twig->render($template, ['params' => $params]);
            //include $fileTemplate;
            return ob_get_clean();
        }
    }
    public function getForm()
    {
        echo $this->render('user/register.html');
    }
    public function register()
    {
        $this->userModel->register();
    }
    public function log_in()
    {
        $this->userModel->log_in();
    }
    public function logout()
    {
        $this->userModel->logout();
    }
}

?>