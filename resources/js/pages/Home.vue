<template>
  <div class="meridian-landing">

    <Hero />

    <div class="stats-band">
      <div class="stats-inner">
        <div v-for="stat in globalStats" :key="stat.label" class="stat-c">
          <span class="sn">{{ stat.value }}</span>
          <div class="sl">{{ stat.label }}</div>
        </div>
      </div>
    </div>

    <Services />
    <DoctorFeature />
    <HowItWorks />
    <WhyMeridian />
    <Testimonials />
    <CtaSection />
    

  </div>
</template>

<script setup>
import { onMounted, onUnmounted, ref } from 'vue';

// 1. Component Imports
import Hero            from '../components/HeroSection.vue';
import ServicesSection from '../components/Service.vue';
import DoctorFeature   from '../components/DoctorFeature.vue';
import HowItWorks      from '../components/HowItWorks.vue';
import WhyMeridian     from '../components/WhyMeridian.vue';
import Testimonials    from '../components/Testimonials.vue';
import CtaSection      from '../components/CtaSection.vue';


// 2. Data for the Stats Band
const globalStats = [
  { label: 'Consultations',   value: '4,800+' },
  { label: 'Satisfaction',    value: '98%' },
  { label: 'Avg. Turnaround', value: '24hr' },
  { label: 'Encrypted',       value: '100%' }
];

// 3. Mobile Navigation Logic
const isNavOpen = ref(false);

const toggleNav = () => {
  isNavOpen.value = !isNavOpen.value;
  document.body.style.overflow = isNavOpen.value ? 'hidden' : '';
};

const closeNav = () => {
  isNavOpen.value = false;
  document.body.style.overflow = '';
};

// 4. Lifecycle Hooks for Scroll Effects
onMounted(() => {
  /* ── Navbar: transparent on hero, frosted on scroll ── */
  const hdr = document.getElementById('site-header');
  const handleScroll = () => {
    if (hdr) {
      hdr.classList.toggle('scrolled', window.scrollY > 55);
    }
  };
  window.addEventListener('scroll', handleScroll);

  /* ── Scroll reveal observer ── */
  const revealObs = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        // We use 'visible' to match your CSS logic
        e.target.classList.add('visible');
      }
    });
  }, { threshold: 0.12 });

  // Select all elements with the .reveal class
  document.querySelectorAll('.reveal').forEach(el => revealObs.observe(el));

  // Cleanup: Remove event listener when user leaves the page
  onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
  });
});
</script>