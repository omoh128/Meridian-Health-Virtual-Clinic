Meridian Health — Virtual Clinic
Meridian Health is a high-performance, secure virtual healthcare platform. It provides a seamless bridge between patients and licensed practitioners, featuring a custom-designed interface built for trust, clarity, and ease of use.

Core Features
Secure Video Consultations: End-to-end encrypted sessions with licensed doctors.

Intuitive Booking: A streamlined 4-step process: Service -> Time -> Confirmation -> Session.



Patient Dashboard: Real-time "Upcoming Appointments" tracking and session management.

Digital Follow-ups: Comprehensive post-session documentation for diagnoses and prescriptions.

🛠️ Technical Architecture
Backend: Laravel 12.52.0

Frontend: Custom Vue.js 3 implementation

Asset Pipeline: Vite (for optimized CSS and JS delivery)

Styling: Custom CSS utilizing a premium typography pairing:

Serif: Cormorant Garamond (for headings and clinical elegance)

Sans-Serif: DM Sans (for UI clarity and readability)

📥 Local Installation
Clone the Repository:

Bash

git clone https://github.com/omomohagiogu/meridian.git
cd meridian
Install Dependencies:

Bash

composer install
npm install
Environment Setup:

Bash

cp .env.example .env
php artisan key:generate
Note: Configure your database in .env (compatible with XAMPP/MySQL).

Database Migration:

Bash

php artisan migrate --seed
Compile & Run:
To resolve the ViteManifestNotFoundException and view the custom styles:

Bash

npm run dev
In a separate terminal, serve the app:

Bash

php artisan serve