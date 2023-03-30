<?php
use Slim\Routing\RouteCollectorProxy;

$app->group('/v1', function(RouteCollectorProxy $group) {
    $group->get('/ping', '\NiloBlack\NotificacoesSlack\Api\Controller\ControllerMessages:ping');
    $group->group('/messages', function(RouteCollectorProxy $group) {
        $group->put('',  '\NiloBlack\NotificacoesSlack\Api\Controller\ControllerMessages:sendFifo')->setName('sendFifo');
        $group->post('', '\NiloBlack\NotificacoesSlack\Api\Controller\ControllerMessages:addFifo')->setName('addFifo');
    });
});