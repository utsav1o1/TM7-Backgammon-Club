<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Form, Field, ErrorMessage } from 'vee-validate';
import * as yup from 'yup';
import { useToast } from 'vue-toastification';

const props = defineProps({
    is_published: Boolean
});

const toast = useToast();

const schema = yup.object({
  name: yup.string().required('Team name is required').min(3, 'Team name must be at least 3 characters'),
  email: yup.string().required('Team contact email is required').email('Must be a valid email'),
  captain_nickname: yup.string().required('Captain nickname is required'),
  co_captain_nickname: yup.string().optional(),
  accepted_terms: yup.boolean().oneOf([true], 'You must agree to the Terms & Conditions and Official Rules').required(),
});

const form = useForm({
  name: '',
  email: '',
  captain_nickname: '',
  co_captain_nickname: '',
  accepted_terms: false,
});

const submitRegistration = (values, { resetForm }) => {
  form.name = values.name;
  form.email = values.email;
  form.captain_nickname = values.captain_nickname;
  form.co_captain_nickname = values.co_captain_nickname || '';
  form.accepted_terms = values.accepted_terms;
  
  form.post(route('signup.team'), {
    preserveScroll: true,
    onSuccess: () => {
      resetForm();
      form.reset();
      form.clearErrors();
    },
    onError: (errors) => {
      Object.keys(errors).forEach(key => toast.error(errors[key]));
    }
  });
};
</script>

<template>
  <Head title="Team Sign Up | TM7 Club" />

  <PublicLayout>
    <div class="max-w-2xl mx-auto px-6 py-12 lg:py-24">
      
      <div v-if="is_published" class="bg-tm7-light/20 backdrop-blur-md rounded-2xl p-12 border border-tm7-gold/30 text-center shadow-2xl">
          <div class="w-20 h-20 bg-tm7-gold/20 rounded-full flex items-center justify-center mx-auto mb-6 text-tm7-gold">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
              </svg>
          </div>
          <h2 class="text-3xl font-serif font-bold text-white mb-4">Registration Locked</h2>
          <p class="text-gray-300 mb-8 text-lg">The team tournament bracket has been published. Registration is now closed for this season. Good luck to all competing teams!</p>
          <Link :href="route('bracket.team')" class="inline-flex items-center px-6 py-3 bg-tm7-gold text-tm7-darker font-bold rounded-full transition-transform hover:scale-105 active:scale-95">
              View Team Bracket
          </Link>
      </div>

      <div v-else>
        <div class="bg-tm7-light/30 backdrop-blur-md rounded-2xl p-8 sm:p-12 border border-white/10 shadow-2xl relative overflow-hidden">
          
          <div class="mb-10 text-center relative z-10">
            <h2 class="text-3xl font-serif font-bold text-white mb-3 tracking-wide">Team Tournament Entry</h2>
            <p class="text-gray-400 mb-4">Register your team to enter the upcoming global team competition.</p>
            <Link :href="route('rules.team')" class="inline-flex items-center text-sm font-semibold text-tm7-gold hover:text-white transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 mr-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
              </svg>
              Read Official Team Rules
            </Link>
          </div>

          <Form @submit="submitRegistration" :validation-schema="schema" class="space-y-6 relative z-10">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
              <div class="sm:col-span-2">
                <label for="name" class="block text-sm font-medium leading-6 text-gray-300">Team Name</label>
                <div class="mt-2 text-tm7-dark">
                  <Field name="name" type="text" id="name" class="block w-full rounded-md border-0 py-2.5 px-4 bg-white/5 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-tm7-accent sm:text-sm sm:leading-6 placeholder:text-gray-500 transition-shadow" placeholder="e.g. The Board Rulers" />
                  <ErrorMessage name="name" class="text-red-400 text-xs mt-1 block" />
                </div>
              </div>

              <div class="sm:col-span-2">
                <label for="email" class="block text-sm font-medium leading-6 text-gray-300">Team Contact Email</label>
                <div class="mt-2 text-tm7-dark">
                  <Field name="email" type="email" id="email" class="block w-full rounded-md border-0 py-2.5 px-4 bg-white/5 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-tm7-accent sm:text-sm sm:leading-6 placeholder:text-gray-500 transition-shadow" placeholder="team@example.com" />
                  <ErrorMessage name="email" class="text-red-400 text-xs mt-1 block" />
                </div>
              </div>

              <div>
                <label for="captain_nickname" class="block text-sm font-medium leading-6 text-gray-300">Captain Nickname</label>
                <div class="mt-2 text-tm7-dark">
                  <Field name="captain_nickname" type="text" id="captain_nickname" class="block w-full rounded-md border-0 py-2.5 px-4 bg-white/5 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-tm7-accent sm:text-sm sm:leading-6 placeholder:text-gray-500 transition-shadow" placeholder="CaptainName" />
                  <ErrorMessage name="captain_nickname" class="text-red-400 text-xs mt-1 block" />
                </div>
              </div>

              <div>
                <label for="co_captain_nickname" class="block text-sm font-medium leading-6 text-gray-300">Co-Captain Nickname <span class="text-gray-500 font-normal">(Optional)</span></label>
                <div class="mt-2 text-tm7-dark">
                  <Field name="co_captain_nickname" type="text" id="co_captain_nickname" class="block w-full rounded-md border-0 py-2.5 px-4 bg-white/5 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-tm7-accent sm:text-sm sm:leading-6 placeholder:text-gray-500 transition-shadow" placeholder="CoCaptainName" />
                  <ErrorMessage name="co_captain_nickname" class="text-red-400 text-xs mt-1 block" />
                </div>
              </div>
            </div>

            <div class="mt-4">
              <div class="flex items-start">
                <div class="flex h-6 items-center">
                  <Field name="accepted_terms" type="checkbox" id="accepted_terms" class="h-4 w-4 rounded border-white/10 bg-white/5 text-tm7-gold focus:ring-tm7-gold focus:ring-offset-gray-900 transition-colors" :value="true" />
                </div>
                <div class="ml-3 text-sm leading-6">
                  <label for="accepted_terms" class="text-gray-400">
                    I agree to the 
                    <Link :href="route('rules.terms')" class="text-tm7-gold hover:text-white transition-colors underline underline-offset-2">Terms & Conditions</Link> 
                    and 
                    <Link :href="route('rules.team')" class="text-tm7-gold hover:text-white transition-colors underline underline-offset-2">Official Rules</Link>.
                  </label>
                </div>
              </div>
              <ErrorMessage name="accepted_terms" class="text-red-400 text-xs mt-1 block pl-7" />
            </div>

          <div class="pt-6">
            <button type="submit" :disabled="form.processing" class="flex w-full justify-center rounded-md bg-tm7-accent px-3 py-3 text-sm font-semibold text-white shadow-sm hover:bg-teal-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-tm7-accent transition-all disabled:opacity-50 disabled:cursor-not-allowed">
              <span v-if="form.processing">Entering Team...</span>
              <span v-else>Register Team</span>
            </button>
          </div>

        </Form>
      </div>
    </div>
  </div>
</PublicLayout>
</template>
