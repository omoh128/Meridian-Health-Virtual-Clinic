<template>
  <div>
    <Navbar />
    <MobileNav />
    
     <router-view />
     <Footer />
  </div>
</template>

<script setup>
import Navbar from './components/Navbar.vue'
import MobileNav from './components/MobileNav.vue'
import Footer from './components/Footer.vue'

</script>
