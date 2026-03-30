<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { watchEffect, ref } from 'vue';

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
            <div class="flex flex-wrap gap-4 items-center justify-between bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                <div class="space-y-1">
                    <h3 class="font-bold text-gray-900">Solo Controls</h3>
                    <p class="text-xs text-gray-500">Manage visibility and registration for the Individual Tournament.</p>
                </div>
                <div class="flex gap-3">
                    <button 
                        @click="generateBracket"
                        class="px-4 py-2 bg-gray-900 text-white rounded-lg text-xs font-bold hover:bg-gray-800 transition-all uppercase tracking-widest shadow-sm"
                    >
                        Generate Bracket
                    </button>
                    <button 
                        @click="togglePublish"
                        :class="[
                            'px-4 py-2 rounded-lg text-xs font-bold transition-all uppercase tracking-widest shadow-sm',
                            is_published ? 'bg-red-500 text-white hover:bg-red-600' : 'bg-tm7-gold text-black hover:bg-tm7-gold/90'
                        ]"
                    >
                        {{ is_published ? 'Unpublish & Unlock' : 'Publish & Lock' }}
                    </button>
                </div>
            </div>

            <!-- Brackets -->
            <div v-if="rounds.length > 0" class="space-y-12">
                <div v-for="round in rounds" :key="round" class="space-y-6">
                    <div class="flex items-center gap-4">
                        <h4 class="text-sm font-bold text-gray-400 uppercase tracking-[0.2em] whitespace-nowrap">Round {{ round }}</h4>
                        <div class="h-px bg-gray-100 w-full"></div>
                        <button 
                            @click="advanceRound(round)"
                            class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-bold rounded uppercase tracking-widest hover:bg-indigo-100 transition-all border border-indigo-100 shadow-sm"
                        >
                            Advance Winners
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <div 
                            v-for="match in matchesByRound[round]" :key="match.id"
                            :class="[
                                'bg-white rounded-xl border shadow-sm overflow-hidden group transition-all',
                                match.status === 'conflict' ? 'border-red-300 ring-2 ring-red-100 shadow-md scale-[1.02]' : 'border-gray-100'
                            ]"
                        >
                            <div class="p-4 space-y-3 relative">
                                <!-- Conflict Badge -->
                                <div v-if="match.status === 'conflict'" class="absolute -top-2 -right-2 bg-red-500 text-white text-[8px] font-black pl-2 pr-4 pt-2 pb-0.5 rounded shadow-sm animate-pulse z-10 border border-red-600 uppercase tracking-widest">
                                    Conflict
                                </div>

                                <!-- Participant 1 -->
                                <div :class="['flex items-center justify-between p-2 rounded-lg transition-all', match.winner_id === match.participant1_id ? 'bg-green-50' : (match.status === 'conflict' && match.p1_claimed_win ? 'bg-red-50' : '')]">
                                    <span :class="['text-xs font-semibold truncate flex items-center gap-2', match.winner_id === match.participant1_id ? 'text-green-700' : 'text-gray-600']">
                                        {{ match.p1_name }}
                                        <span v-if="match.winner_id === match.participant1_id" class="text-[8px] uppercase tracking-widest font-bold">Winner</span>
                                        <span v-if="match.status === 'conflict' && match.p1_claimed_win" class="text-[8px] text-red-500 font-bold italic">Claimed</span>
                                    </span>
                                    <button 
                                        v-if="match.participant1_id && match.status !== 'finalized' && match.p1_name !== 'BYE'"
                                        @click="finalizeMatch(match.id, match.participant1_id)"
                                        :class="['p-1 transition-all', match.status === 'conflict' ? 'text-indigo-500 hover:scale-125' : 'text-gray-300 hover:text-green-500']"
                                        :title="match.status === 'conflict' ? 'Verify as Winner' : 'Declare Winner'"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" :class="['h-4 w-4']" viewBox="0 0 20 20" fill="currentColor">
                                            <path v-if="match.status === 'conflict'" fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            <path v-else fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="flex items-center justify-center p-0.5 relative">
                                    <div class="h-px bg-gray-50 grow"></div>
                                    <span class="text-[8px] font-bold text-gray-300 mx-2 uppercase tracking-widest italic">VS</span>
                                    <div class="h-px bg-gray-50 grow"></div>
                                </div>

                                <!-- Participant 2 -->
                                <div :class="['flex items-center justify-between p-2 rounded-lg transition-all', match.winner_id === match.participant2_id ? 'bg-green-50' : (match.status === 'conflict' && match.p2_claimed_win ? 'bg-red-50' : '')]">
                                    <span :class="['text-xs font-semibold truncate flex items-center gap-2', match.winner_id === match.participant2_id ? 'text-green-700' : 'text-gray-600']">
                                        {{ match.p2_name }}
                                        <span v-if="match.winner_id === match.participant2_id" class="text-[8px] uppercase tracking-widest font-bold">Winner</span>
                                        <span v-if="match.status === 'conflict' && match.p2_claimed_win" class="text-[8px] text-red-500 font-bold italic">Claimed</span>
                                    </span>
                                    <button 
                                        v-if="match.participant2_id && match.status !== 'finalized' && match.p2_name !== 'BYE'"
                                        @click="finalizeMatch(match.id, match.participant2_id)"
                                        :class="['p-1 transition-all', match.status === 'conflict' ? 'text-indigo-500 hover:scale-125' : 'text-gray-300 hover:text-green-500']"
                                        :title="match.status === 'conflict' ? 'Verify as Winner' : 'Declare Winner'"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" :class="['h-4 w-4']" viewBox="0 0 20 20" fill="currentColor">
                                            <path v-if="match.status === 'conflict'" fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            <path v-else fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <!-- Status Bar -->
                            <div :class="['h-1 w-full', match.status === 'finalized' ? 'bg-green-400' : (match.status === 'conflict' ? 'bg-red-400 animate-pulse' : 'bg-gray-100 group-hover:bg-gray-200 transition-colors')]"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="py-24 text-center space-y-4 bg-white rounded-2xl border border-gray-100 border-dashed">
                <div class="text-gray-300 flex justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div class="space-y-1">
                    <p class="text-sm font-bold text-gray-500">No Bracket Generated</p>
                    <p class="text-xs text-gray-400">Press the generate button above to start the tournament.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
