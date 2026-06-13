<script setup>
import { ref, computed, watch } from 'vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    show: Boolean,
    match: Object,
});

const emit = defineEmits(['close', 'success']);

const form = ref({
    winner_id: null,
    notes: '',
});
const errors = ref({});

watch(() => props.show, (newVal) => {
    if (newVal) {
        form.value.winner_id = null;
        form.value.notes = '';
        errors.value = {};
    }
});

const p1Name = computed(() => props.match?.p1_name || 'Participant 1');
const p2Name = computed(() => props.match?.p2_name || 'Participant 2');

const processing = ref(false);

const submit = () => {
    if (!props.match?.id) return;

    processing.value = true;
    router.patch(route('admin.match.override', props.match.id), form.value, {
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
            <h2 class="text-lg font-medium text-gray-900">
                Override Match Winner
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Select the winner for this match. This will move them to the next round.
            </p>

            <div class="mt-6 space-y-4">
                <div
                    class="flex items-center space-x-3 p-3 rounded-lg border border-gray-100 hover:bg-gray-50 transition-colors">
                    <input type="radio" id="p1_win" :value="match?.participant1_id" v-model="form.winner_id"
                        class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600 bg-white" />
                    <label for="p1_win" class="text-sm font-bold text-gray-900 flex-grow cursor-pointer">
                        {{ p1Name }}
                    </label>
                </div>

                <div
                    class="flex items-center space-x-3 p-3 rounded-lg border border-gray-100 hover:bg-gray-50 transition-colors">
                    <input type="radio" id="p2_win" :value="match?.participant2_id" v-model="form.winner_id"
                        class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600 bg-white" />
                    <label for="p2_win" class="text-sm font-bold text-gray-900 flex-grow cursor-pointer">
                        {{ p2Name }}
                    </label>
                </div>

                <InputError :message="errors.winner_id" class="mt-2" />

                <div class="mt-6 pt-4 border-t border-gray-100">
                    <InputLabel for="notes" value="Reason for Override (Optional)" class="text-gray-900" />
                    <textarea id="notes" v-model="form.notes" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-white text-gray-900"
                        placeholder="e.g. Participant forfeited"></textarea>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="close"> Cancel </SecondaryButton>
                    <PrimaryButton @click="submit" :disabled="processing || !form.winner_id"
                        :class="{ 'opacity-50': processing }">
                        {{ processing ? 'Processing...' : 'Confirm Winner' }}
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </Modal>
</template>
