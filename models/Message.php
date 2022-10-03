<?php
require_once "db.php";

class Message
{
    private $id=null;
    private $id_sender=null;
    // type = 0 => chat all, type = 1 => personal notification
    private $type=null;
    const TYPE_CHAT_ALL = 0;
    const TYPE_PERSONAL_NOTIFICATION = 1;
    private $content=null;
    private $date=null;

    public function __construct()
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIdSender()
    {
        return $this->id_sender;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setContent($content): bool
    {
        if (strlen($content) > 0 && (strlen($content) <= 255 || $this->type == self::TYPE_PERSONAL_NOTIFICATION)) {
            $this->content = htmlspecialchars($content);
            return true;
        }
        return false;
    }

    public function setIdSender($id_sender): bool
    {
        $this->id_sender = $id_sender;
        return true;
    }

    public function setType($type): bool
    {
        if($type == Message::TYPE_CHAT_ALL || $type == Message::TYPE_PERSONAL_NOTIFICATION){
            $this->type = $type;
            return true;
        }
        return false;
    }

    static function pushMessage($id_sender, $type, $content): bool
    {
        $message = new Message();
        return $message->setIdSender($id_sender) &&
            $message->setType($type) &&
            $message->setContent($content) &&
            $message->create();
    }

    public function create(): bool
    {
        $res = MyDB::query("INSERT INTO messages (id_sender, type, content) VALUES (?, ?, ?)", [$this->id_sender, $this->type, $this->content]);

        return true;
    }

    static function getMessagesForUser($id_user): array
    {
        $res = MyDB::query("SELECT * FROM messages WHERE id_sender = ? OR type = ? ORDER BY date", [$id_user, Message::TYPE_CHAT_ALL]);
        $messages = [];
        while ($row = $res->fetch_assoc()) {
            $message = new Message();
            $message->id = $row["id"];
            $message->id_sender = $row["id_sender"];
            $message->type = $row["type"];
            $message->content = $row["content"];
            $message->date = $row["date"];
            $messages[] = $message;
        }
        return $messages;
    }

}