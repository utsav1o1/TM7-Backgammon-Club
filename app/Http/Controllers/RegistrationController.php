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

        $this->markTeammateRequestsAsRegistered([$validated['email']]);

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
            'members' => 'required|array|min:3|max:5',
            'members.*.name' => 'required|string|max:255',
            'members.*.email' => 'required|email|max:255|distinct',
        ], [
            'accepted_terms.accepted' => 'You must agree to the Terms & Conditions and Official Rules.',
            'members.required' => 'You must add at least 3 team members.',
            'members.min' => 'A team requires at least 3 members.',
            'members.max' => 'A team can have at most 5 members.',
            'members.*.name.required' => 'Each team member must have a name.',
            'members.*.email.required' => 'Each team member must have an email.',
            'members.*.email.email' => 'Each team member must have a valid email address.',
            'members.*.email.distinct' => 'Each team member must have a unique email address.',
        ]);

        Team::create($validated);

        $emails = [$validated['email']];
        foreach ($validated['members'] as $member) {
            $emails[] = $member['email'];
        }
        $this->markTeammateRequestsAsRegistered($emails);

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

        $this->markTeammateRequestsAsRegistered([$validated['email']]);

        return redirect()->route('signup.success')->with('success', 'Individual entry registered successfully!');
    }

    public function success()
    {
        return Inertia::render('Public/RegistrationSuccess');
    }

    private function markTeammateRequestsAsRegistered(array $emails)
    {
        \App\Models\TeammateRequest::whereIn('email', $emails)
            ->where('status', 'active')
            ->update(['status' => 'registered']);
    }
}
