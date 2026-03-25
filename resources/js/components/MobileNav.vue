<template>
  <div>
    <div class="nav-overlay" :class="{ open: isOpen }" @click="$emit('close')"></div>

    <div class="mobile-nav" :class="{ open: isOpen }">
      <div class="mobile-nav-header"> <span class="mobile-nav-logo">Meridian Health</span> <button class="nav-close" @click="$emit('close')">✕</button> </div>

      <ul>
        <li>
          <router-link to="/" @click="$emit('close')">
            <span class="nav-icon">🏠</span> Home
          </router-link>
        </li>
        <li>
          <router-link to="/services" @click="$emit('close')">
            <span class="nav-icon">🩺</span> Services
          </router-link>
        </li>
        <li>
          <router-link to="/book" @click="$emit('close')">
            <span class="nav-icon">📅</span> Book
          </router-link>
        </li>
        <li>
          <router-link to="/doctor" @click="$emit('close')">
            <span class="nav-icon">👩‍⚕️</span> Doctor
          </router-link>
        </li>
      </ul>

      <div class="mobile-nav-footer"> <router-link to="/book" class="mn-cta" @click="$emit('close')">
          Book a Consultation →
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
// Receive the state from Home.vue
defineProps({
  isOpen: Boolean
})

// Tell Home.vue to close the menu
defineEmits(['close'])
</script>