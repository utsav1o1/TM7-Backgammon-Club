<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Editor from 'primevue/editor';

const props = defineProps({
    rules: String,
});

const form = useForm({
    team_rules: props.rules,
});

const submit = () => {
    form.post(route('admin.rules.update'), {
        preserveScroll: true,
        onSuccess: () => alert('Team Rules updated!'),
    });
};
</script>

<template>
    <Head title="Edit Team Rules" />

    <AuthenticatedLayout>
        <template #header>
            Team Tournament Rules
        </template>

        <div class="max-w-4xl space-y-6">
            <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-xl font-bold font-serif text-gray-900">Configure Rules</h3>
                        <p class="text-sm text-gray-500 mt-1">Changes here will be instantly visible on the public Team Rules page.</p>
                    </div>
                    <button 
                        @click="submit"
                        :disabled="form.processing"
                        class="bg-tm7-gold text-black font-bold px-8 py-3 rounded-xl hover:bg-tm7-gold/90 transition-all shadow-lg active:scale-95 disabled:opacity-50"
                    >
                        SAVE CHANGES
                    </button>
                </div>

                <div class="space-y-4">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Rule Content (Rich Text)</label>
                    <Editor v-model="form.team_rules" editorStyle="height: 500px" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
