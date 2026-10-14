<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BlogPost;

class PublishScheduledBlogPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically publish scheduled blog posts whose scheduled time has arrived.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $postsToPublish = BlogPost::where('status', 'scheduled')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->get();

        $count = 0;
        foreach ($postsToPublish as $post) {
            $post->status = 'published';
            $post->save();
            $count++;
            $this->info("Published scheduled post ID {$post->id}: '{$post->title}'");
        }

        if ($count > 0) {
            $this->info("Successfully published {$count} scheduled post(s).");
        } else {
            $this->line("No scheduled posts due for publication.");
        }

        return Command::SUCCESS;
    }
}
