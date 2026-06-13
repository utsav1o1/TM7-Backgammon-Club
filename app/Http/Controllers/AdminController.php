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
            'total_teams'     => Team::count(),
            'total_solo'      => Player::count(),  // Solo players use the Player model
            'total_players'   => Player::count(),
            'total_matches'   => TournamentMatch::count(),
            'open_conflicts'  => TournamentMatch::where('status', 'conflict')->count(),
            'admin_cc_email'  => TournamentSetting::get('admin_cc_email', 'utsavkarki244@gmail.com'),
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
            'players' => Player::latest()->get(),
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
        $solo = Player::all()->keyBy('id');

        foreach ($matches as $match) {
            $match->p1_name = $solo->get($match->participant1_id)->nickname ?? 'BYE';
            $match->p2_name = $solo->get($match->participant2_id)->nickname ?? 'BYE';
            $match->winner_name = $solo->get($match->winner_id)->nickname ?? null;
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

        return back()->with('success', 'Successfully seeded 8 fake Players (Solo) and 8 fake Teams for testing!');
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
        Player::findOrFail($id)->delete();
        TournamentMatch::where('tournament_type', 'individual')->where(function($q) use ($id) {
            $q->where('participant1_id', $id)->orWhere('participant2_id', $id);
        })->delete();
        return back()->with('success', 'Solo player deleted successfully and affected matches removed.');
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
            $entrants = Player::all(); // Replaced IndividualEntry with Player
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

    // ─────────────────────────────────────────────────────────────────────────
    // FINE-GRAINED MATCH EDITING
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Return a single match + all available participants so the Vue modal can prefill.
     */
    public function editMatch($id)
    {
        $match = TournamentMatch::findOrFail($id);

        if ($match->tournament_type === 'team') {
            $participants = Team::select('id', 'name as label')->get();
        } else {
            $participants = Player::select('id', 'nickname as label')->get();
        }

        return response()->json([
            'match'        => $match,
            'participants' => $participants,
        ]);
    }

    /**
     * Swap participant1 / participant2 in a match.
     * Clears the result and cascade-clears all downstream rounds.
     */
    public function updateMatchParticipants(Request $request, $id)
    {
        $request->validate([
            'participant1_id' => 'nullable|integer',
            'participant2_id' => 'nullable|integer',
        ]);

        $match = TournamentMatch::findOrFail($id);

        // Cascade-clear before saving so downstream is consistent
        $clearedCount = TournamentMatch::cascadeClearDownstream($match);

        $match->update([
            'participant1_id' => $request->participant1_id,
            'participant2_id' => $request->participant2_id,
            'winner_id'       => null,
            'p1_claimed_win'  => false,
            'p2_claimed_win'  => false,
            'is_conflict'     => false,
            'status'          => 'pending',
            'notes'           => $request->notes,
        ]);

        $msg = 'Match participants updated.';
        if ($clearedCount > 0) {
            $msg .= " $clearedCount downstream match(es) were also cleared.";
        }

        return back()->with('success', $msg);
    }

    /**
     * Admin overrides the winner of any match (regardless of current status).
     * Re-runs autoAdvance to populate the next round.
     */
    public function overrideWinner(Request $request, $id)
    {
        $request->validate([
            'winner_id' => 'required|integer',
        ]);

        $match = TournamentMatch::findOrFail($id);

        $match->update([
            'winner_id' => $request->winner_id,
            'status'    => 'finalized',
            'notes'     => $request->notes,
        ]);

        TournamentMatch::autoAdvance($match);

        return back()->with('success', 'Winner overridden successfully and bracket updated.');
    }

    /**
     * Reverse a match result — clears winner, claims, conflict flag, sets pending.
     * Cascade-clears all downstream slots and reports how many were affected.
     */
    public function reverseMatchResult($id)
    {
        $match = TournamentMatch::findOrFail($id);

        $clearedCount = TournamentMatch::cascadeClearDownstream($match);

        $match->update([
            'winner_id'      => null,
            'p1_claimed_win' => false,
            'p2_claimed_win' => false,
            'is_conflict'    => false,
            'status'         => 'pending',
        ]);

        $msg = 'Match result reversed.';
        if ($clearedCount > 0) {
            $msg .= " $clearedCount downstream match(es) were also cleared.";
        }

        return back()->with('success', $msg);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // BRACKET DELETE → BACKUP
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Snapshot the entire bracket, delete all its matches, unpublish.
     */
    public function deleteBracket(Request $request, $type)
    {
        if (!in_array($type, ['team', 'individual'])) {
            return back()->with('error', 'Invalid tournament type.');
        }

        $matchCount = TournamentMatch::where('tournament_type', $type)->count();
        if ($matchCount === 0) {
            return back()->with('error', 'No matches found for this bracket.');
        }

        $label = ucfirst($type) . ' Bracket – deleted ' . now()->format('Y-m-d H:i');

        \App\Models\BracketSnapshot::create([
            'snapshot_label'  => $label,
            'tournament_type' => $type,
            'snapshot_data'   => TournamentMatch::snapshotBracket($type),
            'deleted_by'      => auth()->id(),
        ]);

        TournamentMatch::where('tournament_type', $type)->delete();
        TournamentSetting::set($type . '_published', false);

        \Illuminate\Support\Facades\Cache::forget('tournament_setting_' . $type . '_published');

        return back()->with('success', ucfirst($type) . ' bracket moved to Backup Brackets. Registration is now open.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // BACKUP BRACKETS (snapshots bin)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * List all snapshots (no blob data — metadata only).
     */
    public function listSnapshots()
    {
        $snapshots = \App\Models\BracketSnapshot::select('id', 'snapshot_label', 'tournament_type', 'deleted_by', 'created_at')
            ->latest()
            ->get()
            ->map(function ($s) {
                $s->deleted_by_name = \App\Models\User::find($s->deleted_by)?->name ?? 'Admin';
                return $s;
            });

        return Inertia::render('Admin/Brackets/Snapshots', [
            'snapshots' => $snapshots,
        ]);
    }

    /**
     * Restore a bracket from a snapshot — re-inserts all match rows, re-publishes.
     */
    public function restoreSnapshot($id)
    {
        $snapshot = \App\Models\BracketSnapshot::findOrFail($id);
        $type     = $snapshot->tournament_type;

        // Clear any existing matches for this type first
        TournamentMatch::where('tournament_type', $type)->delete();

        foreach ($snapshot->snapshot_data as $row) {
            // Don't restore auto-generated columns
            unset($row['id'], $row['created_at'], $row['updated_at']);
            TournamentMatch::create($row);
        }

        TournamentSetting::set($type . '_published', true);
        \Illuminate\Support\Facades\Cache::forget('tournament_setting_' . $type . '_published');

        return back()->with('success', ucfirst($type) . ' bracket restored and re-published successfully!');
    }

    /**
     * Permanently delete a snapshot — no recovery after this.
     */
    public function permanentlyDeleteSnapshot($id)
    {
        \App\Models\BracketSnapshot::findOrFail($id)->delete();
        return back()->with('success', 'Snapshot permanently deleted.');
    }
}

