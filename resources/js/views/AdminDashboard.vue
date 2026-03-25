<template>
  <div class="dashboard">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="sidebar-brand">
        <div class="brand-icon">🏥</div>
        <div class="brand-text">
          <span class="brand-main">Meridian</span>
          <span class="brand-sub">Doctor Portal</span>
        </div>
      </div>

      <nav class="sidebar-nav">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          class="nav-item"
          :class="{ active: activeTab === tab.id }"
          @click="activeTab = tab.id"
        >
          <span class="nav-icon">{{ tab.icon }}</span>
          <span class="nav-label">{{ tab.label }}</span>
          <span v-if="tab.count !== null && tab.count !== undefined" class="nav-badge">{{ tab.count }}</span>
        </button>
      </nav>

      <div class="sidebar-footer">
        <div class="doctor-card">
          <div class="doctor-avatar">DR</div>
          <div class="doctor-info">
            <span class="doctor-name">Dr. Admin</span>
            <span class="doctor-role">General Physician</span>
          </div>
        </div>
        <router-link to="/" class="logout-btn">← Back to Site</router-link>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main">
      <!-- Top Bar -->
      <div class="topbar">
        <div class="topbar-left">
          <h1 class="page-title">{{ currentTabLabel }}</h1>
          <p class="page-date">{{ todayFormatted }}</p>
        </div>
        <div class="topbar-right">
          <div class="search-box" v-if="activeTab !== 'availability'">
            <span class="search-icon">🔍</span>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search patients..."
              class="search-input"
            />
          </div>
          <button class="refresh-btn" @click="fetchBookings" :class="{ spinning: loading }" v-if="activeTab !== 'availability'">
            ↻
          </button>
        </div>
      </div>

      <!-- ── AVAILABILITY TAB ── -->
      <AvailabilityManager v-if="activeTab === 'availability'" />

      <!-- ── ALL OTHER TABS ── -->
      <template v-else>

        <!-- Stats Row -->
        <div class="stats-row" v-if="activeTab === 'all'">
          <div class="stat-card" v-for="stat in stats" :key="stat.label">
            <div class="stat-icon">{{ stat.icon }}</div>
            <div class="stat-body">
              <div class="stat-number">{{ stat.value }}</div>
              <div class="stat-label">{{ stat.label }}</div>
            </div>
            <div class="stat-bar" :style="{ '--color': stat.color }">
              <div class="stat-fill" :style="{ width: stat.pct + '%', background: stat.color }"></div>
            </div>
          </div>
        </div>

        <!-- Error State -->
        <div v-if="error" class="error-banner">
          ⚠️ {{ error }}
          <button @click="fetchBookings" class="retry-btn">Retry</button>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="loading-grid">
          <div class="skeleton-card" v-for="n in 4" :key="n"></div>
        </div>

        <!-- Appointments Table -->
        <div v-else-if="filteredBookings.length" class="table-wrapper">
          <table class="appointments-table">
            <thead>
              <tr>
                <th @click="sortBy('id')" class="sortable">
                  # <span class="sort-icon">{{ sortIcon('id') }}</span>
                </th>
                <th @click="sortBy('patient_name')" class="sortable">
                  Patient <span class="sort-icon">{{ sortIcon('patient_name') }}</span>
                </th>
                <th>Contact</th>
                <th @click="sortBy('service')" class="sortable">
                  Service <span class="sort-icon">{{ sortIcon('service') }}</span>
                </th>
                <th @click="sortBy('appointment_date')" class="sortable">
                  Date & Time <span class="sort-icon">{{ sortIcon('appointment_date') }}</span>
                </th>
                <th @click="sortBy('status')" class="sortable">
                  Status <span class="sort-icon">{{ sortIcon('status') }}</span>
                </th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="booking in filteredBookings"
                :key="booking.id"
                class="table-row"
                :class="'row-' + booking.status"
              >
                <td class="id-cell">#{{ booking.id }}</td>

                <td class="patient-cell">
                  <div class="patient-avatar">{{ initials(booking.patient_name) }}</div>
                  <span class="patient-name">{{ booking.patient_name }}</span>
                </td>

                <td class="contact-cell">
                  <div class="contact-email">✉ {{ booking.email }}</div>
                  <div class="contact-phone" v-if="booking.phone">📞 {{ booking.phone }}</div>
                </td>

                <td class="service-cell">
                  <span class="service-tag">{{ booking.service }}</span>
                </td>

                <td class="date-cell">
                  <div class="date-main">{{ formatDate(booking.appointment_date) }}</div>
                  <div class="date-time">{{ booking.appointment_time || '—' }}</div>
                </td>

                <td class="status-cell">
                  <span class="status-badge" :class="'badge-' + booking.status">
                    {{ statusLabel(booking.status) }}
                  </span>
                </td>

                <td class="actions-cell">
                  <button
                    v-if="booking.status === 'pending'"
                    class="action-btn confirm"
                    @click="updateStatus(booking, 'confirmed')"
                    title="Confirm"
                  >✓</button>
                  <button
                    v-if="booking.status === 'confirmed'"
                    class="action-btn complete"
                    @click="updateStatus(booking, 'completed')"
                    title="Mark Completed"
                  >★</button>
                  <button
                    v-if="booking.status !== 'cancelled' && booking.status !== 'completed'"
                    class="action-btn cancel"
                    @click="updateStatus(booking, 'cancelled')"
                    title="Cancel"
                  >✕</button>
                  <button
                    class="action-btn view"
                    @click="openDetail(booking)"
                    title="View Details"
                  >👁</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div v-else-if="!loading" class="empty-state">
          <div class="empty-icon">📭</div>
          <p class="empty-title">No appointments found</p>
          <p class="empty-sub">{{ searchQuery ? 'Try a different search term.' : 'No bookings in this category yet.' }}</p>
        </div>

      </template>
    </main>

    <!-- Detail Modal -->
    <transition name="modal">
      <div v-if="selectedBooking" class="modal-overlay" @click.self="selectedBooking = null">
        <div class="modal">
          <button class="modal-close" @click="selectedBooking = null">✕</button>
          <div class="modal-header">
            <div class="modal-avatar">{{ initials(selectedBooking.patient_name) }}</div>
            <div>
              <h2 class="modal-name">{{ selectedBooking.patient_name }}</h2>
              <span class="status-badge" :class="'badge-' + selectedBooking.status">
                {{ statusLabel(selectedBooking.status) }}
              </span>
            </div>
          </div>
          <div class="modal-body">
            <div class="detail-grid">
              <div class="detail-item">
                <span class="detail-label">Email</span>
                <span class="detail-value">{{ selectedBooking.email }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Phone</span>
                <span class="detail-value">{{ selectedBooking.phone || '—' }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Service</span>
                <span class="detail-value">{{ selectedBooking.service }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Date</span>
                <span class="detail-value">{{ formatDate(selectedBooking.appointment_date) }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Time</span>
                <span class="detail-value">{{ selectedBooking.appointment_time || '—' }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Booking ID</span>
                <span class="detail-value">#{{ selectedBooking.id }}</span>
              </div>
            </div>
            <div class="detail-item full-width" v-if="selectedBooking.notes">
              <span class="detail-label">Notes</span>
              <span class="detail-value">{{ selectedBooking.notes }}</span>
            </div>
          </div>
          <div class="modal-actions">
            <button
              v-if="selectedBooking.status === 'pending'"
              class="modal-btn confirm"
              @click="updateStatus(selectedBooking, 'confirmed'); selectedBooking = null"
            >✓ Confirm Appointment</button>
            <button
              v-if="selectedBooking.status === 'confirmed'"
              class="modal-btn complete"
              @click="updateStatus(selectedBooking, 'completed'); selectedBooking = null"
            >★ Mark as Completed</button>
            <button
              v-if="selectedBooking.status !== 'cancelled' && selectedBooking.status !== 'completed'"
              class="modal-btn cancel"
              @click="updateStatus(selectedBooking, 'cancelled'); selectedBooking = null"
            >✕ Cancel</button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import AvailabilityManager from '../components/AvailabilityManager.vue'

const bookings = ref([])
const loading = ref(false)
const error = ref(null)
const searchQuery = ref('')
const activeTab = ref('all')
const selectedBooking = ref(null)
const sortKey = ref('appointment_date')
const sortDir = ref('asc')

const tabs = computed(() => [
  { id: 'all',          icon: '📋', label: 'All Appointments', count: bookings.value.length },
  { id: 'pending',      icon: '⏳', label: 'Pending',          count: bookings.value.filter(b => b.status === 'pending').length },
  { id: 'confirmed',    icon: '✅', label: 'Confirmed',         count: bookings.value.filter(b => b.status === 'confirmed').length },
  { id: 'completed',    icon: '⭐', label: 'Completed',         count: bookings.value.filter(b => b.status === 'completed').length },
  { id: 'cancelled',    icon: '❌', label: 'Cancelled',         count: bookings.value.filter(b => b.status === 'cancelled').length },
  { id: 'availability', icon: '🗓', label: 'Availability',      count: null },
])

const currentTabLabel = computed(() => tabs.value.find(t => t.id === activeTab.value)?.label || '')

const todayFormatted = computed(() =>
  new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })
)

const stats = computed(() => {
  const total = bookings.value.length || 1
  return [
    { icon: '📋', label: 'Total',     value: bookings.value.length, color: '#6366f1', pct: 100 },
    { icon: '⏳', label: 'Pending',   value: bookings.value.filter(b => b.status === 'pending').length,   color: '#f59e0b', pct: Math.round(bookings.value.filter(b => b.status === 'pending').length / total * 100) },
    { icon: '✅', label: 'Confirmed', value: bookings.value.filter(b => b.status === 'confirmed').length, color: '#10b981', pct: Math.round(bookings.value.filter(b => b.status === 'confirmed').length / total * 100) },
    { icon: '⭐', label: 'Completed', value: bookings.value.filter(b => b.status === 'completed').length, color: '#3b82f6', pct: Math.round(bookings.value.filter(b => b.status === 'completed').length / total * 100) },
  ]
})

const filteredBookings = computed(() => {
  let list = bookings.value

  if (activeTab.value !== 'all' && activeTab.value !== 'availability') {
    list = list.filter(b => b.status === activeTab.value)
  }

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    list = list.filter(b =>
      b.patient_name?.toLowerCase().includes(q) ||
      b.email?.toLowerCase().includes(q) ||
      b.service?.toLowerCase().includes(q)
    )
  }

  return [...list].sort((a, b) => {
    const va = a[sortKey.value] ?? ''
    const vb = b[sortKey.value] ?? ''
    return sortDir.value === 'asc'
      ? String(va).localeCompare(String(vb))
      : String(vb).localeCompare(String(va))
  })
})

async function fetchBookings() {
  loading.value = true
  error.value = null
  try {
    const { data } = await axios.get('/api/bookings')
    bookings.value = Array.isArray(data) ? data : (data.data ?? [])
  } catch (e) {
    error.value = 'Failed to load appointments. Please try again.'
  } finally {
    loading.value = false
  }
}

async function updateStatus(booking, status) {
  try {
    await axios.patch(`/api/bookings/${booking.id}`, { status })
    const index = bookings.value.findIndex(b => b.id === booking.id)
    if (index !== -1) {
      bookings.value[index] = { ...bookings.value[index], status }
    }
  } catch (e) {
    alert('Failed to update status. Please try again.')
  }
}

function openDetail(booking) {
  selectedBooking.value = booking
}

function sortBy(key) {
  if (sortKey.value === key) {
    sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortKey.value = key
    sortDir.value = 'asc'
  }
}

function sortIcon(key) {
  if (sortKey.value !== key) return '↕'
  return sortDir.value === 'asc' ? '↑' : '↓'
}

function initials(name) {
  return (name || '?').split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2)
}

function formatDate(dateStr) {
  if (!dateStr) return '—'
  return new Date(dateStr).toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' })
}

function statusLabel(status) {
  return { pending: 'Pending', confirmed: 'Confirmed', completed: 'Completed', cancelled: 'Cancelled' }[status] ?? status
}

onMounted(fetchBookings)
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

* { box-sizing: border-box; margin: 0; padding: 0; }

.dashboard {
  display: flex;
  min-height: 100vh;
  background: #f0f2f7;
  font-family: 'DM Sans', sans-serif;
}

/* ── SIDEBAR ── */
.sidebar {
  width: 260px;
  min-height: 100vh;
  background: #0f172a;
  display: flex;
  flex-direction: column;
  flex-shrink: 0;
  position: sticky;
  top: 0;
  height: 100vh;
  overflow-y: auto;
}

.sidebar-brand {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 28px 24px 24px;
  border-bottom: 1px solid rgba(255,255,255,0.07);
}

.brand-icon { font-size: 28px; }
.brand-text { display: flex; flex-direction: column; }
.brand-main { color: #fff; font-weight: 700; font-size: 15px; letter-spacing: -0.3px; }
.brand-sub  { color: #64748b; font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase; }

.sidebar-nav {
  flex: 1;
  padding: 16px 12px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 11px 14px;
  border-radius: 10px;
  border: none;
  background: transparent;
  color: #94a3b8;
  cursor: pointer;
  font-family: 'DM Sans', sans-serif;
  font-size: 14px;
  font-weight: 500;
  text-align: left;
  transition: all 0.18s;
  width: 100%;
}

.nav-item:hover { background: rgba(255,255,255,0.06); color: #e2e8f0; }
.nav-item.active { background: #6366f1; color: #fff; }

.nav-icon { font-size: 16px; width: 20px; text-align: center; }
.nav-label { flex: 1; }
.nav-badge {
  background: rgba(255,255,255,0.2);
  color: inherit;
  border-radius: 20px;
  padding: 1px 8px;
  font-size: 11px;
  font-family: 'DM Mono', monospace;
}
.nav-item.active .nav-badge { background: rgba(255,255,255,0.25); }

.sidebar-footer {
  padding: 16px 16px 24px;
  border-top: 1px solid rgba(255,255,255,0.07);
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.doctor-card {
  display: flex;
  align-items: center;
  gap: 10px;
}

.doctor-avatar {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 13px;
  flex-shrink: 0;
}

.doctor-info { display: flex; flex-direction: column; }
.doctor-name { color: #e2e8f0; font-size: 13px; font-weight: 600; }
.doctor-role { color: #64748b; font-size: 11px; }

.logout-btn {
  color: #64748b;
  font-size: 12px;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: color 0.18s;
}
.logout-btn:hover { color: #94a3b8; }

/* ── MAIN ── */
.main {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
  min-width: 0;
}

.topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 28px 32px 20px;
  gap: 16px;
}

.page-title { font-size: 22px; font-weight: 700; color: #0f172a; letter-spacing: -0.5px; }
.page-date  { color: #94a3b8; font-size: 13px; margin-top: 2px; }

.topbar-right { display: flex; align-items: center; gap: 10px; }

.search-box {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 8px 14px;
  width: 240px;
}

.search-icon { font-size: 14px; }
.search-input {
  border: none;
  outline: none;
  background: transparent;
  font-family: 'DM Sans', sans-serif;
  font-size: 14px;
  color: #0f172a;
  width: 100%;
}
.search-input::placeholder { color: #94a3b8; }

.refresh-btn {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  background: #fff;
  font-size: 18px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
  color: #64748b;
}
.refresh-btn:hover { border-color: #6366f1; color: #6366f1; }
.refresh-btn.spinning { animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* ── STATS ── */
.stats-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  padding: 0 32px 24px;
}

.stat-card {
  background: #fff;
  border-radius: 14px;
  padding: 18px 20px 14px;
  display: flex;
  align-items: flex-start;
  gap: 14px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.06);
  border: 1px solid #f1f5f9;
  position: relative;
  overflow: hidden;
}

.stat-icon { font-size: 24px; flex-shrink: 0; }
.stat-body { flex: 1; min-width: 0; }
.stat-number { font-size: 26px; font-weight: 700; color: #0f172a; font-family: 'DM Mono', monospace; line-height: 1; }
.stat-label  { font-size: 12px; color: #94a3b8; margin-top: 4px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }

.stat-bar {
  position: absolute;
  bottom: 0; left: 0; right: 0;
  height: 3px;
  background: #f1f5f9;
}
.stat-fill {
  height: 100%;
  border-radius: 0 2px 2px 0;
  transition: width 0.6s ease;
}

/* ── ERROR / LOADING ── */
.error-banner {
  margin: 0 32px 16px;
  padding: 14px 20px;
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 10px;
  color: #dc2626;
  font-size: 14px;
  display: flex;
  align-items: center;
  gap: 12px;
}

.retry-btn {
  margin-left: auto;
  padding: 6px 14px;
  border-radius: 6px;
  border: 1px solid #dc2626;
  background: transparent;
  color: #dc2626;
  font-size: 13px;
  cursor: pointer;
}

.loading-grid {
  display: grid;
  gap: 12px;
  padding: 0 32px;
}

.skeleton-card {
  height: 64px;
  border-radius: 10px;
  background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
  background-size: 200% 100%;
  animation: shimmer 1.4s infinite;
}

@keyframes shimmer {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

/* ── TABLE ── */
.table-wrapper {
  flex: 1;
  overflow: auto;
  padding: 0 32px 32px;
}

.appointments-table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0,0,0,0.06);
  border: 1px solid #f1f5f9;
}

thead {
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
}

th {
  padding: 14px 16px;
  text-align: left;
  font-size: 11px;
  font-weight: 600;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  white-space: nowrap;
}

th.sortable { cursor: pointer; user-select: none; }
th.sortable:hover { color: #6366f1; }
.sort-icon { opacity: 0.5; font-size: 10px; }

.table-row {
  border-bottom: 1px solid #f1f5f9;
  transition: background 0.15s;
}
.table-row:last-child { border-bottom: none; }
.table-row:hover { background: #f8fafc; }

td { padding: 14px 16px; vertical-align: middle; }

.id-cell {
  font-family: 'DM Mono', monospace;
  font-size: 12px;
  color: #94a3b8;
}

.patient-cell {
  display: flex;
  align-items: center;
  gap: 10px;
}

.patient-avatar {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.patient-name { font-weight: 600; font-size: 14px; color: #0f172a; }

.contact-cell { font-size: 12px; color: #64748b; }
.contact-email { margin-bottom: 2px; }

.service-tag {
  background: #ede9fe;
  color: #7c3aed;
  padding: 3px 10px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
  white-space: nowrap;
}

.date-cell { white-space: nowrap; }
.date-main { font-size: 13px; font-weight: 500; color: #1e293b; }
.date-time  { font-size: 12px; color: #94a3b8; font-family: 'DM Mono', monospace; }

.status-badge {
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.3px;
  text-transform: capitalize;
}
.badge-pending   { background: #fef3c7; color: #d97706; }
.badge-confirmed { background: #d1fae5; color: #059669; }
.badge-completed { background: #dbeafe; color: #2563eb; }
.badge-cancelled { background: #fee2e2; color: #dc2626; }

.actions-cell { display: flex; align-items: center; gap: 6px; }

.action-btn {
  width: 30px;
  height: 30px;
  border-radius: 8px;
  border: 1px solid transparent;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  transition: all 0.15s;
}

.action-btn.confirm  { background: #d1fae5; color: #059669; }
.action-btn.confirm:hover  { background: #059669; color: #fff; }
.action-btn.complete { background: #dbeafe; color: #2563eb; }
.action-btn.complete:hover { background: #2563eb; color: #fff; }
.action-btn.cancel   { background: #fee2e2; color: #dc2626; }
.action-btn.cancel:hover   { background: #dc2626; color: #fff; }
.action-btn.view     { background: #f1f5f9; color: #64748b; }
.action-btn.view:hover     { background: #6366f1; color: #fff; }

/* ── EMPTY ── */
.empty-state {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 80px 32px;
  color: #94a3b8;
}
.empty-icon  { font-size: 56px; margin-bottom: 16px; }
.empty-title { font-size: 18px; font-weight: 600; color: #64748b; margin-bottom: 6px; }
.empty-sub   { font-size: 14px; }

/* ── MODAL ── */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 100;
  padding: 20px;
  backdrop-filter: blur(4px);
}

.modal {
  background: #fff;
  border-radius: 18px;
  width: 100%;
  max-width: 480px;
  padding: 28px;
  position: relative;
  box-shadow: 0 20px 60px rgba(0,0,0,0.2);
}

.modal-close {
  position: absolute;
  top: 16px;
  right: 16px;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  background: transparent;
  cursor: pointer;
  font-size: 14px;
  color: #64748b;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.15s;
}
.modal-close:hover { background: #f1f5f9; }

.modal-header {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 24px;
}

.modal-avatar {
  width: 52px;
  height: 52px;
  border-radius: 50%;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff;
  font-size: 18px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.modal-name { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 6px; }

.modal-body { margin-bottom: 24px; }

.detail-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.detail-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.detail-item.full-width {
  grid-column: 1 / -1;
  margin-top: 8px;
}

.detail-label { font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.detail-value { font-size: 14px; color: #1e293b; font-weight: 500; }

.modal-actions { display: flex; gap: 8px; flex-wrap: wrap; }

.modal-btn {
  flex: 1;
  padding: 11px 16px;
  border-radius: 10px;
  border: none;
  font-family: 'DM Sans', sans-serif;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.18s;
  min-width: 130px;
}

.modal-btn.confirm  { background: #059669; color: #fff; }
.modal-btn.confirm:hover  { background: #047857; }
.modal-btn.complete { background: #2563eb; color: #fff; }
.modal-btn.complete:hover { background: #1d4ed8; }
.modal-btn.cancel   { background: #fee2e2; color: #dc2626; }
.modal-btn.cancel:hover   { background: #fecaca; }

/* ── TRANSITIONS ── */
.modal-enter-active, .modal-leave-active { transition: all 0.2s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
.modal-enter-from .modal, .modal-leave-to .modal { transform: scale(0.95) translateY(10px); }

/* ── RESPONSIVE ── */
@media (max-width: 1024px) {
  .stats-row { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
  .sidebar { display: none; }
  .topbar { padding: 20px 16px 16px; flex-direction: column; align-items: flex-start; }
  .stats-row { padding: 0 16px 16px; grid-template-columns: repeat(2, 1fr); }
  .table-wrapper { padding: 0 16px 24px; }
  th:nth-child(3), td:nth-child(3) { display: none; }
}
</style>