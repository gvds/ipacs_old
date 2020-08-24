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

    public function handle($request, Closure $next, $permission)
    {
        $this->currentProject = \App\project::find(session('currentProject', null));
        if (is_null($this->currentProject)) {
            return redirect('/')->with('warning', 'There is currently no selected project');
        }
        $user = auth()->user();
        if (!$user->isAbleTo($permission, $this->currentProject->team->name) and $user->id !== $this->currentProject->owner) {
            return redirect('/')->with('error', 'You do not have the necessary rights to do access that function in this project');
        }
        $request->request->add(['currentProject' => $this->currentProject]);

        return $next($request);
    }
}
