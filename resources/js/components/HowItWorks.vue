<template>
  <section class="section how-section">
    <div class="wrap">

      <div style="text-align:center; margin-bottom:0.5rem;">
        <div class="eyebrow-c">The Process</div>
        <h2 class="sec-h">How It Works</h2>
        <p class="sec-sub-c">From first click to consultation — done in minutes.</p>
      </div>

      <div class="how-steps">
        <div
          v-for="(step, i) in steps"
          :key="step.title"
          class="how-step reveal"
          :style="{ transitionDelay: (i * 0.1) + 's' }"
          :ref="el => { if (el) stepEls.push(el) }"
        >
          <div class="how-circle">{{ step.emoji }}</div>
          <div class="how-n">{{ step.title }}</div>
          <div class="how-d">{{ step.desc }}</div>
        </div>
      </div>

    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const steps = [
  {
    emoji: '📅',
    title: 'Choose a Service',
    desc: 'Pick the consultation type that fits your health needs from our range of services.'
  },
  {
    emoji: '🕐',
    title: 'Pick a Time',
    desc: 'Browse available slots and choose a time that works perfectly for your schedule.'
  },
  {
    emoji: '📧',
    title: 'Get Confirmed',
    desc: 'Receive your secure Zoom link and calendar invite within minutes of booking.'
  },
  {
    emoji: '🎥',
    title: 'Start Your Session',
    desc: 'Join from any device — no downloads, no hassle. Click your link and connect.'
  }
];

const stepEls = ref([]);

onMounted(() => {
  const observer = new IntersectionObserver(
    (entries) => entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); }),
    { threshold: 0.12 }
  );
  stepEls.value.forEach(el => observer.observe(el));
});
</script>