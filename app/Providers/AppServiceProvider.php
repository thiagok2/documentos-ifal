<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Pagination\Paginator;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        Paginator::useBootstrapFive();

        $events->listen(BuildingMenu::class, function (BuildingMenu $event) {
            $user = auth()->user();
            
            if ($user->isAdmin() || $user->isConselho() || $user->isExtrator()) {
                $event->menu->add('Documentos IFAL');
                $event->menu->add(
                    [
                        'text' => 'Publicar',
                        'url' => 'admin/documentos/publicar',
                        'icon' => 'fas fa-fw fa-upload'
                    ],
                    [
                        'text' => 'Publicar em Lote',
                        'url' => 'admin/documentos/publicar-lote',
                        'icon' => 'fas fa-fw fa-upload'
                    ]
                );
            }
            if ($user->isAdmin() || $user->isConselho()) {
                $event->menu->add(
                    [
                        'text' => 'Últimos Documentos',
                        'url' => 'admin/documentos',
                        'icon' => 'fas fa-fw fa-search'
                    ]
                );
                $event->menu->add(
                    [
                        'text' => 'Documentos Privados',
                        'url' => 'admin/documentos/privados',
                        'icon' => 'fas fa-fw fa-lock'
                    ]
                );
                $event->menu->add('Pendências');
                $event->menu->add(
                    [
                        'text' => 'Pendências de Lote',
                        'url' => 'admin/documentos/pendentes',
                        'icon' => 'fas fa-fw fa-exclamation-circle',
                    ],
                    [
                        'text' => 'Pesquisar Pendências',
                        'url' => 'admin/documentos/pesquisar/status',
                        'icon' => 'fas fa-fw fa-search',
                    ],
                    [
                        'text' => 'Pesquisar Consultas',
                        'url' => 'admin/consultas',
                        'icon' => 'fas fa-fw fa-search',
                    ]

                );
            }
            if ($user->isAdmin() || $user->isAssessor()) {
                $event->menu->add('Unidades');
                $event->menu->add(
                    [
                        'text' => 'Unidades',
                        'icon' => 'fas fa-fw fa-university',
                        'url' => 'admin/unidades',
                    ]
                );
            }
            //if ($user->isAdmin()) {
            //    $event->menu->add(
            //        [
            //            'text' => 'Assessorias',
            //            'icon' => 'unlock-alt',
            //            'url' => 'admin/unidades/assessorias',
            //        ]
            //    );
            //}
            $event->menu->add('Glossário');
            $event->menu->add(
                [
                    'text' => 'Tipos de Documentos',
                    'url' => 'admin/tiposdocumento',
                    'icon' => 'fas fa-fw fa-file',
                ],
                [
                    'text' => 'Assuntos',
                    'url' => 'admin/assuntos',
                    'icon' => 'fas fa-fw fa-bookmark',
                ]
            );
            $event->menu->add(
                [
                    'text' => 'Perfil',
                    'icon' => 'fas fa-fw fa-address-card',
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
