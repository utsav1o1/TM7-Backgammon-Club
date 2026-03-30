<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineProps({
    winners: Array
});

const groupedWinners = (winners) => {
    const groups = {};
    winners.forEach(winner => {
        const key = `${winner.month} ${winner.year}`;
        if (!groups[key]) groups[key] = [];
        groups[key].push(winner);
    });
    return groups;
};
</script>

<template>
  <Head title="Hall of Fame | TM7 Club" />

  <PublicLayout>
    <div class="max-w-7xl mx-auto px-6 py-12 lg:py-24">
      
      <div class="mb-16 text-center">
        <h1 class="text-4xl font-serif font-bold text-white mb-4 tracking-tight">Hall of Fame</h1>
        <p class="text-lg text-gray-400">Celebrating our tournament champions throughout the seasons.</p>
      </div>

      <div class="space-y-16">
        <div v-for="(group, date) in groupedWinners(winners)" :key="date" class="relative">
          <div class="flex items-center gap-4 mb-8">
            <h2 class="text-2xl font-serif font-bold text-tm7-gold shrink-0">{{ date }}</h2>
            <div class="h-px bg-white/10 w-full"></div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div v-for="winner in group" :key="winner.id" class="bg-tm7-light/20 backdrop-blur-md rounded-2xl p-8 border border-white/10 shadow-xl relative overflow-hidden group hover:border-tm7-gold/50 transition-all">
                <!-- Decoration -->
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-tm7-gold/10 rounded-full blur-2xl group-hover:bg-tm7-gold/20 transition-all"></div>
                
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-tm7-gold to-yellow-600 flex items-center justify-center text-tm7-darker text-2xl">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8">
                            <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-tm7-gold uppercase tracking-widest mb-1">{{ winner.type === 'team' ? 'Team Champion' : 'Solo Champion' }}</div>
                        <h3 class="text-2xl font-serif font-bold text-white">{{ winner.name }}</h3>
                    </div>
                </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="winners.length === 0" class="text-center py-20 bg-tm7-light/10 rounded-2xl border border-dashed border-white/10">
        <p class="text-gray-500 italic">The journey has just begun. Our first champions will be enshrined here soon.</p>
      </div>

    </div>
  </PublicLayout>
</template>
