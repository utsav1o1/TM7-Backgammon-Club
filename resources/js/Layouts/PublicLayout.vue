<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Dialog, DialogPanel, Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import { Bars3Icon, XMarkIcon } from '@heroicons/vue/24/outline';
import { ChevronDownIcon } from '@heroicons/vue/20/solid';

const navigation = [
  { name: 'Home', href: route('home') },
  { name: 'Player Sign Up', href: route('signup.player') },
  { name: 'Players List', href: route('players.index') },
  { name: 'Team Sign Up', href: route('signup.team') },
  { name: 'Teams List', href: route('teams.index') },
  { name: 'Hall Of Fame', href: route('hall-of-fame') },
  { name: 'Team Bracket', href: route('bracket.team') },
  { name: 'Solo Bracket', href: route('bracket.individual') },
  { name: 'Rules', href: '#', children: [
    { name: 'Terms & Conditions', href: route('rules.terms') },
    { name: 'Team Rules', href: route('rules.team') },
    { name: 'Individual Rules', href: route('rules.individual') },
  ]},
];

const mobileMenuOpen = ref(false);
</script>

<template>
  <div class="min-h-screen bg-tm7-dark text-gray-300 font-sans selection:bg-tm7-gold selection:text-tm7-darker">
    <!-- Navbar (Glassmorphism) -->
    <header class="absolute inset-x-0 top-0 z-50">
      <nav class="flex items-center justify-between p-6 lg:px-8 bg-tm7-dark/60 backdrop-blur-md border-b border-white/10" aria-label="Global">
        <div class="flex lg:flex-1">
          <Link :href="route('home')" class="-m-1.5 p-1.5 flex items-center gap-3 group">
            <span class="sr-only">TM7 Backgammon</span>
            <img src="/images/logo.png" alt="TM7 Logo" class="h-10 w-auto" @error="$event.target.style.display='none'" />
          </Link>
        </div>
        
        <!-- Mobile menu button -->
        <div class="flex lg:hidden">
          <button type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-400 hover:text-white" @click="mobileMenuOpen = true">
            <span class="sr-only">Open main menu</span>
            <Bars3Icon class="h-6 w-6" aria-hidden="true" />
          </button>
        </div>
        
        <!-- Desktop Nav -->
        <div class="hidden lg:flex lg:gap-x-8">
          <template v-for="item in navigation" :key="item.name">
            <!-- Dropdown for items with children -->
            <Menu v-if="item.children" as="div" class="relative inline-block text-left">
              <MenuButton class="inline-flex items-center gap-x-1 text-sm font-medium leading-6 text-gray-300 hover:text-tm7-gold transition-colors duration-200 focus:outline-none">
                {{ item.name }}
                <ChevronDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
              </MenuButton>

              <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                <MenuItems class="absolute left-0 z-10 mt-2 w-56 origin-top-left rounded-md bg-tm7-dark shadow-lg ring-1 ring-white/10 focus:outline-none border border-white/5 backdrop-blur-xl">
                  <div class="py-1">
                    <MenuItem v-for="child in item.children" :key="child.name" v-slot="{ active }">
                      <Link :href="child.href" :class="[active ? 'bg-tm7-light/10 text-tm7-gold' : 'text-gray-300', 'block px-4 py-2 text-sm transition-colors']">
                        {{ child.name }}
                      </Link>
                    </MenuItem>
                  </div>
                </MenuItems>
              </transition>
            </Menu>

            <!-- Regular Link -->
            <Link v-else :href="item.href" class="text-sm font-medium leading-6 text-gray-300 hover:text-tm7-gold transition-colors duration-200">
              {{ item.name }}
            </Link>
          </template>
        </div>
        
        <div class="hidden lg:flex lg:flex-1 lg:justify-end gap-x-4">
          <Link :href="route('contact')" class="text-sm font-semibold leading-6 text-white bg-tm7-gold px-4 py-2 rounded-full border border-tm7-gold/20 hover:bg-tm7-gold-light text-tm7-darker transition shadow-lg shadow-tm7-gold/10">
            Contact Us
          </Link>
          
          <!-- Admin actions removed from frontend nav by user request -->
        </div>
      </nav>

      <!-- Mobile Nav -->
      <Dialog as="div" class="lg:hidden" @close="mobileMenuOpen = false" :open="mobileMenuOpen">
        <div class="fixed inset-0 z-50 bg-tm7-darker/80 backdrop-blur-sm" />
        <DialogPanel class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-tm7-dark px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-white/10">
          <div class="flex items-center justify-between">
            <Link :href="route('home')" class="-m-1.5 p-1.5 flex items-center gap-3">
              <img src="/images/logo.png" alt="TM7 Logo" class="h-8 w-auto" @error="$event.target.style.display='none'" />
            </Link>
            <button type="button" class="-m-2.5 rounded-md p-2.5 text-gray-400" @click="mobileMenuOpen = false">
              <span class="sr-only">Close menu</span>
              <XMarkIcon class="h-6 w-6" aria-hidden="true" />
            </button>
          </div>
          <div class="mt-6 flow-root">
            <div class="-my-6 divide-y divide-white/10">
              <div class="space-y-4 py-6">
                <template v-for="item in navigation" :key="item.name">
                    <div v-if="item.children" class="space-y-1">
                        <div class="px-3 text-xs font-semibold text-tm7-gold uppercase tracking-wider mb-2">{{ item.name }}</div>
                        <Link v-for="child in item.children" :key="child.name" :href="child.href" @click="mobileMenuOpen = false" class="block rounded-lg px-6 py-2 text-base font-semibold leading-7 text-gray-300 hover:bg-tm7-light/20 hover:text-white transition-colors">
                          {{ child.name }}
                        </Link>
                    </div>
                    <Link v-else :href="item.href" @click="mobileMenuOpen = false" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-gray-300 hover:bg-tm7-light/20 hover:text-white transition-colors">
                      {{ item.name }}
                    </Link>
                </template>
              </div>
              <div class="py-6 space-y-4">
                <Link :href="route('contact')" @click="mobileMenuOpen = false" class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold leading-7 text-tm7-gold hover:bg-tm7-light">
                  Contact Us
                </Link>
              </div>
            </div>
          </div>
        </DialogPanel>
      </Dialog>
    </header>

    <!-- Page Content -->
    <main class="relative isolate pt-24 min-h-screen">
      <!-- Background Effects -->
      <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
        <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-tm7-gold to-[#45A29E] opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)" />
      </div>
      
      <slot />
      
    </main>
    
    <!-- Footer -->
    <footer class="bg-tm7-darker py-8 border-t border-white/5 mt-auto">
      <div class="mx-auto max-w-7xl px-6 lg:px-8 text-center text-sm text-gray-500">
        <p>&copy; {{ new Date().getFullYear() }} The Magnificent 7 Backgammon Club. All rights reserved.</p>
      </div>
    </footer>
  </div>
</template>
