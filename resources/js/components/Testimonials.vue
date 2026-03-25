<template>
  <section class="testi-section">
    <div class="wrap">

      <div style="text-align:center;">
        <div class="eyebrow-c" style="color:var(--gold);">Patient Stories</div>
        <h2 class="sec-h-w">What Our Patients Say</h2>
      </div>

      <div class="testi-grid">
        <div
          v-for="(testimonial, i) in testimonials"
          :key="testimonial.name"
          class="testi-card reveal"
          :style="{ transitionDelay: (i * 0.1) + 's' }"
          :ref="el => { if (el) cardEls.push(el) }"
        >
          <div class="testi-stars">★★★★★</div>
          <p class="testi-text">"{{ testimonial.text }}"</p>
          <div class="testi-foot">
            <div class="testi-av">{{ testimonial.avatar }}</div>
            <div>
              <div class="testi-name">{{ testimonial.name }}</div>
              <div class="testi-date">{{ testimonial.date }}</div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const testimonials = [
  {
    avatar: '😊',
    name: 'Chidinma A.',
    date: 'January 2026',
    text: 'Dr. Okafor was incredibly thorough and compassionate. She listened to every concern and explained everything so clearly. I felt genuinely cared for — and the whole booking experience was seamless.'
  },
  {
    avatar: '😄',
    name: 'James T.',
    date: 'December 2025',
    text: 'Finally a doctor who takes the time to understand your situation. The follow-up notes were incredibly detailed. I highly recommend Meridian Health to anyone managing a chronic condition.'
  },
  {
    avatar: '🙂',
    name: 'Fatima I.',
    date: 'November 2025',
    text: "As someone who struggles with medical anxiety, this virtual format was life-changing. Dr. Okafor made me feel completely at ease from the first minute. She's exactly the doctor I needed."
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