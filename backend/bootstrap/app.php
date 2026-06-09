<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        channels: __DIR__.'/../routes/channels.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();

        $middleware->prepend(\App\Http\Middleware\FixBaseUrl::class);

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'audit' => \App\Http\Middleware\AuditMiddleware::class,
            'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        ]);

        $middleware->web(replace: [
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class => \App\Http\Middleware\VerifyCsrfToken::class,
        ]);

        // Configure authentication redirect
        $middleware->redirectGuestsTo(fn ($request) => route('login'));
        $middleware->redirectUsersTo(fn ($request) => route('dashboard.redirect'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function ($request) {
            return $request->expectsJson() || $request->is('api/*');
        });

        $exceptions->render(function (Throwable $e, $request) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException
                && $e->getStatusCode() === 403
                && (str_starts_with($request->path(), 'email/verify')
                    || str_starts_with($request->path(), 'api/v1/auth/verify-email'))) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Le lien de vérification est invalide ou a expiré. Veuillez renvoyer un nouvel email.',
                    ], 403);
                }

                return redirect()->route('verification.notice')
                    ->with('error', 'Le lien de vérification est invalide ou a expiré. Veuillez renvoyer un nouvel email.');
            }

            if ($request->expectsJson() || $request->is('api/*')) {
                $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
                if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    $status = 401;
                }
                if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                    $status = 403;
                }
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    $status = 404;
                }
                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erreur de validation',
                        'errors' => $e->errors(),
                    ], 422);
                }

                if ($e instanceof \Illuminate\Database\QueryException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erreur base de données. Veuillez réessayer.',
                    ], 422);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Erreur serveur',
                ], $status);
            }
        });
    })->create();
