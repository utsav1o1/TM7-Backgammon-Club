<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
defineProps({
    rules: String,
});
</script>

<template>
  <Head title="Individual Rules | TM7 Club" />

  <PublicLayout>
    <div class="max-w-4xl mx-auto px-6 py-12 lg:py-24">
      <div class="mb-12 text-center">
        <h1 class="text-4xl font-serif font-bold text-white mb-4 tracking-tight">Individual Backgammon Tournament</h1>
        <p class="text-lg text-tm7-gold">Official Rules</p>
      </div>

      <div class="bg-tm7-light/20 backdrop-blur-md rounded-2xl p-8 border border-white/10 shadow-xl space-y-6 text-gray-300">
        
        <div v-if="rules" v-html="rules" class="prose prose-invert max-w-none"></div>

        <template v-else>
          <section>
          <h2 class="text-2xl font-serif font-bold text-tm7-gold mb-3">1. Match Format</h2>
          <ul class="list-disc pl-5 space-y-2">
            <li><strong>The "Best of 5" Series:</strong> Each round consists of a series between two players.</li>
            <li><strong>Individual Matches:</strong> You will play up to five separate matches against your opponent.</li>
            <li><strong>Winning the Round:</strong> The first player to win 3 matches (each played to 5 points) wins the round and advances.</li>
          </ul>
        </section>

        <section>
          <h2 class="text-2xl font-serif font-bold text-tm7-gold mb-3 mt-6">2. Gameplay Settings</h2>
          <ul class="list-disc pl-5 space-y-2">
            <li><strong>Match Length:</strong> Every match in the series is played to 5 points.</li>
            <li><strong>The Crawford Rule:</strong> This rule is in effect. In any 5-point match, when a player reaches 4 points, the "Crawford Game" is played (the doubling cube is frozen for that single game).</li>
            <li><strong>The Doubling Cube:</strong> The cube is active for all games (except during the Crawford Game).</li>
            <li><strong>Automatic Doubles/Beavers:</strong> These are not used in standard tournament match play.</li>
          </ul>
        </section>

        <section>
          <h2 class="text-2xl font-serif font-bold text-tm7-gold mb-3 mt-6">3. Technical & Connection Rules</h2>
          <ul class="list-disc pl-5 space-y-2">
            <li><strong>Disconnections:</strong> If a player disconnects during a match, they have 5 minutes to reconnect. If they fail to return, they forfeit that specific 5-point match.</li>
            <li><strong>Reporting Results:</strong> The winner of the series should take a screenshot of the final series score (e.g., 3-0, 3-1, or 3-2) and submit it to the tournament director.</li>
          </ul>
        </section>

        <section>
          <h2 class="text-2xl font-serif font-bold text-tm7-gold mb-3 mt-6">4. Fair Play & Conduct</h2>
          <ul class="list-disc pl-5 space-y-2">
            <li><strong>No Software Assistance:</strong> The use of any backgammon engines, AI, or analysis tools during live play is strictly prohibited.</li>
            <li><strong>Sportsmanship:</strong> Players are expected to maintain a polite and professional demeanor in the game chat.</li>
          </ul>
        </section>

        <section>
          <h2 class="text-2xl font-serif font-bold text-tm7-gold mb-3 mt-6">5. Progression</h2>
          <ul class="list-disc pl-5 space-y-2">
            <li>Once a player reaches 3 match wins, the series ends immediately.</li>
            <li>The winner moves to the next bracket, and the loser is eliminated (unless the tournament is "Double Elimination").</li>
          </ul>
        </section>

        </template>
      </div>
    </div>
  </PublicLayout>
</template>
