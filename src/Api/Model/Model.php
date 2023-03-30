<?php
namespace NiloBlack\NotificacoesSlack\Api\Model;

use NiloBlack\NotificacoesSlack\Api\DBConfig\DBConfig;
use NiloBlack\NotificacoesSlack\Api\Enum\ValidationType;
use \PDO;

// Essa classe é abstract, logo, não é possível criar um objeto dela, só extender.
abstract class Model {
    protected $connection;        
    protected array $error_validation = []; 

    function getErrorsValidation() {
        return $this->error_validation;
    }

    /**
    * Validar Campos
    * Criado em 30/03/2023 por niloblack
    * @param [string] $field
    * @param [mixed] $value
    * @param [bool] $required
    * @param [string] $type
    * @param [int] $min
    * @param [int] $max
    * @param [string] $format
    * @param [array] $options
    * @return Response
    */
    function validateField(string $field, $value, bool $required, int $min = 0, int $max = 0,  string $type = 'STRING', string $format = '', array $options = []) {        
        if ($required) {
            if ($type == ValidationType::VT_ARRAY && is_array($value)) {
                if (sizeof($value) == 0) {
                    $this->error_validation[$field] = 'Preenchimento obrigatório';
                }
            } else if ($type == ValidationType::VT_ARRAY && !is_array($value)) {
                $this->error_validation[$field] = 'Valor inválido para o campo [Array]';
            }

            if ($type != ValidationType::VT_ARRAY && trim($value) == '') {
                $this->error_validation[$field] = 'Preenchimento obrigatório';           
            }
        }

        if (is_string($value) && trim($value) != '' && (!empty($min) || !empty($max))) {
            if (strlen($value) < $min || strlen($value) > $max) {
                $this->error_validation[$field] = "Tamanho inválido para o campo [{$min}-{$max}]";
            }    
        }
        
        if ($type == ValidationType::VT_NUMERIC) {
            if (!is_numeric($value)) {
                $this->error_validation[$field] = 'Valor inválido para o campo';
            } elseif (trim($value) == '' && ($value < $min || $value > $max)) {
                $this->error_validation[$field] = "Valor inválido para o campo [{$min}-{$max}]";
            }  
        }

        if ($type == ValidationType::VT_BOOL && !is_bool($value)) {            
            $this->error_validation[$field] = 'Valor inválido para o campo';
        }         

        // Validar tipo
        if ((is_string($value) && trim($value) != '') || (is_array($options) && sizeof($options) > 0)){
            if ($type == ValidationType::VT_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $this->error_validation[$field] = 'Valor inválido para o campo';
            } 

            if ($type == ValidationType::VT_DATE) {
                $format_local = empty($format) ? 'Y-m-d H:i:s' : $format;
                $d = \DateTime::createFromFormat($format_local, $value);
                if (!($d && $d->format($format_local) == $value)) {
                    $this->error_validation[$field] = 'Formato inválido para o campo';
                }
            }

            if ($type == ValidationType::VT_ENUM && sizeof($options) > 0) {
                if (!in_array($value, $options)) {
                    $this->error_validation[$field] = 'Valor inválido para o campo';
                }
            }
        }

        return $value;
    }    

    protected function validation() {
        if (sizeof($this->error_validation) > 0) {            
            throw new \InvalidArgumentException("Desculpe, não é possível continuar! Verifique se os campos estão preenchidos corretamente.", 400);
        }
    }

    public function create() {
        $this->validation();
    }

    public function update() {
        $this->validation();
    }    

    public function __construct()
    {
        $this->connection = self::Connect(new DBConfig());          
    }

    public function __destruct()
    {
        $this->connection = null;        
    }

    static function Connect(DBConfig $config) {
        return new \PDO("mysql:host=" . $config->getHost() . ";dbname=" . $config->getDatabase(), 
                        $config->getUserName(), 
                        $config->getPassword(), 
                        [1002 => 'SET NAMES UTF8', PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }
}