<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\TemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;

class TemplateController extends Controller
{
    public function hospital()
    {
        $headings = ['name', 'slug', 'type', 'phone', 'email', 'address', 'area', 'about'];
        $data = [['Example General Hospital', 'example-hospital', 'General', '01700000000', 'info@hospital.com', '123 Dhaka Road', 'Dhaka', 'Our hospital provides...']];
        return Excel::download(new TemplateExport($headings, $data), 'hospital_template.csv', ExcelFormat::CSV);
    }

    public function ambulance()
    {
        $headings = ['provider_name', 'slug', 'phone', 'address', 'area', 'available_24h'];
        $data = [['Example Ambulance Service', 'example-ambulance', '01700000000', '123 Dhaka Road', 'Dhaka', 'Yes']];
        return Excel::download(new TemplateExport($headings, $data), 'ambulance_template.csv', ExcelFormat::CSV);
    }

    public function blogPost()
    {
        $headings = ['title', 'slug', 'category', 'excerpt', 'body'];
        $data = [['How to stay healthy', 'how-to-stay-healthy', 'Health Tips', 'Quick tips for health', 'Long article content...']];
        return Excel::download(new TemplateExport($headings, $data), 'blog_post_template.csv', ExcelFormat::CSV);
    }

    public function location()
    {
        $headings = ['division', 'division_bn', 'division_slug', 'district', 'district_bn', 'district_slug', 'area', 'area_bn', 'area_slug'];
        $data = [['Dhaka', 'ঢাকা', 'dhaka', 'Dhaka', 'ঢাকা', 'dhaka-district', 'Mirpur', 'মিরপুর', 'mirpur']];
        return Excel::download(new TemplateExport($headings, $data), 'location_template.csv', ExcelFormat::CSV);
    }

    public function doctor()
    {
        $headings = ['name', 'slug', 'specialties', 'qualifications', 'designation', 'experience_years', 'phone', 'email', 'bmdc_number', 'bio'];
        $data = [['Dr. John Doe', 'dr-john-doe', 'Medicine, Cardiology', 'MBBS, MD', 'Senior Consultant', '10', '01700000000', 'john@example.com', '12345', 'A short bio...']];
        return Excel::download(new TemplateExport($headings, $data), 'doctor_template.csv', ExcelFormat::CSV);
    }
}
