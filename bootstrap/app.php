<?php

use App\Exceptions\Auth\InvalidLoginCredentials;
use App\Exceptions\TaskNotFound;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (InvalidLoginCredentials $e) {
            return response()->json([
                'message' => 'Invalid credentials.'
            ], 401);
        })
        ->render(function (TaskNotFound $e) {
            return response()->json([
                'message' => 'Task not found.'
            ], 404);
        });
    })->create();
