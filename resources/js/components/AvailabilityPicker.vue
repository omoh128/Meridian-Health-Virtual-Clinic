<template>
  <div class="av-picker">
    <!-- Calendar -->
    <div class="calendar-wrap">
      <div class="cal-header">
        <button class="cal-nav" @click="prevMonth" :disabled="isCurrentMonth">‹</button>
        <span class="cal-title">{{ monthLabel }}</span>
        <button class="cal-nav" @click="nextMonth">›</button>
      </div>

      <div class="cal-weekdays">
        <span v-for="d in weekdays" :key="d">{{ d }}</span>
      </div>

      <div class="cal-grid">
        <!-- Empty cells before month start -->
        <div v-for="n in firstDayOffset" :key="'e' + n" class="cal-empty"></div>

        <!-- Day cells -->
        <button
          v-for="day in calendarDays"
          :key="day.date"
          class="cal-day"
          :class="{
            'is-today':     day.date === today,
            'is-selected':  day.date === selectedDate,
            'is-available': day.available,
            'is-blocked':   day.blocked,
            'is-past':      day.past,
            'is-unavailable': !day.available && !day.past,
          }"
          :disabled="!day.available"
          @click="selectDate(day)"
          :title="day.blocked ? 'Unavailable' : ''"
        >
          {{ day.day }}
          <span v-if="day.available && day.date !== selectedDate" class="day-dot"></span>
        </button>
      </div>

      <div class="cal-legend">
        <span class="leg-item"><span class="leg-dot available"></span> Available</span>
        <span class="leg-item"><span class="leg-dot selected"></span> Selected</span>
        <span class="leg-item"><span class="leg-dot unavailable"></span> Unavailable</span>
      </div>
    </div>

    <!-- Time Slots -->
    <transition name="slide-up">
      <div v-if="selectedDate" class="slots-wrap">
        <div class="slots-header">
          <h3 class="slots-title">Available Times</h3>
          <span class="slots-date">{{ formatSelectedDate }}</span>
        </div>

        <div v-if="loadingSlots" class="slots-loading">
          <div class="slot-skeleton" v-for="n in 6" :key="n"></div>
        </div>

        <div v-else-if="slots.length" class="slots-grid">
          <button
            v-for="slot in slots"
            :key="slot.time"
            class="slot-btn"
            :class="{
              'slot-selected':   selectedTime === slot.time,
              'slot-full':       !slot.available,
              'slot-almost':     slot.available && slot.remaining === 1,
            }"
            :disabled="!slot.available"
            @click="selectSlot(slot)"
          >
            <span class="slot-time">{{ slot.time }}</span>
            <span class="slot-spots" :class="{ 'spots-full': !slot.available, 'spots-low': slot.remaining === 1 }">
              {{ slot.available ? slot.remaining + ' spot' + (slot.remaining > 1 ? 's' : '') + ' left' : 'Full' }}
            </span>
          </button>
        </div>

        <div v-else class="slots-empty">
          <span>😔</span>
          <p>No available slots for this day.</p>
        </div>
      </div>
    </transition>

    <!-- No date selected hint -->
    <div v-if="!selectedDate" class="pick-hint">
      <span class="hint-icon">📅</span>
      <p>Select a date above to see available time slots.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from 'axios'

const emit = defineEmits(['select'])

const today         = new Date().toISOString().split('T')[0]
const currentMonth  = ref(new Date())
const calendarDays  = ref([])
const selectedDate  = ref(null)
const selectedTime  = ref(null)
const slots         = ref([])
const loadingSlots  = ref(false)

const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']

const monthLabel = computed(() =>
  currentMonth.value.toLocaleDateString('en-US', { month: 'long', year: 'numeric' })
)

const isCurrentMonth = computed(() => {
  const now = new Date()
  return currentMonth.value.getFullYear() === now.getFullYear() &&
         currentMonth.value.getMonth() === now.getMonth()
})

const firstDayOffset = computed(() => {
  const d = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth(), 1)
  return d.getDay()
})

const formatSelectedDate = computed(() => {
  if (!selectedDate.value) return ''
  return new Date(selectedDate.value + 'T00:00:00').toLocaleDateString('en-US', {
    weekday: 'long', month: 'long', day: 'numeric', year: 'numeric'
  })
})

async function loadCalendar() {
  const month = currentMonth.value.toISOString().slice(0, 7)
  try {
    const { data } = await axios.get(`/api/availability/calendar?month=${month}`)
    calendarDays.value = data.map(d => ({ ...d, day: parseInt(d.date.split('-')[2]) }))
  } catch {
    calendarDays.value = []
  }
}

async function selectDate(day) {
  selectedDate.value = day.date
  selectedTime.value = null
  slots.value = []
  loadingSlots.value = true
  try {
    const { data } = await axios.get(`/api/availability/slots?date=${day.date}`)
    slots.value = data.slots || []
  } catch {
    slots.value = []
  } finally {
    loadingSlots.value = false
  }
}

function selectSlot(slot) {
  if (!slot.available) return
  selectedTime.value = slot.time
  emit('select', { date: selectedDate.value, time: slot.time })
}

function prevMonth() {
  const d = new Date(currentMonth.value)
  d.setMonth(d.getMonth() - 1)
  currentMonth.value = d
}

function nextMonth() {
  const d = new Date(currentMonth.value)
  d.setMonth(d.getMonth() + 1)
  currentMonth.value = d
}

watch(currentMonth, loadCalendar)
onMounted(loadCalendar)
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

* { box-sizing: border-box; margin: 0; padding: 0; }

.av-picker {
  font-family: 'DM Sans', sans-serif;
  display: flex;
  flex-direction: column;
  gap: 24px;
}

/* ── CALENDAR ── */
.calendar-wrap {
  background: #fff;
  border-radius: 18px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
  box-shadow: 0 2px 16px rgba(0,0,0,0.06);
}

.cal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
}

.cal-title {
  font-size: 15px;
  font-weight: 700;
  color: #0f172a;
  letter-spacing: -0.3px;
}

.cal-nav {
  width: 32px; height: 32px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  background: #fff;
  font-size: 18px;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  color: #64748b;
  transition: all 0.15s;
}
.cal-nav:hover:not(:disabled) { border-color: #6366f1; color: #6366f1; }
.cal-nav:disabled { opacity: 0.3; cursor: not-allowed; }

.cal-weekdays {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  padding: 12px 16px 4px;
  gap: 4px;
}
.cal-weekdays span {
  text-align: center;
  font-size: 11px;
  font-weight: 700;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.cal-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 4px;
  padding: 4px 16px 16px;
}

.cal-empty { }

.cal-day {
  aspect-ratio: 1;
  border-radius: 10px;
  border: 1px solid transparent;
  background: transparent;
  font-family: 'DM Sans', sans-serif;
  font-size: 13px;
  font-weight: 500;
  color: #94a3b8;
  cursor: not-allowed;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 3px;
  transition: all 0.15s;
  position: relative;
}

.cal-day.is-available {
  color: #0f172a;
  cursor: pointer;
  background: #f0fdf4;
  border-color: #d1fae5;
}
.cal-day.is-available:hover {
  background: #dcfce7;
  border-color: #86efac;
  transform: scale(1.05);
}

.cal-day.is-today {
  font-weight: 700;
  border-color: #6366f1 !important;
}

.cal-day.is-selected {
  background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
  color: #fff !important;
  border-color: transparent !important;
  box-shadow: 0 4px 12px rgba(99,102,241,0.35);
  font-weight: 700;
}

.cal-day.is-past { color: #cbd5e1; }
.cal-day.is-blocked { color: #fca5a5; background: #fef2f2; border-color: #fecaca; }

.day-dot {
  width: 4px; height: 4px;
  border-radius: 50%;
  background: #10b981;
}

.cal-legend {
  display: flex;
  gap: 16px;
  padding: 10px 16px 14px;
  border-top: 1px solid #f1f5f9;
  justify-content: center;
}

.leg-item {
  display: flex; align-items: center; gap: 6px;
  font-size: 11px; color: #94a3b8;
}

.leg-dot {
  width: 10px; height: 10px; border-radius: 3px;
}
.leg-dot.available  { background: #f0fdf4; border: 1px solid #86efac; }
.leg-dot.selected   { background: #6366f1; }
.leg-dot.unavailable{ background: #f1f5f9; border: 1px solid #e2e8f0; }

/* ── SLOTS ── */
.slots-wrap {
  background: #fff;
  border-radius: 18px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
  box-shadow: 0 2px 16px rgba(0,0,0,0.06);
}

.slots-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
}

.slots-title { font-size: 14px; font-weight: 700; color: #0f172a; }
.slots-date  { font-size: 12px; color: #64748b; }

.slots-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
  padding: 16px;
}

.slot-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  padding: 12px 8px;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  cursor: pointer;
  transition: all 0.15s;
  font-family: 'DM Sans', sans-serif;
}

.slot-btn:hover:not(:disabled):not(.slot-full) {
  border-color: #6366f1;
  background: #eef2ff;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(99,102,241,0.15);
}

.slot-btn.slot-selected {
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  border-color: transparent;
  box-shadow: 0 4px 16px rgba(99,102,241,0.35);
}

.slot-btn.slot-full {
  opacity: 0.4;
  cursor: not-allowed;
  background: #fef2f2;
  border-color: #fecaca;
}

.slot-btn.slot-almost {
  border-color: #f59e0b;
  background: #fffbeb;
}

.slot-time {
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
  font-family: 'DM Mono', monospace;
}
.slot-btn.slot-selected .slot-time { color: #fff; }
.slot-btn.slot-full .slot-time { color: #ef4444; }

.slot-spots {
  font-size: 10px;
  font-weight: 600;
  color: #10b981;
  text-transform: uppercase;
  letter-spacing: 0.3px;
}
.slot-btn.slot-selected .slot-spots { color: rgba(255,255,255,0.8); }
.spots-full { color: #ef4444 !important; }
.spots-low  { color: #f59e0b !important; }

/* Loading skeletons */
.slots-loading {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
  padding: 16px;
}
.slot-skeleton {
  height: 64px;
  border-radius: 12px;
  background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
  background-size: 200% 100%;
  animation: shimmer 1.4s infinite;
}
@keyframes shimmer {
  0%   { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

.slots-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  padding: 32px;
  color: #94a3b8;
  font-size: 14px;
}
.slots-empty span { font-size: 28px; }

/* Hint */
.pick-hint {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  padding: 24px;
  background: #f8fafc;
  border-radius: 14px;
  border: 1px dashed #e2e8f0;
  color: #94a3b8;
  font-size: 14px;
  text-align: center;
}
.hint-icon { font-size: 28px; }

/* Transition */
.slide-up-enter-active { transition: all 0.3s ease; }
.slide-up-enter-from   { opacity: 0; transform: translateY(12px); }

@media (max-width: 400px) {
  .slots-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>