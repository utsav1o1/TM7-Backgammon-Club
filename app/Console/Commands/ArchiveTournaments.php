<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TournamentMatch;
use App\Models\Team;
use App\Models\IndividualEntry;
use App\Models\TournamentWinner;
use App\Models\TournamentSetting;
use Carbon\Carbon;

class ArchiveTournaments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tournament:archive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically archives completed tournaments after 24 hours of the final match';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $types = ['team', 'individual'];

        foreach ($types as $type) {
            // Check if tournament is published
            if (!TournamentSetting::get("{$type}_published", false)) {
                continue;
            }

            // Find the last concluded match (highest round, highest ID)
            $lastMatch = TournamentMatch::where('tournament_type', $type)
                ->whereIn('status', ['completed', 'finalized'])
                ->orderBy('round', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            // If a finale exists and has a winner
            if ($lastMatch && $lastMatch->winner_id) {
                // Check if there are NO pending matches at all, ensuring tournament is actually over
                $pendingMatches = TournamentMatch::where('tournament_type', $type)
                    ->where('status', 'pending')
                    ->count();

                if ($pendingMatches === 0) {
                    // Check if it's been 24 hours since the match was updated (won)
                    if ($lastMatch->updated_at <= Carbon::now()->subHours(24)) {
                        $this->info("Archiving {$type} tournament...");

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

                        $this->info("Successfully archived and reset {$type} tournament.");
                    }
                }
            }
        }
    }
}
