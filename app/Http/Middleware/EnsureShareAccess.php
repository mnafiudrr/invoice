<?php

namespace App\Http\Middleware;

use App\Models\ShareLink;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureShareAccess
{
    /**
     * Allow the request only when the client has unlocked this share link in the session.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $shareLink = $request->route('shareLink');

        if (! $shareLink instanceof ShareLink || $shareLink->isExpired()) {
            abort(404);
        }

        if (session("share_access.{$shareLink->token}") !== true) {
            return redirect()->route('shares.password', $shareLink);
        }

        return $next($request);
    }
}
