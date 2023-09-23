<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\InputController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\ReleationshipController;
use App\Http\Controllers\InstanceController;
use App\Http\Controllers\MembershipController;
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
});

Route::prefix('teams')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [TeamController::class, 'index']);
    Route::post('/', [TeamController::class, 'store']);

    Route::prefix('{team}')->group(function () {
        Route::get('/', [TeamController::class, 'show']);
        Route::put('/', [TeamController::class, 'update']);
        Route::delete('/', [TeamController::class, 'destroy']);

        Route::prefix('memberships')->group(function () {
            Route::get('/', [MembershipController::class, 'index']);
            Route::post('/', [MembershipController::class, 'store']);

            Route::prefix('{membership}')->group(function () {
                Route::get('/', [MembershipController::class, 'show']);
                Route::put('/', [MembershipController::class, 'update']);
                Route::delete('/', [MembershipController::class, 'destroy']);
            });
        });

        Route::prefix('documents')->group(function () {
            Route::get('/', [DocumentController::class, 'index']);
            Route::post('/', [DocumentController::class, 'store']);

            Route::prefix('{document}')->group(function () {
                Route::get('/', [DocumentController::class, 'show']);
                Route::put('/', [DocumentController::class, 'update']);
                Route::delete('/', [DocumentController::class, 'destroy']);

                Route::prefix('instances')->group(function () {
                    Route::get('/', [InstanceController::class, 'index']);
                    Route::post('/', [InstanceController::class, 'store']);

                    Route::prefix('{instance}')->group(function () {
                        Route::get('/', [InstanceController::class, 'show']);
                        Route::put('/', [InstanceController::class, 'update']);
                        Route::delete('/', [InstanceController::class, 'destroy']);
                    });
                });

                Route::prefix('releationships')->group(function () {
                    Route::get('/', [ReleationshipController::class, 'index']);
                    Route::post('/', [ReleationshipController::class, 'store']);

                    Route::prefix('{releationship}')->group(function () {
                        Route::get('/', [ReleationshipController::class, 'show']);
                        Route::put('/', [ReleationshipController::class, 'update']);
                        Route::delete('/', [ReleationshipController::class, 'destroy']);
                        Route::post('/add', [ReleationshipController::class, 'document_add']);
                        Route::post('/remove', [ReleationshipController::class, 'document_remove']);
                    });
                });
            });
        });

        Route::prefix('templates')->group(function () {
            Route::get('/', [TemplateController::class, 'index']);
            Route::post('/', [TemplateController::class, 'store']);

            Route::prefix('{template}')->group(function () {
                Route::get('/', [TemplateController::class, 'show']);
                Route::put('/', [TemplateController::class, 'update']);
                Route::delete('/', [TemplateController::class, 'destroy']);

                Route::prefix('inputs')->group(function () {
                    Route::get('/', [InputController::class, 'index']);
                    Route::post('/', [InputController::class, 'store']);

                    Route::prefix('{input}')->group(function () {
                        Route::get('/', [InputController::class, 'show']);
                        Route::put('/', [InputController::class, 'update']);
                        Route::delete('/', [InputController::class, 'destroy']);
                    });
                });
            });

        });

    });

});

