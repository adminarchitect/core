<?php

namespace Terranet\Administrator;

use Illuminate\Support\Facades\Route;

class ArchitectRoutes
{
    /**
     * @return $this
     */
    public function withAuthenticationRoutes()
    {
        static::router()->group(function () {
            Route::get('login', [
                'as' => 'scaffold.login',
                'uses' => 'AuthController@getLogin',
            ]);
            Route::post('login', 'AuthController@postLogin');
            Route::get('logout', [
                'as' => 'scaffold.logout',
                'uses' => 'AuthController@getLogout',
            ]);
        });

        return $this;
    }

    /**
     * @return $this
     */
    public function withTranslationRoutes()
    {
        static::router()->group(function () {
            Route::resource('translations', 'TranslationsController', [
                'only' => ['index', 'store'],
                'as' => 'scaffold',
            ]);
        });

        return $this;
    }

    /**
     * @return $this
     */
    public function withMediaRoutes()
    {
        static::router()->group(function () {
            Route::group(['prefix' => 'media'], function () {
                Route::get('/', [
                    'as' => 'scaffold.media',
                    'uses' => 'MediaController@index',
                ]);

                Route::get('popup', [
                    'as' => 'scaffold.media.popup',
                    'uses' => 'MediaController@popup',
                ]);

                Route::post('/', [
                    'as' => 'scaffold.media.mkdir',
                    'uses' => 'MediaController@mkdir',
                ]);

                Route::post('rename', [
                    'as' => 'scaffold.media.rename',
                    'uses' => 'MediaController@rename',
                ]);

                Route::post('move', [
                    'as' => 'scaffold.media.move',
                    'uses' => 'MediaController@move',
                ]);

                Route::post('remove', [
                    'as' => 'scaffold.media.remove',
                    'uses' => 'MediaController@removeSelected',
                ]);

                Route::post('upload', [
                    'as' => 'scaffold.media.upload',
                    'uses' => 'MediaController@upload',
                ]);
            });
        });

        return $this;
    }

    /**
     * @return $this
     */
    public function withSettingRoutes()
    {
        static::router()->group(function () {
            Route::get('settings', [
                'as' => 'scaffold.settings.edit',
                'uses' => 'SettingsController@edit',
            ]);

            Route::post('settings', [
                'as' => 'scaffold.settings.update',
                'uses' => 'SettingsController@update',
            ]);
        });

        return $this;
    }

    /**
     * @param \Closure $callback
     * @return $this
     */
    public function withExtraRoutes(\Closure $callback)
    {
        static::router()->group($callback);

        return $this;
    }

    /**
     * @return \Illuminate\Routing\RouteRegistrar
     */
    protected static function router(): \Illuminate\Routing\RouteRegistrar
    {
        return Route::namespace('Terranet\Administrator\Controllers')
            ->middleware(['web'])
            ->prefix(Architect::path());
    }
}