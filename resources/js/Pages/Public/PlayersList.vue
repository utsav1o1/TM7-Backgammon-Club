<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineProps({
    players: Array
});
</script>

<template>
  <Head title="Registered Players | TM7 Club" />

  <PublicLayout>
    <div class="max-w-7xl mx-auto px-6 py-12 lg:py-24">
      
      <div class="mb-10 text-center">
        <h2 class="text-3xl font-serif font-bold text-white mb-3 tracking-wide">Registered Players</h2>
        <p class="text-gray-400">Join the elite roster of backgammon enthusiasts competing globally.</p>
      </div>

      <div class="mt-8 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow ring-1 ring-white/10 sm:rounded-lg">
              <table class="min-w-full divide-y divide-white/10">
                <thead class="bg-tm7-light/50 backdrop-blur-sm">
                  <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-6">Nickname</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Timezone</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Joined Date</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-white/5 bg-tm7-darker/60 backdrop-blur-sm">
                  <tr v-for="player in players" :key="player.id" class="hover:bg-white/5 transition-colors">
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-tm7-gold sm:pl-6">
                      <a :href="'mailto:' + player.email + '?cc=' + $page.props.admin_cc_email" class="hover:underline transition-all" title="Email Player">
                        {{ player.nickname }}
                      </a>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">{{ player.timezone }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-400">{{ new Date(player.created_at).toLocaleDateString() }}</td>
                  </tr>

                  <tr v-if="players.length === 0">
                    <td colspan="3" class="whitespace-nowrap py-8 text-center text-sm text-gray-500">
                      No players have registered yet. 
                      <Link :href="route('signup.player')" class="text-tm7-accent hover:text-white ml-2 underline">Be the first!</Link>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div>
  </PublicLayout>
</template>
