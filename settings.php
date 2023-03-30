<?php
declare(strict_types=1);
require __DIR__ . '/vendor/autoload.php'; 

use DI\Container; // https://www.slimframework.com/docs/v4/concepts/di.html // Slim usa um contêiner de dependência opcional para preparar, gerenciar e injetar dependências de aplicativos. Slim oferece suporte a contêineres que implementam PSR-11 como PHP-DI .
use Slim\Factory\AppFactory;
use Slim\Middleware\MethodOverrideMiddleware; // O Method Overidding Middleware permite que você use o X-Http-Method-Overridecabeçalho da solicitação ou o parâmetro do corpo da solicitação _METHODpara substituir o método de uma solicitação de entrada. O middleware deve ser colocado após a adição do middleware de roteamento.

$container = new Container();
AppFactory::setContainer($container); // https://www.slimframework.com/docs/v4/concepts/di.html
$app = AppFactory::create();
if (!empty(BASE_PATH_SLIM)) {
    $app->setBasePath(BASE_PATH_SLIM);
}
$app->addRoutingMiddleware();

// https://www.slimframework.com/docs/v4/middleware/method-overriding.html
$methodOverrideMiddleware = new MethodOverrideMiddleware();
$app->add($methodOverrideMiddleware);
$app->addBodyParsingMiddleware(); // Importante para PUT 
                                  //O Method Overidding Middleware permite que você use o X-Http-Method-Override no cabeçalho da solicitação ou o parâmetro do corpo da solicitação _METHOD para substituir o método de uma solicitação de entrada. O middleware deve ser colocado após a adição do middleware de roteamento.

// Definir manipulador de erros personalizado
$customErrorHandler = function () use ($app) {
    $response = $app->getResponseFactory()->createResponse();   
    $response->getBody()->write(json_encode(['success' => false, 'error' => '404 - URI não encontrada!'])); 
    return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(404);   
};

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Adicionar Basic Authentication na rota de API
$app->add(new Tuupola\Middleware\HttpBasicAuthentication([    
    "path" => "/v1", 
    "users" => [
        API_USERNAME => API_PASSWORD,
    ]
]));

// Registra o manipulador para lidar apenas com HttpNotFoundException
// Alterar o primeiro parâmetro registra o manipulador de erros para outros tipos de exceções
$errorMiddleware->setErrorHandler(Slim\Exception\HttpNotFoundException::class, $customErrorHandler);