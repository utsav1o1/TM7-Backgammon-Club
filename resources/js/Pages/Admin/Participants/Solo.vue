<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    players: Array,
});

const page = usePage();
const admin_cc_email = page.props.admin_cc_email ?? 'utsavkarki244@gmail.com';

const deleteForm = useForm({});

const deletePlayer = (id) => {
    if (confirm('Are you sure you want to remove this solo player? Their bracket matches will also be removed.')) {
        deleteForm.delete(route('admin.destroy.individual', id), {
            preserveScroll: true,
        });
    }
};

const getMailtoLink = (player) => {
    const subject = encodeURIComponent(`Tournament Update: ${player.nickname}`);
    const body = encodeURIComponent(`Hello ${player.nickname},\n\n`);
    return `mailto:${player.email}?cc=${admin_cc_email}&subject=${subject}&body=${body}`;
};
</script>

<template>
    <Head title="Solo List" />

    <AuthenticatedLayout>
        <template #header>Solo Participants</template>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-900">Solo Players ({{ players.length }})</h3>
                <div class="text-xs text-gray-500 italic">Click name to contact via email</div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">
                            <th class="px-6 py-4">Nickname</th>
                            <th class="px-6 py-4">Timezone</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="player in players" :key="player.id" class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <a :href="getMailtoLink(player)" class="text-sm font-bold text-gray-900 hover:text-tm7-gold transition-colors decoration-0">
                                    {{ player.nickname }}
                                </a>
                                <p class="text-[10px] text-gray-500">{{ player.email }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-mono">{{ player.timezone }}</td>
                            <td class="px-6 py-4 text-right">
                                <button
                                    @click="deletePlayer(player.id)"
                                    class="text-xs font-bold text-red-500 hover:text-red-700 transition-colors uppercase tracking-wider"
                                >
                                    Remove
                                </button>
                            </td>
                        </tr>
                        <tr v-if="players.length === 0">
                            <td colspan="3" class="px-6 py-12 text-center text-gray-400 italic">No solo players registered yet.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
