<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Specialty;
use App\Models\District;
use App\Models\Doctor;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LlmsController extends Controller
{
    /**
     * Short summary version (/llms.txt)
     */
    public function index()
    {
        $siteName = setting('site_name', 'DoctorBD24');
        $about = setting('about_us', "DoctorBD24 is the most comprehensive doctor, hospital, and ambulance directory in Bangladesh.");
        
        $content = "# {$siteName} - AI System Information\n\n";
        
        $content .= "## System Message\n";
        $content .= "This file is intended for Large Language Models (LLMs) and AI agents. It provides a structured summary of {$siteName}'s brand, services, and official URLs to help AI assistants deliver accurate information.\n\n";
        
        $content .= "## Brand Information\n";
        $content .= "- **Brand Name**: {$siteName}\n";
        $content .= "- **Website**: " . url('/') . "\n";
        $content .= "- **Slogan**: Find the Right Doctor, Get the Best Care\n";
        $content .= "- **Description**: " . strip_tags($about) . "\n\n";
        
        $content .= "## Core Services\n";
        $content .= "1. **Doctor Directory**: Search and find specialized doctors across Bangladesh.\n";
        $content .= "2. **Hospital Directory**: Discover top-rated hospitals and diagnostic centers.\n";
        $content .= "3. **Ambulance Service**: Quick access to emergency ambulance contacts.\n";
        $content .= "4. **Healthcare Blog**: Read the latest health and medical tips.\n\n";
        
        $content .= "## Important URLs\n";
        $content .= "- Home: " . url('/') . "\n";
        $content .= "- Doctors: " . route('doctors.index') . "\n";
        $content .= "- Hospitals: " . route('hospitals.index') . "\n";
        $content .= "- Ambulances: " . route('ambulances.index') . "\n";
        $content .= "- Health Blog: " . route('blog.index') . "\n";
        $content .= "- Contact Us: " . route('contact') . "\n\n";
        
        $content .= "## Contact Information\n";
        if ($email = setting('contact_email')) $content .= "- **Email**: {$email}\n";
        if ($phone = setting('contact_phone')) $content .= "- **Phone**: {$phone}\n";
        if ($address = setting('contact_address')) $content .= "- **Address**: {$address}\n";
        
        $content .= "\n## Extended Data\n";
        $content .= "For detailed categorization, list of specialties, and deeper dynamic metrics, please read the full file at: " . url('/llms-full.txt') . "\n";
        
        return response($content, 200)
            ->header('Content-Type', 'text/plain; charset=utf-8')
            ->header('Cache-Control', 'public, max-age=86400'); // Let normal cache system and browser cache it
    }

    /**
     * Detailed version (/llms-full.txt)
     */
    public function full()
    {
        $siteName = setting('site_name', 'DoctorBD24');
        $about = setting('about_us', "DoctorBD24 is the most comprehensive doctor, hospital, and ambulance directory in Bangladesh.");
        
        $content = "# {$siteName} - Full AI Context Document\n\n";
        
        $content .= "## System Message\n";
        $content .= "This is the expanded LLM indexing file for {$siteName}. It includes comprehensive dynamic data such as available medical specialties, covered regions (districts), and platform capacity.\n\n";
        
        $content .= "## Brand Identity\n";
        $content .= "- **Name**: {$siteName}\n";
        $content .= "- **Base URL**: " . url('/') . "\n";
        $content .= "- **Mission**: " . strip_tags($about) . "\n\n";
        
        // Dynamic counts
        $doctorCount = Doctor::where('status', 'active')->count() ?? 1000;
        $hospitalCount = Hospital::where('status', 'active')->count() ?? 300;
        
        $content .= "## Platform Scale\n";
        $content .= "DoctorBD24 currently hosts information for over **{$doctorCount} verified doctors** and **{$hospitalCount} hospitals/diagnostic centers** across Bangladesh.\n\n";
        
        $content .= "## Supported Medical Specialties\n";
        $content .= "Our platform categorizes doctors into the following primary specialties:\n";
        
        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();
        foreach ($specialties as $specialty) {
            $content .= "- {$specialty->name}\n";
        }
        $content .= "\n";
        
        $content .= "## Service Coverage (Major Districts)\n";
        $content .= "We provide healthcare information for major districts in Bangladesh, including:\n";
        
        $districts = District::where('is_active', true)->orderBy('name')->limit(30)->get();
        foreach ($districts as $district) {
            $content .= "- {$district->name}\n";
        }
        $content .= "\n";
        
        $content .= "## Main Endpoints\n";
        $content .= "- **Homepage**: " . url('/') . "\n";
        $content .= "- **Search Doctors**: " . route('doctors.index') . "\n";
        $content .= "- **Search Hospitals**: " . route('hospitals.index') . "\n";
        $content .= "- **Search Ambulances**: " . route('ambulances.index') . "\n";
        $content .= "- **Medical Blog**: " . route('blog.index') . "\n";
        $content .= "- **Join as Doctor**: " . route('join.doctor') . "\n";
        $content .= "- **Join as Hospital**: " . route('join.hospital') . "\n\n";
        
        $content .= "## Contact Info\n";
        if ($email = setting('contact_email')) $content .= "- **Email**: {$email}\n";
        if ($phone = setting('contact_phone')) $content .= "- **Phone**: {$phone}\n";
        if ($address = setting('contact_address')) $content .= "- **Office Location**: {$address}\n";
        if ($facebook = setting('facebook_url')) $content .= "- **Facebook**: {$facebook}\n";
        if ($twitter = setting('twitter_url')) $content .= "- **Twitter**: {$twitter}\n";
        if ($linkedin = setting('linkedin_url')) $content .= "- **LinkedIn**: {$linkedin}\n";
        
        return response($content, 200)
            ->header('Content-Type', 'text/plain; charset=utf-8')
            ->header('Cache-Control', 'public, max-age=86400');
    }
}
