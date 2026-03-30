<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
</script>

<template>
  <Head title="Terms & Conditions | TM7 Club" />

  <PublicLayout>
    <div class="max-w-4xl mx-auto px-6 py-12 lg:py-24">
      <div class="mb-12 text-center">
        <h1 class="text-4xl font-serif font-bold text-white mb-4 tracking-tight">Terms & Conditions</h1>
        <p class="text-lg text-gray-400">Our code of conduct and global platform expectations.</p>
      </div>

      <div class="space-y-8">
        <div class="bg-tm7-light/20 backdrop-blur-md rounded-2xl p-8 border border-white/10 shadow-xl scroll-mt-24">
          <h2 class="text-2xl font-serif font-bold text-tm7-gold mb-4">Platform Agreement</h2>
          <p class="text-gray-300 leading-relaxed mb-4">By participating in the TM7 Backgammon Club tournaments, you agree to abide by our code of conduct and fair play policies. All participants must be at least 18 years of age or have explicit parental consent prior to registration.</p>
          
          <h2 class="text-2xl font-serif font-bold text-tm7-gold mb-4 mt-8">Our Values</h2>
          <p class="text-gray-300 leading-relaxed">We believe in fairness, respect, and the joy of the game. Our community is built on mutual trust and the shared passion for backgammon. Harassment or unsportsmanlike conduct inside or outside of gameplay will result in immediate disqualification.</p>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>
