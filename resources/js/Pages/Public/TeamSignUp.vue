<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Form, Field, ErrorMessage } from 'vee-validate';
import * as yup from 'yup';
import { useToast } from 'vue-toastification';
import { ref } from 'vue';

const props = defineProps({
  is_published: Boolean
});

const toast = useToast();

const memberSchema = yup.object({
  name: yup.string().required('Member name is required'),
  email: yup.string().required('Member email is required').email('Must be a valid email'),
});

const schema = yup.object({
  name: yup.string().required('Team name is required').min(3, 'Team name must be at least 3 characters'),
  email: yup.string().required('Team contact email is required').email('Must be a valid email'),
  captain_nickname: yup.string().required('Captain nickname is required'),
  co_captain_nickname: yup.string().optional(),
  accepted_terms: yup.boolean().oneOf([true], 'You must agree to the Terms & Conditions and Official Rules').required(),
  members: yup.array()
    .of(memberSchema)
    .min(3, 'You must add at least 3 team members.')
    .max(5, 'A team can have at most 5 members.')
    .test('unique-emails', 'Each team member must have a unique email address.', function (members) {
      if (!members) return true;
      const emails = members.map(m => m.email).filter(e => e); // exclude empty
      const uniqueEmails = new Set(emails);
      return emails.length === uniqueEmails.size;
    }),
});

// Reactive member list (starts with 3 empty slots)
const members = ref([
  { name: '', email: '' },
  { name: '', email: '' },
  { name: '', email: '' },
]);

const addMember = () => {
  if (members.value.length < 5) {
    members.value.push({ name: '', email: '' });
  }
};

const removeMember = (index) => {
  if (members.value.length > 3) {
    members.value.splice(index, 1);
  }
};

const form = useForm({
  name: '',
  email: '',
  captain_nickname: '',
  co_captain_nickname: '',
  accepted_terms: false,
  members: [],
});

const submitRegistration = (values, { resetForm }) => {
  form.name = values.name;
  form.email = values.email;
  form.captain_nickname = values.captain_nickname;
  form.co_captain_nickname = values.co_captain_nickname || '';
  form.accepted_terms = values.accepted_terms;
  form.members = members.value;

  form.post(route('signup.team'), {
    preserveScroll: true,
    onSuccess: () => {
      resetForm();
      form.reset();
      form.clearErrors();
      members.value = [
        { name: '', email: '' },
        { name: '', email: '' },
        { name: '', email: '' },
      ];
    },
    onError: (errors) => {
      Object.keys(errors).forEach(key => toast.error(errors[key]));
    }
  });
};
</script>

<template>

  <Head title="Team Sign Up | Galaxygammon Club" />

  <PublicLayout>
    <div class="max-w-2xl mx-auto px-6 py-12 lg:py-24">

      <div v-if="is_published"
        class="bg-tm7-light/20 backdrop-blur-md rounded-2xl p-12 border border-tm7-gold/30 text-center shadow-2xl">
        <div class="w-20 h-20 bg-tm7-gold/20 rounded-full flex items-center justify-center mx-auto mb-6 text-tm7-gold">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="w-10 h-10">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
          </svg>
        </div>
        <h2 class="text-3xl font-serif font-bold text-white mb-4">Registration Locked</h2>
        <p class="text-gray-300 mb-8 text-lg">The team tournament bracket has been published. Registration is now closed
          for this season. Good luck to all competing teams!</p>
        <Link :href="route('bracket.team')"
          class="inline-flex items-center px-6 py-3 bg-tm7-gold text-tm7-darker font-bold rounded-full transition-transform hover:scale-105 active:scale-95">
          View Team Bracket
        </Link>
      </div>

      <div v-else>
        <div
          class="bg-tm7-light/30 backdrop-blur-md rounded-2xl p-8 sm:p-12 border border-white/10 shadow-2xl relative overflow-hidden">

          <div class="mb-10 text-center relative z-10">
            <h2 class="text-3xl font-serif font-bold text-white mb-3 tracking-wide">Team Tournament Entry</h2>
            <p class="text-gray-400 mb-4">Register your team to enter the upcoming global team competition.</p>
            <Link :href="route('rules.team')"
              class="inline-flex items-center text-sm font-semibold text-tm7-gold hover:text-white transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor" class="w-4 h-4 mr-1">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
              </svg>
              Read Official Team Rules
            </Link>
          </div>

          <Form @submit="submitRegistration" :validation-schema="schema" class="space-y-6 relative z-10">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
              <div class="sm:col-span-2">
                <label for="name" class="block text-sm font-medium leading-6 text-gray-300">Team Name</label>
                <div class="mt-2 text-tm7-dark">
                  <Field name="name" type="text" id="name"
                    class="block w-full rounded-md border-0 py-2.5 px-4 bg-white/5 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-tm7-accent sm:text-sm sm:leading-6 placeholder:text-gray-500 transition-shadow"
                    placeholder="e.g. The Board Rulers" />
                  <ErrorMessage name="name" class="text-red-400 text-xs mt-1 block" />
                </div>
              </div>

              <div class="sm:col-span-2">
                <label for="email" class="block text-sm font-medium leading-6 text-gray-300">Team Contact Email</label>
                <div class="mt-2 text-tm7-dark">
                  <Field name="email" type="email" id="email"
                    class="block w-full rounded-md border-0 py-2.5 px-4 bg-white/5 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-tm7-accent sm:text-sm sm:leading-6 placeholder:text-gray-500 transition-shadow"
                    placeholder="team@example.com" />
                  <ErrorMessage name="email" class="text-red-400 text-xs mt-1 block" />
                </div>
              </div>

              <div>
                <label for="captain_nickname" class="block text-sm font-medium leading-6 text-gray-300">Captain
                  Nickname</label>
                <div class="mt-2 text-tm7-dark">
                  <Field name="captain_nickname" type="text" id="captain_nickname"
                    class="block w-full rounded-md border-0 py-2.5 px-4 bg-white/5 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-tm7-accent sm:text-sm sm:leading-6 placeholder:text-gray-500 transition-shadow"
                    placeholder="CaptainName" />
                  <ErrorMessage name="captain_nickname" class="text-red-400 text-xs mt-1 block" />
                </div>
              </div>

              <div>
                <label for="co_captain_nickname" class="block text-sm font-medium leading-6 text-gray-300">Co-Captain
                  Nickname <span class="text-gray-500 font-normal">(Optional)</span></label>
                <div class="mt-2 text-tm7-dark">
                  <Field name="co_captain_nickname" type="text" id="co_captain_nickname"
                    class="block w-full rounded-md border-0 py-2.5 px-4 bg-white/5 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-tm7-accent sm:text-sm sm:leading-6 placeholder:text-gray-500 transition-shadow"
                    placeholder="CoCaptainName" />
                  <ErrorMessage name="co_captain_nickname" class="text-red-400 text-xs mt-1 block" />
                </div>
              </div>
            </div>

            <!-- Team Members Section -->
            <div class="pt-4">
              <div class="flex items-center justify-between mb-4">
                <div>
                  <h3 class="text-sm font-semibold text-white">Team Members</h3>
                  <p class="text-xs text-gray-500 mt-0.5">Add 3 to 5 players (name &amp; email each)</p>
                </div>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full"
                  :class="members.length >= 3 ? 'bg-tm7-accent/20 text-tm7-accent' : 'bg-red-500/20 text-red-400'">
                  {{ members.length }} / 5
                </span>
              </div>

              <div class="space-y-3">
                <div v-for="(member, index) in members" :key="index"
                  class="flex gap-3 items-start bg-white/5 rounded-xl p-3 border border-white/10">
                  <span
                    class="flex-shrink-0 w-6 h-6 rounded-full bg-tm7-gold/20 text-tm7-gold text-xs font-bold flex items-center justify-center mt-2.5">
                    {{ index + 1 }}
                  </span>
                  <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <div>
                      <input v-model="member.name" type="text" :placeholder="`Member ${index + 1} Name`"
                        class="block w-full rounded-md border-0 py-2 px-3 bg-white/5 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-tm7-accent text-sm placeholder:text-gray-500 transition-shadow" />
                    </div>
                    <div>
                      <input v-model="member.email" type="email" :placeholder="`member${index + 1}@email.com`"
                        class="block w-full rounded-md border-0 py-2 px-3 bg-white/5 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-tm7-accent text-sm placeholder:text-gray-500 transition-shadow" />
                    </div>
                  </div>
                  <button v-if="members.length > 3" type="button" @click="removeMember(index)"
                    class="flex-shrink-0 mt-2 text-gray-500 hover:text-red-400 transition-colors" title="Remove member">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                      <path fill-rule="evenodd"
                        d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z"
                        clip-rule="evenodd" />
                    </svg>
                  </button>
                  <div v-else class="w-4 flex-shrink-0" />
                </div>
              </div>

              <button v-if="members.length < 5" type="button" @click="addMember"
                class="mt-3 w-full flex items-center justify-center gap-2 py-2 rounded-xl border border-dashed border-white/20 text-gray-400 hover:text-tm7-gold hover:border-tm7-gold/40 transition-all text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                  <path
                    d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                </svg>
                Add Member ({{ members.length }}/5)
              </button>
              <p v-if="members.length < 3" class="text-red-400 text-xs mt-2">
                ✕ Minimum 3 members required ({{ 3 - members.length }} more needed)
              </p>
              <ErrorMessage name="members" class="text-red-400 text-xs mt-2 block" />
            </div>

            <div class="mt-4">
              <div class="flex items-start">
                <div class="flex h-6 items-center">
                  <Field name="accepted_terms" type="checkbox" id="accepted_terms"
                    class="h-4 w-4 rounded border-white/10 bg-white/5 text-tm7-gold focus:ring-tm7-gold focus:ring-offset-gray-900 transition-colors"
                    :value="true" />
                </div>
                <div class="ml-3 text-sm leading-6">
                  <label for="accepted_terms" class="text-gray-400">
                    I agree to the
                    <Link :href="route('rules.terms')"
                      class="text-tm7-gold hover:text-white transition-colors underline underline-offset-2">Terms &amp;
                      Conditions</Link>
                    and
                    <Link :href="route('rules.team')"
                      class="text-tm7-gold hover:text-white transition-colors underline underline-offset-2">Official
                      Rules</Link>.
                  </label>
                </div>
              </div>
              <ErrorMessage name="accepted_terms" class="text-red-400 text-xs mt-1 block pl-7" />
            </div>

            <div class="pt-6">
              <button type="submit" :disabled="form.processing || members.length < 3"
                class="flex w-full justify-center rounded-md bg-tm7-accent px-3 py-3 text-sm font-semibold text-white shadow-sm hover:bg-teal-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-tm7-accent transition-all disabled:opacity-50 disabled:cursor-not-allowed">
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
