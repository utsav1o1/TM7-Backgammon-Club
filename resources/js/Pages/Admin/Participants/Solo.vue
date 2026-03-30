<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    individual_entries: Array,
});

const deleteForm = useForm({});

const deleteSolo = (id) => {
    if (confirm('Are you sure you want to remove this solo entry? This will also remove their matches.')) {
        deleteForm.delete(route('admin.destroy.individual', id), {
            preserveScroll: true,
        });
    }
};

const admin_cc_email = "utsavkarki244@gmail.com";

const getMailtoLink = (entry) => {
    const subject = encodeURIComponent(`Tournament Update: ${entry.player_name}`);
    const body = encodeURIComponent(`Hello ${entry.player_name},\n\n`);
    return `mailto:${entry.email}?cc=${admin_cc_email}&subject=${subject}&body=${body}`;
};

</script>

<template>
    <Head title="Solo Management" />

    <AuthenticatedLayout>
        <template #header>Solo Participants</template>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-900">Individual Entries ({{ individual_entries.length }})</h3>
                <div class="text-xs text-gray-500 italic">Click name to contact</div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">
                            <th class="px-6 py-4">Player Nickname</th>
                            <th class="px-6 py-4">Timezone</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="entry in individual_entries" :key="entry.id" class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <a :href="getMailtoLink(entry)" class="text-sm font-bold text-gray-900 hover:text-tm7-gold transition-colors decoration-0">
                                    {{ entry.player_name }}
                                </a>
                                <p class="text-[10px] text-gray-500">{{ entry.email }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-mono">{{ entry.timezone }}</td>
                            <td class="px-6 py-4 text-right">
                                <button 
                                    @click="deleteSolo(entry.id)"
                                    class="text-xs font-bold text-red-500 hover:text-red-700 transition-colors uppercase tracking-wider"
                                >
                                    Remove
                                </button>
                            </td>
                        </tr>
                        <tr v-if="individual_entries.length === 0">
                            <td colspan="3" class="px-6 py-12 text-center text-gray-400 italic">No solo entries registered yet.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
