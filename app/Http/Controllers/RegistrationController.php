<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Team;
use App\Models\IndividualEntry;
use App\Models\TournamentSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RegistrationController extends Controller
{
    public function createPlayer()
    {
        return Inertia::render('Public/PlayerSignUp', [
            'is_published' => TournamentSetting::get('individual_published', false)
        ]);
    }

    public function storePlayer(Request $request)
    {
        $validated = $request->validate([
            'nickname' => 'required|string|max:255|unique:players',
            'email' => 'required|email|max:255|unique:players',
            'timezone' => 'required|string|max:255',
            'accepted_terms' => 'accepted',
        ], [
            'accepted_terms.accepted' => 'You must agree to the Terms & Conditions and Official Rules.'
        ]);

        Player::create($validated);

        return redirect()->route('signup.success')->with('success', 'Player registered successfully!');
    }

    public function terms()
    {
        return Inertia::render('Public/Rules/Terms');
    }

    public function teamRules()
    {
        return Inertia::render('Public/Rules/Team', [
            'rules' => TournamentSetting::get('team_rules', '')
        ]);
    }

    public function individualRules()
    {
        return Inertia::render('Public/Rules/Individual', [
            'rules' => TournamentSetting::get('individual_rules', '')
        ]);
    }

    public function contact()
    {
        return Inertia::render('Public/Contact');
    }

    public function createTeam()
    {
        return Inertia::render('Public/TeamSignUp', [
            'is_published' => TournamentSetting::get('team_published', false)
        ]);
    }

    public function storeTeam(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:teams',
            'email' => 'required|email|max:255|unique:teams',
            'captain_nickname' => 'required|string|max:255',
            'co_captain_nickname' => 'nullable|string|max:255',
            'accepted_terms' => 'accepted',
        ], [
            'accepted_terms.accepted' => 'You must agree to the Terms & Conditions and Official Rules.'
        ]);

        Team::create($validated);

        return redirect()->route('signup.success')->with('success', 'Team registered successfully!');
    }

    public function createIndividual()
    {
        return Inertia::render('Public/IndividualSignUp', [
            'is_published' => TournamentSetting::get('individual_published', false)
        ]);
    }

    public function storeIndividual(Request $request)
    {
        $validated = $request->validate([
            'player_name' => 'required|string|max:255|unique:individual_entries',
            'email' => 'required|email|max:255|unique:individual_entries',
            'timezone' => 'required|string|max:255',
            'accepted_terms' => 'accepted',
        ], [
            'accepted_terms.accepted' => 'You must agree to the Terms & Conditions and Official Rules.'
        ]);

        IndividualEntry::create($validated);

        return redirect()->route('signup.success')->with('success', 'Individual entry registered successfully!');
    }

    public function success()
    {
        return Inertia::render('Public/RegistrationSuccess');
    }
}
