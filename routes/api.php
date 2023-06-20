<?php

use App\Http\Controllers\BienController;
use App\Http\Controllers\BlocController;
use App\Http\Controllers\ImmeubleController;
use App\Http\Controllers\ProjetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocieteController;
use App\Http\Controllers\TrancheController;
use App\Http\Controllers\TypeBienController;
use App\Http\Controllers\TypeProjetController;
use App\Http\Controllers\UserController;
use Nette\Schema\Elements\Type;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
 */

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [UserController::class, 'login'])->name('login');
//Route::post('register', [UserController::class, 'register'])->name('register');

Route::middleware('auth:api')->group(function () {
    
    /*************************************Société***************************** */
        Route::resource('societe', SocieteController::class);
        Route::get('restoreSociete/{id}', [SocieteController::class,'restoreSociete'])->name('restoreSociete');
        Route::get('getTrashedSocietes', [SocieteController::class,'getTrashedSocietes'])->name('getTrashedSocietes');
    
    /*************************************User***************************** */
        Route::resource('user', UserController::class);
        Route::get('getUsersBySocieteId/{id}', [UserController::class,'getUsersBySocieteId'])->name('getUsersBySocieteId');
        Route::put('activateUser/{id}', [UserController::class,'activateUser'])->name('activateUser');
        Route::put('desactivateUser/{id}', [UserController::class,'desactivateUser'])->name('desactivateUser');
        Route::get('restoreUser/{id}', [UserController::class,'restoreUser'])->name('restoreUser');
        Route::get('getTrashedUsers', [UserController::class,'getTrashedUsers'])->name('getTrashedUsers');
        Route::get('getTrashedUsersBySociete/{id}', [UserController::class,'getTrashedUsersBySociete'])->name('getTrashedUsersBySociete');
    
    /*************************************Projet***************************** */
        Route::resource('projet', ProjetController::class);
        Route::resource('typeProjet', TypeProjetController::class);
        Route::get('restoreProjet/{id}', [ProjetController::class,'restoreProjet'])->name('restoreProjet');
        Route::get('getTrashedProjets', [ProjetController::class,'getTrashedProjets'])->name('getTrashedProjets');
        Route::get('restoreTypeProjet/{id}', [TypeProjetController::class,'restoreTypeProjet'])->name('restoreTypeBien');
        Route::get('getTrashedTypesProjet', [TypeProjetController::class,'getTrashedTypesProjet'])->name('getTrashedTypesProjet');
        
    /*************************************Tranche***************************** */
        Route::resource('tranche', TrancheController::class);
        Route::get('restoreTranche/{id}', [TrancheController::class,'restoreTranche'])->name('restoreTranche');
        Route::get('getTrashedTranches', [TrancheController::class,'getTrashedTranches'])->name('getTrashedTranches');
        
    /*************************************Bloc***************************** */
        Route::resource('bloc', BlocController::class);
        Route::get('restoreBloc/{id}', [BlocController::class,'restoreBloc'])->name('restoreBloc');
        Route::get('getTrashedBlocs', [BlocController::class,'getTrashedBlocs'])->name('getTrashedBlocs');
        
    /*************************************Immeuble***************************** */
        Route::resource('immeuble', ImmeubleController::class);
        Route::get('restoreImmeuble/{id}', [ImmeubleController::class,'restoreImmeuble'])->name('restoreImmeuble');
        Route::get('getTrashedImmeubles', [ImmeubleController::class,'getTrashedImmeubles'])->name('getTrashedImmeubles');
        
    /*************************************Bien***************************** */
        Route::resource('typeBien', TypeBienController::class);
        Route::resource('bien', BienController::class);
        Route::get('restoreBien/{id}', [BienController::class,'restoreBien'])->name('restoreBien');
        Route::get('getTrashedBiens', [BienController::class,'getTrashedBiens'])->name('getTrashedBiens');
        Route::get('restoreTypeBien/{id}', [TypeBienController::class,'restoreTypeBien'])->name('restoreTypeBien');
        Route::get('getTrashedTypesBien', [TypeBienController::class,'getTrashedTypesBien'])->name('getTrashedTypesBien');
        
    
   
    

});

