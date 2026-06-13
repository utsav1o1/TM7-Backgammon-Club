<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
</script>

<template>
    <Head title="Welcome | Galaxygammon Backgammon Club" />

    <PublicLayout>
        <!-- Full Page Banner Background with Dark Overlay -->
        <div class="fixed inset-0 -z-20 w-full h-full">
            <!-- The actual background image -->
            <img src="/images/main_banner.webp" alt="Background Banner" class="w-full h-full object-cover" @error="$event.target.src='/images/bnner.jpeg'; $event.target.onerror=null;" />
            <!-- The dark overlay (lightened from /80 to /60) -->
            <div class="absolute inset-0 bg-tm7-darker/60"></div>
        </div>

        <!-- Hero Section -->
        <div class="relative py-24 sm:py-32 overflow-hidden flex flex-col items-center min-h-[80vh] justify-center w-full">

            <!-- Glowing Orb (kept for visual accent) -->
            <div class="absolute -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80 w-full flex justify-center" aria-hidden="true">
                <div class="aspect-[1108/632] w-[69.25rem] bg-gradient-to-r from-tm7-gold to-yellow-600 opacity-20" style="clip-path: polygon(73.6% 51.7%, 91.7% 11.8%, 100% 46.4%, 97.4% 82.2%, 92.5% 84.9%, 75.7% 64%, 55.3% 47.5%, 46.5% 49.4%, 45% 62.9%, 50.3% 87.2%, 21.3% 64.1%, 0.1% 100%, 5.4% 51.1%, 21.4% 63.9%, 58.9% 0.2%, 73.6% 51.7%)"></div>
            </div>

            <div class="mx-auto max-w-7xl px-6 lg:px-8 text-center mt-12 sm:mt-0 relative z-10 w-full">
                
            
            

                <h1 class="text-4xl font-serif tracking-tight text-white sm:text-6xl uppercase tracking-widest drop-shadow-lg">
                   
                    <span class="text-tm7-gold"></span>
                </h1>
                
                <!-- Motto Text Fallback if banner image is missing or just additional styling -->
                <p class="mt-6 text-lg tracking-widest text-tm7-gold max-w-2xl mx-auto font-light uppercase">
                   
                </p>

            </div>
        </div>
    </PublicLayout>
</template>
