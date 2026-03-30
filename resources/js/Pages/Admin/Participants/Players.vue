<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    players: Array,
});

const deleteForm = useForm({});

const deletePlayer = (id) => {
    if (confirm('Are you sure you want to remove this player from the platform?')) {
        deleteForm.delete(route('admin.destroy.player', id), {
            preserveScroll: true,
        });
    }
};

</script>

<template>
    <Head title="Platform Players" />

    <AuthenticatedLayout>
        <template #header>Platform Players</template>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-900">Total Users ({{ players.length }})</h3>
                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Master Directory</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">
                            <th class="px-6 py-4">Nickname</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Timezone</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="player in players" :key="player.id" class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ player.nickname }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ player.email }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 font-mono">{{ player.timezone }}</td>
                            <td class="px-6 py-4 text-right">
                                <button 
                                    @click="deletePlayer(player.id)"
                                    class="text-xs font-bold text-red-500 hover:text-red-700 transition-colors uppercase tracking-wider"
                                >
                                    Delete User
                                </button>
                            </td>
                        </tr>
                        <tr v-if="players.length === 0">
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">No players in the directory.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
