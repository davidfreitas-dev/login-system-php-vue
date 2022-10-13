<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Selective\BasePath\BasePathMiddleware;
use Slim\Factory\AppFactory;
use App\DB\Database;
use App\Model\User;
use App\Model\Auth;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');

$dotenv->load();

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$app->addRoutingMiddleware();

$app->add(new BasePathMiddleware($app));

$app->addErrorMiddleware(true, true, true);

$app->add(new Tuupola\Middleware\JwtAuthentication([
    "header" => "X-Token",
    "regexp" => "/(.*)/",
    "path" => "/",
    "ignore" => ["/login", "/register", "/forgot", "/forgot/reset"],
    "secret" => $_ENV['JWT_SECRET_KEY'],
    "algorithm" => "HS256",
    "error" => function ($response, $arguments) {
        $data["status"] = "error";
        $data["message"] = $arguments["message"];

        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        return $response->withHeader('content-type', 'application/json');
    }
]));

$app->post('/login', function (Request $request, Response $response) {

    $data = $request->getParsedBody();

    $result = Auth::login($data['deslogin'], $data['despassword']);

    $response->getBody()->write($result);

    return $response->withHeader('content-type', 'application/json');

});

$app->post('/register', function (Request $request, Response $response, array $args) {
 
    $data = $request->getParsedBody();

    $result = User::add($data);

    $response->getBody()->write($result);

    return $response->withHeader('content-type', 'application/json');
 
});

$app->post('/forgot', function (Request $request, Response $response, array $args) {
 
    $data = $request->getParsedBody();

    $result = User::getForgot($data['desemail']);

    $response->getBody()->write($result);

    return $response->withHeader('content-type', 'application/json');
 
});

$app->post('/forgot/token', function (Request $request, Response $response, array $args) {
 
    $data = $request->getParsedBody();

    $result = User::validForgotDecrypt($data['code']);

    $response->getBody()->write(json_encode($result));

    return $response->withHeader('content-type', 'application/json');
 
});

$app->post('/forgot/reset', function (Request $request, Response $response, array $args) {
 
    $data = $request->getParsedBody();

    $forgot = User::validForgotDecrypt($data['code']);

    if (is_array($forgot)) {

        User::setForgotUsed($forgot['idrecovery']);
    
        $password = User::getPasswordHash($data['despassword']);
    
        $result = User::setPassword($password, $forgot['iduser']);

    } else {

        $result = $forgot;

    }    

    $response->getBody()->write($result);

    return $response->withHeader('content-type', 'application/json');
 
});

$app->get('/users', function (Request $request, Response $response) {

    $result = User::all();

    $response->getBody()->write($result);

    return $response->withHeader('content-type', 'application/json');

});

$app->get('/users/{id}', function (Request $request, Response $response, array $args) {

    $id = $request->getAttribute('id');

    $result = User::get($id);

    $response->getBody()->write($result);

    return $response->withHeader('content-type', 'application/json');

});

$app->put('/users/update/{id}', function (Request $request, Response $response, array $args) {

    $id = $request->getAttribute('id');

    $data = $request->getParsedBody();

    $result = User::update($id, $data);

    $response->getBody()->write($result);

    return $response->withHeader('content-type', 'application/json');

});

$app->delete('/users/delete/{id}', function (Request $request, Response $response, array $args) {

    $id = $args['id'];

    $result = User::delete($id);

    $response->getBody()->write($result);

    return $response->withHeader('content-type', 'application/json');

});

$app->run();