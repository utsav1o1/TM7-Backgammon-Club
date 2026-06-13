<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

defineProps({
    teams: Array
});
</script>

<template>
  <Head title="Registered Teams | Galaxygammon Club" />

  <PublicLayout>
    <div class="max-w-7xl mx-auto px-6 py-12 lg:py-24">
      
      <div class="mb-10 text-center">
        <h2 class="text-3xl font-serif font-bold text-white mb-3 tracking-wide">Registered Teams</h2>
        <p class="text-gray-400">View the current roster of teams competing in the upcoming tournament.</p>
      </div>

      <div class="mt-8 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow ring-1 ring-white/10 sm:rounded-lg">
              <table class="min-w-full divide-y divide-white/10">
                <thead class="bg-tm7-light/50 backdrop-blur-sm">
                  <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-6">Team Name</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Captain</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-white">Co-Captain</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-white/5 bg-tm7-darker/60 backdrop-blur-sm">
                  <tr v-for="team in teams" :key="team.id" class="hover:bg-white/5 transition-colors">
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-tm7-gold sm:pl-6">
                      <a :href="'mailto:' + team.email + '?cc=' + $page.props.admin_cc_email" class="hover:underline transition-all" title="Email Team Contact">
                        {{ team.name }}
                      </a>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-300">{{ team.captain_nickname }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-400">{{ team.co_captain_nickname || '-' }}</td>
                  </tr>

                  <tr v-if="teams.length === 0">
                    <td colspan="3" class="whitespace-nowrap py-8 text-center text-sm text-gray-500">
                      No teams have registered yet. 
                      <Link :href="route('signup.team')" class="text-tm7-accent hover:text-white ml-2 underline">Be the first!</Link>
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
