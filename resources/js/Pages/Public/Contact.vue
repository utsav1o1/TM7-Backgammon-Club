<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { useToast } from 'vue-toastification';

const toast = useToast();

const form = useForm({
  name: '',
  email: '',
  subject: '',
  message: '',
});

const submitContact = () => {
  form.post(route('contact.send'), {
    onSuccess: () => {
      toast.success('Message sent successfully! We will get back to you soon.');
      form.reset();
    },
    onError: (errors) => {
      const message = errors.message || 'Failed to send message. Please try again later.';
      toast.error(message);
    }
  });
};
</script>

<template>
  <Head title="Contact Us | TM7 Club" />

  <PublicLayout>
    <div class="max-w-5xl mx-auto px-6 py-12 lg:py-24">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
        
        <!-- Contact Info -->
        <div>
          <h1 class="text-4xl font-serif font-bold text-white mb-6 tracking-tight">Get in Touch</h1>
          <p class="text-lg text-gray-400 mb-10 leading-relaxed">
            Have questions about tournaments, team registration, or just want to say hi? We're here to help!
          </p>

          <div class="space-y-8">
            <div class="flex items-start gap-4">
              <div class="w-12 h-12 rounded-full bg-tm7-gold/10 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-tm7-gold">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                </svg>
              </div>
              <div>
                <h3 class="font-bold text-white">Email Us</h3>
                <p class="text-gray-400">admin@thegammonlengends.com</p>
              </div>
            </div>

            <div class="flex items-start gap-4">
              <div class="w-12 h-12 rounded-full bg-tm7-accent/10 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-tm7-accent">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                </svg>
              </div>
              <div>
                <h3 class="font-bold text-white">Global Community</h3>
                <p class="text-gray-400">Join our Discord for real-time discussions.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Contact Form -->
        <div class="bg-tm7-light/20 backdrop-blur-md rounded-2xl p-8 border border-white/10 shadow-2xl">
          <form @submit.prevent="submitContact" class="space-y-6">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Name</label>
              <input v-model="form.name" type="text" id="name" required class="block w-full rounded-md border-0 py-2.5 px-4 bg-white/5 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-tm7-gold sm:text-sm" placeholder="Your Name" />
            </div>

            <div>
              <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email</label>
              <input v-model="form.email" type="email" id="email" required class="block w-full rounded-md border-0 py-2.5 px-4 bg-white/5 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-tm7-gold sm:text-sm" placeholder="you@example.com" />
            </div>

            <div>
              <label for="subject" class="block text-sm font-medium text-gray-300 mb-2">Subject</label>
              <input v-model="form.subject" type="text" id="subject" required class="block w-full rounded-md border-0 py-2.5 px-4 bg-white/5 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-tm7-gold sm:text-sm" placeholder="How can we help?" />
            </div>

            <div>
              <label for="message" class="block text-sm font-medium text-gray-300 mb-2">Message</label>
              <textarea v-model="form.message" id="message" rows="4" required class="block w-full rounded-md border-0 py-2.5 px-4 bg-white/5 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-tm7-gold sm:text-sm" placeholder="Your message..."></textarea>
            </div>

            <button type="submit" :disabled="form.processing" class="w-full flex justify-center items-center gap-2 rounded-md bg-tm7-gold px-3 py-3 text-sm font-semibold text-tm7-darker shadow-sm hover:bg-tm7-gold-light transition-all disabled:opacity-50">
              Send Message
            </button>
          </form>
        </div>

      </div>
    </div>
  </PublicLayout>
</template>
