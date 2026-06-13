<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { useToast } from 'vue-toastification';

const props = defineProps({
    requests: {
        type: Array,
        required: true
    }
});

const toast = useToast();

const form = useForm({
    name: '',
    email: '',
    players_needed: '1',
    message: '',
});

const submitRequest = () => {
    form.post(route('find-teammate.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            toast.success('Your request has been posted!');
        },
        onError: (errors) => {
            Object.keys(errors).forEach(key => toast.error(errors[key]));
        }
    });
};
</script>

<template>

    <Head title="Find a Teammate | Galaxygammon Club" />

    <PublicLayout>
        <div class="max-w-7xl mx-auto px-6 py-12 lg:py-24">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- New Request Form -->
                <div class="lg:col-span-1">
                    <div
                        class="bg-tm7-light/30 backdrop-blur-md rounded-2xl p-8 border border-white/10 shadow-2xl sticky top-8">
                        <h2 class="text-2xl font-serif font-bold text-white mb-2">Looking for a team?</h2>
                        <p class="text-gray-400 mb-6 text-sm">Post your details here so other teams can find and recruit
                            you!</p>

                        <form @submit.prevent="submitRequest" class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium leading-6 text-gray-300">Name /
                                    Nickname</label>
                                <div class="mt-1">
                                    <input v-model="form.name" type="text" id="name" required
                                        class="block w-full rounded-md border-0 py-2.5 px-4 bg-white/5 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-tm7-accent sm:text-sm placeholder:text-gray-500"
                                        placeholder="E.g. GammonKing" />
                                    <span v-if="form.errors.name" class="text-red-400 text-xs mt-1">{{ form.errors.name
                                        }}</span>
                                </div>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium leading-6 text-gray-300">Email
                                    Address</label>
                                <div class="mt-1">
                                    <input v-model="form.email" type="email" id="email" required
                                        class="block w-full rounded-md border-0 py-2.5 px-4 bg-white/5 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-tm7-accent sm:text-sm placeholder:text-gray-500"
                                        placeholder="We won't spam you" />
                                    <span v-if="form.errors.email" class="text-red-400 text-xs mt-1">{{
                                        form.errors.email }}</span>
                                </div>
                            </div>

                            <div>
                                <label for="players_needed" class="block text-sm font-medium leading-6 text-gray-300">I
                                    am looking for...</label>
                                <div class="mt-1">
                                    <select v-model="form.players_needed" id="players_needed"
                                        class="block w-full rounded-md border-0 py-2.5 px-4 bg-white/5 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-tm7-accent sm:text-sm [&>option]:bg-gray-800">
                                        <option value="1">1 Teammate</option>
                                        <option value="2">2 Teammates</option>
                                        <option value="3">3 Teammates</option>
                                        <option value="4+">A Whole Team (4+)</option>
                                    </select>
                                    <span v-if="form.errors.players_needed" class="text-red-400 text-xs mt-1">{{
                                        form.errors.players_needed }}</span>
                                </div>
                            </div>

                            <div>
                                <label for="message" class="block text-sm font-medium leading-6 text-gray-300">Message
                                    (Optional)</label>
                                <div class="mt-1">
                                    <textarea v-model="form.message" id="message" rows="3"
                                        class="block w-full rounded-md border-0 py-2.5 px-4 bg-white/5 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-tm7-accent sm:text-sm placeholder:text-gray-500"
                                        placeholder="We play competitively on weekends..."></textarea>
                                    <span v-if="form.errors.message" class="text-red-400 text-xs mt-1">{{
                                        form.errors.message }}</span>
                                </div>
                            </div>

                            <button type="submit" :disabled="form.processing"
                                class="w-full justify-center rounded-md bg-tm7-gold px-3 py-3 text-sm font-bold text-tm7-darker shadow-sm hover:scale-105 active:scale-95 transition-transform disabled:opacity-50 mt-4">
                                Post Request
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Players List Grid -->
                <div class="lg:col-span-2">
                    <div class="mb-8">
                        <h1 class="text-4xl font-serif font-bold text-white mb-2">Available Players</h1>
                        <p class="text-gray-400">Reach out and form your ultimate backgammon team.</p>
                    </div>

                    <div v-if="requests.length === 0"
                        class="text-center py-16 bg-white/5 rounded-2xl border border-white/10">
                        <div class="text-gray-500 mb-2">No active teammate requests right now.</div>
                        <p class="text-sm text-gray-400">Be the first to post your request!</p>
                    </div>

                    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div v-for="req in requests" :key="req.id"
                            class="bg-white/5 backdrop-blur-md rounded-xl p-6 border border-white/10 flex flex-col justify-between hover:border-tm7-gold/30 transition-colors">

                            <div>
                                <div class="flex justify-between items-start mb-4">
                                    <h3 class="text-xl font-bold text-white truncate pr-4" :title="req.name">{{ req.name
                                        }}</h3>
                                    <span
                                        class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset"
                                        :class="{
                                            'bg-blue-400/10 text-blue-400 ring-blue-400/30': req.players_needed === '1' || req.players_needed === '2',
                                            'bg-tm7-accent/10 text-tm7-accent ring-tm7-accent/30': req.players_needed === '3',
                                            'bg-tm7-gold/10 text-tm7-gold ring-tm7-gold/30': req.players_needed === '4+'
                                        }">
                                        Needs {{ req.players_needed }}
                                    </span>
                                </div>

                                <p v-if="req.message"
                                    class="text-gray-300 text-sm mb-6 bg-black/20 p-3 rounded-lg border border-white/5">
                                    "{{ req.message }}"
                                </p>
                                <p v-else class="text-gray-500 text-sm italic mb-6">No extra details provided.</p>
                            </div>

                            <!-- Contact Button -->
                            <a :href="`mailto:${req.email}?subject=TM7 Backgammon - Let's team up!`"
                                class="inline-flex w-full items-center justify-center gap-x-2 rounded-lg bg-white/10 px-3 py-2.5 text-sm font-semibold text-white hover:bg-white/20 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    class="w-4 h-4 text-tm7-gold">
                                    <path
                                        d="M3 4a2 2 0 00-2 2v1.161l8.441 4.221a1.25 1.25 0 001.118 0L19 7.162V6a2 2 0 00-2-2H3z" />
                                    <path
                                        d="M19 8.839l-7.77 3.885a2.75 2.75 0 01-2.46 0L1 8.839V14a2 2 0 002 2h14a2 2 0 002-2V8.839z" />
                                </svg>
                                Contact {{ req.name.split(' ')[0] }}
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </PublicLayout>
</template>
