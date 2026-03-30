<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Player;
use App\Models\Team;
use App\Models\IndividualEntry;
use App\Models\TournamentMatch;
use App\Models\TournamentWinner;
use App\Models\TournamentSetting;
use Faker\Factory as Faker;

class AdminController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard', [
            'total_teams' => Team::count(),
            'total_solo' => IndividualEntry::count(),
            'total_players' => Player::count(),
            'total_matches' => TournamentMatch::count(),
            'open_conflicts' => TournamentMatch::where('status', 'conflict')->count(),
            'admin_cc_email' => TournamentSetting::get('admin_cc_email', 'utsavkarki244@gmail.com'),
        ]);
    }

    public function teams()
    {
        return Inertia::render('Admin/Participants/Teams', [
            'teams' => Team::latest()->get(),
        ]);
    }

    public function solo()
    {
        return Inertia::render('Admin/Participants/Solo', [
            'individual_entries' => IndividualEntry::latest()->get(),
        ]);
    }

    public function players()
    {
        return Inertia::render('Admin/Participants/Players', [
            'players' => Player::latest()->get(),
        ]);
    }

    public function teamBracket()
    {
        $matches = TournamentMatch::where('tournament_type', 'team')->get();
        $teams = Team::all()->keyBy('id');

        foreach ($matches as $match) {
            $match->p1_name = $teams->get($match->participant1_id)->name ?? 'BYE';
            $match->p2_name = $teams->get($match->participant2_id)->name ?? 'BYE';
            $match->winner_name = $teams->get($match->winner_id)->name ?? null;
        }

        return Inertia::render('Admin/Stats/TeamBracket', [
            'matches' => $matches,
            'is_published' => TournamentSetting::get('team_published', false),
        ]);
    }

    public function individualBracket()
    {
        $matches = TournamentMatch::where('tournament_type', 'individual')->get();
        $solo = IndividualEntry::all()->keyBy('id');

        foreach ($matches as $match) {
            $match->p1_name = $solo->get($match->participant1_id)->player_name ?? 'BYE';
            $match->p2_name = $solo->get($match->participant2_id)->player_name ?? 'BYE';
            $match->winner_name = $solo->get($match->winner_id)->player_name ?? null;
        }

        return Inertia::render('Admin/Stats/IndividualBracket', [
            'matches' => $matches,
            'is_published' => TournamentSetting::get('individual_published', false),
        ]);
    }

    public function editTeamRules()
    {
        return Inertia::render('Admin/Rules/Team', [
            'rules' => TournamentSetting::get('team_rules', ''),
        ]);
    }

    public function editIndividualRules()
    {
        return Inertia::render('Admin/Rules/Individual', [
            'rules' => TournamentSetting::get('individual_rules', ''),
        ]);
    }

    public function updateCcEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        TournamentSetting::set('admin_cc_email', $request->email);

        return back()->with('success', 'Admin CC email updated successfully!');
    }

    public function updateRules(Request $request)
    {
        $request->validate([
            'team_rules' => 'nullable|string',
            'individual_rules' => 'nullable|string',
        ]);

        TournamentSetting::set('team_rules', $request->team_rules);
        TournamentSetting::set('individual_rules', $request->individual_rules);

        return back()->with('success', 'Tournament rules updated successfully!');
    }

    public function seedFakeData()
    {
        $faker = Faker::create();

        // Seed 8 Players
        for ($i = 0; $i < 8; $i++) {
            Player::create([
                'nickname' => $faker->unique()->userName,
                'email' => $faker->unique()->safeEmail,
                'timezone' => $faker->timezone,
            ]);
        }

        // Seed 8 Teams
        for ($i = 0; $i < 8; $i++) {
            Team::create([
                'name' => $faker->unique()->company . ' Squad',
                'email' => $faker->unique()->companyEmail,
                'captain_nickname' => $faker->userName,
                'co_captain_nickname' => $faker->boolean ? $faker->userName : null,
            ]);
        }

        // Seed 8 Solo Entries
        for ($i = 0; $i < 8; $i++) {
            IndividualEntry::create([
                'player_name' => $faker->unique()->userName,
                'email' => $faker->unique()->safeEmail,
                'timezone' => $faker->timezone,
            ]);
        }

        return back()->with('success', 'Successfully seeded 8 Teams, 8 Solo Entries, and 8 Platform Players!');
    }

    public function destroyTeam($id)
    {
        Team::findOrFail($id)->delete();
        TournamentMatch::where('tournament_type', 'team')->where(function($q) use ($id) {
            $q->where('participant1_id', $id)->orWhere('participant2_id', $id);
        })->delete();
        return back()->with('success', 'Team deleted successfully and affected matches removed.');
    }

    public function destroyIndividual($id)
    {
        IndividualEntry::findOrFail($id)->delete();
        TournamentMatch::where('tournament_type', 'individual')->where(function($q) use ($id) {
            $q->where('participant1_id', $id)->orWhere('participant2_id', $id);
        })->delete();
        return back()->with('success', 'Solo entry deleted successfully and affected matches removed.');
    }

    public function destroyPlayer($id)
    {
        Player::findOrFail($id)->delete();
        return back()->with('success', 'Platform player deleted successfully.');
    }

    public function truncateAll()
    {
        Team::truncate();
        IndividualEntry::truncate();
        Player::truncate();
        TournamentMatch::truncate();
        TournamentSetting::truncate();
        
        \Illuminate\Support\Facades\Cache::forget('tournament_setting_team_published');
        \Illuminate\Support\Facades\Cache::forget('tournament_setting_individual_published');
        
        return back()->with('success', 'All relevant tables truncated successfully!');
    }

    public function seedBracket(Request $request)
    {
        $type = $request->input('type'); // 'team' or 'individual'

        if ($type === 'team') {
            $entrants = Team::all();
        } else {
            $entrants = IndividualEntry::all();
        }

        if ($entrants->count() < 2) {
            return back()->with('error', 'Not enough entrants to generate a bracket.');
        }

        TournamentMatch::where('tournament_type', $type)->delete();

        $round = 1;
        $shuffled = $entrants->shuffle();
        
        for ($i = 0; $i < $shuffled->count(); $i += 2) {
            if (isset($shuffled[$i+1])) {
                TournamentMatch::create([
                    'tournament_type' => $type,
                    'round' => $round,
                    'match_number' => $i / 2,
                    'participant1_id' => $shuffled[$i]->id,
                    'participant2_id' => $shuffled[$i+1]->id,
                    'status' => 'pending',
                ]);
            }
        }

        return back()->with('success', 'Bracket generated successfully!');
    }

    public function updateMatchResult(Request $request, $id)
    {
        $match = TournamentMatch::findOrFail($id);
        $match->update([
            'winner_id' => $request->winner_id,
            'status' => 'finalized'
        ]);
        return back()->with('success', 'Match result updated!');
    }

    public function advanceBracket(Request $request)
    {
        $type = $request->input('type');
        $round = $request->input('round');

        $pending = TournamentMatch::where('tournament_type', $type)
            ->where('round', $round)
            ->where('status', 'pending')
            ->count();

        if ($pending > 0) {
            return back()->with('error', 'Cannot advance: Some matches are still pending.');
        }

        $winners = TournamentMatch::where('tournament_type', $type)
            ->where('round', $round)
            ->orderBy('id')
            ->pluck('winner_id');

        if ($winners->count() <= 1) {
            return back()->with('success', 'Tournament concluded! We have a champion.');
        }

        $nextRound = $round + 1;
        for ($i = 0; $i < $winners->count(); $i += 2) {
            TournamentMatch::create([
                'tournament_type' => $type,
                'round' => $nextRound,
                'match_number' => $i / 2,
                'participant1_id' => $winners[$i],
                'participant2_id' => $winners[$i+1] ?? null,
                'status' => 'pending',
            ]);
        }

        return back()->with('success', "Advanced to Round $nextRound!");
    }

    public function publishBracket(Request $request)
    {
        $type = $request->input('type'); // 'team' or 'individual'
        TournamentSetting::set($type . '_published', true);

        return back()->with('success', ucfirst($type) . ' tournament bracket published and registration locked!');
    }

    public function unpublishBracket(Request $request)
    {
        $type = $request->input('type');
        TournamentSetting::set($type . '_published', false);

        return back()->with('success', ucfirst($type) . ' tournament bracket unpublished and registration unlocked!');
    }

    public function finalizeMatch(Request $request, $id)
    {
        $match = TournamentMatch::findOrFail($id);
        $match->update([
            'winner_id' => $request->winner_id,
            'status' => 'finalized',
        ]);
        
        TournamentMatch::autoAdvance($match);

        return back()->with('success', 'Match finalized by Admin and bracket updated.');
    }

    public function resetTournament(Request $request)
    {
        $type = $request->input('type');
        
        // Find the ultimate winner
        $lastMatch = TournamentMatch::where('tournament_type', $type)
            ->whereIn('status', ['completed', 'finalized'])
            ->orderBy('round', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastMatch && $lastMatch->winner_id) {
            $winnerName = '';
            if ($type === 'team') {
                $winnerName = Team::find($lastMatch->winner_id)->name ?? 'Unknown';
            } else {
                $winnerName = IndividualEntry::find($lastMatch->winner_id)->player_name ?? 'Unknown';
            }

            // Archive to Hall of Fame
            TournamentWinner::create([
                'name' => $winnerName,
                'type' => $type,
                'month' => date('F'),
                'year' => date('Y')
            ]);
        }

        // Cleanup registration data
        if ($type === 'team') {
            Team::truncate();
            TournamentSetting::set('team_published', false);
        } else {
            IndividualEntry::truncate();
            TournamentSetting::set('individual_published', false);
        }

        // Delete matches
        TournamentMatch::where('tournament_type', $type)->delete();

        return back()->with('success', "Tournament data for $type has been archived and reset.");
    }
}
