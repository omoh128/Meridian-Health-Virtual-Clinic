<template>
  <section class="hero">
    <div class="orb orb-a"></div>
    <div class="orb orb-b"></div>
    <div class="orb orb-c"></div>

    <div class="hero-inner">

      <!-- Left: Copy -->
      <div class="hero-left">
        <div class="hero-badge">
          <div class="badge-pulse"></div>
          <span class="badge-txt">Now Accepting New Patients</span>
        </div>

        <h1>
          <span class="line">Your health,</span>
          <span class="line">our <span class="accent">priority</span> —</span>
          <span class="line">virtually.</span>
        </h1>

        <p class="hero-desc">
          Confidential, secure video consultations with a licensed
          general practitioner. Book today, be seen today — no waiting
          rooms, no commute, no compromise on care.
        </p>

        <div class="hero-actions">
          <router-link to="/book" class="btn-hero-p">Book a Consultation →</router-link>
          <router-link to="/services" class="btn-hero-g">Explore Services</router-link>
        </div>

        <div class="trust-row">
          <div v-for="chip in trustChips" :key="chip" class="trust-chip">
            <div class="tc-dot"></div>
            <span class="tc-txt">{{ chip }}</span>
          </div>
        </div>
      </div>

      <!-- Right: Dashboard card -->
      <div class="hero-right">
        <div class="float-pill pill-top">🔒 Encrypted &amp; Private</div>

        <div class="dash">
          <div class="dash-top">
            <div class="dash-av">👩‍⚕️</div>
            <div>
              <div class="dash-name">Dr. Sarah Okafor</div>
              <div class="dash-role">General Practitioner · MBBS, FWACP</div>
            </div>
            <div class="online-chip">Online</div>
          </div>

          <div class="dash-sec-lbl">Upcoming Appointments</div>
          <div v-for="appt in appointments" :key="appt.name" class="appt">
            <div class="appt-dot" :class="appt.dotColor"></div>
            <div class="appt-info">
              <div class="appt-name">{{ appt.name }}</div>
              <div class="appt-time">{{ appt.time }}</div>
            </div>
            <button class="appt-btn" :class="appt.btnType">{{ appt.btnLabel }}</button>
          </div>

          <div class="dash-divider"></div>
          <div class="dash-sec-lbl">Practice Stats</div>
          <div class="dash-stats">
            <div v-for="stat in practiceStats" :key="stat.label" class="ds">
              <span class="ds-n">{{ stat.value }}</span>
              <div class="ds-l">{{ stat.label }}</div>
            </div>
          </div>
        </div>

        <div class="float-pill pill-bot">✅ Confirmed in seconds</div>
      </div>

    </div>

    <!-- Scroll cue -->
    <div class="scroll-hint">
      <div class="sh-line"></div>
      <span class="sh-txt">Scroll</span>
    </div>
  </section>
</template>

<script setup>
const trustChips = [
  'End-to-end encrypted',
  'Licensed practitioners',
  'Same-day slots'
];

const appointments = [
  {
    name: 'Initial Consultation',
    time: 'Today · 2:00 PM · 60 min',
    dotColor: 'green',
    btnType: 'join',
    btnLabel: 'Join'
  },
  {
    name: 'Follow-Up Session',
    time: 'Thu · 11:00 AM · 30 min',
    dotColor: 'gold',
    btnType: 'view',
    btnLabel: 'View'
  }
];

const practiceStats = [
  { value: '4.9★', label: 'Rating' },
  { value: '2.4k',  label: 'Patients' },
  { value: '14yr',  label: 'Exp.' }
];
</script>