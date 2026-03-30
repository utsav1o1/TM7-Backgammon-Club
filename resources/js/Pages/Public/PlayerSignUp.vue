<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Form, Field, ErrorMessage } from 'vee-validate';
import * as yup from 'yup';
import { useToast } from 'vue-toastification';
import TimezoneSelect from 'vue-timezone-select';
import { ref } from 'vue';

const props = defineProps({
    is_published: Boolean
});

const toast = useToast();
const selectedTimezone = ref(Intl.DateTimeFormat().resolvedOptions().timeZone);

const schema = yup.object({
  nickname: yup.string().required('Nickname is required').min(3, 'Nickname must be at least 3 characters'),
  email: yup.string().required('Email is required').email('Must be a valid email'),
  accepted_terms: yup.boolean().oneOf([true], 'You must agree to the Terms & Conditions and Official Rules').required(),
});

const form = useForm({
  nickname: '',
  email: '',
  timezone: '',
  accepted_terms: false,
});

const submitRegistration = (values, { resetForm }) => {
  form.nickname = values.nickname;
  form.email = values.email;
  form.timezone = selectedTimezone.value;
  form.accepted_terms = values.accepted_terms;
  
  form.post(route('signup.player'), {
    preserveScroll: true,
    onSuccess: () => {
      resetForm();
      form.reset();
      form.clearErrors();
    },
    onError: (errors) => {
      Object.keys(errors).forEach(key => {
        toast.error(errors[key]);
      });
    }
  });
};
</script>

<template>
  <Head title="Player Sign Up | TM7 Club" />

  <PublicLayout>
    <div class="max-w-3xl mx-auto px-6 py-12 lg:py-24">
      
      <div v-if="is_published" class="bg-tm7-light/20 backdrop-blur-md rounded-2xl p-12 border border-tm7-gold/30 text-center shadow-2xl">
          <div class="w-20 h-20 bg-tm7-gold/20 rounded-full flex items-center justify-center mx-auto mb-6 text-tm7-gold">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
              </svg>
          </div>
          <h2 class="text-3xl font-serif font-bold text-white mb-4">Registration Locked</h2>
          <p class="text-gray-300 mb-8 text-lg">General platform registration is currently locked as the seasonal tournaments have already begun. Please check back next season!</p>
          <Link :href="route('home')" class="inline-flex items-center px-6 py-3 bg-tm7-gold text-tm7-darker font-bold rounded-full transition-transform hover:scale-105 active:scale-95">
              Back to Home
          </Link>
      </div>

      <div v-else class="bg-tm7-light/30 backdrop-blur-md rounded-2xl p-8 sm:p-12 border border-white/10 shadow-2xl relative overflow-hidden">
        
        <!-- Decoration -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-tm7-gold/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="mb-10 text-center relative z-10">
          <h2 class="text-3xl font-serif font-bold text-white mb-3 tracking-wide">Player Registration</h2>
          <p class="text-gray-400 mb-4">Join the TM7 platform to participate in global backgammon events.</p>
          <Link :href="route('rules.individual')" class="inline-flex items-center text-sm font-semibold text-tm7-gold hover:text-white transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 mr-1">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
            </svg>
            Read Official Individual Rules
          </Link>
        </div>

        <Form @submit="submitRegistration" :validation-schema="schema" class="space-y-6 relative z-10">
          
          <div>
            <label for="nickname" class="block text-sm font-medium leading-6 text-gray-300">Nickname</label>
            <div class="mt-2 text-tm7-dark">
              <Field name="nickname" type="text" id="nickname" class="block w-full rounded-md border-0 py-2.5 px-4 bg-white/5 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-tm7-gold sm:text-sm sm:leading-6 placeholder:text-gray-500 transition-shadow" placeholder="e.g. BackgammonKing99" />
              <ErrorMessage name="nickname" class="text-red-400 text-xs mt-1 block" />
            </div>
          </div>

          <div>
            <label for="email" class="block text-sm font-medium leading-6 text-gray-300">Email Address</label>
            <div class="mt-2 text-tm7-dark">
              <Field name="email" type="email" id="email" class="block w-full rounded-md border-0 py-2.5 px-4 bg-white/5 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-tm7-gold sm:text-sm sm:leading-6 placeholder:text-gray-500 transition-shadow" placeholder="you@example.com" />
              <ErrorMessage name="email" class="text-red-400 text-xs mt-1 block" />
            </div>
            <p class="mt-2 text-xs text-gray-500">Your email remains secure and is only used for match scheduling.</p>
          </div>

          <div class="timezone-wrapper">
            <label class="block text-sm font-medium leading-6 text-gray-300 mb-2">Your Timezone</label>
            <div class="text-black">
                <!-- Fallback select if vue-timezone-select throws hydration errors, styling applied via wrapper -->
                <select v-model="selectedTimezone" class="block w-full rounded-md border-0 py-2.5 px-4 bg-white/90 text-tm7-darker ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-tm7-gold sm:text-sm font-medium">
                    <option value="" disabled>Select TimeZone</option>
                    <option value="GMT-12:00 International Date Line West">GMT-12:00 International Date Line West</option>
                    <option value="GMT-11:00 Midway Island, Somoa">GMT-11:00 Midway Island, Somoa</option>
                    <option value="GMT-10:00 Hawaii">GMT-10:00 Hawaii</option>
                    <option value="GMT-09:00 Alaska">GMT-09:00 Alaska</option>
                    <option value="GMT-08:00 Pacific Time(US and Canada); Tijuana">GMT-08:00 Pacific Time(US and Canada); Tijuana</option>
                    <option value="GMT-07:00 Arizona">GMT-07:00 Arizona</option>
                    <option value="GMT-07:00 Chihuahua, LaPaz, Mazatian">GMT-07:00 Chihuahua, LaPaz, Mazatian</option>
                    <option value="GMT-07:00 Mountain Time (US and Canada)">GMT-07:00 Mountain Time (US and Canada)</option>
                    <option value="GMT-07:00 Central America">GMT-07:00 Central America</option>
                    <option value="GMT-06:00 Central Time (US and Canada)">GMT-06:00 Central Time (US and Canada)</option>
                    <option value="GMT-06:00 Guadalajara, Mexico City, Monterey">GMT-06:00 Guadalajara, Mexico City, Monterey</option>
                    <option value="GMT-06:00 Saskatchewan">GMT-06:00 Saskatchewan</option>
                    <option value="GMT-05:00 Eastern Time(US and Canada)">GMT-05:00 Eastern Time(US and Canada)</option>
                    <option value="GMT-05:00 Indiana (East)">GMT-05:00 Indiana (East)</option>
                    <option value="GMT-05:00 Bogota, Lima, Quito">GMT-05:00 Bogota, Lima, Quito</option>
                    <option value="GMT-04:00 Atlantic Time (Canada)">GMT-04:00 Atlantic Time (Canada)</option>
                    <option value="GMT-04:00 Caracas, La Paz">GMT-04:00 Caracas, La Paz</option>
                    <option value="GMT-04:00 Santiago">GMT-04:00 Santiago</option>
                    <option value="GMT-03:30 Newfoundland">GMT-03:30 Newfoundland</option>
                    <option value="GMT-03:00 Brasilia">GMT-03:00 Brasilia</option>
                    <option value="GMT-03:00 Buenos Aires, Georgetown">GMT-03:00 Buenos Aires, Georgetown</option>
                    <option value="GMT-03:00 Fortaleza">GMT-03:00 Fortaleza</option>
                    <option value="GMT-03:00 Montevideo">GMT-03:00 Montevideo</option>
                    <option value="GMT-02:00 Mid-Atlantic">GMT-02:00 Mid-Atlantic</option>
                    <option value="GMT-01:00 Azores">GMT-01:00 Azores</option>
                    <option value="GMT-01:00 Cape Verde Is.">GMT-01:00 Cape Verde Is.</option>
                    <option value="GMT-00:00 Casablanca, Monrovia">GMT-00:00 Casablanca, Monrovia</option>
                    <option value="GMT-00:00 Greenwich Mean Time: Dublin, Edinburgh, Lisbon, London">GMT-00:00 Greenwich Mean Time: Dublin, Edinburgh, Lisbon, London</option>
                    <option value="GMT+01:00 Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna">GMT+01:00 Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna</option>
                    <option value="GMT+01:00 Belgrade, Bratislava, Budapest, Ljubljana, Prague">GMT+01:00 Belgrade, Bratislava, Budapest, Ljubljana, Prague</option>
                    <option value="GMT+01:00 Brussels, Copenhagen, Madrid, Paris">GMT+01:00 Brussels, Copenhagen, Madrid, Paris</option>
                    <option value="GMT+01:00 Sarajevo, Skopje, Warsaw, Zagreb">GMT+01:00 Sarajevo, Skopje, Warsaw, Zagreb</option>
                    <option value="GMT+01:00 West Central Africa">GMT+01:00 West Central Africa</option>
                    <option value="GMT+02:00 Athens, Beirut">GMT+02:00 Athens, Beirut</option>
                    <option value="GMT+02:00 Bucharest">GMT+02:00 Bucharest</option>
                    <option value="GMT+02:00 Cairo">GMT+02:00 Cairo</option>
                    <option value="GMT+02:00 Harare, Pretoria">GMT+02:00 Harare, Pretoria</option>
                    <option value="GMT+02:00 Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius">GMT+02:00 Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius</option>
                    <option value="GMT+02:00 Jerusalem">GMT+02:00 Jerusalem</option>
                    <option value="GMT+03:00 Baghdad">GMT+03:00 Baghdad</option>
                    <option value="GMT+03:00 Kuwait, Riyadh">GMT+03:00 Kuwait, Riyadh</option>
                    <option value="GMT+03:00 Moskow, St. Petersburg, Volgograd">GMT+03:00 Moskow, St. Petersburg, Volgograd</option>
                    <option value="GMT+03:00 Istanbul, Nairobi">GMT+03:00 Istanbul, Nairobi</option>
                    <option value="GMT+03:00 Tehran">GMT+03:00 Tehran</option>
                    <option value="GMT+04:00 Abu Dhabi, Muscat">GMT+04:00 Abu Dhabi, Muscat</option>
                    <option value="GMT+04:00 Baku, Tbilisi, Yerevan">GMT+04:00 Baku, Tbilisi, Yerevan</option>
                    <option value="GMT+04:30 Kabul">GMT+04:30 Kabul</option>
                    <option value="GMT+05:00 Ekaterinburg">GMT+05:00 Ekaterinburg</option>
                    <option value="GMT+05:00 Islamabad, Karachi, Tashkent">GMT+05:00 Islamabad, Karachi, Tashkent</option>
                    <option value="GMT+05:30 Chennai, Kolkata, Mumbai, New Delhi">GMT+05:30 Chennai, Kolkata, Mumbai, New Delhi</option>
                    <option value="GMT+05:45 Kathmandu">GMT+05:45 Kathmandu</option>
                    <option value="GMT+06:00 Almaty, Novosibirsk">GMT+06:00 Almaty, Novosibirsk</option>
                    <option value="GMT+06:00 Astana, Dhaka">GMT+06:00 Astana, Dhaka</option>
                    <option value="GMT+06:00 Sri Jayawardenupura">GMT+06:00 Sri Jayawardenupura</option>
                    <option value="GMT+06:30 Rangoon">GMT+06:30 Rangoon</option>
                    <option value="GMT+07:00 Bangkok, Hanoi, Jakarta">GMT+07:00 Bangkok, Hanoi, Jakarta</option>
                    <option value="GMT+07:00 Krasnoyarsk">GMT+07:00 Krasnoyarsk</option>
                    <option value="GMT+08:00 Beijing, Chongqing, Hong King, Urumqi">GMT+08:00 Beijing, Chongqing, Hong King, Urumqi</option>
                    <option value="GMT+08:00 Irkutsk, Ulaan Bataar">GMT+08:00 Irkutsk, Ulaan Bataar</option>
                    <option value="GMT+08:00 Kuala Lumpur, Singapore">GMT+08:00 Kuala Lumpur, Singapore</option>
                    <option value="GMT+08:00 Perth">GMT+08:00 Perth</option>
                    <option value="GMT+08:00 Taipei">GMT+08:00 Taipei</option>
                    <option value="GMT+09:00 Osaka, Sapporo, Tokyo">GMT+09:00 Osaka, Sapporo, Tokyo</option>
                    <option value="GMT+09:00 Seoul">GMT+09:00 Seoul</option>
                    <option value="GMT+09:00 Yakutsk">GMT+09:00 Yakutsk</option>
                    <option value="GMT+09:30 Adelaide">GMT+09:30 Adelaide</option>
                    <option value="GMT+09:30 Darwin">GMT+09:30 Darwin</option>
                    <option value="GMT+10:00 Brisbane">GMT+10:00 Brisbane</option>
                    <option value="GMT+10:00 Canberra, Melbourne, Sydney">GMT+10:00 Canberra, Melbourne, Sydney</option>
                    <option value="GMT+10:00 Guam, Port Moresby">GMT+10:00 Guam, Port Moresby</option>
                    <option value="GMT+10:00 Hobart">GMT+10:00 Hobart</option>
                    <option value="GMT+10:00 Vladivostok">GMT+10:00 Vladivostok</option>
                    <option value="GMT+11:00 Magadan, Solomon Is., New Caledonia">GMT+11:00 Magadan, Solomon Is., New Caledonia</option>
                    <option value="GMT+12:00 Auckland, Wellington">GMT+12:00 Auckland, Wellington</option>
                    <option value="GMT+12:00 Fiji, Marshall Is.">GMT+12:00 Fiji, Marshall Is.</option>
                    <option value="GMT+13:00 Chatham Islands">GMT+13:00 Chatham Islands</option>
                </select>
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
                  <Link :href="route('rules.individual')" class="text-tm7-gold hover:text-white transition-colors underline underline-offset-2">Official Rules</Link>.
                </label>
              </div>
            </div>
            <ErrorMessage name="accepted_terms" class="text-red-400 text-xs mt-1 block pl-7" />
          </div>

          <div class="pt-6">
            <button type="submit" :disabled="form.processing" class="flex w-full justify-center rounded-md bg-tm7-gold px-3 py-3 text-sm font-semibold text-tm7-darker shadow-sm hover:bg-tm7-gold-light focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-tm7-gold transition-all disabled:opacity-50 disabled:cursor-not-allowed">
              <span v-if="form.processing">Registering...</span>
              <span v-else>Complete Registration</span>
            </button>
          </div>

        </Form>
      </div>
    </div>
  </PublicLayout>
</template>

<style>
/* Custom style to handle vue-timezone-select if it doesn't support Tailwind fully out of the box */
.timezone-wrapper input {
    background-color: rgba(255,255,255,0.05) !important;
    color: white !important;
}
</style>
