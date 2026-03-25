<template>
  <section class="section services-section">
    <div class="wrap">
      <div class="srv-header">
        <div>
          <div class="eyebrow">What We Offer</div>
          <h2 class="sec-h">Our Consultation Services</h2>
        </div>
        <router-link to="/services" class="see-all">View all services →</router-link>
      </div>

      <div class="srv-grid">
        <router-link
          v-for="(service, i) in services"
          :key="service.name"
          to="/book"
          class="srv-card reveal"
          :style="{ transitionDelay: (i * 0.08) + 's' }"
          :ref="el => { if (el) cards.push(el) }"
        >
          <div class="srv-img">
            {{ service.emoji }}
            <div class="srv-dur">{{ service.duration }}</div>
          </div>
          <div class="srv-body">
            <div class="srv-name">{{ service.name }}</div>
            <div class="srv-desc">{{ service.desc }}</div>
            <div class="srv-arrow">Book now →</div>
          </div>
        </router-link>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const services = [
  {
    emoji: '🩺',
    duration: '60 min',
    name: 'Initial Consultation',
    desc: 'Comprehensive health assessment covering your full medical history, symptoms, and goals — with a personalised care plan.'
  },
  {
    emoji: '📋',
    duration: '30 min',
    name: 'Follow-Up Session',
    desc: 'Review your progress, adjust your treatment plan, and address any ongoing or new concerns with your practitioner.'
  },
  {
    emoji: '🧠',
    duration: '50 min',
    name: 'Mental Wellness',
    desc: 'Confidential, safe sessions for stress, anxiety, burnout, and emotional wellbeing in a non-judgmental space.'
  },
  {
    emoji: '🥗',
    duration: '45 min',
    name: 'Nutrition Consult',
    desc: 'Build sustainable eating habits and personalised dietary plans with a certified nutritionist aligned to your health goals.'
  }
];

const cards = ref([]);

onMounted(() => {
  const observer = new IntersectionObserver(
    (entries) => entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); }),
    { threshold: 0.12 }
  );
  cards.value.forEach(card => {
    const el = card.$el ?? card;
    if (el) observer.observe(el);
  });
});
</script>