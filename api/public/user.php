<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Model\Auth;
use App\Model\User;

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