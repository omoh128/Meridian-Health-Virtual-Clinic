import { createRouter, createWebHistory } from 'vue-router'

import Home from './pages/Home.vue'
import Services from './pages/Services.vue'
import Doctor from './pages/Doctor.vue'
import Book from './pages/Book.vue'
import DoctorPortal from './views/DoctorPortal.vue'  // ← swap this in
import Confirmation from './pages/mail/Confirmation.vue'
import AppointmentCompleted from './pages/mail/AppointmentCompleted.vue'
import CancelBooking from './pages/CancelBooking.vue'


const routes = [
    { path: '/',        name: 'Home',     component: Home },
    { path: '/services',name: 'Services', component: Services },
    { path: '/doctor',  name: 'Doctor',   component: Doctor },
    { path: '/book',    name: 'Book',     component: Book },

    // 🔒 Password-protected admin dashboard
    { path: '/admin', name: 'AdminDashboard', component: DoctorPortal },

    { path: '/confirmation', name: 'Confirmation', component: Confirmation },
   { path: '/completed',    name: 'Completed',    component: AppointmentCompleted },
   { path: '/cancel', name: 'CancelBooking', component: CancelBooking }

]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router
