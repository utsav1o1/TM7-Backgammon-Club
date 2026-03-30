<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_type', 'round', 'match_number', 'participant1_id', 
        'participant2_id', 'winner_id', 'status',
        'p1_claimed_win', 'p2_claimed_win', 'is_conflict'
    ];

    /**
     * Deterministically move a winner to the next round box.
     */
    public static function autoAdvance($match)
    {
        if (!in_array($match->status, ['completed', 'finalized']) || !$match->winner_id) {
            // If match is no longer completed (e.g. conflict), clear the next slot
            self::clearNextSlot($match);
            return;
        }

        // Ensure we don't advance if this is the final match of the tournament
        $matchesInCurrentRound = self::where('tournament_type', $match->tournament_type)
            ->where('round', $match->round)
            ->count();

        if ($matchesInCurrentRound === 1) {
            return; // This is the finale! No further rounds to generate.
        }

        $nextRound = $match->round + 1;
        $nextMatchNumber = floor($match->match_number / 2);
        $slot = $match->match_number % 2; // 0 for p1, 1 for p2

        $nextMatch = self::firstOrCreate([
            'tournament_type' => $match->tournament_type,
            'round' => $nextRound,
            'match_number' => $nextMatchNumber
        ], [
            'status' => 'pending'
        ]);

        if ($slot === 0) {
            $nextMatch->update(['participant1_id' => $match->winner_id]);
        } else {
            $nextMatch->update(['participant2_id' => $match->winner_id]);
        }
    }

    public static function clearNextSlot($match)
    {
        $nextRound = $match->round + 1;
        $nextMatchNumber = floor($match->match_number / 2);
        $slot = $match->match_number % 2;

        $nextMatch = self::where('tournament_type', $match->tournament_type)
            ->where('round', $nextRound)
            ->where('match_number', $nextMatchNumber)
            ->first();

        if ($nextMatch) {
            if ($slot === 0) {
                $nextMatch->update(['participant1_id' => null]);
            } else {
                $nextMatch->update(['participant2_id' => null]);
            }
            
            // If the next match is now empty, we could delete it, 
            // but keeping it 'pending' with nulls is safer for the bracket UI.
        }
    }
}
