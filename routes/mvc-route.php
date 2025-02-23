<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => config('mvc.route_prefix')], function () { // remove this line if you dont have route group prefix
    Route::group(['middleware' => ['userRoles']], function () {
        //Condominiums
        Route::prefix('Condominiums')->as('Condominiums')->group(function () {
            Route::get('data', 'Condominiums\CondominiumsController@data');
            Route::get('delete/{id}', 'Condominiums\CondominiumsController@delete');
        });
        Route::resource('Condominiums', 'Condominiums\CondominiumsController');
        //end-Condominiums
        //tower-sector
        Route::prefix('tower-sector')->as('tower-sector')->group(function () {
            Route::get('data', 'TowerSector\TowerSectorController@data');
            Route::get('delete/{id}', 'TowerSector\TowerSectorController@delete');
        });
        Route::resource('tower-sector', 'TowerSector\TowerSectorController');
        //end-tower-sector
        //floor-street
        Route::prefix('floor-street')->as('floor-street')->group(function () {
            Route::get('data', 'FloorStreet\FloorStreetController@data');
            Route::get('delete/{id}', 'FloorStreet\FloorStreetController@delete');
        });
        Route::resource('floor-street', 'FloorStreet\FloorStreetController');
        //end-floor-street
        //unit-type
        Route::prefix('unit-type')->as('unit-type')->group(function () {
            Route::get('data', 'UnitType\UnitTypeController@data');
            Route::get('delete/{id}', 'UnitType\UnitTypeController@delete');
        });
        Route::resource('unit-type', 'UnitType\UnitTypeController');
        //end-unit-type
        //type-dweller
        Route::prefix('type-dweller')->as('type-dweller')->group(function () {
            Route::get('data', 'TypeDweller\TypeDwellerController@data');
            Route::get('delete/{id}', 'TypeDweller\TypeDwellerController@delete');
        });
        Route::resource('type-dweller', 'TypeDweller\TypeDwellerController');
        //end-type-dweller
        //document-id-type
        Route::prefix('document-type')->as('document-type')->group(function () {
            Route::get('data', 'DocumentTypes\DocumentTypesController@data');
            Route::get('delete/{id}', 'DocumentTypes\DocumentTypesController@delete');
        });
        Route::resource('document-type', 'DocumentTypes\DocumentTypesController');
        //end-document-id-type
        //dweller
        Route::prefix('dweller')->as('dweller')->group(function () {
            Route::get('data', 'Dweller\DwellerController@data');
            Route::get('delete/{id}', 'Dweller\DwellerController@delete');
        });
        Route::resource('dweller', 'Dweller\DwellerController');
        //end-dweller
        //unit
        Route::prefix('unit')->as('unit')->group(function () {
            Route::get('data', 'Unit\UnitController@data');
            Route::get('delete/{id}', 'Unit\UnitController@delete');
        });
        Route::resource('unit', 'Unit\UnitController');
        //end-unit
        //banks
        Route::prefix('banks')->as('banks')->group(function () {
            Route::get('data', 'Banks\BanksController@data');
            Route::get('delete/{id}', 'Banks\BanksController@delete');
        });
        Route::resource('banks', 'Banks\BanksController');
        //end-banks
        //banks-condominium
        Route::prefix('banks-condominium')->as('banks-condominium')->group(function () {
            Route::get('data', 'BanksCondominium\BanksCondominiumController@data');
            Route::get('delete/{id}', 'BanksCondominium\BanksCondominiumController@delete');
        });
        Route::resource('banks-condominium', 'BanksCondominium\BanksCondominiumController');
        //end-banks-condominium
        //ways-to-pays
        Route::prefix('ways-to-pays')->as('ways-to-pays')->group(function () {
            Route::get('data', 'WaysToPays\WaysToPaysController@data');
            Route::get('delete/{id}', 'WaysToPays\WaysToPaysController@delete');
        });
        Route::resource('ways-to-pays', 'WaysToPays\WaysToPaysController');
        //end-ways-to-pays
        //payments
        Route::prefix('payments')->as('payments')->group(function () {
            Route::get('data', 'Payments\PaymentsController@data');
            Route::get('delete/{id}', 'Payments\PaymentsController@delete');
        });
        Route::resource('payments', 'Payments\PaymentsController');
        //end-payments
        //payments-history
        Route::prefix('payments-history')->as('payments')->group(function () {
            Route::get('data', 'Payments\PaymentsHistoryController@data');
        });
        Route::resource('payments-history', 'Payments\PaymentsHistoryController');
        //end-payments-history
        //{{route replacer}} DON'T REMOVE THIS LINE
    });
});
