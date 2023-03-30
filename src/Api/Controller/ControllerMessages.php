<?php
namespace NiloBlack\NotificacoesSlack\Api\Controller;

use NiloBlack\NotificacoesSlack\Api\Model\ModelMessages as Model;
use Psr\Container\ContainerInterface;

Class ControllerMessages extends Controller {
    private $_model; 

    public function __construct(ContainerInterface $conteiner)
    {
        parent::__construct($conteiner);
        $this->_model = new Model();
    }

    public function ping($request, $response, $args) {        
        $response->getBody()->write(json_encode(['success' => true, 'message' => 'Ping OK!']));
        return $response
                    ->withHeader('Content-type', 'application/json')
                    ->withStatus(200);
    }

    public function sendFifo($request, $response, $args) {
        $perSecond = $request->getParsedBody()["per_second"] ?? 5;

        if ($perSecond < 1 || $perSecond > 10) {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Desculpe, não é possível continuar! Verifique se os campos estão preenchidos corretamente.', 'errors' => array('per_second' => 'Total de mensagens por segundo fora do intervalo permitido [1-10]!')]));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(400);
        }

        $messages = $this->_model->findAll(['sent_at' => 'IS NULL']);
        $successes = [];
        $errors = [];

        if ($messages) {            
            foreach ($messages as $message) {
                try {
                    $this->postRESTAPI($message->url,
                                       json_encode(['username' => $message->username, 
                                                    'channel' => $message->channel, 
                                                    'text' => $message->text]));
                    $this->_model->setId($message->id);
                    $this->_model->updateSentAt();

                    array_push(
                        $successes,
                        array('id' => $message->id, 'username' => $message->username, 'channel' => $message->channel)
                    );
                } catch (\Exception $e){                    
                    array_push(
                        $errors,
                        array('id' => $message->id, 'username' => $message->username, 'channel' => $message->channel)
                    );
                }

                usleep(1000000/$perSecond);
            }

            $response->getBody()->write(json_encode(['success' => true, 'successes' => $successes, 'errors' => $errors]));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(['success' => false, 'message' => 'Não há o que ser enviado!']));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(204);
        }
    }

    public function addFifo($request, $response, $args) {
        try{
            $this->_model->setUrl($request->getParsedBody()["url"]);
            $this->_model->setChannel($request->getParsedBody()["channel"]);
            $this->_model->setUsername($request->getParsedBody()["username"]);
            $this->_model->setIcon_url($request->getParsedBody()["icon_url"]);
            $this->_model->setText($request->getParsedBody()["text"]);            
            $this->_model->setSchedule_for($request->getParsedBody()["schedule_for"]);
            $this->_model->create();

            $data = $this->_model->create();            
            
            $response->getBody()->write(json_encode(['success' => true, 'message'=> 'Mensagem cadastrada com sucesso!', 'data' => $data]));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(201);
        } catch (\Exception $e){
            if (sizeof($this->_model->getErrorsValidation()) > 0) {                                
                $errors = $this->_model->getErrorsValidation();
                $response->getBody()->write(json_encode(['success' => false, 'message'=> $e->getMessage(), 'errors' => $errors]));
            } else {
                if ($e->getPrevious() != null) {
                    $response->getBody()->write(json_encode(['success' => false, 'message'=> $e->getMessage(), 'detail' => $e->getPrevious()->getMessage()]));
                } else {
                    $response->getBody()->write(json_encode(['success' => false, 'message'=> $e->getMessage()]));
                }                
            }

            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus($e->getCode() ?? 500);
        }
    }
}
