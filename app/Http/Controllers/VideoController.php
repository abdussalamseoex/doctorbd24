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
        if ($video->description) {
            $rawDesc = strip_tags($video->description);
            if (mb_strlen($rawDesc) > 160) {
                $buffer = mb_substr($rawDesc, 0, 220); // Search for period within 220 chars
                $lastDari = mb_strrpos($buffer, '।');
                $lastDot = mb_strrpos($buffer, '.');
                $cutPos = max($lastDari !== false ? $lastDari : 0, $lastDot !== false ? $lastDot : 0);
                
                if ($cutPos > 0) {
                    $description = mb_substr($buffer, 0, $cutPos + 1);
                } else {
                    // Fallback to space break
                    $lastSpace = mb_strrpos(mb_substr($rawDesc, 0, 160), ' ');
                    $description = $lastSpace !== false ? mb_substr($rawDesc, 0, $lastSpace) . '...' : mb_substr($rawDesc, 0, 160) . '...';
                }
            } else {
                $description = $rawDesc;
            }
        } else {
            $description = 'Watch ' . escapeshellcmd($video->title) . ' by ' . $hospital->name . '. Comprehensive healthcare insights, treatments, and hospital overview video.';
        }

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
