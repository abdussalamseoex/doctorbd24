<?php

namespace App\Http\Controllers;

use App\Models\Hospital;
use App\Models\HospitalVideo;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function show($hospital_slug, $video_slug)
    {
        $hospital = Hospital::where('slug', $hospital_slug)
            ->where('status', 'published')
            ->firstOrFail();

        $video = HospitalVideo::where('hospital_id', $hospital->id)
            ->where('slug', $video_slug)
            ->where('is_active', true)
            ->firstOrFail();

        $title = $video->title . ' | ' . $hospital->name;
        $description = $video->description ? mb_substr(strip_tags($video->description), 0, 160) : ('Watch ' . escapeshellcmd($video->title) . ' by ' . $hospital->name . '. Comprehensive healthcare insights, treatments, and hospital overview video.');

        SEOMeta::setTitle($title);
        SEOMeta::setDescription($description);

        OpenGraph::setTitle($title)
            ->setDescription($description)
            ->setType('video.other')
            ->setUrl(url()->current());

        if ($video->thumbnail_url) {
            OpenGraph::addImage($video->thumbnail_url);
        }

        TwitterCard::setTitle($title)->setSite('@DoctorBD24');

        $relatedVideos = HospitalVideo::where('hospital_id', $hospital->id)
            ->where('id', '!=', $video->id)
            ->where('is_active', true)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('videos.show', compact('hospital', 'video', 'relatedVideos'));
    }
}
