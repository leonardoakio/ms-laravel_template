[JWT Tymon Designs](https://github.com/tymondesigns/jwt-auth?tab=readme-ov-file)

| jwt-auth version | Laravel version(s) |
|---|---|
| 1.x  | 6 / 7 / 8 |
| 2.x  | 9 / 10 / 11 |

OBS: O pacote baixado automaticamente é o mais recente, caso tenha uma versão antiga do Laravel, **escolher a versão do pacote 1x**

## Etapas:

1. Instalação de lib através do comando
```shell
composer require tymon/jwt-auth
```
2. Publicar o pacote com o _publish_ e escolher o Provider `Tymon\JWTAuth\Providers\LaravelServiceProvider`
```shell
php artisan vendor:publish
```
3. No Model `User`, implemente o uso da lib JWT
```php
class User extends Authenticatable implements JWTSubject
```
4. Crie os métodos obrigatórios pela interface
```php
public function getJWTIdentifier() {
    return $this->getKey();
}

public function getJWTCustomClaims() {
    return [];
}
```
5. No arquivo de configuração `config/auth.php`, alterar o `guard` para as rotas de `api`
```php
'defaults' => [
    'guard' => env('AUTH_GUARD', 'api'),
    'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
]
```
6. Ainda no arquivo, adicionar o `guard` de `api` definido no passo anterior onde terá o driver `jwt`
```php
'guards' => [
    ...
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],
],
```
**_OBS:_** Veja que o provider `user` encontramos mais embaixo no arquivo onde ele define que `users` seja equivalente ao `'model' => env('AUTH_MODEL', App\Models\User::class)`
 ```php
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => env('AUTH_MODEL', App\Models\User::class),
    ],
]
```
7. Executar um comando para a criação da `secret` do `jwt`
```shell
php artisan jwt:secret
```
8. Criamos um usuário fake para teste no **Tinker** por dentro do container ou utilizar o usuário default `test@example.com` e `password`
```shell
php artisan tinker 
User::factory()->create()
```
9. Criamos uma rota de login para validar se o token jwt será gerado
```php
Route::group(["prefix" => "/v1"], function () {
    Route::group(["prefix" => "auth"], function () {
        Route::post("/login", function (Request $request) {
            $credentials = $request->only(["email", "password"]);

            if (!$token = auth()->attempt($credentials)) {
                return response()->json([
                    'error' => 'Unauthorized'
                ], 401);
            }

            return response()->json([
                'data' => [
                    "token" => $token,
                    "token_type" => 'bearer',
                    "expires_in" => auth()->factory()->getTTL() * 60
                ]
            ]);
        });
    });
});
```
Veja como ficará a requisicao completa
```shell
curl --location 'http://localhost:8083/api/v1/auth/login' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--data-raw '{ 
    "email": "test@example.com",
    "password": "password"
}'
```
10. Agora para testarmos a validade do token, criaremos uma **rota protegida** por token jwt que retornará a Model `User`
```php
Route::middleware('api')->get('/user', function (Request $request) {
    return $request->user();
});
```
Passaremos no header um campo `Authorization` e no valor será um prefixo `Bearer` com o restante do token 
```shell
curl --location 'http://localhost:8083/api/user' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwODMvYXBpL3YxL2F1dGgvbG9naW4iLCJpYXQiOjE3Mzc0MzY3NzQsImV4cCI6MTczNzQ0MDM3NCwibmJmIjoxNzM3NDM2Nzc0LCJqdGkiOiJZd2ZUbTI4dkVyblJtdFJQIiwic3ViIjoiMSIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.hJj-oRLg4ERZWgVejFwWSDXtMclEMNDr6QaUoGXORkI' \
--data ''
```
