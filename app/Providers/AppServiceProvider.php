<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        $events->listen(BuildingMenu::class, function (BuildingMenu $event) {
            $user = auth()->user();
            

            if ($user->isAdmin() || $user->isConselho() || $user->isExtrator()) {
                $event->menu->add('Atos Normativos');
                $event->menu->add(
                    [
                        'text' => 'Publicar',
                        'url' => 'admin/documentos/publicar',
                        'icon' => 'upload'
                    ],
                    [
                        'text' => 'Publicar em Lote',
                        'url' => 'admin/documentos/publicar-lote',
                        'icon' => 'upload'
                    ]
                );
            }
            if ($user->isAdmin() || $user->isConselho()) {
                $event->menu->add(
                    [
                        'text' => 'Últimos Documentos',
                        'url' => 'admin/documentos',
                        'icon' => 'search'
                    ]
                );
                $event->menu->add('Pendências');
                $event->menu->add(
                    [
                        'text' => 'Pendências de Lote',
                        'url' => 'admin/documentos/pendentes',
                        'icon' => 'exclamation-circle',
                    ],
                    [
                        'text' => 'Pesquisar Pendências',
                        'url' => 'admin/documentos/pesquisar/status',
                        'icon' => 'search',
                    ],
                    [
                        'text' => 'Pesquisar Consultas',
                        'url' => 'admin/consultas',
                        'icon' => 'search',
                    ]

                );
            }
            if ($user->isAdmin() || $user->isAssessor()) {
                $event->menu->add('Unidades');
                $event->menu->add(
                    [
                        'text' => 'Conselhos',
                        'icon' => 'university',
                        'url' => 'admin/unidades',
                    ]
                );
            }
            if ($user->isAdmin()) {
                $event->menu->add(
                    [
                        'text' => 'Assessorias',
                        'icon' => 'unlock-alt',
                        'url' => 'admin/unidades/assessorias',
                    ]
                );
            }
            $event->menu->add('Glossário');
            $event->menu->add(
                [
                    'text' => 'Tipos de Documentos',
                    'url' => 'admin/tiposdocumento',
                    'icon' => 'file',
                ],
                [
                    'text' => 'Assuntos',
                    'url' => 'admin/assuntos',
                    'icon' => 'bookmark',
                ]
            );
            $event->menu->add(
                [
                    'text' => 'Perfil',
                    'icon' => 'address-card',
                    'url' => 'admin/usuarios'
                ]
            );
        });

        if (env('REDIRECT_HTTPS')) {
            \URL::forceScheme('https');
        }


    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
