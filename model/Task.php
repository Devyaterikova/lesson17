<?php

class Task {

    private $db = null;
    function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Добавление задачи
     * @param $params array
     * @return mixed
     */
    function add($params)
    {
        $username = $this->db->quote($_COOKIE['logged_in']);
        $authorId = $this->db->query("SELECT `id` FROM `user` WHERE login=$username")->fetch()['id'];

        $sth = $this->db->prepare(
            'INSERT INTO task (description, user_id, assigned_user_id, is_done, date_added)'
            .' VALUES (:description, :user_id, :assigned_user_id, false, CURRENT_TIMESTAMP)'
        );
        $sth->bindValue(':description', $params['description'], PDO::PARAM_STR);
        $sth->bindValue(':user_id', $authorId, PDO::PARAM_INT);
        $sth->bindValue(':assigned_user_id', $authorId, PDO::PARAM_INT);
        return $sth->execute();
    }

    /**
     * Удаление задачи
     * @param $id int
     * @return mixed
     */
    function delete($id)
    {
        $sth = $this->db->prepare('DELETE FROM `task` WHERE id=:id');
        $sth->bindValue(':id', $id, PDO::PARAM_INT);
        return $sth->execute();
    }

    /**
     * Изменение задачи
     * @param $id int
     * @param $params array
     * @return mixed
     */
    function update($id, $params)
    {
        if (count($params) == 0) {
            return false;
        }
        $update = [];
        foreach ($params as $param => $value) {
            $update[] = $param.'`=:'.$param;
        }
        $sth = $this->db->prepare('UPDATE `task` SET `'.implode(', `', $update).' WHERE `id`=:id');
        if (isset($params['description'])) {
            $sth->bindValue(':description', $params['description'], PDO::PARAM_STR);
        }
        if ($params['is_done'] === 'true') {
            $sth->bindValue(':is_done', 1, PDO::PARAM_INT);
        } elseif ($params['is_done'] === 'false') {
            $sth->bindValue(':is_done', 0, PDO::PARAM_INT);
        }
        $sth->bindValue(':assigned_user_id', $params['assigned_user_id'], PDO::PARAM_INT);
        $sth->bindValue(':id', $id, PDO::PARAM_INT);
        return $sth->execute();
    }

    /**
     * Получение всех задач
     * @return array
     */
    public function findAll()
    {
        $sth = $this->db->prepare('SELECT t.id as task_id, t.description as description, u.id as author_id, u.login as author_name, au.id as assigned_user_id, au.login as assigned_user_name, t.is_done as is_done, t.date_added as date_added FROM task t INNER JOIN user u ON u.id=t.user_id INNER JOIN user au ON t.assigned_user_id=au.id');
        if ($sth->execute()) {
            return $sth->fetchAll();
        }
        return false;
    }
    /**
     * Получение одной книги
     * @param $id int
     * @return array
     */
    public function find($id)
    {
        $sth = $this->db->prepare('SELECT t.id as id, t.description as description, au.id as assigned_user_id, t.is_done as is_done
FROM task t INNER JOIN user au ON t.assigned_user_id=au.id WHERE t.id=:id');
        $sth->bindValue(':id', $id, PDO::PARAM_INT);
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
}

?>