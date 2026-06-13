<script setup>
import { ref, watch } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';

const page = usePage();
const toast = useToast();

watch(() => page.props.flash, (flash) => {
    if (flash?.success) {
        toast.success(flash.success);
    }
    if (flash?.error) {
        toast.error(flash.error);
    }
}, { deep: true, immediate: true });

const isSidebarOpen = ref(true);

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

// Sub-menu toggles
const isParticipantsOpen = ref(true);
const isStatsOpen = ref(true);
const isRulesOpen = ref(true);
</script>

<template>
    <div class="min-h-screen bg-gray-50 text-gray-900 font-sans antialiased flex">
        <!-- Sidebar -->
        <aside :class="[
            'fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0',
            isSidebarOpen ? 'translate-x-0' : '-translate-x-full'
        ]">
            <div class="h-full flex flex-col">
                <!-- Sidebar Header / Logo -->
                <div class="h-16 flex items-center px-6 border-b border-gray-100">
                    <Link :href="route('dashboard')" class="flex items-center gap-3 no-underline">
                        <ApplicationLogo class="h-8 w-auto fill-current text-tm7-gold" />
                        <span class="text-gray-900 font-serif font-bold tracking-wider text-lg uppercase">TM7
                            Admin</span>
                    </Link>
                </div>

                <!-- Navigation Links -->
                <nav class="flex-grow py-6 px-4 space-y-1 overflow-y-auto custom-scrollbar">
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-2 mb-2">Main</div>

                    <Link :href="route('dashboard')" :class="[
                        'flex items-center gap-3 px-3 py-2 rounded-lg transition-all',
                        route().current('dashboard') ? 'bg-tm7-gold/10 text-tm7-gold border border-tm7-gold/20' : 'hover:bg-gray-50 text-gray-600 hover:text-gray-900'
                    ]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="text-sm font-medium">Dashboard</span>
                    </Link>

                    <!-- Participants -->
                    <div class="pt-4 pb-2">
                        <button @click="isParticipantsOpen = !isParticipantsOpen"
                            class="w-full flex items-center justify-between px-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 hover:text-gray-600 transition-colors">
                            <span>Participants</span>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                :class="['h-3 w-3 transition-transform', isParticipantsOpen ? 'rotate-180' : '']"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div v-show="isParticipantsOpen" class="space-y-1 mt-1">
                            <Link :href="route('admin.participants.teams')"
                                :class="['flex items-center gap-3 px-3 py-2 rounded-lg transition-all', route().current('admin.participants.teams') ? 'bg-gray-100 text-gray-900 border border-gray-200' : 'hover:bg-gray-50 text-gray-500 hover:text-gray-900']">
                                <div
                                    :class="['w-1.5 h-1.5 rounded-full', route().current('admin.participants.teams') ? 'bg-tm7-gold' : 'bg-gray-300']">
                                </div>
                                <span class="text-sm font-medium">Team List</span>
                            </Link>
                            <Link :href="route('admin.participants.solo')"
                                :class="['flex items-center gap-3 px-3 py-2 rounded-lg transition-all', route().current('admin.participants.solo') ? 'bg-gray-100 text-gray-900 border border-gray-200' : 'hover:bg-gray-50 text-gray-500 hover:text-gray-900']">
                                <div
                                    :class="['w-1.5 h-1.5 rounded-full', route().current('admin.participants.solo') ? 'bg-tm7-gold' : 'bg-gray-300']">
                                </div>
                                <span class="text-sm font-medium">Solo List</span>
                            </Link>
                        </div>
                    </div>

                    <!-- Tournament Stats -->
                    <div class="pt-2 pb-2">
                        <button @click="isStatsOpen = !isStatsOpen"
                            class="w-full flex items-center justify-between px-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 hover:text-gray-600 transition-colors">
                            <span>Tournament Stats</span>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                :class="['h-3 w-3 transition-transform', isStatsOpen ? 'rotate-180' : '']" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div v-show="isStatsOpen" class="space-y-1 mt-1">
                            <Link :href="route('admin.stats.team-bracket')"
                                :class="['flex items-center gap-3 px-3 py-2 rounded-lg transition-all', route().current('admin.stats.team-bracket') ? 'bg-gray-100 text-gray-900 border border-gray-200' : 'hover:bg-gray-50 text-gray-500 hover:text-gray-900']">
                                <div
                                    :class="['w-1.5 h-1.5 rounded-full', route().current('admin.stats.team-bracket') ? 'bg-tm7-gold' : 'bg-gray-300']">
                                </div>
                                <span class="text-sm font-medium">Team Bracket</span>
                            </Link>
                            <Link :href="route('admin.stats.individual-bracket')"
                                :class="['flex items-center gap-3 px-3 py-2 rounded-lg transition-all', route().current('admin.stats.individual-bracket') ? 'bg-gray-100 text-gray-900 border border-gray-200' : 'hover:bg-gray-50 text-gray-500 hover:text-gray-900']">
                                <div
                                    :class="['w-1.5 h-1.5 rounded-full', route().current('admin.stats.individual-bracket') ? 'bg-tm7-gold' : 'bg-gray-300']">
                                </div>
                                <span class="text-sm font-medium">Individual Bracket</span>
                            </Link>
                        </div>
                    </div>

                    <!-- Tournament Rules -->
                    <div class="pt-2 pb-2">
                        <button @click="isRulesOpen = !isRulesOpen"
                            class="w-full flex items-center justify-between px-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 hover:text-gray-600 transition-colors">
                            <span>Tournament Rules</span>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                :class="['h-3 w-3 transition-transform', isRulesOpen ? 'rotate-180' : '']" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div v-show="isRulesOpen" class="space-y-1 mt-1">
                            <Link :href="route('admin.rules.team')"
                                :class="['flex items-center gap-3 px-3 py-2 rounded-lg transition-all', route().current('admin.rules.team') ? 'bg-gray-100 text-gray-900 border border-gray-200' : 'hover:bg-gray-50 text-gray-500 hover:text-gray-900']">
                                <div
                                    :class="['w-1.5 h-1.5 rounded-full', route().current('admin.rules.team') ? 'bg-tm7-gold' : 'bg-gray-300']">
                                </div>
                                <span class="text-sm font-medium">Team Rules</span>
                            </Link>
                            <Link :href="route('admin.rules.individual')"
                                :class="['flex items-center gap-3 px-3 py-2 rounded-lg transition-all', route().current('admin.rules.individual') ? 'bg-gray-100 text-gray-900 border border-gray-200' : 'hover:bg-gray-50 text-gray-500 hover:text-gray-900']">
                                <div
                                    :class="['w-1.5 h-1.5 rounded-full', route().current('admin.rules.individual') ? 'bg-tm7-gold' : 'bg-gray-300']">
                                </div>
                                <span class="text-sm font-medium">Individual Rules</span>
                            </Link>
                        </div>
                    </div>

                    <!-- Maintenance -->
                    <div class="pt-4 border-t border-gray-100 mt-4">
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-2 mb-2">Maintenance
                        </div>
                        <Link :href="route('admin.snapshots.index')" :class="[
                            'flex items-center gap-3 px-3 py-2 rounded-lg transition-all',
                            route().current('admin.snapshots.*') ? 'bg-indigo-50 text-indigo-600 border border-indigo-100' : 'hover:bg-gray-50 text-gray-500 hover:text-gray-900'
                        ]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span class="text-sm font-medium">Backup Brackets</span>
                        </Link>
                    </div>
                </nav>

                <!-- Sidebar Footer -->
                <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button
                                class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white transition-all text-left">
                                <div
                                    class="h-8 w-8 rounded-full bg-tm7-gold/20 flex items-center justify-center text-tm7-gold font-bold text-xs">
                                    {{ $page.props.auth.user.name.charAt(0) }}
                                </div>
                                <div class="flex-grow min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{
                                        $page.props.auth.user.name }}</p>
                                    <p class="text-[10px] text-gray-500 truncate">Administrator</p>
                                </div>
                            </button>
                        </template>
                        <template #content>
                            <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-grow flex flex-col min-w-0 overflow-hidden">
            <!-- Header -->
            <header
                class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-40">
                <div class="flex items-center gap-4">
                    <button @click="toggleSidebar"
                        class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h2 class="font-serif text-xl font-bold text-gray-900 tracking-tight">
                        <slot name="header" />
                    </h2>
                </div>
                <div class="flex items-center gap-4">
                    <Link :href="route('home')" target="_blank"
                        class="hidden sm:flex items-center gap-2 px-4 py-2 bg-gray-900 border border-gray-800 text-white rounded-lg text-xs font-bold hover:bg-gray-800 transition-all uppercase tracking-widest no-underline">
                        View Club Website
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </Link>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-grow overflow-y-auto p-8 custom-scrollbar">
                <slot />
            </div>
        </main>
    </div>
</template>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #d1d5db;
}

a {
    text-underline-offset: initial !important;
}
</style>
