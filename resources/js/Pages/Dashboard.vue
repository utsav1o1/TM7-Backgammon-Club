<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    total_teams: Number,
    total_solo: Number,
    total_players: Number,
    total_matches: Number,
    open_conflicts: Number,
    admin_cc_email: String,
});

const ccForm = useForm({
    email: props.admin_cc_email,
});

const updateCcEmail = () => {
    ccForm.post(route('admin.cc_email.update'), {
        preserveScroll: true,
        onSuccess: () => alert('CC Email updated!'),
    });
};



const wipeForm = useForm({});

const wipeAllData = () => {
    if (confirm('DANGER: This will wipe ALL registration and match data. Continue?')) {
        wipeForm.delete(route('admin.truncate'));
    }
};

const resetForm = useForm({ type: '' });
const resetTournament = (type) => {
    const label = type === 'team' ? 'Team' : 'Solo';
    if (confirm(`Archive current ${label} winners and wipe all registration data for this category?`)) {
        resetForm.type = type;
        resetForm.post(route('admin.reset'));
    }
};

</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            Dashboard Overview
        </template>

        <div class="space-y-8">
            <!-- System Overview & Reset Actions -->
            <div class="grid grid-cols-1 xl:grid-cols-4 gap-8 items-start">
                <!-- Stats Cards (3/4) -->
                <div class="xl:col-span-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-2 bg-tm7-gold/10 rounded-lg text-tm7-gold group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Teams</span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900">{{ total_teams }}</p>
                        <p class="text-xs text-gray-500 mt-1">Registered squads</p>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Solo</span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900">{{ total_solo }}</p>
                        <p class="text-xs text-gray-500 mt-1">Individual entries</p>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow group">
                        <div class="flex items-center justify-between mb-4">
                            <div :class="['p-2 rounded-lg group-hover:scale-110 transition-transform', open_conflicts > 0 ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600']">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Conflicts</span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900">{{ open_conflicts }}</p>
                        <p class="text-xs text-gray-500 mt-1">Needs admin review</p>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-2 bg-gray-100 rounded-lg text-gray-600 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Users</span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900">{{ total_players }}</p>
                        <p class="text-xs text-gray-500 mt-1">Unique platform users</p>
                    </div>
                </div>

                <!-- Reset Actions (1/4) -->
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset Actions
                    </h3>
                    
                    <button 
                        @click="resetTournament('team')"
                        class="w-full flex flex-col items-start px-4 py-3 bg-red-50 border border-red-100 rounded-xl hover:bg-red-100 transition-all text-left group"
                    >
                        <span class="text-sm font-bold text-red-600">Reset Team Tournament</span>
                        <span class="text-[10px] text-red-400 leading-tight mt-1">Archiving winners & wiping team data.</span>
                    </button>

                    <button 
                        @click="resetTournament('individual')"
                        class="w-full flex flex-col items-start px-4 py-3 bg-red-50 border border-red-100 rounded-xl hover:bg-red-100 transition-all text-left group"
                    >
                        <span class="text-sm font-bold text-red-600">Reset Solo Tournament</span>
                        <span class="text-[10px] text-red-400 leading-tight mt-1">Archiving winners & wiping individual data.</span>
                    </button>
                    
                    <div class="pt-2">
                        <button 
                            @click="wipeAllData"
                            class="w-full py-2 border-2 border-dashed border-red-200 text-red-400 rounded-xl hover:bg-red-50 hover:border-red-300 transition-all text-xs font-bold uppercase tracking-widest"
                        >
                            Wipe Entire Platform
                        </button>
                    </div>
                </div>
            </div>

            <!-- Future Expansion Area (Empty Space) -->
            <div class="min-h-[300px] w-full border-2 border-dashed border-gray-200 rounded-2xl flex flex-col items-center justify-center text-gray-400 bg-gray-50/50 hover:bg-gray-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="text-sm font-bold tracking-widest uppercase">Reserved Space</span>
                <span class="text-xs mt-1">For future graphs, options, or widget integrations</span>
            </div>

            <!-- Settings & Quick Controls (Moved to Bottom) -->
            <div class="mt-8 grid grid-cols-1 gap-8">
                <!-- Contact Configuration -->
                <div class="bg-gray-900 p-8 rounded-2xl shadow-xl flex flex-col md:flex-row items-center gap-8 border border-gray-800">
                    <div class="flex-grow space-y-2">
                        <h3 class="text-xl font-bold text-white font-serif">Contact Configuration</h3>
                        <p class="text-sm text-gray-400 leading-relaxed">
                            This email address will be automatically CC'd whenever users click to email a team or player from the bracket / list pages.
                        </p>
                    </div>
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <input 
                            v-model="ccForm.email"
                            type="email" 
                            class="bg-[#1a1a1a] border-gray-700 text-white rounded-lg px-4 py-2.5 w-full md:w-64 focus:ring-tm7-gold focus:border-tm7-gold transition-all"
                            placeholder="utsavkarki244@gmail.com"
                        >
                        <button 
                            @click="updateCcEmail"
                            :disabled="ccForm.processing"
                            class="bg-tm7-gold text-black font-bold px-6 py-2.5 rounded-lg hover:bg-tm7-gold/90 transition-all shadow-lg active:scale-95 disabled:opacity-50"
                        >
                            SAVE
                        </button>
                    </div>
                </div>
            </div>


        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.text-decoration-0 {
    text-decoration: none;
}
</style>
