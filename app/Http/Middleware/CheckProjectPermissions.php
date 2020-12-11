<?php

namespace App\Http\Middleware;

use Closure;

class CheckProjectPermissions
{
    public $currentProject;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next, $permissions)
    {
        $this->currentProject = \App\project::find(session('currentProject', null));
        if (is_null($this->currentProject)) {
            return redirect('/')->with('warning', 'There is currently no selected project');
        }
        $user = auth()->user();
        // if ($user->hasRole('sysadmin') or $user->isAbleTo($permission, $this->currentProject->team->name) or $user->owns($this->currentProject, 'owner')) {
        if ($user->isAbleTo([$permissions], $this->currentProject->team->name)) {
            $request->request->add(['currentProject' => $this->currentProject]);
        } else {
            return redirect('/')->with('error', 'You do not have the necessary rights to do access that function in this project');
        }

        return $next($request);
    }
}
