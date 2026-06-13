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
        'p1_claimed_win', 'p2_claimed_win', 'is_conflict', 'notes'
    ];

    /**
     * Deterministically move a winner to the next round box.
     */
    public static function autoAdvance($match)
    {
        if (!in_array($match->status, ['completed', 'finalized']) || !$match->winner_id) {
            self::clearNextSlot($match);
            return;
        }

        $matchesInCurrentRound = self::where('tournament_type', $match->tournament_type)
            ->where('round', $match->round)
            ->count();

        if ($matchesInCurrentRound === 1) {
            return;
        }

        $nextRound      = $match->round + 1;
        $nextMatchNumber = floor($match->match_number / 2);
        $slot           = $match->match_number % 2;

        $nextMatch = self::firstOrCreate([
            'tournament_type' => $match->tournament_type,
            'round'           => $nextRound,
            'match_number'    => $nextMatchNumber,
        ], [
            'status' => 'pending',
        ]);

        if ($slot === 0) {
            $nextMatch->update(['participant1_id' => $match->winner_id]);
        } else {
            $nextMatch->update(['participant2_id' => $match->winner_id]);
        }
    }

    public static function clearNextSlot($match)
    {
        $nextRound       = $match->round + 1;
        $nextMatchNumber = floor($match->match_number / 2);
        $slot            = $match->match_number % 2;

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
        }
    }

    /**
     * Recursively clear the winner slot of all downstream matches
     * when a match result is reversed or participants are swapped.
     * Returns the count of matches that were cleared.
     */
    public static function cascadeClearDownstream(self $match): int
    {
        $cleared = 0;

        $nextRound       = $match->round + 1;
        $nextMatchNumber = floor($match->match_number / 2);
        $slot            = $match->match_number % 2;

        $nextMatch = self::where('tournament_type', $match->tournament_type)
            ->where('round', $nextRound)
            ->where('match_number', $nextMatchNumber)
            ->first();

        if (!$nextMatch) {
            return $cleared;
        }

        // Clear the slot this match feeds into
        if ($slot === 0) {
            $nextMatch->participant1_id = null;
        } else {
            $nextMatch->participant2_id = null;
        }

        // If the next match had a result, clear it too and keep cascading
        if ($nextMatch->winner_id || in_array($nextMatch->status, ['completed', 'finalized', 'conflict'])) {
            $nextMatch->winner_id      = null;
            $nextMatch->p1_claimed_win = false;
            $nextMatch->p2_claimed_win = false;
            $nextMatch->is_conflict    = false;
            $nextMatch->status         = 'pending';
            $cleared++;

            $nextMatch->save();
            $cleared += self::cascadeClearDownstream($nextMatch);
        } else {
            $nextMatch->save();
        }

        return $cleared;
    }

    /**
     * Snapshot all matches for a bracket type into a plain array for archiving.
     */
    public static function snapshotBracket(string $type): array
    {
        return self::where('tournament_type', $type)
            ->orderBy('round')
            ->orderBy('match_number')
            ->get()
            ->toArray();
    }
}
