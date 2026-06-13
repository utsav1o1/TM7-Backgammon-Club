<script setup>
import { ref, watch, onMounted } from 'vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { router } from '@inertiajs/vue3';

import axios from 'axios';

const props = defineProps({
    show: Boolean,
    matchId: Number,
});

const emit = defineEmits(['close', 'success']);

const loading = ref(true);
const participants = ref([]);
const form = ref({
    participant1_id: null,
    participant2_id: null,
    notes: '',
});
const errors = ref({});

watch(() => props.show, (newVal) => {
    if (newVal && props.matchId) {
        fetchMatchDetails();
    }
});

const fetchMatchDetails = async () => {
    loading.value = true;
    try {
        const response = await axios.get(route('admin.match.edit', props.matchId));
        participants.value = response.data.participants;
        form.value.participant1_id = response.data.match.participant1_id;
        form.value.participant2_id = response.data.match.participant2_id;
        form.value.notes = response.data.match.notes || '';
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};

const processing = ref(false);

const submit = () => {
    processing.value = true;
    router.patch(route('admin.match.swap', props.matchId), form.value, {
        onSuccess: () => {
            emit('success');
            close();
        },
        onError: (err) => {
            errors.value = err;
        },
        onFinish: () => {
            processing.value = false;
        }
    });
};

const close = () => {
    emit('close');
};
</script>

<template>
    <Modal :show="show" @close="close">
        <div class="p-6 bg-white">
            <h2 class="text-lg font-bold text-gray-900">
                Edit Match Participants
            </h2>

            <div v-if="loading" class="py-10 text-center">
                <span class="text-gray-500">Loading match data...</span>
            </div>

            <div v-else class="mt-6">
                <div class="mb-6 rounded-lg bg-yellow-50 p-4 border border-yellow-100">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-bold text-yellow-800">Attention</h3>
                            <div class="mt-1 text-sm text-yellow-700">
                                <p>Swapping participants will **clear this match's result** and all downstream results
                                    in the bracket.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <InputLabel for="p1" value="Participant 1" class="text-gray-900" />
                        <select id="p1" v-model="form.participant1_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white text-gray-900">
                            <option :value="null">BYE / Empty</option>
                            <option v-for="p in participants" :key="p.id" :value="p.id">
                                {{ p.label }}
                            </option>
                        </select>
                        <InputError :message="errors.participant1_id" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="p2" value="Participant 2" class="text-gray-900" />
                        <select id="p2" v-model="form.participant2_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white text-gray-900">
                            <option :value="null">BYE / Empty</option>
                            <option v-for="p in participants" :key="p.id" :value="p.id">
                                {{ p.label }}
                            </option>
                        </select>
                        <InputError :message="errors.participant2_id" class="mt-2" />
                    </div>
                </div>

                <div class="mt-6">
                    <InputLabel for="notes" value="Admin Notes (Reason for change)" class="text-gray-900" />
                    <textarea id="notes" v-model="form.notes" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white text-gray-900"
                        placeholder="e.g. Swapped due to registration error"></textarea>
                </div>

                <div class="mt-6 flex justify-end gap-3 pt-6 border-t border-gray-100">
                    <SecondaryButton @click="close"> Cancel </SecondaryButton>
                    <PrimaryButton @click="submit" :disabled="processing" :class="{ 'opacity-50': processing }">
                        {{ processing ? 'Saving...' : 'Save Changes' }}
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </Modal>
</template>
