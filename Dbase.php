<?php

/**
 * Created by PhpStorm.
 * User: Max
 * Date: 09.03.2018
 * Time: 23:32
 */

include "config.php";
class Dbase // класс для работы с MySQL
{
    const ALL_MESSAGES = "SELECT * FROM gb_messages ORDER BY id DESC";
    const ALL_MESSAGES_AND_COMMENTS =
       "SELECT 
        m.id AS id, m.username AS user, m.text AS messagetext, m.date AS messagedate, 
        c.id AS commentid, c.text AS comment, c.date AS cdate
        FROM gb_messages AS m 
        LEFT JOIN gb_comments AS c 
        ON m.id = c.messageid 
        ORDER BY m.id DESC, cdate ASC";

    private $mysql;
    private $result_set = array(); // результат запроса

    public function __construct()
    {
        global $db_config;
        $this->mysql = new mysqli($db_config['db_address'], $db_config['db_user'], $db_config['db_password'], $db_config['db_name'], $db_config['db_port']);
        $this->mysql->set_charset("utf8");
        $this->mysql->query("SET NAMES utf8");
    }

    public function NewMessage($name, $text)
    {
        $text = $this->mysql->real_escape_string(htmlspecialchars($text));
        $name = $this->mysql->real_escape_string(htmlspecialchars($name));
        $name = strlen($name) ? $name : "Guest";
        $this->mysql->query("INSERT INTO gb_messages (username,text) VALUES('$name','$text')");
        return $this->mysql->query("SELECT LAST_INSERT_ID()");
    } // Добавляет новое сообщение: имя пользователя и текст сообщения

    public function EditMessage($id, $newtext)
    {
        $newtext = $this->mysql->real_escape_string(htmlspecialchars($newtext));
        $this->mysql->query("UPDATE gb_messages SET `text`='$newtext',`date`=NOW() WHERE `id`='$id'");
    }

    public function DeleteMessage($id)
    {
        $id = $this->mysql->real_escape_string($id);
        $this->mysql->query("DELETE FROM gb_messages WHERE id='$id'");
        $this->mysql->query("DELETE FROM gb_comments WHERE messageid='$id'");
        return 1;
    } // удаление сообщения

    public function NewComment($id, $text)
    {
        $text = $this->mysql->real_escape_string(htmlspecialchars($text));
        if (strlen($text)) {
            $this->mysql->query("INSERT INTO gb_comments (messageid,text) VALUES('$id','$text')");
            return 1;//$this->mysql->query("SELECT LAST_INSERT_ID()");
        } else {
            return 0;
        }

    } // добавление комментария к сообщению: имя пользователя и текст комментария

    public function DeleteComment($id)
    {
        return $this->mysql->query("DELETE FROM gb_comments WHERE id='$id'");

    }

    /**
     * @return array
     */
    public function getResultSet()
    {
        return $this->result_set;
    }



    public function getAllMessages()
    {
        /*  Таблицы базы данных:
          `gb_messages`(`id`,`username`,`text`,`date`)
          `gb_comments`(`id`,`messageid`,`text`,`date`)*/

//        $result = $this->mysql->query(self::ALL_MESSAGES);
        $result = $this->mysql->query(self::ALL_MESSAGES_AND_COMMENTS);

        if ($result !== false) { // был запрос
            $i =0;
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $this->result_set[$i] =
                    array( 'messageid' => $row['id'],
                        'username' => $row['user'],
                        'text' => $row['messagetext'],
                        'messagedate' => $row['messagedate'],
                        'commentid' => $row['commentid'],
                        'comment' => $row['comment'],
                        'commentdate' => $row['cdate']);
                $i++;
            } // доставлять след. строку запроса, пока она там есть
            $result->free_result();
        } else {
            echo "что-то пошло не так";
        } //ошибка запроса
        $this->mysql->close();
        return $this->result_set;
    } // возвращает все сообщения в ленте
}
