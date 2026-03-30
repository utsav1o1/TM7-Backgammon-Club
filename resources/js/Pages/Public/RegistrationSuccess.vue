<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
</script>

<template>
  <Head title="Registration Successful | TM7 Club" />

  <PublicLayout>
    <div class="max-w-2xl mx-auto px-6 py-16 lg:py-32">
      <div class="bg-tm7-light/20 backdrop-blur-md rounded-2xl p-12 text-center shadow-2xl border border-white/10 relative overflow-hidden">
        
        <!-- Decoration -->
        <div class="absolute -top-32 -left-32 w-64 h-64 bg-green-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 w-24 h-24 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-8 text-green-400 ring-4 ring-green-500/30">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-12 h-12">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <h2 class="relative z-10 text-4xl font-serif font-black text-white mb-4 tracking-tight">Registration Successful!</h2>
        
        <p class="relative z-10 text-gray-300 mb-2 text-lg">
            Thank you for registering with TM7 Backgammon Club.
        </p>
        <p class="relative z-10 text-gray-400 mb-10">
            Your entry has been securely received and recorded. You will be matched up in the brackets shortly. Good luck and play well!
        </p>

        <div class="relative z-10 flex flex-col sm:flex-row items-center justify-center gap-4">
            <Link :href="route('bracket.team')" class="inline-flex items-center px-8 py-3.5 bg-gradient-to-r from-tm7-gold to-yellow-600 text-tm7-darker font-black rounded-full transition-transform hover:scale-105 active:scale-95 shadow-xl hover:shadow-yellow-500/20 uppercase tracking-widest text-sm">
                View Team Bracket
            </Link>
            
            <Link :href="route('bracket.individual')" class="inline-flex items-center px-8 py-3.5 bg-white/5 text-white font-bold rounded-full border border-white/10 transition-all hover:bg-white/10 active:scale-95 shadow-xl hover:shadow-black/20 uppercase tracking-widest text-sm">
                View Solo Bracket
            </Link>
        </div>
        
      </div>
    </div>
  </PublicLayout>
</template>
