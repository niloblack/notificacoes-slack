<?php
namespace NiloBlack\NotificacoesSlack\Api\Controller;

use Psr\Container\ContainerInterface;

abstract class Controller {
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    public function __get($property) {        
        if($this->container->get($property)){            
            return $this->container->get($property);
        }
    }

    public function getRESTAPI($service_url, &$respREST){        
        try {
            $ch = curl_init($service_url);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                        
            $resp = curl_exec($ch);        
            $respREST = json_decode($resp, true);
            
            return curl_getinfo($ch, CURLINFO_HTTP_CODE);
        } finally {
            curl_close($ch);  
        }        
    }
    
    public function postRESTAPI($service_url, $body, &$respREST = null){
        try {
            $ch = curl_init($service_url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            // curl_setopt($ch, CURLOPT_CAINFO, 'local/crt.crt');            
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($body))
            );    
            $resp = curl_exec($ch);
            $respREST = json_decode($resp, true);
            
            return curl_getinfo($ch, CURLINFO_HTTP_CODE);
        } finally {
            curl_close($ch);
        }
    }
}