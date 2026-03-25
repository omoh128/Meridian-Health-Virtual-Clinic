<template>
  <section class="section why-section">
    <div class="wrap">

      <div class="eyebrow-c">Why Choose Us</div>
      <h2 class="sec-h" style="text-align:center;">The Meridian Difference</h2>

      <div class="why-grid">
        <div
          v-for="(feature, i) in features"
          :key="feature.title"
          class="why-card reveal"
          :style="{ transitionDelay: (i * 0.08) + 's' }"
          :ref="el => { if (el) cardEls.push(el) }"
        >
          <div class="why-icon">{{ feature.emoji }}</div>
          <div class="why-title">{{ feature.title }}</div>
          <p class="why-desc">{{ feature.desc }}</p>
        </div>
      </div>

    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const features = [
  {
    emoji: '🔒',
    title: 'Fully Encrypted',
    desc: 'All sessions are end-to-end encrypted. Your health data is never shared with third parties — ever.'
  },
  {
    emoji: '🌍',
    title: 'Available Anywhere',
    desc: 'Connect from anywhere in Nigeria or abroad. All you need is a stable internet connection and a device.'
  },
  {
    emoji: '⚡',
    title: 'Same-Day Booking',
    desc: 'Book today and often see a doctor the same day. No long queues, no waiting rooms — just fast, quality care.'
  },
  {
    emoji: '👩‍⚕️',
    title: 'Licensed Practitioners',
    desc: 'Every doctor on Meridian is fully board-certified and vetted through our rigorous clinical onboarding process.'
  },
  {
    emoji: '📋',
    title: 'Detailed Follow-Ups',
    desc: 'Receive comprehensive written notes after every session — diagnosis, care plan, prescriptions, referrals and all.'
  },
  {
    emoji: '💳',
    title: 'Flexible Payment',
    desc: 'Pay by card, bank transfer, or HMO. Transparent pricing with no hidden fees. Insurance claims supported.'
  }
];

const cardEls = ref([]);

onMounted(() => {
  const observer = new IntersectionObserver(
    (entries) => entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); }),
    { threshold: 0.12 }
  );
  cardEls.value.forEach(el => observer.observe(el));
});
</script>