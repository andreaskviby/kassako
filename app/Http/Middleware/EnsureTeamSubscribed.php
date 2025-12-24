<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeamSubscribed
{
    public function handle(Request $request, Closure $next): Response
    {
        $team = $request->user()?->currentTeam;

        if (!$team) {
            return redirect()->route('login');
        }

        if ($team->onTrial() || $team->subscribed('default')) {
            return $next($request);
        }

        return redirect()->route('billing')
            ->with('error', 'Du behöver en aktiv prenumeration för att fortsätta.');
    }
}
