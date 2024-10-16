<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        //web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->prependToGroup('cors', \Illuminate\Http\Middleware\HandleCors::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e) {
            return match (true) {
                $e instanceof \Predis\Connection\ConnectionException => response()->json(['statusCode' => 500, 'message' => 'Connection refused [tcp://127.0.0.1:6379]'], \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR),
                $e instanceof \Illuminate\Http\Exceptions\PostTooLargeException => response()->json(['statusCode' => 500, 'message' => 'File too large, consider changing php.ini settings'], \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR),
                //$e instanceof \KeycloakGuard\Exceptions\TokenException => response()->json(['statusCode' => 401, 'message' => 'UNAUTHORIZED [KeycloakGuard]'], \Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED),
                $e instanceof \Illuminate\Auth\AuthenticationException => response()->json(['statusCode' => 401, 'message' => 'UNAUTHORIZED'], \Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED),
                $e instanceof NotFoundHttpException => response()->json(['statusCode' => 404, 'message' => 'Not Found'], \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND),
                default => response()->json(['statusCode' => 500, 'message' => 'Internal Server Error ' . $e], \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR),
            };
        });
        //Integration::handles($exceptions);
    })->create();
