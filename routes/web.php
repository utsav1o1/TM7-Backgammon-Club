<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

Route::get('/rules/terms', [\App\Http\Controllers\RegistrationController::class, 'terms'])->name('rules.terms');
Route::get('/rules/team', [\App\Http\Controllers\RegistrationController::class, 'teamRules'])->name('rules.team');
Route::get('/rules/individual', [\App\Http\Controllers\RegistrationController::class, 'individualRules'])->name('rules.individual');
Route::get('/contact', [\App\Http\Controllers\RegistrationController::class, 'contact'])->name('contact');
Route::post('/contact-us', [\App\Http\Controllers\ContactController::class, 'send'])->name('contact.send');

// Public Features
Route::get('/signup/success', [\App\Http\Controllers\RegistrationController::class, 'success'])->name('signup.success');

Route::get('/signup/player', [\App\Http\Controllers\RegistrationController::class, 'createPlayer'])->name('signup.player');
Route::post('/signup/player', [\App\Http\Controllers\RegistrationController::class, 'storePlayer']);

Route::get('/signup/team', [\App\Http\Controllers\RegistrationController::class, 'createTeam'])->name('signup.team');
Route::post('/signup/team', [\App\Http\Controllers\RegistrationController::class, 'storeTeam']);

Route::get('/signup/individual', [\App\Http\Controllers\RegistrationController::class, 'createIndividual'])->name('signup.individual');
Route::post('/signup/individual', [\App\Http\Controllers\RegistrationController::class, 'storeIndividual']);

Route::get('/teams', [\App\Http\Controllers\TournamentController::class, 'teams'])->name('teams.index');
Route::get('/players', [\App\Http\Controllers\TournamentController::class, 'players'])->name('players.index');
Route::get('/bracket/team', [\App\Http\Controllers\TournamentController::class, 'teamBracket'])->name('bracket.team');
Route::get('/bracket/solo', [\App\Http\Controllers\TournamentController::class, 'individualBracket'])->name('bracket.individual');

Route::post('/matches/{id}/declare-winner', [\App\Http\Controllers\TournamentController::class, 'declareWinner'])->name('matches.declare-winner');
Route::get('/hall-of-fame', [\App\Http\Controllers\TournamentController::class, 'hallOfFame'])->name('hall-of-fame');

Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    // Modular Admin Views
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::prefix('participants')->name('participants.')->group(function () {
            Route::get('/teams', [\App\Http\Controllers\AdminController::class, 'teams'])->name('teams');
            Route::get('/solo', [\App\Http\Controllers\AdminController::class, 'solo'])->name('solo');
            Route::get('/players', [\App\Http\Controllers\AdminController::class, 'players'])->name('players');
        });
        Route::prefix('stats')->name('stats.')->group(function () {
            Route::get('/team-bracket', [\App\Http\Controllers\AdminController::class, 'teamBracket'])->name('team-bracket');
            Route::get('/individual-bracket', [\App\Http\Controllers\AdminController::class, 'individualBracket'])->name('individual-bracket');
        });
        Route::prefix('rules')->name('rules.')->group(function () {
            Route::get('/team', [\App\Http\Controllers\AdminController::class, 'editTeamRules'])->name('team');
            Route::get('/individual', [\App\Http\Controllers\AdminController::class, 'editIndividualRules'])->name('individual');
        });
    });

    // Admin Actions
    Route::delete('/admin/truncate', [\App\Http\Controllers\AdminController::class, 'truncateAll'])->name('admin.truncate');
    
    // Admin Destroys
    Route::delete('/admin/teams/{id}', [\App\Http\Controllers\AdminController::class, 'destroyTeam'])->name('admin.destroy.team');
    Route::delete('/admin/individuals/{id}', [\App\Http\Controllers\AdminController::class, 'destroyIndividual'])->name('admin.destroy.individual');
    Route::delete('/admin/players/{id}', [\App\Http\Controllers\AdminController::class, 'destroyPlayer'])->name('admin.destroy.player');

    // Match Management
    Route::post('/admin/matches/{id}/result', [\App\Http\Controllers\AdminController::class, 'updateMatchResult'])->name('admin.match.result');
    Route::post('/admin/bracket/advance', [\App\Http\Controllers\AdminController::class, 'advanceBracket'])->name('admin.bracket.advance');
    Route::post('/admin/publish-bracket', [\App\Http\Controllers\AdminController::class, 'publishBracket'])->name('admin.publish');
    Route::post('/admin/unpublish-bracket', [\App\Http\Controllers\AdminController::class, 'unpublishBracket'])->name('admin.unpublish');
    Route::post('/admin/finalize-match/{id}', [\App\Http\Controllers\AdminController::class, 'finalizeMatch'])->name('admin.finalize');
    Route::post('/admin/reset-tournament', [\App\Http\Controllers\AdminController::class, 'resetTournament'])->name('admin.reset');
    Route::post('/admin/settings/cc-email', [\App\Http\Controllers\AdminController::class, 'updateCcEmail'])->name('admin.cc_email.update');
    Route::post('/admin/settings/rules', [\App\Http\Controllers\AdminController::class, 'updateRules'])->name('admin.rules.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
