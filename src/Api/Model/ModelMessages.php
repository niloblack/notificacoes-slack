<?php
namespace NiloBlack\NotificacoesSlack\Api\Model;

use NiloBlack\NotificacoesSlack\Api\Enum\ValidationType;

Class ModelMessages extends Model {
    private $id; 
    private $url; 
    private $channel;
    private $username; 
    private $icon_url; 
    private $text; 
    private $sent_at; 
    private $schedule_for; 

    function getId() {
        return $this->id;
    }

    function getUrl() {
        return $this->url;
    }

    function getChannel() {
        return $this->channel;
    }

    function getUsername() {
        return $this->username;
    }

    function getIcon_url() {
        return $this->icon_url;
    }

    function getText() {
        return $this->text;
    }

    function getSent_at() {
        return $this->sent_at;
    }

    function getSchedule_for() {
        return $this->schedule_for;
    }

    function setId($id) {
        if (!$id && !is_string($id)) {
            throw new \InvalidArgumentException("id é requirido!", 400);
        }

        $this->id = $id;
    }

    function setUrl($url) {
        $this->url = $url;
    }

    function setChannel($channel) {
        $this->channel = $channel;
    }

    function setUsername($username) {
        $this->username = $username;
    }

    function setIcon_url($icon_url) {
        $this->icon_url = $icon_url;
    }

    function setText($text) {
        $this->text = $this->validateField('text', $text, true);
    }

    function setSent_at($sent_at) {
        $this->sent_at = $sent_at;
    }

    function setSchedule_for($schedule_for) {
        $this->schedule_for = $schedule_for;
    }

    public function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    public function findAll($search = []) {
        $sql = " SELECT * ".
                " FROM tb_messages m ".
                " WHERE TRUE ";

        if (!empty($search['sent_at'])) {
            $sql .= " AND m.sent_at ".$search['sent_at'];
        }
        
        $sql .= " ORDER BY m.created_at ASC ";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public function create() {
        $this->url = $this->validateField('url', $this->getUrl(), true, 12, 255, ValidationType::VT_STRING);
        $this->channel = $this->validateField('channel', $this->getChannel(), false, 3, 80);            
        $this->username = $this->validateField('username', $this->getUsername(), false, 3, 80, ValidationType::VT_STRING);
    
        parent::create();

        $sql = " INSERT INTO tb_messages( ";
        $sql .= " url, "; 
        $sql .= " channel, "; 
        $sql .= " username, "; 
        $sql .= " icon_url, "; 
        $sql .= " text ";         
        if (isset($this->schedule_for) && !empty($this->schedule_for)) :
          $sql .= " , schedule_for "; 
        endif;
        $sql .= ") VALUES ( ";
        $sql .= " :url, "; 
        $sql .= " :channel, "; 
        $sql .= " :username, "; 
        $sql .= " :icon_url, "; 
        $sql .= " :text "; 
        if (isset($this->schedule_for) && !empty($this->schedule_for)) :
          $sql .= " , :schedule_for "; 
        endif;
        $sql .= ") "; 
        $stmt = $this->connection->prepare($sql);  
        $stmt->bindValue('url', empty($this->getUrl()) ? \PDO::PARAM_NULL : $this->getUrl());
        $stmt->bindValue('channel', empty($this->getChannel()) ? \PDO::PARAM_NULL : $this->getChannel());
        $stmt->bindValue('username', empty($this->getUsername()) ? \PDO::PARAM_NULL : $this->getUsername());
        $stmt->bindValue('icon_url', $this->getIcon_url());
        $stmt->bindValue('text', $this->getText(), \PDO::PARAM_LOB);
        if (isset($this->schedule_for) && !empty($this->schedule_for)) :
          $stmt->bindValue('schedule_for', $this->getSchedule_for());
        endif;

        if (!$stmt->execute()){
            throw new \Exception("Comando não executado!", 500);
        }
    }

    public function updateSentAt() {
        parent::update();

        $sql = " UPDATE tb_messages SET ";         
        $sql .= " sent_at = CURRENT_TIMESTAMP ";        
        $sql .= " WHERE id = :id ";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('id', $this->getId()); 

        if (!$stmt->execute()){ 
            throw new \InvalidArgumentException("Comando não executado!", 500); 
        } 
    }
}
