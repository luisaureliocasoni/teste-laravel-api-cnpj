<?php

use App\Http\Controllers\EmpresaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/


Route::get('/', function (){
    return response()->json([
        "message" => "Em funcionamento",
        "application" => env("APP_NAME", "VAPI")
    ]);
});

Route::controller(EmpresaController::class)->group(function () {
    Route::get('/empresa/{cnpj}', 'get');
    Route::put('/empresa/{cnpj}', 'edit');
    Route::delete('/empresa/{cnpj}', 'delete');
});
