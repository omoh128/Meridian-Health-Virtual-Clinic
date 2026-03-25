<template>
  <section class="doc-section">
    <div class="doc-inner">

      <!-- Left: Copy -->
      <div class="doc-left">
        <div class="doc-eyebrow">Meet Your Doctor</div>
        <h2>Trusted care from<br><em>Dr. Sarah Okafor</em></h2>
        <p class="doc-bio">
          Board-certified General and Family Physician with 14 years of clinical
          experience across Lagos and London. Known for her warm, patient-centred
          approach and her belief that quality healthcare should be accessible to all.
        </p>
        <div class="doc-tags">
          <span v-for="tag in specialties" :key="tag" class="doc-tag">{{ tag }}</span>
        </div>
        <router-link to="/doctor" class="btn-gold">View Full Profile →</router-link>
      </div>

      <!-- Right: Profile card -->
      <div class="doc-card reveal" ref="card">
        <div class="doc-photo">
          👩‍⚕️
          <div class="doc-photo-badge">
            <div class="dpb-dot"></div>
            <span class="dpb-txt">Available for consultations today</span>
          </div>
        </div>
        <div class="doc-card-body">
          <div class="dcb-name">Dr. Sarah Okafor</div>
          <div class="dcb-role">MBBS, FWACP · General &amp; Family Medicine</div>
          <div class="dcb-grid">
            <div v-for="stat in docStats" :key="stat.label" class="dcg">
              <span class="dcg-n">{{ stat.value }}</span>
              <div class="dcg-l">{{ stat.label }}</div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const specialties = [
  'General Medicine',
  'Family Health',
  'Chronic Disease',
  'Mental Wellness',
  "Women's Health",
  'Nutrition'
];

const docStats = [
  { value: '2.4k+', label: 'Patients' },
  { value: '4.9★', label: 'Rating' },
  { value: '14yr',  label: 'Exp.' },
  { value: '98%',   label: 'Sat.' }
];

const card = ref(null);

onMounted(() => {
  const observer = new IntersectionObserver(
    (entries) => entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); }),
    { threshold: 0.12 }
  );
  if (card.value) observer.observe(card.value);
});
</script>