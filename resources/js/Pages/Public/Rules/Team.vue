<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
defineProps({
    rules: String,
});
</script>

<template>
  <Head title="Team Tournament Rules | TM7 Club" />

  <PublicLayout>
    <div class="max-w-4xl mx-auto px-6 py-12 lg:py-24">
      <div class="mb-12 text-center">
        <h1 class="text-4xl font-serif font-bold text-white mb-4 tracking-tight">Backgammon Team Tournament</h1>
        <p class="text-lg text-tm7-gold">Official Rules</p>
      </div>

      <div class="bg-tm7-light/20 backdrop-blur-md rounded-2xl p-8 border border-white/10 shadow-xl space-y-6 text-gray-300">
        
        <div v-if="rules" v-html="rules" class="prose prose-invert max-w-none"></div>
        
        <template v-else>
          <section>
          <h2 class="text-2xl font-serif font-bold text-tm7-gold mb-3">1. Team Structure & Match Format</h2>
          <ul class="list-disc pl-5 space-y-2">
            <li><strong>The "Best of 5" Rule:</strong> Each team consists of 5 players.</li>
            <li><strong>Individual Matches:</strong> Each player is paired against an opponent from the opposing team for a single 5-point match.</li>
            <li><strong>Winning the Round:</strong> The first team to win 3 individual matches wins the round and advances to the next stage of the tournament.</li>
          </ul>
        </section>

        <section>
          <h2 class="text-2xl font-serif font-bold text-tm7-gold mb-3 mt-6">2. Gameplay Settings</h2>
          <ul class="list-disc pl-5 space-y-2">
            <li><strong>Match Length:</strong> All individual games are played to 5 points.</li>
            <li><strong>The Crawford Rule:</strong> This rule is in effect. In any 5-point match, when a player reaches 4 points, the "Crawford Game" is played (no doubling cube allowed for that single game).</li>
            <li><strong>The Doubling Cube:</strong> The cube is active for all games (except the Crawford Game).</li>
            <li><strong>Automatic Doubles/Beavers:</strong> These are not allowed in tournament match play.</li>
          </ul>
        </section>

        <section>
          <h2 class="text-2xl font-serif font-bold text-tm7-gold mb-3 mt-6">3. Technical & Connection Rules</h2>
          <ul class="list-disc pl-5 space-y-2">
            <li><strong>Match Order:</strong> Team captains should submit their player lineup (Player 1 through Player 5) before the round starts. Matches should ideally be played simultaneously.</li>
            <li><strong>Disconnections:</strong> If a player disconnects during their 5-point match, they have 5 minutes to reconnect. If they cannot return, that specific 5-point match is forfeited to the opponent.</li>
            <li><strong>Reporting:</strong> Once a match finishes, the winner must take a screenshot of the final score and send it to their Team Captain.</li>
          </ul>
        </section>

        <section>
          <h2 class="text-2xl font-serif font-bold text-tm7-gold mb-3 mt-6">4. Fair Play & Conduct</h2>
          <ul class="list-disc pl-5 space-y-2">
            <li><strong>No Outside Help:</strong> Players must play their own matches. The use of backgammon engines (like Extreme Gammon) or "coaching" from teammates during a live match is strictly prohibited.</li>
            <li><strong>Sportsmanship:</strong> All participants are expected to be respectful in the global and match chats.</li>
          </ul>
        </section>

        <section>
          <h2 class="text-2xl font-serif font-bold text-tm7-gold mb-3 mt-6">5. Tie-Breakers (Optional)</h2>
          <ul class="list-disc pl-5 space-y-2">
            <li>Since this is a "Best of 5" format, there are no draws. A team will always win either 3-0, 3-1, or 3-2.</li>
            <li>For ranking purposes, you can track the "Game Spread" (the total number of points won across all 5 matches) to determine seeding for future rounds.</li>
          </ul>
        </section>

        <div class="mt-8 pt-6 border-t border-white/10 text-sm text-gray-400 italic">
          <strong>Tournament Tip:</strong> Since the first team to reach 3 wins takes the round, you can decide if the remaining 2 matches should be finished for "fun" or stopped immediately to save time. Most competitive tournaments stop once the 3rd win is secured.
        </div>

        </template>
      </div>
    </div>
  </PublicLayout>
</template>
