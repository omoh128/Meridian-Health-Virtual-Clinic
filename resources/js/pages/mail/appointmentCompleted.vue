<template>
  <div class="completed-page">
    <!-- Animated background -->
    <div class="bg-grid"></div>
    <div class="bg-glow glow-1"></div>
    <div class="bg-glow glow-2"></div>

    <!-- Confetti -->
    <div class="confetti-wrap">
      <span
        v-for="n in 20"
        :key="n"
        class="confetti-piece"
        :style="confettiStyle(n)"
      ></span>
    </div>

    <div class="completed-wrap">

      <!-- Star badge -->
      <div class="star-wrap">
        <div class="star-burst">
          <span v-for="n in 8" :key="n" class="burst-ray" :style="{ transform: `rotate(${n * 45}deg)` }"></span>
        </div>
        <div class="star-circle">⭐</div>
      </div>

      <!-- Heading -->
      <div class="completed-header">
        <h1 class="completed-title">Appointment Complete!</h1>
        <p class="completed-sub">
          Thank you for choosing <span class="brand">Meridian Health</span>.<br>
          We hope your consultation was helpful and you're feeling better.
        </p>
      </div>

      <!-- Summary Card -->
      <div class="summary-card" v-if="booking">
        <div class="card-top">
          <div class="patient-row">
            <div class="patient-avatar">{{ initials }}</div>
            <div class="patient-info">
              <span class="patient-name">{{ booking.first_name }} {{ booking.last_name }}</span>
              <span class="patient-email">{{ booking.email }}</span>
            </div>
            <span class="completed-badge">✅ Completed</span>
          </div>
        </div>

        <div class="card-body">
          <div class="detail-row">
            <span class="dr-label">🩺 Service</span>
            <span class="dr-value">{{ booking.service }}</span>
          </div>
          <div class="detail-row">
            <span class="dr-label">⏰ Time</span>
            <span class="dr-value">{{ booking.time }}</span>
          </div>
          <div class="detail-row">
            <span class="dr-label">⏱ Duration</span>
            <span class="dr-value">{{ booking.duration }}</span>
          </div>
          <div class="detail-row" v-if="booking.reason">
            <span class="dr-label">📝 Notes</span>
            <span class="dr-value">{{ booking.reason }}</span>
          </div>
        </div>
      </div>

      <!-- Care tips -->
      <div class="care-card">
        <h3 class="care-title">💚 Post-Appointment Care</h3>
        <div class="care-grid">
          <div class="care-item" v-for="(tip, i) in tips" :key="i" :style="{ animationDelay: (0.5 + i * 0.1) + 's' }">
            <span class="care-icon">{{ tip.icon }}</span>
            <span class="care-text">{{ tip.text }}</span>
          </div>
        </div>
      </div>

      <!-- Rating -->
      <div class="rating-card">
        <p class="rating-title">How was your experience?</p>
        <div class="stars">
          <button
            v-for="n in 5"
            :key="n"
            class="star-btn"
            :class="{ active: rating >= n, hovered: hovered >= n }"
            @click="rating = n"
            @mouseenter="hovered = n"
            @mouseleave="hovered = 0"
          >★</button>
        </div>
        <transition name="fade">
          <p v-if="rating" class="rating-thanks">
            {{ ratingMessage }}
          </p>
        </transition>
      </div>

      <!-- Actions -->
      <div class="completed-actions">
        <router-link to="/book" class="btn-rebook">
          📅 Book Another Appointment
        </router-link>
        <router-link to="/" class="btn-home">
          ← Back to Home
        </router-link>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'

const route  = useRoute()
const rating = ref(0)
const hovered = ref(0)

const booking = computed(() => {
  return history.state?.booking || {
    first_name: route.query.first_name || 'Patient',
    last_name:  route.query.last_name  || '',
    email:      route.query.email      || '',
    service:    route.query.service    || 'Consultation',
    time:       route.query.time       || '',
    duration:   route.query.duration   || '',
    reason:     route.query.reason     || '',
  }
})

const initials = computed(() =>
  ((booking.value.first_name?.[0] || '') + (booking.value.last_name?.[0] || '')).toUpperCase()
)

const ratingMessage = computed(() => {
  const msgs = ['', 'We\'re sorry to hear that. We\'ll do better!', 'Thank you for your feedback.', 'Glad it was okay!', 'Great to hear! Thank you!', 'Amazing! Thank you so much! 🎉']
  return msgs[rating.value] || ''
})

const tips = [
  { icon: '💧', text: 'Stay hydrated and rest well today.' },
  { icon: '💊', text: 'Follow any prescriptions given during your consultation.' },
  { icon: '📞', text: 'Call us if symptoms worsen or you have questions.' },
  { icon: '📅', text: 'Book a follow-up if recommended by your doctor.' },
]

function confettiStyle(n) {
  const colors = ['#10b981','#6366f1','#f59e0b','#3b82f6','#ec4899','#8b5cf6']
  return {
    left: (Math.random() * 100) + '%',
    background: colors[n % colors.length],
    animationDelay: (Math.random() * 3) + 's',
    animationDuration: (3 + Math.random() * 3) + 's',
    width:  (Math.random() * 8 + 4) + 'px',
    height: (Math.random() * 8 + 4) + 'px',
    borderRadius: Math.random() > 0.5 ? '50%' : '2px',
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Nunito:wght@300;400;500;600;700&display=swap');

* { box-sizing: border-box; margin: 0; padding: 0; }

.completed-page {
  min-height: 100vh;
  background: #060d1f;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  font-family: 'Nunito', sans-serif;
  position: relative;
  overflow: hidden;
}

/* BG */
.bg-grid {
  position: fixed; inset: 0;
  background-image: linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
  background-size: 40px 40px;
  pointer-events: none;
}

.bg-glow {
  position: fixed;
  border-radius: 50%;
  filter: blur(120px);
  pointer-events: none;
  opacity: 0.12;
}
.glow-1 { width: 600px; height: 600px; background: #6366f1; top: -200px; right: -100px; }
.glow-2 { width: 500px; height: 500px; background: #10b981; bottom: -150px; left: -100px; }

/* Confetti */
.confetti-wrap { position: fixed; inset: 0; pointer-events: none; z-index: 0; }
.confetti-piece {
  position: absolute;
  top: -10px;
  animation: confetti-fall linear infinite;
  opacity: 0.7;
}
@keyframes confetti-fall {
  0%   { transform: translateY(-10px) rotate(0deg); opacity: 0.8; }
  100% { transform: translateY(110vh) rotate(720deg); opacity: 0; }
}

/* Wrap */
.completed-wrap {
  position: relative;
  z-index: 1;
  width: 100%;
  max-width: 580px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 24px;
  animation: rise 0.6s ease both;
}
@keyframes rise {
  from { opacity: 0; transform: translateY(30px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* Star */
.star-wrap {
  position: relative;
  width: 100px;
  height: 100px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.star-burst {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.burst-ray {
  position: absolute;
  width: 3px;
  height: 40px;
  background: linear-gradient(to top, transparent, #f59e0b);
  border-radius: 2px;
  transform-origin: center 50px;
  animation: ray-pulse 2s ease-in-out infinite;
}
@keyframes ray-pulse {
  0%, 100% { opacity: 0.3; transform-origin: center 50px; }
  50%       { opacity: 0.8; }
}

.star-circle {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  background: linear-gradient(135deg, #d97706, #f59e0b);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 32px;
  box-shadow: 0 0 40px rgba(245,158,11,0.5);
  animation: star-pop 0.6s cubic-bezier(0.175,0.885,0.32,1.275) both;
  position: relative;
  z-index: 1;
}
@keyframes star-pop {
  from { transform: scale(0) rotate(-180deg); }
  to   { transform: scale(1) rotate(0deg); }
}

/* Header */
.completed-header { text-align: center; }
.completed-title {
  font-family: 'Playfair Display', serif;
  font-size: 36px;
  font-weight: 700;
  color: #f8fafc;
  letter-spacing: -0.5px;
  margin-bottom: 10px;
}
.completed-sub { font-size: 15px; color: #64748b; line-height: 1.7; }
.brand { color: #10b981; font-weight: 700; }

/* Summary Card */
.summary-card {
  width: 100%;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 20px;
  overflow: hidden;
  backdrop-filter: blur(10px);
}

.card-top {
  padding: 20px 24px;
  border-bottom: 1px solid rgba(255,255,255,0.07);
  background: rgba(255,255,255,0.03);
}

.patient-row {
  display: flex;
  align-items: center;
  gap: 14px;
}

.patient-avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: linear-gradient(135deg, #10b981, #059669);
  color: #fff;
  font-size: 16px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.patient-info { flex: 1; display: flex; flex-direction: column; gap: 2px; }
.patient-name  { font-size: 15px; font-weight: 700; color: #f1f5f9; }
.patient-email { font-size: 12px; color: #64748b; }

.completed-badge {
  background: #d1fae5;
  color: #059669;
  font-size: 11px;
  font-weight: 700;
  padding: 4px 12px;
  border-radius: 20px;
  white-space: nowrap;
}

.card-body { padding: 8px 0; }

.detail-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 24px;
  border-bottom: 1px solid rgba(255,255,255,0.04);
}
.detail-row:last-child { border-bottom: none; }

.dr-label { font-size: 13px; color: #64748b; }
.dr-value { font-size: 13px; color: #e2e8f0; font-weight: 600; text-align: right; max-width: 60%; }

/* Care Card */
.care-card {
  width: 100%;
  background: rgba(16,185,129,0.08);
  border: 1px solid rgba(16,185,129,0.2);
  border-radius: 20px;
  padding: 24px;
}

.care-title {
  font-size: 15px;
  font-weight: 700;
  color: #10b981;
  margin-bottom: 16px;
}

.care-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.care-item {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  background: rgba(255,255,255,0.04);
  border-radius: 10px;
  padding: 12px;
  animation: rise 0.5s ease both;
}

.care-icon { font-size: 18px; flex-shrink: 0; }
.care-text { font-size: 13px; color: #94a3b8; line-height: 1.5; }

/* Rating */
.rating-card {
  width: 100%;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 20px;
  padding: 24px;
  text-align: center;
}

.rating-title {
  font-size: 14px;
  color: #94a3b8;
  margin-bottom: 16px;
  font-weight: 600;
}

.stars { display: flex; justify-content: center; gap: 8px; margin-bottom: 12px; }

.star-btn {
  background: transparent;
  border: none;
  font-size: 32px;
  cursor: pointer;
  color: #334155;
  transition: all 0.15s;
  line-height: 1;
}
.star-btn.active, .star-btn.hovered { color: #f59e0b; transform: scale(1.15); }

.rating-thanks {
  font-size: 13px;
  color: #10b981;
  font-weight: 600;
}

/* Actions */
.completed-actions {
  display: flex;
  flex-direction: column;
  gap: 10px;
  width: 100%;
}

.btn-rebook, .btn-home {
  display: block;
  width: 100%;
  padding: 15px;
  border-radius: 12px;
  font-family: 'Nunito', sans-serif;
  font-size: 15px;
  font-weight: 700;
  text-decoration: none;
  text-align: center;
  transition: all 0.2s;
}

.btn-rebook {
  background: linear-gradient(135deg, #059669, #10b981);
  color: #fff;
  box-shadow: 0 4px 20px rgba(16,185,129,0.35);
}
.btn-rebook:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(16,185,129,0.45); }

.btn-home {
  background: rgba(255,255,255,0.06);
  color: #94a3b8;
  border: 1px solid rgba(255,255,255,0.1);
}
.btn-home:hover { background: rgba(255,255,255,0.1); color: #e2e8f0; }

/* Transition */
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

@media (max-width: 640px) {
  .care-grid { grid-template-columns: 1fr; }
  .completed-title { font-size: 28px; }
  .patient-row { flex-wrap: wrap; }
}
</style>