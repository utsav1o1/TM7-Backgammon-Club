<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { watchEffect, ref } from 'vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import MatchEditModal from '@/Components/MatchEditModal.vue';
import OverrideWinnerModal from '@/Components/OverrideWinnerModal.vue';

const props = defineProps({
    matches: Array,
    is_published: Boolean,
});

const form = useForm({
    type: 'individual',
});

const generateBracket = () => {
    if (confirm('Regenerate Solo Bracket? This will delete current solo matches.')) {
        form.post(route('admin.seed'), { preserveScroll: true });
    }
};

const publishToggle = useForm({ type: 'individual' });
const togglePublish = () => {
    const action = props.is_published ? 'unpublish' : 'publish';
    publishToggle.post(route(`admin.${action}`), { preserveScroll: true });
};

const matchForm = useForm({ winner_id: null });
const finalizeMatch = (matchId, winnerId) => {
    matchForm.winner_id = winnerId;
    matchForm.post(route('admin.finalize', matchId), { preserveScroll: true });
};

const advanceForm = useForm({ type: 'individual', round: 1 });
const advanceRound = (round) => {
    advanceForm.round = round;
    advanceForm.post(route('admin.bracket.advance'), { preserveScroll: true });
};

// ─── ADMIN FULL CONTROL ACTIONS ──────────────────────────────────────────

const showingEditModal = ref(false);
const editMatchId = ref(null);
const openEditMatch = (id) => {
    editMatchId.value = id;
    showingEditModal.value = true;
};

const showingOverrideModal = ref(false);
const activeOverrideMatch = ref(null);
const openOverrideMatch = (match) => {
    activeOverrideMatch.value = match;
    showingOverrideModal.value = true;
};

const showingReverseConfirm = ref(false);
const reverseMatchId = ref(null);
const openReverseMatch = (id) => {
    reverseMatchId.value = id;
    showingReverseConfirm.value = true;
};
const confirmReverse = () => {
    router.patch(route('admin.match.reverse', reverseMatchId.value), {}, {
        preserveScroll: true,
        onSuccess: () => {
            showingReverseConfirm.value = false;
        }
    });
};

const showingDeleteBracketConfirm = ref(false);
const openDeleteBracket = () => {
    showingDeleteBracketConfirm.value = true;
};
const confirmDeleteBracket = () => {
    router.delete(route('admin.bracket.delete', 'individual'), {
        onSuccess: () => {
            showingDeleteBracketConfirm.value = false;
        }
    });
};

// ──────────────────────────────────────────────────────────────────────────

// Group matches by round
const matchesByRound = ref({});
const rounds = ref([]);

watchEffect(() => {
    const grouped = {};
    props.matches.forEach(m => {
        if (!grouped[m.round]) grouped[m.round] = [];
        grouped[m.round].push(m);
    });
    matchesByRound.value = grouped;
    rounds.value = Object.keys(grouped).sort((a, b) => a - b);
});

</script>

<template>

    <Head title="Solo Bracket Management" />

    <AuthenticatedLayout>
        <template #header>Individual Bracket Management</template>

        <div class="space-y-8">
            <!-- Controls -->
            <div
                class="flex flex-wrap gap-4 items-center justify-between bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                <div class="space-y-1">
                    <h3 class="font-bold text-gray-900">Solo Controls</h3>
                    <p class="text-xs text-gray-500">Manage visibility and registration for the Individual Tournament.
                    </p>
                </div>
                <div class="flex gap-3">
                    <button @click="generateBracket"
                        class="px-4 py-2 bg-gray-900 text-white rounded-lg text-xs font-bold hover:bg-gray-800 transition-all uppercase tracking-widest shadow-sm">
                        Generate Bracket
                    </button>
                    <button @click="togglePublish" :class="[
                        'px-4 py-2 rounded-lg text-xs font-bold transition-all uppercase tracking-widest shadow-sm',
                        is_published ? 'bg-indigo-50 text-indigo-600 hover:bg-indigo-100' : 'bg-tm7-gold text-black hover:bg-tm7-gold/90'
                    ]">
                        {{ is_published ? 'Unpublish & Unlock' : 'Publish & Lock' }}
                    </button>
                    <button v-if="matches.length > 0" @click="openDeleteBracket"
                        class="px-4 py-2 bg-red-500 text-white rounded-lg text-xs font-bold hover:bg-red-600 transition-all uppercase tracking-widest shadow-sm">
                        Delete Bracket
                    </button>
                </div>
            </div>

            <!-- Brackets -->
            <div v-if="rounds.length > 0" class="space-y-12">
                <div v-for="round in rounds" :key="round" class="space-y-6">
                    <div class="flex items-center gap-4">
                        <h4 class="text-sm font-bold text-gray-400 uppercase tracking-[0.2em] whitespace-nowrap">Round
                            {{ round
                            }}</h4>
                        <div class="h-px bg-gray-100 w-full"></div>
                        <button @click="advanceRound(round)"
                            class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-bold rounded uppercase tracking-widest hover:bg-indigo-100 transition-all border border-indigo-100 shadow-sm">
                            Advance Winners
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <div v-for="match in matchesByRound[round]" :key="match.id" :class="[
                            'bg-white rounded-xl border shadow-sm overflow-hidden group transition-all flex flex-col',
                            match.status === 'conflict' ? 'border-red-300 ring-2 ring-red-100 shadow-md scale-[1.02]' : 'border-gray-100'
                        ]">
                            <div class="p-4 space-y-3 relative flex-grow">
                                <!-- Conflict Badge -->
                                <div v-if="match.status === 'conflict'"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white text-[8px] font-black pl-2 pr-4 pt-2 pb-0.5 rounded shadow-sm animate-pulse z-10 border border-red-600 uppercase tracking-widest">
                                    Conflict
                                </div>

                                <!-- Participant 1 -->
                                <div
                                    :class="['flex items-center justify-between p-2 rounded-lg transition-all', match.winner_id === match.participant1_id ? 'bg-green-50' : (match.status === 'conflict' && match.p1_claimed_win ? 'bg-red-50' : '')]">
                                    <span
                                        :class="['text-xs font-semibold truncate flex items-center gap-2', match.winner_id === match.participant1_id ? 'text-green-700' : 'text-gray-600']">
                                        {{ match.p1_name }}
                                        <span v-if="match.winner_id === match.participant1_id"
                                            class="text-[8px] uppercase tracking-widest font-bold">Winner</span>
                                        <span v-if="match.status === 'conflict' && match.p1_claimed_win"
                                            class="text-[8px] text-red-500 font-bold italic">Claimed</span>
                                    </span>
                                    <button
                                        v-if="match.participant1_id && match.status !== 'finalized' && match.p1_name !== 'BYE'"
                                        @click="finalizeMatch(match.id, match.participant1_id)"
                                        :class="['p-1 transition-all', match.status === 'conflict' ? 'text-indigo-500 hover:scale-125' : 'text-gray-300 hover:text-green-500']"
                                        :title="match.status === 'conflict' ? 'Verify as Winner' : 'Declare Winner'">
                                        <svg xmlns="http://www.w3.org/2000/svg" :class="['h-4 w-4']" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path v-if="match.status === 'conflict'" fill-rule="evenodd"
                                                d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                            <path v-else fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="flex items-center justify-center p-0.5 relative">
                                    <div class="h-px bg-gray-50 grow"></div>
                                    <span
                                        class="text-[8px] font-bold text-gray-300 mx-2 uppercase tracking-widest italic">VS</span>
                                    <div class="h-px bg-gray-50 grow"></div>
                                </div>

                                <!-- Participant 2 -->
                                <div
                                    :class="['flex items-center justify-between p-2 rounded-lg transition-all', match.winner_id === match.participant2_id ? 'bg-green-50' : (match.status === 'conflict' && match.p2_claimed_win ? 'bg-red-50' : '')]">
                                    <span
                                        :class="['text-xs font-semibold truncate flex items-center gap-2', match.winner_id === match.participant2_id ? 'text-green-700' : 'text-gray-600']">
                                        {{ match.p2_name }}
                                        <span v-if="match.winner_id === match.participant2_id"
                                            class="text-[8px] uppercase tracking-widest font-bold">Winner</span>
                                        <span v-if="match.status === 'conflict' && match.p2_claimed_win"
                                            class="text-[8px] text-red-500 font-bold italic">Claimed</span>
                                    </span>
                                    <button
                                        v-if="match.participant2_id && match.status !== 'finalized' && match.p2_name !== 'BYE'"
                                        @click="finalizeMatch(match.id, match.participant2_id)"
                                        :class="['p-1 transition-all', match.status === 'conflict' ? 'text-indigo-500 hover:scale-125' : 'text-gray-300 hover:text-green-500']"
                                        :title="match.status === 'conflict' ? 'Verify as Winner' : 'Declare Winner'">
                                        <svg xmlns="http://www.w3.org/2000/svg" :class="['h-4 w-4']" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path v-if="match.status === 'conflict'" fill-rule="evenodd"
                                                d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                            <path v-else fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Admin Match Controls Overlay/Toolbar (Minimal) -->
                                <div class="pt-3 border-t border-gray-50 flex justify-between gap-2">
                                    <button @click="openEditMatch(match.id)"
                                        class="text-[10px] text-gray-400 hover:text-indigo-600 flex items-center gap-1 font-bold uppercase transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path
                                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                        Edit
                                    </button>
                                    <button @click="openOverrideMatch(match)"
                                        class="text-[10px] text-gray-400 hover:text-indigo-600 flex items-center gap-1 font-bold uppercase transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Winner
                                    </button>
                                    <button v-if="match.status === 'completed' || match.status === 'finalized'"
                                        @click="openReverseMatch(match.id)"
                                        class="text-[10px] text-gray-400 hover:text-red-600 flex items-center gap-1 font-bold uppercase transition-colors ml-auto">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Reverse
                                    </button>
                                </div>
                            </div>
                            <!-- Status Bar -->
                            <div
                                :class="['h-1 w-full shrink-0', match.status === 'finalized' ? 'bg-green-400' : (match.status === 'conflict' ? 'bg-red-400 animate-pulse' : 'bg-gray-100 group-hover:bg-gray-200 transition-colors')]">
                            </div>
                            <div v-if="match.notes"
                                class="px-3 py-1 bg-gray-50 border-t border-gray-100 italic text-[10px] text-gray-400 truncate"
                                :title="match.notes">
                                Note: {{ match.notes }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="py-24 text-center space-y-4 bg-white rounded-2xl border border-gray-100 border-dashed">
                <div class="text-gray-300 flex justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div class="space-y-1">
                    <p class="text-sm font-bold text-gray-500">No Bracket Generated</p>
                    <p class="text-xs text-gray-400">Press the generate button above to start the tournament.</p>
                </div>
            </div>
        </div>

        <!-- Admin Control Modals -->
        <MatchEditModal :show="showingEditModal" :match-id="editMatchId" @close="showingEditModal = false"
            @success="router.reload({ preserveScroll: true })" />

        <OverrideWinnerModal :show="showingOverrideModal" :match="activeOverrideMatch"
            @close="showingOverrideModal = false" @success="router.reload({ preserveScroll: true })" />

        <ConfirmModal :show="showingReverseConfirm" title="Reverse Match Result?"
            message="This will clear the current winner and **cascade downward**, clearing all results in subsequent rounds that depend on this match. This action is reversible by re-finalizing the match later."
            confirm-label="Confirm Reverse" danger-level="warn" @close="showingReverseConfirm = false"
            @confirm="confirmReverse" />

        <ConfirmModal :show="showingDeleteBracketConfirm" title="Delete Entire Bracket?"
            message="Are you sure you want to delete all individual matches? <br><br>A **backup snapshot** will be saved in the Backup Brackets menu, so you can restore it later if needed. However, registration will be unlocked immediately."
            confirm-label="Snapshot & Delete" :require-typed-confirm="true" keyword="DELETE"
            @close="showingDeleteBracketConfirm = false" @confirm="confirmDeleteBracket" />

    </AuthenticatedLayout>
</template>
