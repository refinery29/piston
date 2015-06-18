# Piston
Micro Framework for APIs


## Routing

### Controller Based Routing

```
$application = new Application();

$application->addRoute(Route::get('jedi/{id}', 'JediController::useTheForce'));

```

### Closure Based Routing

```
$application = new Application();

$application->addRoute(Route::get('jedi/{id}', function(Request $request, Response $response)
{
    //do some cool stuff here
    return $response;
}));

```