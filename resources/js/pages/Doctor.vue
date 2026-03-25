<template>
  <div class="page">

    <!-- ══ HERO ══ -->
    <section class="hero">
      <div class="hero-glow hero-glow--r" />
      <div class="hero-glow hero-glow--l" />

      <div class="hero-inner">

        <!-- Left -->
        <div class="hero-left">
          <nav class="breadcrumb">
            <router-link to="/">Home</router-link>
            <span class="bc-sep">›</span>
            <router-link to="/doctors">Doctors</router-link>
            <span class="bc-sep">›</span>
            <span class="bc-active">{{ doctor.name }}</span>
          </nav>

          <div v-if="doctor.availableToday" class="avail-pill">
            <span class="avail-dot" />
            <span class="avail-label">Available Today</span>
          </div>

          <h1>
            {{ doctor.firstName }}<br />
            <em>{{ doctor.lastName }}</em>
          </h1>

          <p class="hero-role">
            {{ doctor.credentials }}
            <span class="sep">·</span>
            {{ doctor.specialty }}
            <span class="sep">·</span>
            {{ doctor.experience }}
          </p>

          <div class="meta-chips">
            <div v-for="chip in doctor.chips" :key="chip" class="meta-chip">
              {{ chip }}
            </div>
          </div>

          <div class="hero-actions">
            <router-link to="/book" class="btn btn--primary">
              Book a Consultation
            </router-link>
            <a href="#reviews" class="btn btn--ghost">Read Reviews</a>
          </div>
        </div>

        <!-- Right — floating card -->
        <div class="profile-card">
          <div class="profile-card__photo">
            <span class="profile-card__emoji">👩‍⚕️</span>
            <div v-if="doctor.isOnline" class="online-badge">
              <span class="online-badge__dot" />
              <span>Online Now</span>
            </div>
          </div>
          <div class="profile-card__body">
            <p class="pc-name">{{ doctor.name }}</p>
            <p class="pc-role">{{ doctor.title }}</p>
            <div class="pc-stats">
              <div v-for="s in stats" :key="s.label" class="pc-stat">
                <span class="pc-stat__val">{{ s.value }}</span>
                <span class="pc-stat__lbl">{{ s.label }}</span>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>

    <!-- ══ STATS BAND ══ -->
    <div class="stats-band">
      <div class="stats-band__inner">
        <div v-for="s in stats" :key="s.fullLabel" class="stat-item">
          <span class="stat-item__val">{{ s.value }}</span>
          <span class="stat-item__lbl">{{ s.fullLabel || s.label }}</span>
        </div>
      </div>
    </div>

    <!-- ══ BODY ══ -->
    <div class="body-grid">

      <!-- Main column -->
      <main class="main-col">

        <!-- About -->
        <div class="sec-block">
          <span class="eyebrow">About</span>
          <h2 class="sec-title">Meet Dr. {{ doctor.lastName }}</h2>
          <p v-for="(p, i) in doctor.bio" :key="i" class="body-p">{{ p }}</p>
          <blockquote class="pull-quote">
            <p>"{{ doctor.quote }}"</p>
            <cite>— {{ doctor.name }}</cite>
          </blockquote>
        </div>

        <!-- Expertise -->
        <div class="sec-block">
          <span class="eyebrow">Expertise</span>
          <h2 class="sec-title">Areas of Specialisation</h2>
          <div class="tag-cloud">
            <span v-for="spec in doctor.expertise" :key="spec" class="tag">
              {{ spec }}
            </span>
          </div>
        </div>

        <!-- Education -->
        <div class="sec-block">
          <span class="eyebrow">Credentials</span>
          <h2 class="sec-title">Education &amp; Training</h2>
          <div class="timeline">
            <div
              v-for="(item, i) in timeline"
              :key="item.year"
              class="tl-item"
              :class="{ 'tl-item--last': i === timeline.length - 1 }"
            >
              <div class="tl-icon">{{ item.icon }}</div>
              <div class="tl-body">
                <span class="tl-year">{{ item.year }}</span>
                <p class="tl-degree">{{ item.degree }}</p>
                <p class="tl-school">{{ item.school }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Reviews -->
        <div id="reviews" class="sec-block">
          <span class="eyebrow">Testimonials</span>
          <h2 class="sec-title">What Patients Say</h2>
          <div class="reviews">
            <div v-for="rev in reviews" :key="rev.name" class="rev-card">
              <span class="rev-card__deco">❝</span>
              <div class="rev-card__top">
                <div>
                  <p class="rev-card__name">{{ rev.name }}</p>
                  <p class="rev-card__date">{{ rev.date }}</p>
                </div>
                <span class="rev-card__stars">{{ rev.stars }}</span>
              </div>
              <p class="rev-card__text">{{ rev.text }}</p>
            </div>
          </div>
        </div>

      </main>

      <!-- Sidebar -->
      <aside class="sidebar">

        <!-- Book CTA -->
        <div class="cta-card">
          <h3 class="cta-card__title">Ready to consult?</h3>
          <p class="cta-card__sub">
            Book a secure video session with {{ doctor.name }} in minutes.
          </p>
          <router-link to="/book" class="cta-card__btn">
            Book Appointment →
          </router-link>
        </div>

        <!-- Schedule -->
        <div class="sb-card">
          <h4 class="sb-card__title">📅 Weekly Schedule</h4>
          <div class="schedule">
            <div
              v-for="day in schedule"
              :key="day.name"
              class="sched-row"
              :class="{ 'sched-row--today': day.isToday }"
            >
              <span class="sched-row__day">{{ day.name }}</span>
              <span
                class="sched-row__time"
                :class="{ 'sched-row__time--closed': day.closed }"
              >{{ day.time }}</span>
            </div>
          </div>
        </div>

        <!-- Languages -->
        <div class="sb-card">
          <h4 class="sb-card__title">🌐 Languages</h4>
          <div class="lang-chips">
            <span v-for="lang in doctor.languages" :key="lang" class="lang-chip">
              {{ lang }}
            </span>
          </div>
        </div>

        <!-- Payment -->
        <div class="sb-card">
          <h4 class="sb-card__title">💳 Insurance &amp; Payment</h4>
          <p class="sb-card__note">{{ doctor.paymentNote }}</p>
        </div>

      </aside>
    </div>

  </div>
</template>


<script setup>
const doctor = {
  name: 'Dr. Sarah Okafor',
  firstName: 'Dr. Sarah',
  lastName: 'Okafor',
  title: 'General & Family Physician',
  credentials: 'MBBS, FWACP',
  specialty: 'General & Family Medicine',
  experience: '14 yrs',
  availableToday: true,
  isOnline: true,
  chips: [
    '📍 Lagos, Nigeria (Remote)',
    '⭐ 4.9 · 312 reviews',
    '🎥 Video consultations',
    '🌐 Eng · Igbo · Yoruba',
  ],
  bio: [
    "Dr. Sarah Okafor is a board-certified General and Family Physician with over 14 years of clinical experience across Lagos and London. She completed her MBBS at the University of Lagos and her Fellowship at the West African College of Physicians, with a specialist focus on preventive medicine, chronic disease management, and mental health integration.",
    "Known for her warm, patient-centred approach, Dr. Okafor believes that quality healthcare should be accessible to everyone — which is why she joined Meridian Health's virtual clinic. She is fluent in English, Igbo, and Yoruba, and welcomes patients from all backgrounds.",
  ],
  quote: "Quality healthcare is a right, not a privilege. Every patient deserves a doctor who truly listens.",
  expertise: [
    'General Medicine', 'Family Health', 'Preventive Care', 'Chronic Disease',
    'Hypertension', 'Diabetes Management', "Women's Health", 'Mental Wellness',
    'Nutrition Guidance', 'Travel Medicine', 'Paediatric Care', 'Geriatric Health',
  ],
  languages: ['English', 'Igbo', 'Yoruba'],
  paymentNote: 'Accepts most Nigerian HMO plans, NHIS, and self-pay. International cards accepted via secure gateway.',
}

const stats = [
  { label: 'Patients',     value: '2,400+', fullLabel: 'Patients Seen'  },
  { label: 'Rating',       value: '4.9★',   fullLabel: 'Avg. Rating'    },
  { label: 'Experience',   value: '14yrs',  fullLabel: 'Experience'     },
  { label: 'Satisfaction', value: '98%',    fullLabel: 'Satisfaction'   },
]

const timeline = [
  { icon: '🎓', year: '2008', degree: 'MBBS — Bachelor of Medicine & Surgery',             school: 'University of Lagos, Nigeria'            },
  { icon: '🏅', year: '2014', degree: 'FWACP — Fellow, West African College of Physicians', school: 'Internal Medicine & General Practice'     },
  { icon: '🔬', year: '2018', degree: 'Advanced Diploma in Clinical Nutrition',             school: 'University of London (Distance Learning)' },
  { icon: '💻', year: '2021', degree: 'Certificate in Digital Health & Telemedicine',       school: 'University of Edinburgh (Online)'         },
]

const reviews = [
  { name: 'Chidinma A.', date: 'January 2026',  stars: '★★★★★', text: 'Dr. Okafor was incredibly thorough and compassionate. She listened to every concern and explained everything clearly. Booking was seamless and the video call quality was perfect.' },
  { name: 'James T.',    date: 'December 2025', stars: '★★★★★', text: 'Finally a doctor who takes the time to actually understand your situation. The follow-up notes she sent were incredibly detailed. Highly recommend for anyone managing a chronic condition.' },
  { name: 'Fatima I.',   date: 'November 2025', stars: '★★★★★', text: 'As someone who struggles with anxiety about doctors, this virtual format was a game changer. Dr. Okafor made me feel completely at ease.' },
]

const schedule = [
  { name: 'Monday',    time: '9:00 – 5:00 PM', isToday: true  },
  { name: 'Tuesday',   time: '9:00 – 5:00 PM'                 },
  { name: 'Wednesday', time: '9:00 – 1:00 PM'                 },
  { name: 'Thursday',  time: '9:00 – 5:00 PM'                 },
  { name: 'Friday',    time: '9:00 – 3:00 PM'                 },
  { name: 'Saturday',  time: 'Unavailable',    closed: true   },
  { name: 'Sunday',    time: 'Unavailable',    closed: true   },
]
</script>


<style scoped>
/*
  ╔══════════════════════════════════════════════╗
  ║  MERIDIAN HEALTH — DoctorProfile.vue styles  ║
  ║  Fonts: add to index.html / App.vue:          ║
  ║  <link href="https://fonts.googleapis.com/   ║
  ║  css2?family=Cormorant+Garamond:ital,wght    ║
  ║  @0,400;0,600;1,400;1,600&family=DM+Sans:   ║
  ║  wght@300;400;500&display=swap"              ║
  ║  rel="stylesheet">                           ║
  ╚══════════════════════════════════════════════╝
*/

/* ── Design tokens ── */
.page {
  --sage:      #3d6b5a;
  --sage-l:    #5a8a74;
  --sage-pale: #edf4f0;
  --cream:     #faf7f2;
  --warm:      #fff9f2;
  --stone:     #8c7b6e;
  --charcoal:  #1a1a1a;
  --muted:     #a09590;
  --gold:      #c4a96a;
  --gold-l:    #f0e0ba;
  --border:    #e8e0d5;

  font-family: 'DM Sans', sans-serif;
  font-weight: 300;
  background: var(--cream);
  color: var(--charcoal);
  line-height: 1.65;
}

.desktop-nav a {
    padding: 0.46rem 0.9rem;
    border-radius: 50px;
    text-decoration: none;
    font-size: 0.77rem;
    font-weight: 400;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: rgb(102 149 127) important;
    transition: all 0.22s;
}
.desktop-nav a:hover{color:white;background:rgba(255,255,255,0.14);}
/* ════════════════════════════════════════════
   HERO
════════════════════════════════════════════ */
.hero {
  position: relative;
  overflow: hidden;
  background: linear-gradient(155deg, #e3f0e8 0%, var(--cream) 48%, #fdf5e4 100%);
  padding: 4rem 5% 0;
}

/* Ambient glows */
.hero-glow {
  position: absolute;
  border-radius: 50%;
  pointer-events: none;
}
.hero-glow--r {
  width: 600px; height: 600px;
  right: -10%; top: -25%;
  background: radial-gradient(circle, rgba(196,169,106,.12) 0%, transparent 68%);
}
.hero-glow--l {
  width: 380px; height: 380px;
  left: -8%; bottom: -20%;
  background: radial-gradient(circle, rgba(61,107,90,.08) 0%, transparent 68%);
}

.hero-inner {
  position: relative;
  z-index: 1;
  max-width: 1100px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1fr 340px;
  gap: 3rem;
  align-items: end;
}

.hero-left { padding-bottom: 4rem; }

/* Breadcrumb */
.breadcrumb {
  display: flex;
  align-items: center;
  gap: .5rem;
  font-size: .7rem;
  text-transform: uppercase;
  letter-spacing: .18em;
  color: var(--muted);
  margin-bottom: 1.8rem;
}
.breadcrumb a {
  color: var(--muted);
  text-decoration: none;
  transition: color .2s;
}
.breadcrumb a:hover { color: var(--sage); }
.bc-sep    { color: var(--border); }
.bc-active { color: var(--stone); }

/* Availability pill */
.avail-pill {
  display: inline-flex;
  align-items: center;
  gap: .5rem;
  background: #fff;
  border: 1px solid rgba(34,197,94,.22);
  border-radius: 50px;
  padding: .34rem .9rem;
  margin-bottom: 1.4rem;
}
.avail-dot {
  width: 8px; height: 8px;
  border-radius: 50%;
  background: #22c55e;
  animation: pulse 2s infinite;
}
@keyframes pulse {
  0%,100% { box-shadow: 0 0 0 0 rgba(34,197,94,.45); }
  50%      { box-shadow: 0 0 0 6px rgba(34,197,94,0); }
}
.avail-label {
  font-size: .7rem;
  font-weight: 500;
  color: #16a34a;
  text-transform: uppercase;
  letter-spacing: .1em;
}

/* Name heading */
h1 {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(2.8rem, 5vw, 4.2rem);
  font-weight: 600;
  line-height: 1.06;
  color: var(--charcoal);
  margin-bottom: .45rem;
}
h1 em {
  font-style: italic;
  color: var(--sage);
}

/* Role */
.hero-role {
  font-size: .86rem;
  color: var(--stone);
  letter-spacing: .04em;
  margin-bottom: 1.8rem;
  display: flex;
  align-items: center;
  gap: .45rem;
  flex-wrap: wrap;
}
.sep { color: var(--border); }

/* Meta chips */
.meta-chips {
  display: flex;
  flex-wrap: wrap;
  gap: .55rem;
  margin-bottom: 2.4rem;
}
.meta-chip {
  background: #fff;
  border: 1px solid var(--border);
  border-radius: 50px;
  padding: .38rem .88rem;
  font-size: .78rem;
  color: var(--stone);
}

/* Buttons */
.hero-actions {
  display: flex;
  gap: .8rem;
  flex-wrap: wrap;
}
.btn {
  padding: .9rem 2rem;
  border-radius: 50px;
  font-family: 'DM Sans', sans-serif;
  font-size: .9rem;
  font-weight: 500;
  text-decoration: none;
  display: inline-block;
  cursor: pointer;
  transition: all .3s;
  border: none;
}
.btn--primary {
  background: var(--sage);
  color: #fff;
  box-shadow: 0 10px 28px rgba(61,107,90,.32);
}
.btn--primary:hover {
  background: var(--sage-l);
  transform: translateY(-2px);
  box-shadow: 0 14px 36px rgba(61,107,90,.42);
}
.btn--ghost {
  background: transparent;
  color: var(--charcoal);
  border: 1.5px solid var(--border);
}
.btn--ghost:hover {
  border-color: var(--sage);
  color: var(--sage);
  background: var(--sage-pale);
}

/* ── Profile card ── */
.profile-card {
  background: #fff;
  border-radius: 28px;
  overflow: hidden;
  box-shadow:
    0 28px 72px rgba(0,0,0,.10),
    0 0 0 1px rgba(196,169,106,.14);
  margin-bottom: -3rem;
  animation: cardRise .75s .2s both;
}
@keyframes cardRise {
  from { opacity: 0; transform: translateY(28px); }
  to   { opacity: 1; transform: translateY(0);    }
}

.profile-card__photo {
  height: 250px;
  background: linear-gradient(135deg, #3d6b5a, #5a8a74, #8ab5a0);
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}
.profile-card__emoji { font-size: 6rem; }

.online-badge {
  position: absolute;
  bottom: 1rem; right: 1rem;
  background: #fff;
  border-radius: 10px;
  padding: .48rem .85rem;
  display: flex;
  align-items: center;
  gap: .45rem;
  box-shadow: 0 4px 14px rgba(0,0,0,.10);
  font-size: .7rem;
  font-weight: 500;
  color: var(--charcoal);
}
.online-badge__dot {
  width: 7px; height: 7px;
  border-radius: 50%;
  background: #22c55e;
}

.profile-card__body { padding: 1.55rem; }
.pc-name {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.28rem; font-weight: 600;
  margin-bottom: .14rem; color: var(--charcoal);
}
.pc-role { font-size: .75rem; color: var(--stone); margin-bottom: 1.1rem; }

.pc-stats {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: .65rem;
}
.pc-stat {
  background: var(--sage-pale);
  border-radius: 10px;
  padding: .68rem;
  text-align: center;
}
.pc-stat__val {
  display: block;
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.32rem; font-weight: 600;
  color: var(--sage); line-height: 1;
}
.pc-stat__lbl {
  display: block;
  font-size: .6rem;
  text-transform: uppercase;
  letter-spacing: .08em;
  color: var(--muted);
  margin-top: 3px;
}

/* ════════════════════════════════════════════
   STATS BAND
════════════════════════════════════════════ */
.stats-band {
  background: var(--charcoal);
  padding: 2.4rem 5%;
  margin-top: 3rem;
}
.stats-band__inner {
  max-width: 1100px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
}
.stat-item {
  text-align: center;
  padding: .5rem 1rem;
  border-right: 1px solid rgba(255,255,255,.08);
}
.stat-item:last-child { border-right: none; }
.stat-item__val {
  display: block;
  font-family: 'Cormorant Garamond', serif;
  font-size: 2.1rem; font-weight: 600;
  color: var(--gold); line-height: 1;
}
.stat-item__lbl {
  display: block;
  font-size: .64rem;
  text-transform: uppercase;
  letter-spacing: .14em;
  color: rgba(255,255,255,.42);
  margin-top: 5px;
}

/* ════════════════════════════════════════════
   BODY GRID
════════════════════════════════════════════ */
.body-grid {
  max-width: 1100px;
  margin: 0 auto;
  padding: 4rem 5%;
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: 4rem;
  align-items: start;
}

/* ── Shared section helpers ── */
.sec-block { margin-bottom: 3rem; }
.sec-block:last-child { margin-bottom: 0; }

.eyebrow {
  display: flex;
  align-items: center;
  gap: .55rem;
  font-size: .62rem;
  text-transform: uppercase;
  letter-spacing: .26em;
  color: var(--gold);
  margin-bottom: .45rem;
}
.eyebrow::before {
  content: '';
  width: 22px; height: 1px;
  background: var(--gold);
  flex-shrink: 0;
}

.sec-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.82rem; font-weight: 600;
  color: var(--charcoal);
  margin-bottom: 1.35rem;
  line-height: 1.15;
}

.body-p {
  font-size: .93rem;
  color: var(--stone);
  line-height: 1.9;
  margin-bottom: 1rem;
}

/* Pull quote */
.pull-quote {
  border-left: 3px solid var(--gold);
  background: var(--warm);
  border-radius: 0 12px 12px 0;
  padding: 1.15rem 1.6rem;
  margin-top: 1.6rem;
}
.pull-quote p {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.12rem; font-style: italic;
  color: var(--charcoal); line-height: 1.65;
}
.pull-quote cite {
  display: block;
  margin-top: .5rem;
  font-size: .67rem;
  text-transform: uppercase;
  letter-spacing: .12em;
  color: var(--muted);
  font-style: normal;
}

/* Tag cloud */
.tag-cloud { display: flex; flex-wrap: wrap; gap: .5rem; }
.tag {
  background: #fff;
  color: var(--charcoal);
  border: 1.5px solid var(--border);
  font-size: .78rem;
  padding: .42rem .95rem;
  border-radius: 50px;
  transition: all .22s;
  cursor: default;
}
.tag:hover {
  border-color: var(--sage);
  color: var(--sage);
  background: var(--sage-pale);
}

/* Timeline */
.timeline { display: flex; flex-direction: column; }
.tl-item {
  display: grid;
  grid-template-columns: 52px 1fr;
  gap: 1.1rem;
  position: relative;
  padding-bottom: 1.8rem;
}
.tl-item:not(.tl-item--last)::after {
  content: '';
  position: absolute;
  left: 25px; top: 52px; bottom: 0;
  width: 2px;
  background: linear-gradient(to bottom, var(--border), transparent);
}
.tl-icon {
  width: 50px; height: 50px;
  border-radius: 14px;
  background: linear-gradient(135deg, var(--sage-pale), #fff);
  border: 2px solid var(--border);
  display: flex; align-items: center; justify-content: center;
  font-size: 1.2rem; flex-shrink: 0;
  position: relative; z-index: 1;
}
.tl-year {
  display: block;
  font-size: .65rem;
  text-transform: uppercase;
  letter-spacing: .12em;
  color: var(--gold); margin-bottom: .22rem;
}
.tl-degree {
  font-size: .92rem; font-weight: 500;
  color: var(--charcoal); margin-bottom: .12rem;
}
.tl-school { font-size: .78rem; color: var(--muted); }

/* Reviews */
.reviews { display: flex; flex-direction: column; gap: 1.2rem; }
.rev-card {
  background: #fff;
  border-radius: 18px;
  padding: 1.7rem;
  border: 1px solid var(--border);
  position: relative; overflow: hidden;
  transition: box-shadow .28s, transform .28s;
}
.rev-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 14px 38px rgba(0,0,0,.08);
}
.rev-card__deco {
  position: absolute; top: .5rem; right: 1.1rem;
  font-size: 3.2rem; line-height: 1;
  color: var(--sage-pale);
  font-family: 'Cormorant Garamond', serif;
  pointer-events: none;
}
.rev-card__top {
  display: flex; justify-content: space-between;
  align-items: flex-start; margin-bottom: .85rem;
}
.rev-card__name  { font-weight: 500; font-size: .88rem; margin-bottom: 2px; }
.rev-card__date  { font-size: .7rem; color: var(--muted); }
.rev-card__stars { color: var(--gold); font-size: .88rem; letter-spacing: 1px; }
.rev-card__text  { font-size: .83rem; color: var(--stone); line-height: 1.75; }

/* ════════════════════════════════════════════
   SIDEBAR
════════════════════════════════════════════ */
.sidebar {
  display: flex; flex-direction: column;
  gap: 1.15rem;
  position: sticky; top: 84px;
}

/* CTA card */
.cta-card {
  background: linear-gradient(135deg, var(--sage), var(--sage-l));
  border-radius: 22px; padding: 2rem; text-align: center;
  box-shadow: 0 18px 44px rgba(61,107,90,.28);
}
.cta-card__title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.28rem; font-weight: 600;
  color: #fff; margin-bottom: .38rem;
}
.cta-card__sub {
  font-size: .77rem; color: rgba(255,255,255,.7);
  line-height: 1.65; margin-bottom: 1.4rem;
}
.cta-card__btn {
  display: block; width: 100%; padding: .88rem;
  background: #fff; color: var(--sage); border-radius: 12px;
  font-family: 'DM Sans', sans-serif;
  font-size: .88rem; font-weight: 500;
  text-align: center; text-decoration: none;
  box-shadow: 0 6px 20px rgba(0,0,0,.12);
  transition: all .25s;
}
.cta-card__btn:hover {
  background: var(--gold-l);
  transform: translateY(-1px);
}

/* Generic sidebar card */
.sb-card {
  background: #fff; border-radius: 18px;
  padding: 1.6rem; border: 1px solid var(--border);
  box-shadow: 0 4px 18px rgba(0,0,0,.04);
}
.sb-card__title {
  font-family: 'Cormorant Garamond', serif;
  font-size: .98rem; font-weight: 600;
  color: var(--charcoal);
  margin-bottom: 1.1rem; padding-bottom: .82rem;
  border-bottom: 1px solid var(--border);
  display: flex; align-items: center; gap: .4rem;
}
.sb-card__note { font-size: .8rem; color: var(--stone); line-height: 1.72; }

/* Schedule */
.schedule { display: flex; flex-direction: column; }
.sched-row {
  display: flex; justify-content: space-between; align-items: center;
  padding: .5rem 0; font-size: .81rem;
  border-bottom: 1px solid #f5f0ea;
}
.sched-row:last-child { border-bottom: none; }
.sched-row--today {
  background: var(--sage-pale);
  margin: 0 -.5rem; padding: .5rem .5rem;
  border-radius: 8px; border-bottom: none;
}
.sched-row--today .sched-row__day { color: var(--sage); font-weight: 500; }
.sched-row__day  { color: var(--charcoal); }
.sched-row__time { color: var(--sage); font-size: .78rem; font-weight: 500; }
.sched-row__time--closed { color: var(--muted); font-style: italic; font-weight: 300; }

/* Language chips */
.lang-chips { display: flex; flex-wrap: wrap; gap: .4rem; }
.lang-chip {
  background: var(--sage-pale); color: var(--sage);
  font-size: .75rem; padding: .28rem .72rem; border-radius: 20px;
}

/* ════════════════════════════════════════════
   RESPONSIVE
════════════════════════════════════════════ */
@media (max-width: 960px) {
  .hero-inner         { grid-template-columns: 1fr; }
  .profile-card       { display: none; }
  .body-grid          { grid-template-columns: 1fr; }
  .sidebar            { position: static; }
  .stats-band__inner  { grid-template-columns: repeat(2, 1fr); }
  .stat-item:nth-child(2) { border-right: none; }
}

@media (max-width: 560px) {
  .hero-left    { padding-bottom: 3rem; }
  h1            { font-size: 2.6rem; }
  .hero-actions { flex-direction: column; }
  .btn          { width: 100%; text-align: center; }
}
</style>