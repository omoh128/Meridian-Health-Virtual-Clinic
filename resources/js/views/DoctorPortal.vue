<template>
  <div>
    <!-- LOGIN SCREEN -->
    <transition name="fade">
      <div v-if="!authenticated" class="login-page">
        <!-- Animated background blobs -->
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>

        <div class="login-card">
          <div class="login-logo">
            <div class="logo-ring">
              <span class="logo-emoji">🏥</span>
            </div>
          </div>

          <div class="login-header">
            <h1 class="login-title">Doctor Portal</h1>
            <p class="login-sub">Meridian Health · Restricted Access</p>
          </div>

          <form class="login-form" @submit.prevent="attemptLogin">
            <div class="field-group" :class="{ error: showError }">
              <label class="field-label">Password</label>
              <div class="field-input-wrap">
                <span class="field-icon">🔒</span>
                <input
                  ref="passwordInput"
                  v-model="password"
                  :type="showPassword ? 'text' : 'password'"
                  class="field-input"
                  placeholder="Enter doctor password"
                  autocomplete="current-password"
                  @input="showError = false"
                />
                <button
                  type="button"
                  class="toggle-pw"
                  @click="showPassword = !showPassword"
                  tabindex="-1"
                >{{ showPassword ? '🙈' : '👁' }}</button>
              </div>

              <transition name="shake">
                <div v-if="showError" class="error-msg">
                  ✕ Incorrect password. Please try again.
                </div>
              </transition>
            </div>

            <button type="submit" class="login-btn" :class="{ loading: logging }">
              <span v-if="!logging">Enter Dashboard →</span>
              <span v-else class="spinner">⟳</span>
            </button>
          </form>

          <p class="login-footer">
            <router-link to="/" class="back-link">← Back to Meridian Health</router-link>
          </p>
        </div>
      </div>
    </transition>

    <!-- DASHBOARD (shown after login) -->
    <transition name="slide-up">
      <AdminDashboard v-if="authenticated" @logout="logout" />
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AdminDashboard from './AdminDashboard.vue'

// ─── Change this to your desired password ───
const DOCTOR_PASSWORD = 'meridian2024'
// ────────────────────────────────────────────

const password     = ref('')
const showPassword = ref(false)
const showError    = ref(false)
const logging      = ref(false)
const authenticated = ref(false)
const passwordInput = ref(null)

onMounted(() => {
  // Persist login across page refresh using sessionStorage
  if (sessionStorage.getItem('doctor_auth') === 'true') {
    authenticated.value = true
  }
})

function attemptLogin() {
  if (!password.value) return
  logging.value = true

  // Small delay for UX feel
  setTimeout(() => {
    if (password.value === DOCTOR_PASSWORD) {
      sessionStorage.setItem('doctor_auth', 'true')
      authenticated.value = true
    } else {
      showError.value = true
      password.value  = ''
      passwordInput.value?.focus()
    }
    logging.value = false
  }, 600)
}

function logout() {
  sessionStorage.removeItem('doctor_auth')
  authenticated.value = false
  password.value = ''
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,300;0,400;0,600;1,300&family=DM+Sans:wght@300;400;500;600&display=swap');

* { box-sizing: border-box; margin: 0; padding: 0; }

/* ── LOGIN PAGE ── */
.login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #060d1f;
  position: relative;
  overflow: hidden;
  padding: 24px;
}

/* Animated background blobs */
.blob {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  opacity: 0.18;
  animation: drift 10s ease-in-out infinite alternate;
}

.blob-1 {
  width: 500px; height: 500px;
  background: #4f46e5;
  top: -120px; left: -100px;
  animation-delay: 0s;
}

.blob-2 {
  width: 400px; height: 400px;
  background: #0ea5e9;
  bottom: -80px; right: -60px;
  animation-delay: -4s;
}

.blob-3 {
  width: 300px; height: 300px;
  background: #8b5cf6;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  animation-delay: -7s;
}

@keyframes drift {
  from { transform: translate(0, 0) scale(1); }
  to   { transform: translate(30px, -20px) scale(1.08); }
}

/* ── CARD ── */
.login-card {
  position: relative;
  z-index: 1;
  width: 100%;
  max-width: 420px;
  background: rgba(255, 255, 255, 0.04);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 24px;
  padding: 44px 40px;
  backdrop-filter: blur(20px);
  box-shadow:
    0 0 0 1px rgba(255,255,255,0.05),
    0 40px 80px rgba(0,0,0,0.5);
}

/* ── LOGO ── */
.login-logo {
  display: flex;
  justify-content: center;
  margin-bottom: 24px;
}

.logo-ring {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  background: linear-gradient(135deg, #4f46e5, #7c3aed);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 32px;
  box-shadow: 0 0 40px rgba(99, 102, 241, 0.4);
  animation: pulse-ring 3s ease-in-out infinite;
}

@keyframes pulse-ring {
  0%, 100% { box-shadow: 0 0 30px rgba(99,102,241,0.3); }
  50%       { box-shadow: 0 0 60px rgba(99,102,241,0.6); }
}

/* ── HEADER ── */
.login-header {
  text-align: center;
  margin-bottom: 36px;
}

.login-title {
  font-family: 'Fraunces', serif;
  font-size: 28px;
  font-weight: 600;
  color: #f8fafc;
  letter-spacing: -0.5px;
  margin-bottom: 6px;
}

.login-sub {
  font-family: 'DM Sans', sans-serif;
  font-size: 13px;
  color: #64748b;
  letter-spacing: 0.3px;
}

/* ── FORM ── */
.login-form { display: flex; flex-direction: column; gap: 20px; }

.field-group { display: flex; flex-direction: column; gap: 8px; }

.field-label {
  font-family: 'DM Sans', sans-serif;
  font-size: 12px;
  font-weight: 600;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.8px;
}

.field-input-wrap {
  display: flex;
  align-items: center;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 12px;
  padding: 0 14px;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.field-input-wrap:focus-within {
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
}

.field-group.error .field-input-wrap {
  border-color: #ef4444;
  box-shadow: 0 0 0 3px rgba(239,68,68,0.15);
}

.field-icon { font-size: 15px; margin-right: 10px; opacity: 0.5; }

.field-input {
  flex: 1;
  background: transparent;
  border: none;
  outline: none;
  padding: 14px 0;
  font-family: 'DM Sans', sans-serif;
  font-size: 15px;
  color: #f1f5f9;
  letter-spacing: 0.5px;
}

.field-input::placeholder { color: #475569; }

.toggle-pw {
  background: transparent;
  border: none;
  cursor: pointer;
  font-size: 15px;
  padding: 4px;
  opacity: 0.5;
  transition: opacity 0.15s;
}
.toggle-pw:hover { opacity: 1; }

.error-msg {
  font-family: 'DM Sans', sans-serif;
  font-size: 12px;
  color: #f87171;
  display: flex;
  align-items: center;
  gap: 6px;
  animation: shake 0.4s ease;
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  20%       { transform: translateX(-8px); }
  40%       { transform: translateX(8px); }
  60%       { transform: translateX(-4px); }
  80%       { transform: translateX(4px); }
}

/* ── LOGIN BUTTON ── */
.login-btn {
  width: 100%;
  padding: 15px;
  border-radius: 12px;
  border: none;
  background: linear-gradient(135deg, #4f46e5, #7c3aed);
  color: #fff;
  font-family: 'DM Sans', sans-serif;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  letter-spacing: 0.2px;
  box-shadow: 0 4px 20px rgba(99,102,241,0.35);
}

.login-btn:hover:not(.loading) {
  transform: translateY(-1px);
  box-shadow: 0 8px 30px rgba(99,102,241,0.5);
}

.login-btn:active { transform: translateY(0); }

.login-btn.loading {
  opacity: 0.7;
  cursor: not-allowed;
}

.spinner {
  display: inline-block;
  animation: spin 0.8s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }

/* ── FOOTER ── */
.login-footer {
  text-align: center;
  margin-top: 24px;
}

.back-link {
  font-family: 'DM Sans', sans-serif;
  font-size: 13px;
  color: #475569;
  text-decoration: none;
  transition: color 0.15s;
}

.back-link:hover { color: #94a3b8; }

/* ── TRANSITIONS ── */
.fade-enter-active, .fade-leave-active { transition: opacity 0.35s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.slide-up-enter-active { transition: all 0.4s ease; }
.slide-up-enter-from { opacity: 0; transform: translateY(20px); }
</style>