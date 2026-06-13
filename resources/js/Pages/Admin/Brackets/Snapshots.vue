<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    snapshots: Array,
});

const showingRestoreConfirm = ref(false);
const showingDeleteConfirm = ref(false);
const activeSnapshot = ref(null);

const openRestore = (snapshot) => {
    activeSnapshot.value = snapshot;
    showingRestoreConfirm.value = true;
};

const openDelete = (snapshot) => {
    activeSnapshot.value = snapshot;
    showingDeleteConfirm.value = true;
};

const confirmRestore = () => {
    router.post(route('admin.snapshots.restore', activeSnapshot.value.id), {}, {
        onSuccess: () => {
            showingRestoreConfirm.value = false;
        }
    });
};

const confirmDelete = () => {
    router.delete(route('admin.snapshots.destroy', activeSnapshot.value.id), {
        onSuccess: () => {
            showingDeleteConfirm.value = false;
        }
    });
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString();
};

</script>

<template>

    <Head title="Backup Brackets" />

    <AuthenticatedLayout>
        <template #header>Backup Brackets Bin</template>

        <div class="space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                <div class="space-y-1">
                    <h3 class="font-bold text-gray-900">Archived Snapshots</h3>
                    <p class="text-xs text-gray-500">Restore or permanently delete bracket backups created during
                        deletion.</p>
                </div>

                <div class="mt-6 overflow-hidden border border-gray-100 rounded-xl">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Label / Incident</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Type</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Deleted By</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Date</th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <tr v-for="snapshot in snapshots" :key="snapshot.id"
                                class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ snapshot.snapshot_label }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="[
                                        'px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-widest',
                                        snapshot.tournament_type === 'team' ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600'
                                    ]">
                                        {{ snapshot.tournament_type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                    {{ snapshot.deleted_by_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                    {{ formatDate(snapshot.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium space-x-2">
                                    <button @click="openRestore(snapshot)"
                                        class="text-indigo-600 hover:text-indigo-900 font-bold uppercase tracking-widest">
                                        Restore
                                    </button>
                                    <button @click="openDelete(snapshot)"
                                        class="text-red-400 hover:text-red-600 font-bold uppercase tracking-widest">
                                        Burn
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="snapshots.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-400 italic">
                                    Zero snapshots found. The bin is clean.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Restore Confirm -->
        <ConfirmModal :show="showingRestoreConfirm" title="Restore Bracket?"
            :message="`This will **replace the current active ${activeSnapshot?.tournament_type} bracket** with this backup. Any results in the current bracket will be lost. <br><br>Match IDs will change, but participants and status will be preserved.`"
            confirm-label="Restore & Publish" danger-level="warn" @close="showingRestoreConfirm = false"
            @confirm="confirmRestore" />

        <!-- Burn Confirm -->
        <ConfirmModal :show="showingDeleteConfirm" title="Permanently Delete Snapshot?"
            message="This backup will be **deleted forever**. You cannot restore it after this action."
            confirm-label="Burn Snapshot" :require-typed-confirm="true" keyword="BURN"
            @close="showingDeleteConfirm = false" @confirm="confirmDelete" />

    </AuthenticatedLayout>
</template>
