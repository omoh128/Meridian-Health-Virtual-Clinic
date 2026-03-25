<template>
  <div>
    <section class="hero">
      <div class="hero-inner">
        <div class="hero-left">
          <div class="hero-eyebrow">Our Services</div>
          <h1>Expert care,<br>delivered <em>virtually</em></h1>
          <p class="hero-desc">Comprehensive, confidential consultations with licensed practitioners — from your home, on your schedule, without the waiting room.</p>
          <div class="hero-stats">
            <div class="hs"><span class="hs-num">4,800+</span><div class="hs-lbl">Consultations</div></div>
            <div class="hs"><span class="hs-num">98%</span><div class="hs-lbl">Satisfaction</div></div>
            <div class="hs"><span class="hs-num">24hr</span><div class="hs-lbl">Booking</div></div>
          </div>
        </div>
        
        <div class="hero-cards">
          <div 
            v-for="service in services" 
            :key="service.id" 
            class="hc" 
            @click="$router.push('/book')"
          >
            <div class="hc-icon">{{ service.emoji }}</div>
            <div>
              <div class="hc-name">{{ service.name }}</div>
              <div class="hc-dur">{{ service.duration }}</div>
            </div>
            <div class="hc-arrow">→</div>
          </div>
        </div>
      </div>
    </section>

    <section class="services-section">
      <div class="section-header">
        <div class="eyebrow">Consultation Types</div>
        <h2 class="sec-h">What We Offer</h2>
        <p class="sec-sub">Every session is tailored to your needs, conducted over secure video, and documented with detailed follow-up notes.</p>
      </div>

      <div class="services-grid">
        <div v-for="service in services" :key="service.id" class="srv-card">
          <div class="srv-top">
            <div class="srv-emoji">{{ service.emoji }}</div>
            <div class="srv-duration">{{ service.duration }}</div>
          </div>
          <div class="srv-body">
            <div class="srv-name">{{ service.name }}</div>
            <div class="srv-desc">{{ service.description }}</div>
            <div class="srv-features">
              <div v-for="feature in service.features" :key="feature" class="sf">
                {{ feature }}
              </div>
            </div>
            <div class="srv-footer">
              <div class="srv-price">{{ service.price }} <span>/ session</span></div>
              <router-link to="/book" class="srv-btn">Book Now</router-link>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="how-section">
      <div class="how-inner">
        <div class="how-header">
          <div class="how-eyebrow">The Process</div>
          <div class="how-h">How It Works</div>
          <div class="how-sub">From booking to consultation in 4 simple steps</div>
        </div>
        <div class="steps">
          <div v-for="step in steps" :key="step.title" class="step">
            <div class="step-num">{{ step.icon }}</div>
            <div class="step-n">{{ step.title }}</div>
            <div class="step-d">{{ step.desc }}</div>
          </div>
        </div>
      </div>
    </section>

    <section class="why-section">
      <div class="why-inner">
        <div class="why-header">
          <div class="eyebrow">Why Meridian</div>
          <h2 class="sec-h">The Meridian Difference</h2>
        </div>
        <div class="why-grid">
          <div v-for="reason in whyUs" :key="reason.title" class="why-card">
            <div class="wc-icon">{{ reason.icon }}</div>
            <div class="wc-title">{{ reason.title }}</div>
            <p class="wc-desc">{{ reason.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <section class="cta-section">
      <div class="cta-eyebrow">Ready to Start?</div>
      <h2 class="cta-h">Your health journey<br>begins <em>today</em></h2>
      <p class="cta-p">Join thousands of patients who've experienced the convenience and quality of Meridian Health's virtual consultations.</p>
      <div class="cta-btns">
        <router-link to="/book" class="cta-btn-w">Book a Consultation →</router-link>
        <router-link to="/doctors" class="cta-btn-o">Meet the Doctor</router-link>
      </div>
    </section>
  </div>
</template>

<script setup>
const services = [
  {
    id: 1,
    emoji: '🩺',
    name: 'Initial Consultation',
    duration: '60 minutes',
    price: '₦15,000',
    description: 'A comprehensive, in-depth health assessment covering your full medical history, current symptoms, lifestyle, and goals.',
    features: ['Full medical history review', 'Personalised care plan', 'Prescription referrals', 'Detailed follow-up notes']
  },
  {
    id: 2,
    emoji: '📋',
    name: 'Follow-Up Session',
    duration: '30 minutes',
    price: '₦8,000',
    description: 'Review your progress since your last visit, adjust your treatment or care plan if needed, and address any new concerns.',
    features: ['Progress review', 'Treatment plan adjustments', 'Lab result interpretation', 'Ongoing medication review']
  },
  {
    id: 3,
    emoji: '🧠',
    name: 'Mental Wellness',
    duration: '50 minutes',
    price: '₦12,000',
    description: 'Confidential, dedicated sessions for stress, anxiety, burnout, and emotional wellbeing in a safe environment.',
    features: ['Stress & anxiety support', 'Coping strategies', 'Burnout management', '100% confidential']
  },
  {
    id: 4,
    emoji: '🥗',
    name: 'Nutrition Consult',
    duration: '45 minutes',
    price: '₦10,000',
    description: 'Work with a certified nutritionist to build sustainable eating habits and create a personalised dietary plan.',
    features: ['Personalised meal planning', 'Weight management', 'Chronic disease nutrition', 'Supplement guidance']
  }
];

const steps = [
  { icon: '📅', title: 'Choose a Service', desc: 'Select the consultation type that fits your needs.' },
  { icon: '🕐', title: 'Pick a Time', desc: 'Browse available slots and choose a time that works for you.' },
  { icon: '📧', title: 'Get Confirmed', desc: 'Receive your secure Zoom link and calendar invite.' },
  { icon: '🎥', title: 'Start Your Session', desc: 'Join from any device. No downloads needed.' }
];

const whyUs = [
  { icon: '🔒', title: 'Fully Encrypted', desc: 'All sessions are end-to-end encrypted. Your data is secure.' },
  { icon: '🌍', title: 'Available Anywhere', desc: 'Connect from anywhere in Nigeria or abroad.' },
  { icon: '⚡', title: 'Same-Day Booking', desc: 'Get an appointment confirmed in minutes.' },
  { icon: '👩‍⚕️', title: 'Licensed Practitioners', desc: 'Every doctor is fully licensed and board-certified.' },
  { icon: '📋', title: 'Detailed Follow-Ups', desc: 'Receive comprehensive written notes after every session.' },
  { icon: '💳', title: 'Flexible Payment', desc: 'Pay securely by card, bank transfer, or HMO.' }
];
</script>
