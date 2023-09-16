<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\InputController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\ReleationshipController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::post('/auth/register', [AuthController::class, 'createUser']);
Route::post('/auth/login', [AuthController::class, 'loginUser']);


Route::middleware(['auth:sanctum'])->get('/beadmin', function (Request $request) {
    //make user admin
    $user = $request->user();
    $user->is_admin = true;
    $user->save();
    return $user;
});

Route::middleware(['auth:sanctum'])->prefix('user')->group(function () {
    Route::get('/', function (Request $request) {
        return $request->user();
    });
    Route::get('/teams', function (Request $request) {
        return $request->user()->teams()->get();
    });
});


// Route for teams

Route::middleware(['auth:sanctum'])->prefix('team')->group(function () {

    Route::get('/', function (Request $request) {
        return $request->user()->teams()->get();
    });

    Route::post('/create', [TeamController::class, 'store']);

    // Route for {team}

    Route::prefix('{team}')->group(function ($team) {

        Route::get('/', [TeamController::class, 'index']);

        Route::prefix('users')->group(function () {
            Route::get('/', [TeamController::class, 'showUsers']); //get users
            Route::post('/add', [TeamController::class, 'addUser']); //add user @Param: user
            Route::post('/remove', [TeamController::class, 'removeUser']); //remove user @Param: user
        });

        // Route for {document}

        Route::prefix('documents')->group(function () {
            Route::get('/', [DocumentController::class, 'index']);
            Route::post('/add', [DocumentController::class, 'store']);

            Route::prefix('/relationships')->group(function () {
                Route::get('/', [ReleationshipController::class, 'index']);
                Route::post('/add', [ReleationshipController::class, 'store']);
                Route::post('/remove', [ReleationshipController::class, 'destroy']);

                Route::prefix('{releationship}')->group(function () {
                    Route::get('/', [ReleationshipController::class, 'show']);
                    Route::post('/add', [ReleationshipController::class, 'addDocument']);
                    Route::post('/remove', [ReleationshipController::class, 'removeDocument']);
                });
            });

            Route::prefix('{document}')->group(function () {
                Route::get('/', [DocumentController::class, 'show']);
                Route::get('/show', [DocumentController::class, 'show']);
                Route::post('/update', [DocumentController::class, 'update']);
                Route::post('/remove', [DocumentController::class, 'destroy']);
                Route::get('/fields', [DocumentController::class, 'fields']);

                Route::get('/value', [DocumentController::class, 'value']);
                Route::post('/value', [DocumentController::class, 'value']);


            });
        });

        // Route for {template}
        Route::prefix('templates')->group(function () {
            Route::get('/', [TemplateController::class, 'index']);
            Route::post('/add', [TemplateController::class, 'store']);

            Route::prefix('{template}')->group(function () {
                Route::get('/', [TemplateController::class, 'show']);
                Route::post('/update', [TemplateController::class, 'update']);
                Route::post('/remove', [TemplateController::class, 'destroy']);
                
                //Route for Inputs
                Route::prefix('inputs')->group(function () {
                    Route::get('/', [InputController::class, 'index']);
                    Route::post('/add', [InputController::class, 'store']);

                    
                    Route::prefix('{input}')->group(function () {
                        Route::get('/', [InputController::class, 'show']);
                        Route::post('/update', [InputController::class, 'update']);
                        Route::post('/remove', [InputController::class, 'destroy']);
                    });

                });
            });
        });
    });
});