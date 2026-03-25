<template>
  <footer>
    <div class="footer-grid">
      <div class="fb-brand">
        <router-link to="/" class="flogo">
          <div class="fmark">🏥</div>
          <span class="fname">Meridian Health</span>
        </router-link>
        <p>Quality virtual healthcare, accessible to everyone. Licensed practitioners, encrypted sessions, personalised care for every patient.</p>
      </div>

      <div class="fcol">
        <h4>Pages</h4>
        <ul>
          <li><router-link to="/">Home</router-link></li>
          <li><router-link to="/services">Services</router-link></li>
          <li><router-link to="/book">Book Appointment</router-link></li>
          <li><router-link to="/doctor">Doctor Profile</router-link></li>
        </ul>
      </div>

      <div class="fcol">
        <h4>Services</h4>
        <ul>
          <li v-for="service in services" :key="service">
            <router-link to="/book">{{ service }}</router-link>
          </li>
        </ul>
      </div>

      <div class="fcol">
        <h4>Contact</h4>
        <ul>
          <li><a href="mailto:hello@meridianhealth.ng">hello@meridianhealth.ng</a></li>
          <li><a href="tel:+2348000000000">+234 800 000 0000</a></li>
          <li>Lagos, Nigeria</li>
          <li><router-link to="/privacy">Privacy Policy</router-link></li>
          <li><router-link to="/terms">Terms of Service</router-link></li>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <span class="fcopy">© {{ new Date().getFullYear() }} Meridian Health. All rights reserved.</span>
      <div class="flinks">
        <router-link to="/privacy">Privacy</router-link>
        <router-link to="/terms">Terms</router-link>
        <router-link to="/cookies">Cookies</router-link>
      </div>
    </div>
  </footer>
</template>

<script setup>
const services = [
  'Initial Consultation',
  'Follow-Up Session',
  'Mental Wellness',
  'Nutrition Consult'
];
</script>