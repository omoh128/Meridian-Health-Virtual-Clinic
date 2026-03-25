<template>
  <div class="cancel-page">
    <div class="bg-noise"></div>
    <div class="bg-glow g1"></div>
    <div class="bg-glow g2"></div>

    <div class="cancel-wrap">

      <!-- STEP 1: Email Lookup -->
      <transition name="slide" mode="out-in">
        <div v-if="step === 'lookup'" key="lookup" class="card">
          <div class="card-icon warning">
            <span>📋</span>
          </div>
          <h1 class="card-title">Cancel Appointment</h1>
          <p class="card-sub">Enter your email address to find your booking.</p>

          <div class="field-wrap" :class="{ error: lookupError }">
            <label class="field-label">Email Address</label>
            <div class="field-row">
              <span class="field-prefix">✉</span>
              <input
                v-model="email"
                type="email"
                placeholder="you@example.com"
                class="field-input"
                @keyup.enter="lookupBookings"
                @input="lookupError = ''"
              />
            </div>
            <p v-if="lookupError" class="field-error">{{ lookupError }}</p>
          </div>

          <button class="btn-primary" @click="lookupBookings" :disabled="looking">
            <span v-if="!looking">Find My Bookings →</span>
            <span v-else class="spinner">⟳</span>
          </button>

          <router-link to="/" class="back-link">← Back to Home</router-link>
        </div>

        <!-- STEP 2: Select Booking -->
        <div v-else-if="step === 'select'" key="select" class="card wide">
          <div class="card-icon warning">
            <span>📅</span>
          </div>
          <h1 class="card-title">Your Bookings</h1>
          <p class="card-sub">Select the appointment you want to cancel.</p>

          <div class="bookings-list">
            <div
              v-for="b in activeBookings"
              :key="b.id"
              class="booking-item"
              :class="{ selected: selectedBooking?.id === b.id }"
              @click="selectedBooking = b"
            >
              <div class="bi-radio">
                <div class="radio-dot" v-if="selectedBooking?.id === b.id"></div>
              </div>
              <div class="bi-body">
                <div class="bi-top">
                  <span class="bi-service">{{ b.service }}</span>
                  <span class="bi-status" :class="'s-' + b.status">{{ b.status }}</span>
                </div>
                <div class="bi-details">
                  <span>📅 {{ b.appointment_date }}</span>
                  <span>⏰ {{ b.appointment_time }}</span>
                  <span>⏱ {{ b.duration }}</span>
                </div>
              </div>
            </div>

            <div v-if="!activeBookings.length" class="no-bookings">
              <span>😔</span>
              <p>No active bookings found for this email.</p>
            </div>
          </div>

          <button
            class="btn-primary danger"
            :disabled="!selectedBooking"
            @click="step = 'confirm'"
          >
            Continue to Cancel →
          </button>
          <button class="btn-ghost" @click="step = 'lookup'">← Try Another Email</button>
        </div>

        <!-- STEP 3: Confirm Cancellation -->
        <div v-else-if="step === 'confirm'" key="confirm" class="card">
          <div class="card-icon danger">
            <span>⚠️</span>
          </div>
          <h1 class="card-title">Are You Sure?</h1>
          <p class="card-sub">You're about to cancel this appointment. This cannot be undone.</p>

          <!-- Booking preview -->
          <div class="preview-card">
            <div class="preview-row">
              <span class="pr-label">Patient</span>
              <span class="pr-value">{{ selectedBooking.patient_name }}</span>
            </div>
            <div class="preview-row">
              <span class="pr-label">Service</span>
              <span class="pr-value">{{ selectedBooking.service }}</span>
            </div>
            <div class="preview-row">
              <span class="pr-label">Date</span>
              <span class="pr-value">{{ selectedBooking.appointment_date }}</span>
            </div>
            <div class="preview-row">
              <span class="pr-label">Time</span>
              <span class="pr-value">{{ selectedBooking.appointment_time }}</span>
            </div>
            <div class="preview-row">
              <span class="pr-label">Duration</span>
              <span class="pr-value">{{ selectedBooking.duration }}</span>
            </div>
          </div>

          <div class="reason-wrap">
            <label class="field-label">Reason for cancellation <span class="optional">(optional)</span></label>
            <textarea
              v-model="cancelReason"
              class="reason-input"
              placeholder="Let us know why you're cancelling..."
              rows="3"
            ></textarea>
          </div>

          <div class="confirm-btns">
            <button class="btn-ghost" @click="step = 'select'">← Go Back</button>
            <button class="btn-primary danger" @click="cancelBooking" :disabled="cancelling">
              <span v-if="!cancelling">Yes, Cancel It</span>
              <span v-else class="spinner">⟳</span>
            </button>
          </div>
        </div>

        <!-- STEP 4: Cancelled Successfully -->
        <div v-else-if="step === 'done'" key="done" class="card">
          <div class="card-icon success">
            <span>✅</span>
          </div>
          <h1 class="card-title">Booking Cancelled</h1>
          <p class="card-sub">
            Your appointment has been cancelled successfully.<br>
            A confirmation has been sent to <strong>{{ email }}</strong>.
          </p>

          <div class="done-divider"></div>

          <p class="done-note">
            Changed your mind? You can always book a new appointment.
          </p>

          <router-link to="/book" class="btn-primary">Book a New Appointment</router-link>
          <router-link to="/" class="back-link">← Back to Home</router-link>
        </div>
      </transition>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'

const route = useRoute()

const step            = ref('lookup')
const email           = ref('')
const lookupError     = ref('')
const looking         = ref(false)
const bookings        = ref([])
const selectedBooking = ref(null)
const cancelReason    = ref('')
const cancelling      = ref(false)

const activeBookings = computed(() =>
  bookings.value.filter(b => b.status !== 'cancelled' && b.status !== 'completed')
)

// Support direct cancellation link (?token=xxx&id=xxx)
onMounted(async () => {
  const token = route.query.token
  const id    = route.query.id
  if (token && id) {
    try {
      const { data } = await axios.get(`/api/bookings/by-token?token=${token}&id=${id}`)
      selectedBooking.value = data
      email.value = data.email
      step.value = 'confirm'
    } catch {
      // token invalid, fall through to email lookup
    }
  }
})

async function lookupBookings() {
  if (!email.value) {
    lookupError.value = 'Please enter your email address.'
    return
  }
  looking.value = true
  lookupError.value = ''
  try {
    const { data } = await axios.get(`/api/bookings/by-email?email=${encodeURIComponent(email.value)}`)
    bookings.value = Array.isArray(data) ? data : (data.data ?? [])
    if (!bookings.value.length) {
      lookupError.value = 'No bookings found for this email address.'
    } else {
      step.value = 'select'
    }
  } catch {
    lookupError.value = 'Something went wrong. Please try again.'
  } finally {
    looking.value = false
  }
}

async function cancelBooking() {
  cancelling.value = true
  try {
    await axios.patch(`/api/bookings/${selectedBooking.value.id}`, {
      status: 'cancelled',
      cancel_reason: cancelReason.value,
    })
    step.value = 'done'
  } catch {
    alert('Failed to cancel booking. Please try again.')
  } finally {
    cancelling.value = false
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=Mulish:wght@300;400;500;600;700&display=swap');

* { box-sizing: border-box; margin: 0; padding: 0; }

.cancel-page {
  min-height: 100vh;
  background: #0c0f1a;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  font-family: 'Mulish', sans-serif;
  position: relative;
  overflow: hidden;
}

.bg-noise {
  position: fixed; inset: 0;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
  pointer-events: none; opacity: 0.4;
}

.bg-glow {
  position: fixed; border-radius: 50%;
  filter: blur(100px); pointer-events: none;
}
.g1 { width: 500px; height: 500px; background: #ef4444; opacity: 0.06; top: -150px; right: -100px; }
.g2 { width: 400px; height: 400px; background: #6366f1; opacity: 0.07; bottom: -100px; left: -80px; }

.cancel-wrap {
  position: relative; z-index: 1;
  width: 100%; max-width: 480px;
}

/* Cards */
.card {
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 24px;
  padding: 40px 36px;
  backdrop-filter: blur(16px);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 20px;
  animation: rise 0.4s ease both;
}

.card.wide { max-width: 520px; width: 100%; }

@keyframes rise {
  from { opacity: 0; transform: translateY(20px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* Icon */
.card-icon {
  width: 72px; height: 72px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 30px;
}
.card-icon.warning { background: rgba(245,158,11,0.15); box-shadow: 0 0 30px rgba(245,158,11,0.15); }
.card-icon.danger  { background: rgba(239,68,68,0.15);  box-shadow: 0 0 30px rgba(239,68,68,0.2); }
.card-icon.success { background: rgba(16,185,129,0.15); box-shadow: 0 0 30px rgba(16,185,129,0.2); }

.card-title {
  font-family: 'Syne', sans-serif;
  font-size: 26px;
  font-weight: 800;
  color: #f1f5f9;
  text-align: center;
  letter-spacing: -0.5px;
}

.card-sub {
  font-size: 14px;
  color: #64748b;
  text-align: center;
  line-height: 1.7;
}
.card-sub strong { color: #94a3b8; }

/* Field */
.field-wrap { width: 100%; display: flex; flex-direction: column; gap: 8px; }
.field-label {
  font-size: 11px; font-weight: 700;
  color: #64748b;
  text-transform: uppercase; letter-spacing: 0.8px;
}
.optional { font-weight: 400; text-transform: none; font-size: 11px; color: #475569; }

.field-row {
  display: flex; align-items: center;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 12px;
  padding: 0 14px;
  transition: border-color 0.2s;
}
.field-row:focus-within { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.15); }
.field-wrap.error .field-row { border-color: #ef4444; }

.field-prefix { font-size: 15px; opacity: 0.4; margin-right: 10px; }

.field-input {
  flex: 1; background: transparent; border: none; outline: none;
  padding: 14px 0;
  font-family: 'Mulish', sans-serif;
  font-size: 15px; color: #f1f5f9;
}
.field-input::placeholder { color: #475569; }
.field-error { font-size: 12px; color: #f87171; }

/* Buttons */
.btn-primary {
  width: 100%; padding: 14px;
  border-radius: 12px; border: none;
  background: linear-gradient(135deg, #4f46e5, #7c3aed);
  color: #fff;
  font-family: 'Syne', sans-serif;
  font-size: 15px; font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 4px 20px rgba(99,102,241,0.3);
  text-decoration: none;
  text-align: center;
  display: block;
}
.btn-primary:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(99,102,241,0.4); }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-primary.danger { background: linear-gradient(135deg, #dc2626, #ef4444); box-shadow: 0 4px 20px rgba(239,68,68,0.3); }
.btn-primary.danger:hover:not(:disabled) { box-shadow: 0 8px 28px rgba(239,68,68,0.4); }

.btn-ghost {
  width: 100%; padding: 12px;
  border-radius: 12px;
  border: 1px solid rgba(255,255,255,0.1);
  background: transparent;
  color: #64748b;
  font-family: 'Mulish', sans-serif;
  font-size: 14px; font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}
.btn-ghost:hover { background: rgba(255,255,255,0.06); color: #94a3b8; }

.back-link {
  font-size: 13px; color: #475569;
  text-decoration: none; transition: color 0.15s;
}
.back-link:hover { color: #94a3b8; }

/* Bookings List */
.bookings-list { width: 100%; display: flex; flex-direction: column; gap: 10px; }

.booking-item {
  display: flex; align-items: flex-start; gap: 14px;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 14px;
  padding: 16px;
  cursor: pointer;
  transition: all 0.18s;
}
.booking-item:hover { border-color: rgba(99,102,241,0.4); background: rgba(99,102,241,0.06); }
.booking-item.selected { border-color: #6366f1; background: rgba(99,102,241,0.1); }

.bi-radio {
  width: 20px; height: 20px; border-radius: 50%;
  border: 2px solid rgba(255,255,255,0.2);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0; margin-top: 2px;
  transition: border-color 0.15s;
}
.booking-item.selected .bi-radio { border-color: #6366f1; }
.radio-dot { width: 10px; height: 10px; border-radius: 50%; background: #6366f1; }

.bi-body { flex: 1; display: flex; flex-direction: column; gap: 6px; }
.bi-top { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
.bi-service { font-size: 14px; font-weight: 700; color: #e2e8f0; }
.bi-status {
  font-size: 11px; font-weight: 700;
  padding: 2px 10px; border-radius: 20px; text-transform: capitalize;
}
.s-pending   { background: #fef3c7; color: #d97706; }
.s-confirmed { background: #d1fae5; color: #059669; }

.bi-details {
  display: flex; flex-wrap: wrap; gap: 8px;
  font-size: 12px; color: #64748b;
}

.no-bookings {
  text-align: center; padding: 30px;
  display: flex; flex-direction: column; align-items: center; gap: 10px;
  color: #475569; font-size: 14px;
}
.no-bookings span { font-size: 32px; }

/* Preview Card */
.preview-card {
  width: 100%;
  background: rgba(239,68,68,0.06);
  border: 1px solid rgba(239,68,68,0.2);
  border-radius: 14px;
  overflow: hidden;
}

.preview-row {
  display: flex; justify-content: space-between; align-items: center;
  padding: 12px 18px;
  border-bottom: 1px solid rgba(239,68,68,0.1);
}
.preview-row:last-child { border-bottom: none; }
.pr-label { font-size: 12px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
.pr-value { font-size: 13px; color: #f1f5f9; font-weight: 700; }

/* Reason */
.reason-wrap { width: 100%; display: flex; flex-direction: column; gap: 8px; }
.reason-input {
  width: 100%; background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 12px; padding: 12px 14px;
  font-family: 'Mulish', sans-serif;
  font-size: 14px; color: #f1f5f9;
  resize: none; outline: none;
  transition: border-color 0.2s;
}
.reason-input:focus { border-color: #6366f1; }
.reason-input::placeholder { color: #475569; }

.confirm-btns { display: flex; gap: 10px; width: 100%; }
.confirm-btns .btn-ghost { flex: 1; }
.confirm-btns .btn-primary { flex: 2; }

/* Done */
.done-divider {
  width: 60px; height: 2px;
  background: linear-gradient(90deg, transparent, #334155, transparent);
}
.done-note { font-size: 13px; color: #475569; text-align: center; }

/* Spinner */
.spinner { display: inline-block; animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* Transitions */
.slide-enter-active, .slide-leave-active { transition: all 0.3s ease; }
.slide-enter-from { opacity: 0; transform: translateX(30px); }
.slide-leave-to   { opacity: 0; transform: translateX(-30px); }

@media (max-width: 520px) {
  .card { padding: 28px 20px; }
  .confirm-btns { flex-direction: column-reverse; }
}
</style>