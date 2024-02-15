# Auth

Classes de autenticação e criação de tokens jwt genéricos

## Classe Session

A classe Session Funciona a partir da sessão do php e é regerado sempre que a sessão é desligada, além disso pode durar uma sessão ou um tempo pré-determinado.

### Configuração da classe Session

  ```php
  <?php
   // .../index.php
  require __DIR__ . '/vendor/autoload.php';
  /***/
  use Pkit\Auth\Session;
  /***/
  # pode ser configurado pelo .env 'SESSION_EXPIRES' e 'SESSION_PATH' respectivamente
  Session::config(
    /*tempo em segundos*/, 
    /*caminho para a sessão(opcional)*/
  );//opcional
  /***
  ```

### Uso da classe Session

  ```php
  use Pkit\Auth\Session;
  /***/
  Session::login(/*payload: array*/);
  /***/
  $logged = Session::logged(); //: bool
  /***/
  $login = Session::getPayload(); //: bool
  /***/
  Session::logout()//: bool
  ```

## Classe Jwt

O jwt é token criptografado que é enviado para o cliente e então validado no retorno, por padrão é enviado pelo cabeçalho 'Authorization' com o sufixo `Bearer`, além disso pode ser valido pra sempre ou como recomendado, ter um tempo de expiração.

### Configuração da classe Jwt

  ```php
  use Pkit\Auth\Jwt;

  # pode ser configurado pelo .env 'JWT_KEY', 'JWT_EXPIRES' e 'JWT_ALG' respectivamente
  Jwt::config(
    /*chave para criptografia*/, 
    /*tempo de expiração em segundos #opcional*/, 
    /*algoritmo de criptografia*/
  );
  ```

### Uso da classe Jwt

  ```php
  $token = Jwt::tokenize(/*payload:array*/)//:string;
  /***/
  $valid = Jwt::validate(/*token:string*/);//:boolean
  /***/
  $payload = Jwt::getPayload(/*token:string*/)//:object
  /***/
  $tokenBearer = Jwt::createBearer(/*token:string*/)//:string Authorization
  /***/
  $token = Jwt::parseBearer(/*authorization:string*/)//:string;

  ```
