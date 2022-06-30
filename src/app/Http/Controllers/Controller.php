<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Stops the application and shows a permission error if
     * the application is in demo mode.
     */
    protected function preventAccessInDemoMode()
    {
        if (config('app.env') === 'demo') {
            $this->showPermissionError();
        }
    }

    /**
     * On a permission error redirect to home and display.
     * the error as a notification.
     *
     * @return never
     */
    protected function showPermissionError()
    {
        $message = request()->wantsJson() ? trans('errors.permissionJson') : trans('errors.permission');

        throw new NotifyException($message, '/', 403);
    }

    /**
     * Checks that the current user has the given permission otherwise throw an exception.
     */
    protected function checkPermission(string $permission): void
    {
        if (!auth()->user() || !auth()->user()->can($permission)) {
            $this->showPermissionError();
        }
    }
}
