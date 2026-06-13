<script setup>
import { ref, watch } from 'vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps({
    show: Boolean,
    title: { type: String, default: 'Confirm Action' },
    message: String,
    confirmLabel: { type: String, default: 'Confirm' },
    dangerLevel: { type: String, default: 'danger' }, // 'warn' or 'danger'
    requireTypedConfirm: { type: Boolean, default: false },
    keyword: { type: String, default: 'DELETE' },
});

const emit = defineEmits(['close', 'confirm']);

const typedValue = ref('');
const isConfirmed = ref(false);

watch(() => props.show, (val) => {
    if (val) {
        typedValue.value = '';
        isConfirmed.value = !props.requireTypedConfirm;
    }
});

const handleDeepConfirm = () => {
    if (props.requireTypedConfirm) {
        if (typedValue.value.toUpperCase() === props.keyword.toUpperCase()) {
            emit('confirm');
        }
    } else {
        emit('confirm');
    }
};

const close = () => {
    emit('close');
};
</script>

<template>
    <Modal :show="show" @close="close">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ title }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400" v-html="message"></p>

            <div v-if="requireTypedConfirm" class="mt-6">
                <InputLabel for="typed_confirm" :value="`Type ${keyword} to confirm:`" />
                <TextInput id="typed_confirm" type="text" class="mt-1 block w-full text-gray-900" v-model="typedValue"
                    :placeholder="keyword" autofocus />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <SecondaryButton @click="close"> Cancel </SecondaryButton>

                <DangerButton v-if="dangerLevel === 'danger'"
                    :class="{ 'opacity-25': requireTypedConfirm && typedValue.toUpperCase() !== keyword.toUpperCase() }"
                    :disabled="requireTypedConfirm && typedValue.toUpperCase() !== keyword.toUpperCase()"
                    @click="handleDeepConfirm">
                    {{ confirmLabel }}
                </DangerButton>

                <PrimaryButton v-else
                    :class="{ 'opacity-25': requireTypedConfirm && typedValue.toUpperCase() !== keyword.toUpperCase() }"
                    :disabled="requireTypedConfirm && typedValue.toUpperCase() !== keyword.toUpperCase()"
                    @click="handleDeepConfirm">
                    {{ confirmLabel }}
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
