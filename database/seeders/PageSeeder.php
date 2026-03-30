<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Page::updateOrCreate(['slug' => 'about-us'], [
            'title' => 'About Us',
            'content' => '<p>Your trusted digital healthcare directory in Bangladesh. We aim to bridge the gap between patients and healthcare providers by offering a comprehensive, easy-to-use platform.</p>
                          <h2>Our Mission</h2>
                          <p>Finding the right doctor or healthcare facility in Bangladesh can often be an exhausting and time-consuming process. DoctorBD24 was created with a singular mission: to simplify healthcare access for every citizen.</p>
                          <p>We provide a centralized portal where users can search for specialist doctors, top-tier hospitals, and emergency ambulance services across all districts of Bangladesh. Whether you need a regular checkup or specialized emergency care, the right information is just a click away.</p>
                          <p>By empowering patients with reliable and up-to-date information, we envision a healthier, more connected Bangladesh.</p>',
            'is_active' => true,
        ]);

        Page::updateOrCreate(['slug' => 'privacy-policy'], [
            'title' => 'Privacy Policy',
            'content' => '<h2>1. Information We Collect</h2>
                          <p>At DoctorBD24, we collect basic information required to provide you with the best experience. This includes personal information provided during registration (like your name, email, and phone number) and usage data to help us improve our platform.</p>
                          <h2>2. How We Use Your Information</h2>
                          <p>The information we collect is primarily used to:</p>
                          <ul>
                              <li>Maintain and improve our Directory services.</li>
                              <li>Connect patients with doctors and hospitals.</li>
                              <li>Respond to your inquiries via the Contact Form.</li>
                              <li>Send periodic updates if you have opted in.</li>
                          </ul>
                          <h2>3. Data Security</h2>
                          <p>We are highly committed to securing your data. We implement adequate security measures to prevent unauthorized access, disclosure, or destruction of your personal information. However, no internet-based service can be 100% secure.</p>
                          <h2>4. Third-Party Links</h2>
                          <p>Our website may contain links to external hospitals or individual doctor websites. Please note that we do not have control over these external sites and are not responsible for their privacy practices.</p>
                          <h2>5. Changes to This Policy</h2>
                          <p>We may update this Privacy Policy from time to time. Any changes will be reflected on this page with an updated revision date. We encourage you to review this policy periodically.</p>',
            'is_active' => true,
        ]);

        Page::updateOrCreate(['slug' => 'terms-and-conditions'], [
            'title' => 'Terms and Conditions',
            'content' => '<h2>1. Acceptance of Terms</h2>
                          <p>By accessing or using DoctorBD24, you agree to comply with and be bound by these Terms and Conditions. If you do not agree with any part of these terms, you may not use our platform.</p>
                          <h2>2. Service Description</h2>
                          <p>DoctorBD24 is an online directory that aggregates information regarding doctors, hospitals, and ambulance services across Bangladesh. We do not provide medical advice or direct healthcare services. Always consult with a qualified medical professional for medical emergencies.</p>
                          <h2>3. User Responsibilities</h2>
                          <p>You agree to use this platform responsibly and not to engage in any conduct that restricts or inhibits anyone else\'s use or enjoyment of the site. Making false appointments, posting abusive reviews, or utilizing automated scrapers to extract our data is strictly prohibited.</p>
                          <h2>4. Information Accuracy</h2>
                          <p>While we strive to keep all doctor and hospital information up-to-date, healthcare schedules and locations change frequently. DoctorBD24 is not liable for any discrepancies in the chamber times, contact numbers, or visiting fees listed on the site.</p>
                          <h2>5. Reviews and Ratings</h2>
                          <p>Users may leave reviews for doctors and hospitals. All reviews are subject to moderation. We reserve the right to remove any review that is deemed offensive, factually incorrect, or spam-oriented without prior notice.</p>
                          <h2>6. Limitation of Liability</h2>
                          <p>In no event shall DoctorBD24 or its administrators be liable for any direct, indirect, incidental, or consequential damages arising out of the use or inability to use our platform or reliance on the information provided herein.</p>',
            'is_active' => true,
        ]);
    }
}
