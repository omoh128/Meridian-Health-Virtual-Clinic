<template>
  <div>
    <section class="doc">
      <div class="doc-inner">
        <div class="doc-left">
          <div class="breadcrumb">
            <router-link to="/">Home</router-link><span>/</span>
            <router-link to="/doctors">Doctors</router-link><span>/</span>
            {{ doctor.name }}
          </div>
          <div v-if="doctor.availableToday" class="avail-pill">
            <div class="avail-dot"></div>
            <span class="avail-text">Available Today</span>
          </div>
          <h1>{{ doctor.firstName }}<br><em>{{ doctor.lastName }}</em></h1>
          <div class="doc-role">
            {{ doctor.credentials }} <span class="rs">·</span> 
            {{ doctor.specialty }} <span class="rs">·</span> 
            {{ doctor.experience }}
          </div>
          <div class="doc-chips">
            <div v-for="chip in doctor.chips" :key="chip" class="chip">{{ chip }}</div>
          </div>
          <div class="doc-btns">
            <router-link to="/book" class="btn-p">Book a Consultation</router-link>
            <a href="#reviews" class="btn-g">Read Reviews</a>
          </div>
        </div>

        <div class="doc-card">
          <div class="card-photo">
            <div class="card-photo-emoji">👩‍⚕️</div>
            <div v-if="doctor.isOnline" class="card-online">
              <div class="cod"></div>
              <div class="cot">Online Now</div>
            </div>
          </div>
          <div class="docard-info">
            <div class="docard-name">{{ doctor.name }}</div>
            <div class="docard-role-sm">{{ doctor.title }}</div>
            <div class="docard-grid">
              <div v-for="stat in stats" :key="stat.label" class="cg-item">
                <span class="docg-num">{{ stat.value }}</span>
                <div class="dcg-lbl">{{ stat.label }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <div class="docstats-band">
      <div class="docstats-inner">
        <div v-for="stat in stats" :key="stat.label" class="docstat-c">
          <span class="dsn">{{ stat.value }}</span>
          <div class="dsl">{{ stat.fullLabel || stat.label }}</div>
        </div>
      </div>
    </div>

    <div class="docbody-wrap">
      <main>
        <div class="doceyebrow">About</div>
        <h2 class="docsec-h">Meet {{ doctor.lastName }}</h2>
        <p v-for="(para, index) in doctor.bio" :key="index" class="docabout-p">
          {{ para }}
        </p>
        
        <div class="docpullout">
          <p>"{{ doctor.quote }}"</p>
          <cite>— {{ doctor.name }}</cite>
        </div>

        <div class="docspecs">
          <div class="doceyebrow">Expertise</div>
          <h2 class="docsec-h">Areas of Specialisation</h2>
            <span v-for="spec in doctor.expertise" :key="spec" class="spec-tag">
              {{ spec }}
            </span>
          </div>
        </div>

        <div class="doctimeline">
          <div class="doceyebrow">Credentials</div>
          <h2 class="docsec-h">Education &amp; Training</h2>
          <div v-for="item in doctor.timeline" :key="item.year" class="tl-item">
            <div class="dtl-dot">{{ item.icon }}</div>
            <div>
              <div class="dtl-year">{{ item.year }}</div>
              <div class="dtl-deg">{{ item.degree }}</div>
              <div class="dtl-sch">{{ item.school }}</div>
            </div>
          </div>
        </div>

        <div id="docreviews">
          <div class="doceyebrow">Testimonials</div>
          <h2 class="docsec-h">What Patients Say</h2>
          <div class="dreviews">
            <div v-for="rev in reviews" :key="rev.name" class="rev-card">
              <div class="drev-top">
                <div>
                  <div class="drev-name">{{ rev.name }}</div>
                  <div class="drev-date">{{ rev.date }}</div>
                </div>
                <div class="drev-stars">{{ rev.stars }}</div>
              </div>
              <div class="drev-text">{{ rev.text }}</div>
            </div>
          </div>
        </div>
      

      <aside class="docsidebar">
        <div class="doccta-card">
          <div class="doccta-title">Ready to consult?</div>
          <div class="doccta-sub">Book a secure video session with {{ doctor.name }} in minutes.</div>
          <router-link to="/book" class="doccta-btn">Book Appointment →</router-link>
        </div>

        <div class="ds-card">
          <div class="ds-title">📅 Weekly Schedule</div>
          <div class="dsched">
            <div 
              v-for="day in schedule" 
              :key="day.name" 
              class="dsr" 
              :class="{ today: day.isToday }"
            >
              <span class="dsd">{{ day.name }}</span>
              <span :class="day.closed ? 'sc' : 'sh'">
                {{ day.time }}
              </span>
            </div>
          </div>
        </div>

        <div class="ds-card">
          <div class="ds-title">🌐 Languages</div>
          <div class="doclangs">
            <span v-for="lang in doctor.languages" :key="lang" class="lang">
              {{ lang }}
            </span>
          </div>
        </div>

        <div class="ds-card">
          <div class="ds-title">💳 Insurance &amp; Payment</div>
          <p class="dins-note">{{ doctor.paymentNote }}</p>
        </div>
      </aside>
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
  chips: ['📍 Lagos, Nigeria (Remote)', '⭐ 4.9 · 312 reviews', '🎥 Video consultations', '🌐 Eng · Igbo · Yoruba'],
  bio: [
    "Dr. Sarah Okafor is a board-certified General and Family Physician with over 14 years of clinical experience across Lagos and London. She completed her MBBS at the University of Lagos and her Fellowship at the West African College of Physicians, with a specialist focus on preventive medicine, chronic disease management, and mental health integration.",
    "Known for her warm, patient-centred approach, Dr. Okafor believes that quality healthcare should be accessible to everyone — which is why she joined Meridian Health's virtual clinic. She is fluent in English, Igbo, and Yoruba, and welcomes patients from all backgrounds."
  ],
  quote: "Quality healthcare is a right, not a privilege. Every patient deserves a doctor who truly listens.",
  expertise: ['General Medicine', 'Family Health', 'Preventive Care', 'Chronic Disease', 'Hypertension', 'Diabetes Management', 'Women\'s Health', 'Mental Wellness', 'Nutrition Guidance', 'Travel Medicine', 'Paediatric Care', 'Geriatric Health'],
  languages: ['English', 'Igbo', 'Yoruba'],
  paymentNote: 'Accepts most Nigerian HMO plans, NHIS, and self-pay. International cards accepted via secure gateway.'
};

const stats = [
  { label: 'Patients', value: '2,400+', fullLabel: 'Patients Seen' },
  { label: 'Rating', value: '4.9★', fullLabel: 'Avg. Rating' },
  { label: 'Experience', value: '14yrs', fullLabel: 'Experience' },
  { label: 'Satisfaction', value: '98%', fullLabel: 'Satisfaction' }
];

const timeline = [
  { icon: '🎓', year: '2008', degree: 'MBBS — Bachelor of Medicine & Surgery', school: 'University of Lagos, Nigeria' },
  { icon: '🏅', year: '2014', degree: 'FWACP — Fellow, West African College of Physicians', school: 'Internal Medicine & General Practice' },
  { icon: '🔬', year: '2018', degree: 'Advanced Diploma in Clinical Nutrition', school: 'University of London (Distance Learning)' },
  { icon: '💻', year: '2021', degree: 'Certificate in Digital Health & Telemedicine', school: 'University of Edinburgh (Online)' }
];

const reviews = [
  { name: 'Chidinma A.', date: 'January 2026', stars: '★★★★★', text: 'Dr. Okafor was incredibly thorough and compassionate. She listened to every concern and explained everything clearly.' },
  { name: 'James T.', date: 'December 2025', stars: '★★★★★', text: 'Finally a doctor who takes the time to actually understand your situation. The follow-up notes she sent were incredibly detailed.' },
  { name: 'Fatima I.', date: 'November 2025', stars: '★★★★★', text: 'As someone who struggles with anxiety about doctors, this virtual format was a game changer.' }
];

const schedule = [
  { name: 'Monday', time: '9:00 – 5:00 PM', isToday: true },
  { name: 'Tuesday', time: '9:00 – 5:00 PM' },
  { name: 'Wednesday', time: '9:00 – 1:00 PM' },
  { name: 'Thursday', time: '9:00 – 5:00 PM' },
  { name: 'Friday', time: '9:00 – 3:00 PM' },
  { name: 'Saturday', time: 'Unavailable', closed: true },
  { name: 'Sunday', time: 'Unavailable', closed: true }
];
</script>