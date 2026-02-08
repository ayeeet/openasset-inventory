<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleWriteAccess
{
    /**
     * Write action route name suffixes (create, store, edit, update, destroy).
     */
    protected array $writeSuffixes = ['.create', '.store', '.edit', '.update', '.destroy'];

    /**
     * Handle an incoming request.
     * Employees with module access have read-only access; block them from write actions.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            return $next($request);
        }

        if ($user->hasWriteAccess()) {
            return $next($request);
        }

        $routeName = $request->route()?->getName() ?? '';
        foreach ($this->writeSuffixes as $suffix) {
            if (str_ends_with($routeName, $suffix)) {
                abort(403, 'You have read-only access to this module.');
            }
        }

        return $next($request);
    }
}
