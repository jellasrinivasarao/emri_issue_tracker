<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;



use App\Interfaces\IssueRepositoryInterface;
use App\Repositories\IssueRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(IssueRepositoryInterface::class,IssueRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::if('routeCan', function (string $privilegeCode) {
            $routeName = Route::currentRouteName();
            $user = Auth::user();

            if (! $user || ! $routeName) {
                return false;
            }

            return $user->hasPrivilegeOnRoute($routeName, $privilegeCode);
        });

        View::composer('*', function ($view) {
            $routeName = Route::currentRouteName();
            $user = Auth::user();

            if (! $user || ! $routeName || $view->offsetExists('permissions')) {
                return;
            }

            $active = collect(['view', 'create', 'edit', 'delete', 'export', 'activate', 'deactivate'])
                ->mapWithKeys(fn ($code) => [$code => $user->hasPrivilegeOnRoute($routeName, $code)]);

            $view->with('permissions', $active->all());
        });
    }
}