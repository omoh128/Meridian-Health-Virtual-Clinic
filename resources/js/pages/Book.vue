<template>
  <div class="booking-page">

    <!-- ── CONFIRMATION SCREEN ── -->
    <transition name="fade">
      <div v-if="bookingConfirmed" class="confirmed-overlay">
        <div class="confirmed-wrap">

          <!-- Animated check icon -->
          <div class="confirmed-icon-wrap">
            <div class="ring r1"></div>
            <div class="ring r2"></div>
            <div class="ring r3"></div>
            <div class="check-circle">
              <svg viewBox="0 0 52 52" class="check-svg">
                <circle class="check-bg" cx="26" cy="26" r="25"/>
                <path class="check-mark" fill="none" d="M14 27l8 8 16-16"/>
              </svg>
            </div>
          </div>

          <h1 class="confirmed-title">Appointment Confirmed! 🎉</h1>
          <p class="confirmed-sub">
            Thank you, <strong>{{ booking.firstName }}</strong>!<br>
            Your appointment has been received. We'll send a confirmation to
            <strong>{{ booking.email }}</strong> once the doctor approves.
          </p>

          <!-- Booking summary card -->
          <div class="confirmed-card">
            <div class="cc-row">
              <span class="cc-label">Service</span>
              <span class="cc-value">{{ booking.service }}</span>
            </div>
            <div class="cc-row">
              <span class="cc-label">Duration</span>
              <span class="cc-value">{{ booking.duration }}</span>
            </div>
            <div class="cc-row">
              <span class="cc-label">Date</span>
              <span class="cc-value">{{ booking.date || 'Today' }}</span>
            </div>
            <div class="cc-row">
              <span class="cc-label">Time</span>
              <span class="cc-value">{{ booking.time }}</span>
            </div>
          </div>

          <div class="confirmed-actions">
            <router-link to="/" class="cbtn-home">← Back to Home</router-link>
            <button class="cbtn-new" @click="resetBooking">Book Another</button>
          </div>
        </div>
      </div>
    </transition>

    <!-- ── BOOKING FORM ── -->
    <div v-if="!bookingConfirmed">

      <div class="page-head">
        <div class="eyebrow">Schedule a Visit</div>
        <h1>Book Your Appointment</h1>
        <p>Pick a service, choose a time, and fill in your details — done in under 2 minutes.</p>
      </div>

      <!-- Progress Bar -->
      <div class="progress-bar">
        <div class="progress-inner">
          <div v-for="step in 3" :key="step" class="progress-step-wrapper">
            <div :class="['progress-step', { current: currentStep === step, completed: currentStep > step }]">
              <div class="ps-num">
                <span v-if="currentStep > step">✓</span>
                <span v-else>{{ step }}</span>
              </div>
              <div class="ps-label">{{ stepLabels[step - 1] }}</div>
            </div>
            <div v-if="step < 3" :class="['ps-line', { completed: currentStep > step }]"></div>
          </div>
        </div>
      </div>

      <!-- General error -->
      <div v-if="errors.general" class="error-banner">
        ⚠️ {{ errors.general }}
      </div>

      <div class="main">
        <div class="form-area">

          <!-- STEP 1: Service -->
          <div v-if="currentStep === 1" class="step-panel active">
            <div class="form-card">
              <div class="card-title"><div class="step-badge">1</div> Your Doctor</div>
              <div class="doc-mini">
                <div class="doc-mini-avatar">👩‍⚕️</div>
                <div>
                  <div class="doc-mini-name">Dr. Sarah Okafor</div>
                  <div class="doc-mini-role">General &amp; Family Medicine · 14 yrs experience</div>
                </div>
                <router-link to="/doctor" class="doc-mini-link">View Profile →</router-link>
              </div>
            </div>

            <div class="form-card">
              <div class="card-title"><div class="step-badge">2</div> Select a Consultation Type</div>
              <div class="service-options">
                <div
                  v-for="service in services"
                  :key="service.name"
                  :class="['service-opt', { selected: booking.service === service.name }]"
                  @click="selectService(service)"
                >
                  <div class="so-icon">{{ service.icon }}</div>
                  <div class="so-info">
                    <div class="so-name">{{ service.name }}</div>
                    <div class="so-desc">{{ service.duration }} · {{ service.desc }}</div>
                  </div>
                  <div class="so-check">{{ booking.service === service.name ? '✓' : '' }}</div>
                </div>
              </div>
            </div>

            <div class="nav-btns">
              <button class="btn-next" :disabled="!booking.service" @click="currentStep = 2">
                Continue to Schedule →
              </button>
            </div>
          </div>

          <!-- STEP 2: Date & Time via AvailabilityPicker -->
          <div v-if="currentStep === 2" class="step-panel active">
            <div class="form-card">
              <div class="card-title"><div class="step-badge">2</div> Choose a Date &amp; Time</div>
              <p class="picker-hint">Select an available date, then pick a time slot.</p>
              <AvailabilityPicker @select="onSlotSelected" />
            </div>
            <div class="nav-btns">
              <button class="btn-back" @click="currentStep = 1">← Back</button>
              <button class="btn-next" :disabled="!booking.time" @click="currentStep = 3">
                Continue to Details →
              </button>
            </div>
          </div>

          <!-- STEP 3: Patient Details -->
          <div v-if="currentStep === 3" class="step-panel active">
            <div class="form-card">
              <div class="card-title"><div class="step-badge">3</div> Your Details</div>
              <div class="form-row">
                <div class="form-group" :class="{ 'has-error': errors.first_name }">
                  <label>First Name *</label>
                  <input type="text" v-model="booking.firstName" placeholder="e.g. Amaka" @input="clearError('first_name')" />
                  <span v-if="errors.first_name" class="field-error">{{ errors.first_name[0] }}</span>
                </div>
                <div class="form-group" :class="{ 'has-error': errors.last_name }">
                  <label>Last Name *</label>
                  <input type="text" v-model="booking.lastName" placeholder="e.g. Okonkwo" @input="clearError('last_name')" />
                  <span v-if="errors.last_name" class="field-error">{{ errors.last_name[0] }}</span>
                </div>
              </div>
              <div class="form-group" :class="{ 'has-error': errors.email }">
                <label>Email Address *</label>
                <input type="email" v-model="booking.email" placeholder="you@example.com" @input="clearError('email')" />
                <span v-if="errors.email" class="field-error">{{ errors.email[0] }}</span>
              </div>
              <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" v-model="booking.phone" placeholder="+234 801 000 0000" />
              </div>
              <div class="form-group">
                <label>Reason for Visit <span class="optional">(optional)</span></label>
                <textarea v-model="booking.reason" rows="3" placeholder="Briefly describe your main concern..."></textarea>
              </div>
            </div>

            <div class="nav-btns">
              <button class="btn-back" @click="currentStep = 2">← Back</button>
              <button
                class="btn-next"
                @click="submitBooking"
                :disabled="isSubmitting || !booking.firstName || !booking.lastName || !booking.email"
              >
                <span v-if="!isSubmitting">Confirm Appointment →</span>
                <span v-else class="spinner">⟳ Submitting...</span>
              </button>
            </div>
          </div>

        </div>

        <!-- Sidebar Summary -->
        <div class="sidebar">
          <div class="summary-card">
            <h3>📋 Appointment Summary</h3>
            <div class="sum-row">
              <span class="sum-lbl">Doctor</span>
              <span class="sum-val">Dr. Sarah Okafor</span>
            </div>
            <div class="sum-row">
              <span class="sum-lbl">Service</span>
              <span :class="['sum-val', { empty: !booking.service }]">
                {{ booking.service || 'Not selected' }}
              </span>
            </div>
            <div class="sum-row">
              <span class="sum-lbl">Duration</span>
              <span :class="['sum-val', { empty: !booking.duration }]">
                {{ booking.duration || '—' }}
              </span>
            </div>
            <div class="sum-divider"></div>
            <div class="sum-row">
              <span class="sum-lbl">Date</span>
              <span :class="['sum-val', { empty: !booking.date }]">
                {{ booking.date || 'Not selected' }}
              </span>
            </div>
            <div class="sum-row">
              <span class="sum-lbl">Time</span>
              <span :class="['sum-val', { empty: !booking.time }]">
                {{ booking.time || 'Not selected' }}
              </span>
            </div>
            <div class="sum-divider"></div>
            <div class="sum-row">
              <span class="sum-lbl">Format</span>
              <span class="sum-val">Video · Zoom</span>
            </div>
            <div class="security-note">
              <span>🔒</span>
              <p>Your session is fully encrypted and completely confidential.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import axios from '../plugins/axios.js'
import AvailabilityPicker from '../components/AvailabilityPicker.vue'

const currentStep      = ref(1)
const isSubmitting     = ref(false)
const bookingConfirmed = ref(false)
const errors           = reactive({})
const stepLabels       = ['Service', 'Schedule', 'Details']

const booking = reactive({
  service:   '',
  duration:  '',
  date:      '',
  time:      '',
  firstName: '',
  lastName:  '',
  email:     '',
  phone:     '',
  reason:    '',
})

const services = [
  { name: 'Initial Consultation', duration: '60 minutes', desc: 'Comprehensive health assessment',       icon: '🩺' },
  { name: 'Follow-Up Session',    duration: '30 minutes', desc: 'Progress review & care adjustment',     icon: '📋' },
  { name: 'Mental Wellness',      duration: '50 minutes', desc: 'Stress, anxiety & emotional wellbeing', icon: '🧠' },
  { name: 'Nutrition Consult',    duration: '45 minutes', desc: 'Personalised dietary planning',         icon: '🥗' },
]

function selectService(service) {
  booking.service  = service.name
  booking.duration = service.duration
}

// Called by AvailabilityPicker when user picks a date + slot
function onSlotSelected({ date, time }) {
  booking.date = date
  booking.time = time
}

function clearError(field) {
  delete errors[field]
}

function resetBooking() {
  bookingConfirmed.value = false
  currentStep.value = 1
  Object.assign(booking, {
    service: '', duration: '', date: '', time: '',
    firstName: '', lastName: '', email: '', phone: '', reason: '',
  })
}

async function submitBooking() {
  isSubmitting.value = true
  Object.keys(errors).forEach(k => delete errors[k])

  try {
    await axios.post('/bookings', {
      first_name: booking.firstName,
      last_name:  booking.lastName,
      email:      booking.email,
      phone:      booking.phone,
      service:    booking.service,
      duration:   booking.duration,
      time:       booking.time,
      reason:     booking.reason,
    })

    // Show inline confirmation — no page redirect needed
    bookingConfirmed.value = true
    window.scrollTo({ top: 0, behavior: 'smooth' })

  } catch (err) {
    if (err.response?.status === 422) {
      Object.assign(errors, err.response.data.errors)
    } else {
      errors.general = 'Something went wrong. Please try again.'
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Nunito:wght@300;400;500;600;700&display=swap');

* { box-sizing: border-box; margin: 0; padding: 0; }

.booking-page {
  font-family: 'Nunito', sans-serif;
  background: #f8fafc;
  min-height: 100vh;
}

/* ── PAGE HEADER ── */
.page-head {
  text-align: center;
  padding: 60px 20px 32px;
  background: linear-gradient(160deg, #f0fdf4, #f0f9ff);
}

.eyebrow {
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 2px;
  color: #059669;
  margin-bottom: 10px;
}

.page-head h1 {
  font-family: 'Playfair Display', serif;
  font-size: 36px;
  color: #0f172a;
  letter-spacing: -0.5px;
  margin-bottom: 10px;
}

.page-head p {
  font-size: 15px;
  color: #64748b;
  max-width: 480px;
  margin: 0 auto;
  line-height: 1.6;
}

/* ── PROGRESS BAR ── */
.progress-bar {
  padding: 24px 20px;
  background: #fff;
  border-bottom: 1px solid #e2e8f0;
}

.progress-inner {
  display: flex;
  align-items: center;
  justify-content: center;
  max-width: 480px;
  margin: 0 auto;
}

.progress-step-wrapper {
  display: flex;
  align-items: center;
}

.progress-step {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
}

.ps-num {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #e2e8f0;
  color: #94a3b8;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 14px;
  transition: all 0.3s;
}

.progress-step.current .ps-num {
  background: linear-gradient(135deg, #059669, #10b981);
  color: #fff;
  box-shadow: 0 4px 12px rgba(16,185,129,0.35);
}

.progress-step.completed .ps-num {
  background: #064e3b;
  color: #fff;
}

.ps-label {
  font-size: 11px;
  font-weight: 600;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  white-space: nowrap;
}

.progress-step.current .ps-label,
.progress-step.completed .ps-label { color: #059669; }

.ps-line {
  width: 80px;
  height: 2px;
  background: #e2e8f0;
  margin: 0 8px;
  margin-bottom: 20px;
  transition: background 0.3s;
  flex-shrink: 0;
}
.ps-line.completed { background: #059669; }

/* ── ERROR BANNER ── */
.error-banner {
  max-width: 900px;
  margin: 16px auto 0;
  padding: 12px 20px;
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 10px;
  color: #dc2626;
  font-size: 14px;
}

/* ── MAIN LAYOUT ── */
.main {
  max-width: 900px;
  margin: 0 auto;
  padding: 32px 20px 60px;
  display: grid;
  grid-template-columns: 1fr 320px;
  gap: 28px;
  align-items: start;
}

/* ── FORM CARDS ── */
.step-panel { display: flex; flex-direction: column; gap: 20px; }

.form-card {
  background: #fff;
  border-radius: 18px;
  padding: 24px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}

.card-title {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 15px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 18px;
}

.step-badge {
  width: 26px;
  height: 26px;
  border-radius: 50%;
  background: linear-gradient(135deg, #059669, #10b981);
  color: #fff;
  font-size: 12px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

/* Doctor mini card */
.doc-mini {
  display: flex;
  align-items: center;
  gap: 14px;
  background: #f0fdf4;
  border-radius: 12px;
  padding: 14px 16px;
  border: 1px solid #d1fae5;
}

.doc-mini-avatar { font-size: 32px; }
.doc-mini-name   { font-size: 14px; font-weight: 700; color: #0f172a; }
.doc-mini-role   { font-size: 12px; color: #64748b; margin-top: 2px; }
.doc-mini-link   {
  margin-left: auto;
  font-size: 12px;
  color: #059669;
  text-decoration: none;
  font-weight: 600;
  white-space: nowrap;
  flex-shrink: 0;
}
.doc-mini-link:hover { text-decoration: underline; }

/* Service options */
.service-options { display: flex; flex-direction: column; gap: 10px; }

.service-opt {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 14px 16px;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  cursor: pointer;
  transition: all 0.18s;
  background: #f8fafc;
}

.service-opt:hover { border-color: #10b981; background: #f0fdf4; }
.service-opt.selected {
  border-color: #059669;
  background: #f0fdf4;
  box-shadow: 0 0 0 2px rgba(16,185,129,0.15);
}

.so-icon { font-size: 24px; flex-shrink: 0; }
.so-info { flex: 1; }
.so-name { font-size: 14px; font-weight: 700; color: #0f172a; }
.so-desc { font-size: 12px; color: #64748b; margin-top: 2px; }

.so-check {
  width: 22px; height: 22px;
  border-radius: 50%;
  border: 2px solid #e2e8f0;
  display: flex; align-items: center; justify-content: center;
  font-size: 11px; color: #fff; font-weight: 700;
  transition: all 0.18s;
  flex-shrink: 0;
}
.service-opt.selected .so-check {
  background: #059669;
  border-color: #059669;
}

/* Availability picker hint */
.picker-hint {
  font-size: 13px;
  color: #94a3b8;
  margin-bottom: 16px;
}

/* Form fields */
.form-row   { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
.form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
.form-group:last-child { margin-bottom: 0; }

.form-group label {
  font-size: 12px;
  font-weight: 700;
  color: #475569;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.optional { font-weight: 400; text-transform: none; color: #94a3b8; font-size: 11px; }

.form-group input,
.form-group textarea {
  padding: 11px 14px;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  font-family: 'Nunito', sans-serif;
  font-size: 14px;
  color: #0f172a;
  background: #f8fafc;
  outline: none;
  transition: border-color 0.15s, box-shadow 0.15s;
  width: 100%;
}

.form-group input:focus,
.form-group textarea:focus {
  border-color: #10b981;
  box-shadow: 0 0 0 3px rgba(16,185,129,0.12);
  background: #fff;
}

.form-group.has-error input,
.form-group.has-error textarea { border-color: #ef4444; }

.field-error { font-size: 12px; color: #ef4444; font-weight: 500; }

/* Nav buttons */
.nav-btns {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
}

.btn-back {
  padding: 12px 22px;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  background: #fff;
  color: #64748b;
  font-family: 'Nunito', sans-serif;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.18s;
}
.btn-back:hover { border-color: #94a3b8; color: #0f172a; }

.btn-next {
  padding: 12px 28px;
  border-radius: 10px;
  border: none;
  background: linear-gradient(135deg, #059669, #10b981);
  color: #fff;
  font-family: 'Nunito', sans-serif;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 4px 14px rgba(16,185,129,0.3);
}
.btn-next:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(16,185,129,0.4);
}
.btn-next:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

.spinner { display: inline-block; animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* ── SIDEBAR SUMMARY ── */
.sidebar { position: sticky; top: 24px; }

.summary-card {
  background: #fff;
  border-radius: 18px;
  padding: 24px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}

.summary-card h3 {
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 18px;
}

.sum-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
}

.sum-lbl { font-size: 12px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; }
.sum-val  { font-size: 13px; color: #0f172a; font-weight: 600; text-align: right; max-width: 55%; }
.sum-val.empty { color: #cbd5e1; font-weight: 400; font-style: italic; }

.sum-divider { height: 1px; background: #f1f5f9; margin: 6px 0; }

.security-note {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  background: #f0fdf4;
  border-radius: 10px;
  padding: 10px 12px;
  margin-top: 16px;
}
.security-note span { font-size: 16px; flex-shrink: 0; }
.security-note p    { font-size: 12px; color: #64748b; line-height: 1.5; }

/* ── CONFIRMATION OVERLAY ── */
.confirmed-overlay {
  min-height: 100vh;
  background: linear-gradient(160deg, #f0fdf4 0%, #ecfdf5 50%, #f0f9ff 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
}

.confirmed-wrap {
  max-width: 520px;
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 24px;
  animation: rise 0.6s ease both;
}

@keyframes rise {
  from { opacity: 0; transform: translateY(24px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* Animated rings + check */
.confirmed-icon-wrap {
  position: relative;
  width: 120px;
  height: 120px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.ring {
  position: absolute;
  border-radius: 50%;
  border: 2px solid #10b981;
  animation: pulse-ring 2s ease-out infinite;
}
.r1 { width: 120px; height: 120px; opacity: 0.1; animation-delay: 0s; }
.r2 { width: 92px;  height: 92px;  opacity: 0.2; animation-delay: 0.35s; }
.r3 { width: 66px;  height: 66px;  opacity: 0.3; animation-delay: 0.7s; }

@keyframes pulse-ring {
  0%   { transform: scale(0.85); opacity: 0.5; }
  100% { transform: scale(1.25); opacity: 0; }
}

.check-circle {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  background: linear-gradient(135deg, #059669, #10b981);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 32px rgba(16,185,129,0.4);
  animation: pop 0.5s 0.1s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
  position: relative;
  z-index: 1;
}

@keyframes pop {
  from { transform: scale(0); }
  to   { transform: scale(1); }
}

.check-svg  { width: 36px; height: 36px; }
.check-bg   { fill: none; }
.check-mark {
  stroke: #fff;
  stroke-width: 3.5;
  stroke-linecap: round;
  stroke-linejoin: round;
  stroke-dasharray: 48;
  stroke-dashoffset: 48;
  animation: draw 0.5s 0.5s ease forwards;
}
@keyframes draw { to { stroke-dashoffset: 0; } }

.confirmed-title {
  font-family: 'Playfair Display', serif;
  font-size: 32px;
  font-weight: 700;
  color: #064e3b;
  letter-spacing: -0.5px;
  text-align: center;
}

.confirmed-sub {
  font-size: 15px;
  color: #6b7280;
  text-align: center;
  line-height: 1.8;
}
.confirmed-sub strong { color: #059669; }

/* Confirmed booking card */
.confirmed-card {
  width: 100%;
  background: #fff;
  border-radius: 16px;
  border: 1px solid #d1fae5;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0,0,0,0.06);
}

.cc-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 20px;
  border-bottom: 1px solid #f0fdf4;
}
.cc-row:last-child { border-bottom: none; }

.cc-label { font-size: 12px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.cc-value { font-size: 14px; color: #0f172a; font-weight: 700; }

/* Confirmed action buttons */
.confirmed-actions {
  display: flex;
  gap: 12px;
  width: 100%;
}

.cbtn-home, .cbtn-new {
  flex: 1;
  padding: 14px;
  border-radius: 12px;
  font-family: 'Nunito', sans-serif;
  font-size: 14px;
  font-weight: 700;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none;
  display: block;
}

.cbtn-home {
  background: #f0fdf4;
  color: #059669;
  border: 2px solid #d1fae5;
}
.cbtn-home:hover { background: #d1fae5; }

.cbtn-new {
  background: linear-gradient(135deg, #059669, #10b981);
  color: #fff;
  border: 2px solid transparent;
  box-shadow: 0 4px 16px rgba(16,185,129,0.3);
}
.cbtn-new:hover { transform: translateY(-1px); box-shadow: 0 6px 22px rgba(16,185,129,0.4); }

/* ── TRANSITIONS ── */
.fade-enter-active, .fade-leave-active { transition: opacity 0.4s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

/* ── RESPONSIVE ── */
@media (max-width: 768px) {
  .main { grid-template-columns: 1fr; }
  .sidebar { position: static; }
  .form-row { grid-template-columns: 1fr; }
  .page-head h1 { font-size: 26px; }
  .confirmed-title { font-size: 24px; }
  .confirmed-actions { flex-direction: column; }
  .ps-line { width: 40px; }
}
</style>