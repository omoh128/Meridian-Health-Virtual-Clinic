<template>
  <div class="confirm-page">
    <!-- Floating particles -->
    <div class="particles">
      <span v-for="n in 12" :key="n" class="particle" :style="particleStyle(n)"></span>
    </div>

    <div class="confirm-wrap">
      <!-- Success Icon -->
      <div class="success-ring">
        <div class="ring ring-3"></div>
        <div class="ring ring-2"></div>
        <div class="ring ring-1"></div>
        <div class="check-circle">
          <svg class="check-svg" viewBox="0 0 52 52">
            <circle class="check-bg" cx="26" cy="26" r="25"/>
            <path class="check-mark" fill="none" d="M14 27l8 8 16-16"/>
          </svg>
        </div>
      </div>

      <!-- Heading -->
      <div class="confirm-header">
        <h1 class="confirm-title">Booking Confirmed!</h1>
        <p class="confirm-sub">
          Your appointment request has been received.<br>
          We'll send a confirmation to <strong>{{ booking.email }}</strong> once the doctor approves.
        </p>
      </div>

      <!-- Booking ID badge -->
      <div class="booking-id-badge">
        <span class="bid-label">Booking Reference</span>
        <span class="bid-value">#MH-{{ String(booking.id).padStart(4, '0') }}</span>
      </div>

      <!-- Summary Card -->
      <div class="summary-card">
        <div class="summary-header">
          <span class="summary-title">Appointment Summary</span>
          <span class="status-pill pending">⏳ Awaiting Confirmation</span>
        </div>

        <div class="summary-grid">
          <div class="summary-item">
            <div class="si-icon">👤</div>
            <div class="si-body">
              <span class="si-label">Patient</span>
              <span class="si-value">{{ booking.first_name }} {{ booking.last_name }}</span>
            </div>
          </div>

          <div class="summary-item">
            <div class="si-icon">✉️</div>
            <div class="si-body">
              <span class="si-label">Email</span>
              <span class="si-value">{{ booking.email }}</span>
            </div>
          </div>

          <div class="summary-item">
            <div class="si-icon">🩺</div>
            <div class="si-body">
              <span class="si-label">Service</span>
              <span class="si-value">{{ booking.service }}</span>
            </div>
          </div>

          <div class="summary-item">
            <div class="si-icon">⏰</div>
            <div class="si-body">
              <span class="si-label">Time</span>
              <span class="si-value">{{ booking.time }}</span>
            </div>
          </div>

          <div class="summary-item">
            <div class="si-icon">⏱️</div>
            <div class="si-body">
              <span class="si-label">Duration</span>
              <span class="si-value">{{ booking.duration }}</span>
            </div>
          </div>

          <div class="summary-item" v-if="booking.phone">
            <div class="si-icon">📞</div>
            <div class="si-body">
              <span class="si-label">Phone</span>
              <span class="si-value">{{ booking.phone }}</span>
            </div>
          </div>

          <div class="summary-item full" v-if="booking.reason">
            <div class="si-icon">📝</div>
            <div class="si-body">
              <span class="si-label">Reason / Notes</span>
              <span class="si-value">{{ booking.reason }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- What's Next -->
      <div class="next-steps">
        <h3 class="next-title">What happens next?</h3>
        <div class="steps">
          <div class="step" v-for="(step, i) in steps" :key="i" :style="{ animationDelay: (0.6 + i * 0.12) + 's' }">
            <div class="step-num">{{ i + 1 }}</div>
            <div class="step-body">
              <span class="step-label">{{ step.label }}</span>
              <span class="step-desc">{{ step.desc }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="confirm-actions">
        <router-link to="/" class="btn-home">← Back to Home</router-link>
        <router-link to="/book" class="btn-book">Book Another</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

// Booking data passed as route state or query params
const booking = computed(() => {
  return history.state?.booking || {
    id: route.query.id || '0000',
    first_name: route.query.first_name || '',
    last_name:  route.query.last_name  || '',
    email:      route.query.email      || '',
    service:    route.query.service    || '',
    time:       route.query.time       || '',
    duration:   route.query.duration   || '',
    phone:      route.query.phone      || '',
    reason:     route.query.reason     || '',
  }
})

const steps = [
  { label: 'Doctor Review',    desc: 'Our doctor will review your booking within a few hours.' },
  { label: 'Email Confirmation', desc: 'You\'ll receive an email once your appointment is confirmed.' },
  { label: 'Your Appointment', desc: 'Attend your virtual consultation at your scheduled time.' },
]

function particleStyle(n) {
  const size = Math.random() * 6 + 3
  return {
    width:  size + 'px',
    height: size + 'px',
    left:   (Math.random() * 100) + '%',
    top:    (Math.random() * 100) + '%',
    animationDelay: (n * 0.3) + 's',
    animationDuration: (4 + Math.random() * 4) + 's',
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Nunito:wght@300;400;500;600;700&display=swap');

* { box-sizing: border-box; margin: 0; padding: 0; }

.confirm-page {
  min-height: 100vh;
  background: linear-gradient(160deg, #f0fdf4 0%, #ecfdf5 40%, #f0f9ff 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  font-family: 'Nunito', sans-serif;
  position: relative;
  overflow: hidden;
}

/* Particles */
.particles { position: fixed; inset: 0; pointer-events: none; z-index: 0; }
.particle {
  position: absolute;
  border-radius: 50%;
  background: #10b981;
  opacity: 0.15;
  animation: float linear infinite;
}
@keyframes float {
  0%   { transform: translateY(0) scale(1); opacity: 0.15; }
  50%  { opacity: 0.3; }
  100% { transform: translateY(-100vh) scale(0.5); opacity: 0; }
}

.confirm-wrap {
  position: relative;
  z-index: 1;
  width: 100%;
  max-width: 600px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 28px;
  animation: rise 0.6s ease both;
}
@keyframes rise {
  from { opacity: 0; transform: translateY(30px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* Success Ring */
.success-ring {
  position: relative;
  width: 110px;
  height: 110px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.ring {
  position: absolute;
  border-radius: 50%;
  border: 2px solid #10b981;
  animation: expand 2s ease-out infinite;
}
.ring-1 { width: 110px; height: 110px; opacity: 0.15; animation-delay: 0s; }
.ring-2 { width: 85px;  height: 85px;  opacity: 0.25; animation-delay: 0.3s; }
.ring-3 { width: 60px;  height: 60px;  opacity: 0.35; animation-delay: 0.6s; }

@keyframes expand {
  0%   { transform: scale(0.8); opacity: 0.4; }
  100% { transform: scale(1.3); opacity: 0; }
}

.check-circle {
  width: 72px;
  height: 72px;
  background: linear-gradient(135deg, #059669, #10b981);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 32px rgba(16,185,129,0.4);
  animation: pop 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
}
@keyframes pop {
  from { transform: scale(0); }
  to   { transform: scale(1); }
}

.check-svg { width: 36px; height: 36px; }
.check-bg  { fill: none; }
.check-mark {
  stroke: #fff;
  stroke-width: 3.5;
  stroke-linecap: round;
  stroke-linejoin: round;
  stroke-dasharray: 48;
  stroke-dashoffset: 48;
  animation: draw 0.5s 0.3s ease forwards;
}
@keyframes draw {
  to { stroke-dashoffset: 0; }
}

/* Header */
.confirm-header { text-align: center; }
.confirm-title {
  font-family: 'Playfair Display', serif;
  font-size: 36px;
  font-weight: 700;
  color: #064e3b;
  letter-spacing: -0.5px;
  margin-bottom: 10px;
}
.confirm-sub {
  font-size: 15px;
  color: #6b7280;
  line-height: 1.7;
}
.confirm-sub strong { color: #059669; }

/* Booking ID */
.booking-id-badge {
  display: flex;
  align-items: center;
  gap: 12px;
  background: #064e3b;
  color: #fff;
  padding: 10px 24px;
  border-radius: 100px;
}
.bid-label { font-size: 12px; opacity: 0.6; text-transform: uppercase; letter-spacing: 1px; }
.bid-value { font-size: 16px; font-weight: 700; letter-spacing: 1px; font-family: 'Nunito', monospace; }

/* Summary Card */
.summary-card {
  width: 100%;
  background: #fff;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 4px 40px rgba(0,0,0,0.08);
  border: 1px solid #d1fae5;
}

.summary-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 18px 24px;
  background: #f0fdf4;
  border-bottom: 1px solid #d1fae5;
}

.summary-title {
  font-size: 13px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  color: #065f46;
}

.status-pill {
  font-size: 12px;
  font-weight: 600;
  padding: 4px 12px;
  border-radius: 20px;
}
.status-pill.pending { background: #fef3c7; color: #d97706; }

.summary-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0;
  padding: 8px 0;
}

.summary-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 14px 24px;
  border-bottom: 1px solid #f0fdf4;
  transition: background 0.15s;
}
.summary-item:hover { background: #f0fdf4; }
.summary-item.full  { grid-column: 1 / -1; }

.si-icon { font-size: 18px; flex-shrink: 0; margin-top: 1px; }
.si-body { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.si-label { font-size: 11px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }
.si-value { font-size: 14px; color: #111827; font-weight: 600; word-break: break-word; }

/* Next Steps */
.next-steps {
  width: 100%;
  background: #fff;
  border-radius: 20px;
  padding: 24px;
  box-shadow: 0 4px 40px rgba(0,0,0,0.06);
  border: 1px solid #e5e7eb;
}

.next-title {
  font-family: 'Playfair Display', serif;
  font-size: 18px;
  color: #111827;
  margin-bottom: 20px;
  font-weight: 600;
}

.steps { display: flex; flex-direction: column; gap: 16px; }

.step {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  animation: rise 0.5s ease both;
}

.step-num {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background: linear-gradient(135deg, #059669, #10b981);
  color: #fff;
  font-size: 13px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.step-body { display: flex; flex-direction: column; gap: 2px; padding-top: 4px; }
.step-label { font-size: 14px; font-weight: 700; color: #111827; }
.step-desc  { font-size: 13px; color: #6b7280; line-height: 1.5; }

/* Actions */
.confirm-actions {
  display: flex;
  gap: 12px;
  width: 100%;
}

.btn-home, .btn-book {
  flex: 1;
  padding: 14px;
  border-radius: 12px;
  font-family: 'Nunito', sans-serif;
  font-size: 14px;
  font-weight: 700;
  text-decoration: none;
  text-align: center;
  transition: all 0.2s;
}

.btn-home {
  background: #f0fdf4;
  color: #059669;
  border: 2px solid #d1fae5;
}
.btn-home:hover { background: #d1fae5; }

.btn-book {
  background: linear-gradient(135deg, #059669, #10b981);
  color: #fff;
  border: 2px solid transparent;
  box-shadow: 0 4px 16px rgba(16,185,129,0.35);
}
.btn-book:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(16,185,129,0.4); }

@media (max-width: 640px) {
  .summary-grid { grid-template-columns: 1fr; }
  .confirm-title { font-size: 28px; }
  .confirm-actions { flex-direction: column; }
}
</style>