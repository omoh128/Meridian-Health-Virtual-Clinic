<template>
  <div class="av-manager">

    <div class="am-header">
      <div>
        <h2 class="am-title">Availability Schedule</h2>
        <p class="am-sub">Set your working hours and block dates as needed.</p>
      </div>
      <button class="save-btn" @click="saveSchedule" :disabled="saving">
        <span v-if="!saving">💾 Save Schedule</span>
        <span v-else class="spinner">⟳</span>
      </button>
    </div>

    <transition name="toast">
      <div v-if="toast" class="toast" :class="'toast-' + toast.type">
        {{ toast.msg }}
      </div>
    </transition>

    <!-- Weekly Schedule -->
    <div class="section-card">
      <h3 class="section-title">📅 Weekly Schedule</h3>
      <p class="section-sub">Toggle days on/off and set working hours for each day.</p>

      <div class="schedule-grid">
        <div
          v-for="day in schedule"
          :key="day.day_of_week"
          class="day-card"
          :class="{ 'day-active': day.is_active, 'day-inactive': !day.is_active }"
        >
          <div class="day-top">
            <div class="day-name-row">
              <span class="day-name">{{ dayNames[day.day_of_week] }}</span>
              <span class="day-short">{{ dayShort[day.day_of_week] }}</span>
            </div>
            <label class="toggle">
              <input type="checkbox" v-model="day.is_active" />
              <span class="toggle-track">
                <span class="toggle-thumb"></span>
              </span>
            </label>
          </div>

          <transition name="expand">
            <div v-if="day.is_active" class="day-fields">
              <div class="time-row">
                <div class="time-field">
                  <label>From</label>
                  <input type="time" v-model="day.start_time" class="time-input" />
                </div>
                <div class="time-sep">—</div>
                <div class="time-field">
                  <label>To</label>
                  <input type="time" v-model="day.end_time" class="time-input" />
                </div>
              </div>

              <div class="slot-row">
                <div class="slot-field">
                  <label>Slot Duration</label>
                  <select v-model="day.slot_duration" class="slot-select">
                    <option :value="15">15 min</option>
                    <option :value="30">30 min</option>
                    <option :value="45">45 min</option>
                    <option :value="60">60 min</option>
                    <option :value="90">90 min</option>
                  </select>
                </div>
                <div class="slot-field">
                  <label>Max per Slot</label>
                  <select v-model="day.max_per_slot" class="slot-select">
                    <option v-for="n in 5" :key="n" :value="n">{{ n }} patient{{ n > 1 ? 's' : '' }}</option>
                  </select>
                </div>
              </div>

              <div class="slots-preview">
                <span class="preview-label">Preview:</span>
                <div class="preview-slots">
                  <span v-for="slot in previewSlots(day)" :key="slot" class="preview-pill">{{ slot }}</span>
                </div>
              </div>
            </div>
          </transition>

          <div v-if="!day.is_active" class="day-off-label">Day Off</div>
        </div>
      </div>
    </div>

    <!-- Blocked Dates -->
    <div class="section-card">
      <h3 class="section-title">🚫 Blocked Dates</h3>
      <p class="section-sub">Block specific dates for holidays, leave, or any other reason.</p>

      <div class="block-form">
        <div class="block-fields">
          <div class="bfield">
            <label class="bfield-label">Date</label>
            <input type="date" v-model="newBlock.date" class="bfield-input" :min="today" />
          </div>
          <div class="bfield grow">
            <label class="bfield-label">Reason <span class="opt">(optional)</span></label>
            <input type="text" v-model="newBlock.reason" class="bfield-input" placeholder="e.g. Public Holiday" />
          </div>
          <button class="block-btn" @click="addBlockedDate" :disabled="!newBlock.date || blockingDate">
            <span v-if="!blockingDate">+ Block</span>
            <span v-else class="spinner">⟳</span>
          </button>
        </div>
      </div>

      <div v-if="blockedDates.length" class="blocked-list">
        <div v-for="b in blockedDates" :key="b.id" class="blocked-item">
          <div class="bi-left">
            <span class="bi-date">{{ formatDate(b.date) }}</span>
            <span v-if="b.reason" class="bi-reason">{{ b.reason }}</span>
          </div>
          <button class="bi-remove" @click="removeBlockedDate(b.id)" title="Unblock">✕</button>
        </div>
      </div>

      <div v-else class="empty-blocked">
        <span>✅</span>
        <p>No dates blocked. All available days are open.</p>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const dayNames = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']
const dayShort = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat']
const today    = new Date().toISOString().split('T')[0]

const schedule     = ref([])
const blockedDates = ref([])
const saving       = ref(false)
const blockingDate = ref(false)
const toast        = ref(null)
const newBlock     = ref({ date: '', reason: '' })

// Default schedule template
function defaultSchedule() {
  return Array.from({ length: 7 }, (_, i) => ({
    day_of_week:   i,
    start_time:    '09:00',
    end_time:      '17:00',
    slot_duration: 60,
    max_per_slot:  1,
    is_active:     i >= 1 && i <= 5, // Mon–Fri on by default
  }))
}

function previewSlots(day) {
  const slots = []
  let [h, m] = day.start_time.split(':').map(Number)
  const [eh, em] = day.end_time.split(':').map(Number)
  const endMins = eh * 60 + em
  let cur = h * 60 + m
  while (cur + day.slot_duration <= endMins) {
    const hh = Math.floor(cur / 60)
    const mm = cur % 60
    const ampm = hh >= 12 ? 'PM' : 'AM'
    const displayH = hh > 12 ? hh - 12 : hh === 0 ? 12 : hh
    slots.push(`${displayH}:${String(mm).padStart(2,'0')} ${ampm}`)
    cur += day.slot_duration
    if (slots.length >= 6) { slots.push('…'); break }
  }
  return slots
}

async function loadSchedule() {
  try {
    const { data } = await axios.get('/api/availability')
    if (data.length) {
      schedule.value = dayNames.map((_, i) => {
        const found = data.find(d => d.day_of_week === i)
        return found || { day_of_week: i, start_time: '09:00', end_time: '17:00', slot_duration: 60, max_per_slot: 1, is_active: false }
      })
    } else {
      schedule.value = defaultSchedule()
    }
  } catch {
    schedule.value = defaultSchedule()
  }
}

async function saveSchedule() {
  saving.value = true
  try {
    await axios.post('/api/availability', { schedule: schedule.value })
    showToast('Schedule saved successfully! ✓', 'success')
  } catch {
    showToast('Failed to save. Please try again.', 'error')
  } finally {
    saving.value = false
  }
}

async function loadBlockedDates() {
  try {
    const { data } = await axios.get('/api/availability/blocked')
    blockedDates.value = data
  } catch {
    blockedDates.value = []
  }
}

async function addBlockedDate() {
  if (!newBlock.value.date) return
  blockingDate.value = true
  try {
    const { data } = await axios.post('/api/availability/blocked', newBlock.value)
    blockedDates.value.push(data)
    blockedDates.value.sort((a, b) => a.date.localeCompare(b.date))
    newBlock.value = { date: '', reason: '' }
    showToast('Date blocked successfully.', 'success')
  } catch {
    showToast('Failed to block date.', 'error')
  } finally {
    blockingDate.value = false
  }
}

async function removeBlockedDate(id) {
  try {
    await axios.delete(`/api/availability/blocked/${id}`)
    blockedDates.value = blockedDates.value.filter(b => b.id !== id)
    showToast('Date unblocked.', 'success')
  } catch {
    showToast('Failed to unblock date.', 'error')
  }
}

function formatDate(dateStr) {
  return new Date(dateStr + 'T00:00:00').toLocaleDateString('en-US', {
    weekday: 'short', month: 'long', day: 'numeric', year: 'numeric'
  })
}

function showToast(msg, type = 'success') {
  toast.value = { msg, type }
  setTimeout(() => { toast.value = null }, 3000)
}

onMounted(() => {
  loadSchedule()
  loadBlockedDates()
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap');

* { box-sizing: border-box; margin: 0; padding: 0; }

.av-manager {
  padding: 0 32px 40px;
  font-family: 'DM Sans', sans-serif;
  display: flex;
  flex-direction: column;
  gap: 24px;
}

/* Header */
.am-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}
.am-title { font-size: 20px; font-weight: 700; color: #0f172a; letter-spacing: -0.4px; }
.am-sub   { font-size: 13px; color: #94a3b8; margin-top: 3px; }

.save-btn {
  padding: 10px 22px;
  border-radius: 10px;
  border: none;
  background: linear-gradient(135deg, #4f46e5, #7c3aed);
  color: #fff;
  font-family: 'DM Sans', sans-serif;
  font-size: 14px; font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 4px 14px rgba(99,102,241,0.3);
  white-space: nowrap;
}
.save-btn:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(99,102,241,0.4); }
.save-btn:disabled { opacity: 0.6; cursor: not-allowed; }

/* Toast */
.toast {
  padding: 12px 20px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 500;
  animation: rise 0.3s ease;
}
.toast-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.toast-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

@keyframes rise { from { opacity:0; transform: translateY(8px); } to { opacity:1; transform:translateY(0); } }

/* Section cards */
.section-card {
  background: #fff;
  border-radius: 16px;
  padding: 24px;
  border: 1px solid #f1f5f9;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.section-title { font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
.section-sub   { font-size: 13px; color: #94a3b8; margin-bottom: 20px; }

/* Schedule Grid */
.schedule-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 14px;
}

.day-card {
  border-radius: 14px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
  transition: border-color 0.2s;
}
.day-card.day-active   { border-color: #c7d2fe; }
.day-card.day-inactive { background: #f8fafc; }

.day-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 16px;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
}
.day-card.day-active .day-top { background: #eef2ff; border-color: #c7d2fe; }

.day-name-row { display: flex; flex-direction: column; }
.day-name  { font-size: 14px; font-weight: 700; color: #0f172a; }
.day-short { font-size: 11px; color: #94a3b8; }

/* Toggle */
.toggle { position: relative; cursor: pointer; }
.toggle input { opacity: 0; width: 0; height: 0; position: absolute; }

.toggle-track {
  display: block;
  width: 44px; height: 24px;
  background: #e2e8f0;
  border-radius: 100px;
  transition: background 0.2s;
  position: relative;
}
.toggle input:checked ~ .toggle-track { background: #6366f1; }

.toggle-thumb {
  position: absolute;
  top: 3px; left: 3px;
  width: 18px; height: 18px;
  background: #fff;
  border-radius: 50%;
  transition: transform 0.2s;
  box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
.toggle input:checked ~ .toggle-track .toggle-thumb { transform: translateX(20px); }

/* Day fields */
.day-fields { padding: 14px 16px; display: flex; flex-direction: column; gap: 12px; }

.time-row {
  display: flex; align-items: center; gap: 8px;
}
.time-field { flex: 1; display: flex; flex-direction: column; gap: 4px; }
.time-field label { font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.time-input {
  padding: 8px 10px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  font-family: 'DM Sans', sans-serif;
  font-size: 13px;
  color: #0f172a;
  background: #f8fafc;
  outline: none;
  transition: border-color 0.15s;
  width: 100%;
}
.time-input:focus { border-color: #6366f1; }
.time-sep { color: #94a3b8; font-size: 16px; margin-top: 16px; flex-shrink: 0; }

.slot-row { display: flex; gap: 10px; }
.slot-field { flex: 1; display: flex; flex-direction: column; gap: 4px; }
.slot-field label { font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.slot-select {
  padding: 8px 10px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  font-family: 'DM Sans', sans-serif;
  font-size: 13px;
  color: #0f172a;
  background: #f8fafc;
  outline: none;
  cursor: pointer;
}
.slot-select:focus { border-color: #6366f1; }

.slots-preview { display: flex; flex-direction: column; gap: 6px; }
.preview-label { font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.preview-slots { display: flex; flex-wrap: wrap; gap: 4px; }
.preview-pill {
  background: #eef2ff;
  color: #6366f1;
  font-size: 11px;
  font-weight: 600;
  padding: 3px 8px;
  border-radius: 20px;
}

.day-off-label {
  padding: 12px 16px;
  font-size: 13px;
  color: #94a3b8;
  font-weight: 500;
  text-align: center;
}

/* Blocked dates */
.block-form { margin-bottom: 16px; }
.block-fields {
  display: flex;
  align-items: flex-end;
  gap: 10px;
  flex-wrap: wrap;
}

.bfield { display: flex; flex-direction: column; gap: 6px; }
.bfield.grow { flex: 1; min-width: 160px; }
.bfield-label { font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.opt { text-transform: none; font-weight: 400; }

.bfield-input {
  padding: 10px 12px;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  font-family: 'DM Sans', sans-serif;
  font-size: 14px;
  color: #0f172a;
  background: #f8fafc;
  outline: none;
  transition: border-color 0.15s;
}
.bfield-input:focus { border-color: #6366f1; }

.block-btn {
  padding: 10px 18px;
  border-radius: 10px;
  border: none;
  background: #ef4444;
  color: #fff;
  font-family: 'DM Sans', sans-serif;
  font-size: 13px; font-weight: 700;
  cursor: pointer;
  transition: all 0.15s;
  white-space: nowrap;
  height: 40px;
}
.block-btn:hover:not(:disabled) { background: #dc2626; }
.block-btn:disabled { opacity: 0.5; cursor: not-allowed; }

.blocked-list { display: flex; flex-direction: column; gap: 8px; }
.blocked-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 16px;
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 10px;
}
.bi-left { display: flex; flex-direction: column; gap: 2px; }
.bi-date   { font-size: 13px; font-weight: 700; color: #991b1b; }
.bi-reason { font-size: 12px; color: #ef4444; }
.bi-remove {
  width: 28px; height: 28px;
  border-radius: 6px;
  border: 1px solid #fecaca;
  background: #fff;
  color: #ef4444;
  font-size: 12px;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transition: all 0.15s;
  flex-shrink: 0;
}
.bi-remove:hover { background: #ef4444; color: #fff; }

.empty-blocked {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  padding: 24px;
  color: #94a3b8;
  font-size: 13px;
  text-align: center;
}
.empty-blocked span { font-size: 24px; }

/* Spinner */
.spinner { display: inline-block; animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* Transitions */
.expand-enter-active, .expand-leave-active { transition: all 0.25s ease; overflow: hidden; }
.expand-enter-from, .expand-leave-to { opacity: 0; max-height: 0; }
.expand-enter-to, .expand-leave-from { max-height: 400px; }
.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(-8px); }

@media (max-width: 768px) {
  .av-manager { padding: 0 16px 24px; }
  .schedule-grid { grid-template-columns: 1fr; }
  .block-fields { flex-direction: column; }
  .block-btn { width: 100%; }
}
</style>