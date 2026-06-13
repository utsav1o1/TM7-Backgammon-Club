<?php

namespace App\Http\Controllers;

use App\Mail\ConflictDetected;
use App\Models\Team;
use App\Models\Player;
use App\Models\IndividualEntry;
use App\Models\TournamentMatch;
use App\Models\TournamentWinner;
use App\Models\TournamentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class TournamentController extends Controller
{
    public function teams()
    {
        $teams = Team::select('id', 'name', 'email', 'captain_nickname', 'co_captain_nickname')->get();
        return Inertia::render('Public/TeamsList', [
            'teams' => $teams
        ]);
    }

    public function players()
    {
        $players = Player::select('id', 'nickname', 'email', 'timezone', 'created_at')->get();
        return Inertia::render('Public/PlayersList', [
            'players' => $players
        ]);
    }

    public function teamBracket()
    {
        $isPublished = TournamentSetting::get('team_published', false);

        if (!$isPublished) {
            $matches = collect([]);
        } else {
            $matches = TournamentMatch::where('tournament_type', 'team')->get();
            $teams = Team::all()->keyBy('id');
            
            foreach ($matches as $match) {
                $match->p1 = $teams->get($match->participant1_id);
                $match->p2 = $teams->get($match->participant2_id);
            }
        }

        return Inertia::render('Public/TeamBracket', [
            'matches' => $matches,
            'is_published' => $isPublished
        ]);
    }

    public function individualBracket()
    {
        $isPublished = TournamentSetting::get('individual_published', false);

        if (!$isPublished) {
            $matches = collect([]);
        } else {
            $matches = TournamentMatch::where('tournament_type', 'individual')->get();
            $players = Player::all()->keyBy('id');
            
            foreach ($matches as $match) {
                $match->p1 = $players->get($match->participant1_id);
                $match->p2 = $players->get($match->participant2_id);
            }
        }

        return Inertia::render('Public/IndividualBracket', [
            'matches' => $matches,
            'is_published' => $isPublished
        ]);
    }

    public function declareWinner(Request $request, $id)
    {
        $match = TournamentMatch::findOrFail($id);
        if ($match->status === 'finalized') {
            return back()->with('error', 'This match has been finalized by an admin.');
        }

        $participant_id = $request->participant_id;

        if ($match->participant1_id == $participant_id) {
            $match->p1_claimed_win = true;
        } elseif ($match->participant2_id == $participant_id) {
            $match->p2_claimed_win = true;
        }

        // Resolve status based on claims
        if ($match->p1_claimed_win && $match->p2_claimed_win) {
            $match->winner_id = null;
            $match->status = 'conflict';
            $match->is_conflict = true;

            // Automatically notify admin via email (queued — won't block or cause 500 on SMTP failure)
            try {
                $adminEmail = TournamentSetting::get('admin_cc_email', config('mail.from.address'));

                if ($match->tournament_type === 'team') {
                    $p1Name = Team::find($match->participant1_id)->name ?? 'Team 1';
                    $p2Name = Team::find($match->participant2_id)->name ?? 'Team 2';
                } else {
                    $p1Name = Player::find($match->participant1_id)->nickname ?? 'Player 1';
                    $p2Name = Player::find($match->participant2_id)->nickname ?? 'Player 2';
                }

                Mail::to($adminEmail)->queue(
                    new ConflictDetected($match, $p1Name, $p2Name, $match->tournament_type)
                );
            } catch (\Exception $e) {
                \Log::error('Conflict notification email failed: ' . $e->getMessage());
            }
        } elseif ($match->p1_claimed_win || $match->p2_claimed_win) {
            $match->winner_id = $match->p1_claimed_win ? $match->participant1_id : $match->participant2_id;
            $match->status = 'completed';
            $match->is_conflict = false;
        } else {
            $match->winner_id = null;
            $match->status = 'pending';
            $match->is_conflict = false;
        }
        $match->save();

        // Auto-advance logic
        TournamentMatch::autoAdvance($match);

        return back()->with('success', 'Winner declaration recorded and bracket updated!');
    }

    public function hallOfFame()
    {
        $winners = TournamentWinner::orderBy('year', 'desc')->orderBy('created_at', 'desc')->get();
        return Inertia::render('Public/HallOfFame', [
            'winners' => $winners
        ]);
    }
}
