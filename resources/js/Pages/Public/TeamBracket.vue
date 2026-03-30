<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Bracket from 'vue-tournament-bracket';
import { computed } from 'vue';
import { useToast } from 'vue-toastification';

const props = defineProps({
    matches: Array,
    is_published: Boolean
});

const toast = useToast();
const declareForm = useForm({
    participant_id: null
});

const declareWinner = (matchId, participantId) => {
    declareForm.participant_id = participantId;
    declareForm.post(route('matches.declare-winner', matchId), {
        onSuccess: () => toast.success('You have declared yourself the winner! Admin will verify soon.'),
        preserveScroll: true
    });
};

// Since the backend might not have seeded games yet, we provide a placeholder empty structure if none exist
const tournamentData = computed(() => {
    if (!props.matches || props.matches.length === 0) {
        return [
            { games: [{ player1: { name: 'TBD' }, player2: { name: 'TBD' } }] }
        ];
    }
    
    // Find highest round and max games in round 1
    let round1Matches = props.matches.filter(m => m.round === 1);
    
    let currentGamesCount = round1Matches.length;
    let powerOf2 = 1;
    while (powerOf2 < currentGamesCount) {
        powerOf2 *= 2;
    }
    
    // Create an empty perfectly-sized binary tree
    let roundsList = [];
    let expectedGames = powerOf2;
    
    while (expectedGames >= 1) {
        let emptyGames = [];
        for (let i = 0; i < expectedGames; i++) {
            emptyGames.push({ 
                player1: { name: expectedGames === powerOf2 ? 'BYE' : 'TBD' }, 
                player2: { name: expectedGames === powerOf2 ? 'BYE' : 'TBD' } 
            });
        }
        roundsList.push({ games: emptyGames });
        expectedGames = expectedGames / 2;
    }

    // Now populate the tree using match_number
    props.matches.forEach(match => {
        let rIndex = match.round - 1;
        let mIndex = match.match_number;
        
        if (roundsList[rIndex] && roundsList[rIndex].games[mIndex]) {
            roundsList[rIndex].games[mIndex] = {
                player1: { 
                    name: match.p1 ? (match.p1.player_name || match.p1.name) : 'BYE', 
                    email: match.p1 ? match.p1.email : null,
                    id: match.participant1_id,
                    winner: match.winner_id && match.winner_id == match.participant1_id,
                    claimed: match.p1_claimed_win,
                    match_id: match.id,
                    match_status: match.status,
                    is_conflict: match.is_conflict
                },
                player2: { 
                    name: match.p2 ? (match.p2.player_name || match.p2.name) : 'BYE', 
                    email: match.p2 ? match.p2.email : null,
                    id: match.participant2_id,
                    winner: match.winner_id && match.winner_id == match.participant2_id,
                    claimed: match.p2_claimed_win,
                    match_id: match.id,
                    match_status: match.status,
                    is_conflict: match.is_conflict
                }
            };
        }
    });

    return roundsList;
});

</script>

<template>
  <Head title="Team Bracket | TM7 Club" />

  <PublicLayout>
    <div class="max-w-7xl mx-auto px-6 py-12 lg:py-24 overflow-x-hidden">
      
      <div class="mb-10 text-center relative z-10">
        <h2 class="text-3xl font-serif font-bold text-white mb-3 tracking-wide">Team Tournament Bracket</h2>
        <p class="text-gray-400">Follow the progress of the global team knockout event.</p>
      </div>

      <div class="bg-tm7-light/20 backdrop-blur-md rounded-2xl p-6 sm:p-12 border border-white/5 shadow-2xl overflow-x-auto bracket-container">
        
        <div v-if="!is_published" class="text-center py-10">
            <div class="inline-block px-4 py-2 bg-tm7-gold/20 text-tm7-gold rounded-full border border-tm7-gold/30 text-sm font-medium mb-8 animate-pulse">
                Registration is currently open. Bracket will be generated soon.
            </div>
            
            <div class="min-w-[800px] flex justify-center opacity-40 grayscale pointer-events-none">
                 <Bracket :rounds="tournamentData">
                    <template #player="{ player }">
                        <div class="px-4 py-2 bg-tm7-darker border border-white/10 rounded-md text-gray-300 shadow-md min-w-[150px] text-center font-medium">
                            {{ player.name || 'TBD' }}
                        </div>
                    </template>
                 </Bracket>
            </div>
        </div>

        <div v-else class="min-w-[800px] flex justify-center">
            <Bracket :rounds="tournamentData">
               <template #player="{ player }">
                   <div :class="['relative px-4 py-2 border rounded-md shadow-md min-w-[180px] text-center font-medium transition-all group', player.winner ? 'bg-tm7-gold text-tm7-darker border-tm7-gold' : 'bg-tm7-darker border-white/10 text-gray-300']">
                       <a v-if="player.email" :href="'mailto:' + player.email + '?cc=' + $page.props.admin_cc_email" class="hover:underline transition-all" title="Email Team Contact">
                           {{ player.name }}
                       </a>
                       <span v-else>{{ player.name }}</span>
                       
                       <!-- Winner declaration button -->
                       <div v-if="player.match_status !== 'finalized' && player.id && !player.claimed" class="absolute -right-2 -top-2 opacity-0 group-hover:opacity-100 transition-opacity z-20">
                           <button 
                                @click="declareWinner(player.match_id, player.id)"
                                class="bg-tm7-gold text-tm7-darker text-[10px] font-bold px-2 py-1 rounded-full shadow-lg border border-white/20 hover:scale-110 active:scale-95 transition-transform"
                                title="Click if this player/team won"
                           >
                                CLAIM WIN
                           </button>
                       </div>

                       <div v-if="player.claimed && !player.winner" class="absolute -right-1 -top-1">
                           <span class="flex h-3 w-3">
                               <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-tm7-gold opacity-75"></span>
                               <span class="relative inline-flex rounded-full h-3 w-3 bg-tm7-gold"></span>
                           </span>
                       </div>

                       <!-- Conflict marker -->
                       <div v-if="player.is_conflict" class="absolute -left-2 -bottom-2">
                           <span class="bg-red-500 text-white text-[8px] px-1.5 py-0.5 rounded-sm font-bold animate-pulse">CONFLICT</span>
                       </div>
                   </div>
               </template>
            </Bracket>
        </div>

      </div>

    </div>
  </PublicLayout>
</template>

<style>
/* Adjusts bracket specific styles natively injected by the library */
.vtb-wrapper {
    display: flex;
}
.vtb-item {
    color: white;
}
.bracket-container::-webkit-scrollbar {
    height: 8px;
}
.bracket-container::-webkit-scrollbar-thumb {
    background-color: #D4AF37;
    border-radius: 4px;
}
</style>
