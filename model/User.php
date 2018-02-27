<?php
class User {
    public $db = null;
    private $login;
    private $password;
    public $check;
    public $cookie_expiration_time;

    function __construct($db)
    {
        $this->db = $db;
        $login = trim(htmlspecialchars(stripslashes($_POST['login'])));
        $this->login = $login;
        $this->password = password_hash(trim(htmlspecialchars(stripslashes($_POST['password']))), PASSWORD_DEFAULT);

        $stmt = $db->prepare("SELECT `login`, `password` FROM user WHERE `login` = ?");
        $stmt->execute([$login]);
        $this->check = $stmt->fetch();
        $this->cookie_expiration_time = time() + 60 * 15;
    }


    function getUsers()
    {
        $users = [];
        foreach ($this->db->query("SELECT `id`, `login` FROM `user`") as $user)
        {
            $users[] = [$user['id'], $user['login']];
        }
        return $users;
    }

    function register()
    {
        if ($this->check !== false) {
            echo 'Такой пользователь уже есть!';
        } else {
            $add = $this->db->prepare("INSERT INTO `user` (login, password) VALUES (?, ?)");
            $add->execute([$this->login, $this->password]);
            setcookie("logged_in", $this->login, $this->cookie_expiration_time);
            header("Location: /");
        }
    }

    function log_in()
    {
        if ($this->check && password_verify($_POST['password'], $this->check['password'])) {
            setcookie("logged_in", $this->login, $this->cookie_expiration_time);
            header("Location: /");
        } else {
            echo 'Введены неверные данные. Попробуйте еще раз.';
        }
    }
    function logout()
    {
        setcookie("logged_in", '', -1);
        header("Location: /");
    }
}

?>

