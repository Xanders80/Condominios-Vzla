<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => config('master.app.url.backend')], function () {
    // public route
    Route::resource('dashboard', "Dashboard\DashboardController")->name('index', 'dashboard')->except(['show']);
    Route::get('/list-menu', "Menu\MenuController@listMenu")->name('menu.list-menu');
    Route::get('announcement-detail/{id}/{slug}', "Announcement\AnnouncementController@detail")->name('announcement');
    Route::get('user-detail/{id}/', "User\UserController@detail")->name('user-detail');
    Route::get('sidebar-notification', 'Notification\NotificationController@getSideBarNotification');
    Route::get('get-notification', 'Notification\NotificationController@getNotification');
    Route::get('clear-notification', 'Notification\NotificationController@markAsRead');
    Route::post('logout', 'Auth\AuthController@logout')->name('logout');
    //Extra Page Routs
    Route::get('privacy-policy', "Dashboard\DashboardController@privacypolicy")->name('privacy-policy');
    Route::get('term-of-use', "Dashboard\DashboardController@termsofuse")->name('term-of-use');
    Route::get('dashboard/data-cards/{year}', 'Dashboard\DashboardController@dataCards');
    Route::get('dashboard/payment-by-month/{year}', 'Dashboard\DashboardController@paymentDataMonthByYear');
    Route::get('dashboard/payment-by-year', 'Dashboard\DashboardController@paymentDataByYear');

    // end public route
    // question
    Route::prefix('question')->as('question')->group(function () {
        Route::get('data', "Question\QuestionController@data");
        Route::get('page/{page}', "Question\QuestionController@page");
        Route::get('viewer', "Question\QuestionController@updateViewer");
        Route::post('response', "Question\QuestionController@response");
    });
    Route::resource('question', "Question\QuestionController")->name('index', 'question');
    // file
    Route::prefix('file')->as('file')->group(function () {
        Route::get('stream/{id}/{name}', "File\FileController@getFile");
        Route::get('download/{id}/{name}', "File\FileController@downloadFile");
        Route::get('delete/{id}/{name}', "File\FileController@deleteFile");
        Route::post('upload-image-editor', 'File\FileController@handleEditorImageUpload');
    });
    Route::group(['middleware' => ['userRoles']], function () {
        // user
        Route::prefix('user')->as('user')->group(function () {
            Route::get('data', "User\UserController@data");
            Route::get('delete/{id}', "User\UserController@delete");
        });
        Route::resource('user', "User\UserController");
        // end-user
        // menu
        Route::post('/sorted', "Menu\MenuController@sorted")->name('menu.sorted');
        Route::prefix('menu')->as('menu')->group(function () {
            Route::get('/data', "Menu\MenuController@data");
            Route::get('delete/{id}', "Menu\MenuController@delete");
        });
        Route::resource('menu', "Menu\MenuController");
        // end-menu
        // access-group
        Route::prefix('access-group')->as('access-group')->group(function () {
            Route::get('data', "AccessGroup\AccessGroupController@data");
            Route::get('delete/{id}', "AccessGroup\AccessGroupController@delete");
        });
        Route::resource('access-group', "AccessGroup\AccessGroupController");
        // end-access-group
        // level
        Route::prefix('level')->as('level')->group(function () {
            Route::get('data', "Level\LevelController@data");
            Route::get('delete/{id}', "Level\LevelController@delete");
        });
        Route::resource('level', "Level\LevelController");
        // end-level
        // access-menu
        Route::prefix('access-menu')->as('access-menu')->group(function () {
            Route::get('data', "AccessMenu\AccessMenuController@data");
            Route::get('delete/{id}', "AccessMenu\AccessMenuController@delete");
        });
        Route::resource('access-menu', "AccessMenu\AccessMenuController");
        // end-access-menu
        // faq
        Route::prefix('faq')->as('faq')->group(function () {
            Route::get('data', "Faq\FaqController@data");
            Route::get('delete/{id}', "Faq\FaqController@delete");
        });
        Route::resource('faq', "Faq\FaqController");
        // end-faq
        // announcement
        Route::prefix('announcement')->as('announcement')->group(function () {
            Route::get('data', 'Announcement\AnnouncementController@data');
            Route::get('delete/{id}', 'Announcement\AnnouncementController@delete');
        });
        Route::resource('announcement', 'Announcement\AnnouncementController');
        // end-announcement
        // notification
        Route::prefix('notification')->as('notification')->group(function () {
            Route::get('data', 'Notification\NotificationController@data');
            Route::get('delete/{id}', 'Notification\NotificationController@delete');
        });
        Route::resource('notification', 'Notification\NotificationController');
        // end-notification

        // Condominiums
        Route::prefix('condominiums')->as('condominiums')->group(function () {
            Route::get('data', 'Condominiums\CondominiumsController@data');
            Route::get('delete/{id}', 'Condominiums\CondominiumsController@delete');
            Route::get('/municipalities/{stateId}', 'Condominiums\CondominiumsController@getMunicipalities');
            Route::get('/cities/{stateId}/{municipalityId}', 'Condominiums\CondominiumsController@getCities');
            Route::get('/countries/{municipalityId}', 'Condominiums\CondominiumsController@getCountries');
            Route::get('/zipcodes/{countryId}', 'Condominiums\CondominiumsController@getZipCode');
            Route::get('/full-address/{zipName}', 'Condominiums\CondominiumsController@getAddressByZone');
        });
        Route::resource('condominiums', 'Condominiums\CondominiumsController');
        // end-Condominiums
        // tower-sector
        Route::prefix('tower-sector')->as('tower-sector')->group(function () {
            Route::get('data', 'TowerSector\TowerSectorController@data');
            Route::get('delete/{id}', 'TowerSector\TowerSectorController@delete');
        });
        Route::resource('tower-sector', 'TowerSector\TowerSectorController');
        // end-tower-sector
        // floor-street
        Route::prefix('floor-street')->as('floor-street')->group(function () {
            Route::get('data', 'FloorStreet\FloorStreetController@data');
            Route::get('delete/{id}', 'FloorStreet\FloorStreetController@delete');
        });
        Route::resource('floor-street', 'FloorStreet\FloorStreetController');
        // end-floor-street
        // unit-type
        Route::prefix('unit-type')->as('unit-type')->group(function () {
            Route::get('data', 'UnitType\UnitTypeController@data');
            Route::get('delete/{id}', 'UnitType\UnitTypeController@delete');
        });
        Route::resource('unit-type', 'UnitType\UnitTypeController');
        // end-unit-type
        // type-dweller
        Route::prefix('dweller-type')->as('dweller-type')->group(function () {
            Route::get('data', 'DwellerType\DwellerTypeController@data');
            Route::get('delete/{id}', 'DwellerType\DwellerTypeController@delete');
        });
        Route::resource('dweller-type', 'DwellerType\DwellerTypeController');
        // end-type-dweller
        // document-id-type
        Route::prefix('document-type')->as('document-type')->group(function () {
            Route::get('data', 'DocumentType\DocumentTypeController@data');
            Route::get('delete/{id}', 'DocumentType\DocumentTypeController@delete');
        });
        Route::resource('document-type', 'DocumentType\DocumentTypeController');
        // end-document-id-type
        // dweller
        Route::prefix('dweller')->as('dweller')->group(function () {
            Route::get('data', 'Dweller\DwellerController@data');
            Route::get('delete/{id}', 'Dweller\DwellerController@delete');
        });
        Route::resource('dweller', 'Dweller\DwellerController');
        // end-dweller
        // unit
        Route::prefix('unit')->as('unit')->group(function () {
            Route::get('data', 'Unit\UnitController@data');
            Route::get('delete/{id}', 'Unit\UnitController@delete');
            Route::get('/floor-streets/{towerSectorId}', 'Unit\UnitController@getFloorStreets');
        });
        Route::resource('unit', 'Unit\UnitController');
        // end-unit
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
            Route::get('data-cards/{month}/{year}', 'Payments\PaymentsController@dataCards');
            Route::get('get-months/{year}', 'Payments\PaymentsController@getMonthsForYearJson');
        });
        Route::resource('payments', 'Payments\PaymentsController');
        //end-payments
    });
});
