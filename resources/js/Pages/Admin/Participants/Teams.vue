<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    teams: Array,
});

const deleteForm = useForm({});

const deleteTeam = (id) => {
    if (confirm('Are you sure you want to remove this team? This will also remove their matches.')) {
        deleteForm.delete(route('admin.destroy.team', id), {
            preserveScroll: true,
        });
    }
};

const admin_cc_email = "utsavkarki244@gmail.com";

const getMailtoLink = (team) => {
    const subject = encodeURIComponent(`Tournament Update: ${team.name}`);
    const body = encodeURIComponent(`Hello Team ${team.name},\n\n`);
    return `mailto:${team.email}?cc=${admin_cc_email}&subject=${subject}&body=${body}`;
};

const expandedTeam = ref(null);
const toggleMembers = (id) => {
    expandedTeam.value = expandedTeam.value === id ? null : id;
};
</script>

<template>

    <Head title="Team Management" />

    <AuthenticatedLayout>
        <template #header>Team Participants</template>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-900">Registered Squads ({{ teams.length }})</h3>
                <div class="text-xs text-gray-500 italic">Click team name to contact</div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-gray-50 text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">
                            <th class="px-6 py-4">Team Name</th>
                            <th class="px-6 py-4">Captain</th>
                            <th class="px-6 py-4">Members</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template v-for="team in teams" :key="team.id">
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <a :href="getMailtoLink(team)"
                                        class="text-sm font-bold text-gray-900 hover:text-tm7-gold transition-colors decoration-0">
                                        {{ team.name }}
                                    </a>
                                    <p class="text-[10px] text-gray-500">{{ team.email }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-700 font-medium">{{ team.captain_nickname
                                            }}</span>
                                        <span v-if="team.co_captain_nickname" class="text-[10px] text-gray-400">Co: {{
                                            team.co_captain_nickname }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <button v-if="team.members && team.members.length" @click="toggleMembers(team.id)"
                                        class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                            class="w-3.5 h-3.5">
                                            <path
                                                d="M10 9a3 3 0 100-6 3 3 0 000 6zM6 8a2 2 0 11-4 0 2 2 0 014 0zM1.49 15.326a.78.78 0 01-.358-.442 3 3 0 014.308-3.516 6.484 6.484 0 00-1.905 3.959c-.023.222-.014.442.025.654a4.97 4.97 0 01-2.07-.655zM16.44 15.98a4.97 4.97 0 002.07-.654.78.78 0 00.357-.442 3 3 0 00-4.308-3.517 6.484 6.484 0 011.907 3.96 2.32 2.32 0 01-.026.654zM18 8a2 2 0 11-4 0 2 2 0 014 0zM5.304 16.19a.844.844 0 01-.277-.71 5 5 0 019.947 0 .843.843 0 01-.277.71A6.975 6.975 0 0110 18a6.974 6.974 0 01-4.696-1.81z" />
                                        </svg>
                                        {{ team.members.length }} member{{ team.members.length !== 1 ? 's' : '' }}
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                                            class="w-3 h-3 transition-transform"
                                            :class="expandedTeam === team.id ? 'rotate-180' : ''">
                                            <path fill-rule="evenodd"
                                                d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <span v-else class="text-xs text-gray-400 italic">No members</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button @click="deleteTeam(team.id)"
                                        class="text-xs font-bold text-red-500 hover:text-red-700 transition-colors uppercase tracking-wider">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                            <!-- Expanded members row -->
                            <tr v-if="expandedTeam === team.id && team.members && team.members.length"
                                class="bg-indigo-50/50">
                                <td colspan="4" class="px-6 py-3">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                        <div v-for="(member, i) in team.members" :key="i"
                                            class="flex items-center gap-2 bg-white rounded-lg px-3 py-2 border border-gray-100">
                                            <span
                                                class="w-5 h-5 rounded-full bg-indigo-100 text-indigo-600 text-[10px] font-bold flex items-center justify-center flex-shrink-0">{{
                                                i + 1 }}</span>
                                            <div class="min-w-0">
                                                <p class="text-xs font-semibold text-gray-800 truncate">{{ member.name
                                                    }}</p>
                                                <p class="text-[10px] text-gray-400 truncate">{{ member.email }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr v-if="teams.length === 0">
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">No teams registered yet.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
