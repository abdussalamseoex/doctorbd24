<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SystemUpdaterController extends Controller
{
    public function index()
    {
        $hasGit = is_dir(base_path('.git'));
        
        $currentCommit = null;
        $branch = null;
        if ($hasGit) {
            $currentCommit = trim(shell_exec('git log -1 --format="%h - %s (%ci)" 2>&1'));
            $branch = trim(shell_exec('git rev-parse --abbrev-ref HEAD 2>&1'));
            if (empty($branch) || str_contains($branch, 'fatal') || str_contains($branch, 'not found')) {
                $hasGit = false;
            }
        }

        return view('admin.updater.index', compact('hasGit', 'currentCommit', 'branch'));
    }

    public function run(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            return response()->json(['success' => false, 'output' => 'Permission denied. Only Admins can run the updater.']);
        }

        // Initialize Git if requested
        if ($request->has('init_git') && $request->init_git) {
            $base = base_path();
            $user = trim($request->git_username);
            $token = trim($request->git_token);
            $repo = trim($request->git_repo); // e.g., github.com/user/repo.git

            if (empty($user) || empty($token) || empty($repo)) {
                return response()->json(['success' => false, 'output' => 'Missing GitHub credentials for initialization.']);
            }

            // Remove protocol if user pasted https://
            $repo = str_replace(['https://', 'http://'], '', $repo);
            $remoteUrl = "https://{$user}:{$token}@{$repo}";

            $outputLog = "Initializing Git Repository...\n";
            $cmdLine = [
                "cd {$base} && git init 2>&1",
                "cd {$base} && " . str_replace($token, 'HIDDEN_TOKEN', "git remote add origin https://{$user}:{$token}@{$repo} 2>&1") . " -- (actual command executed with token)",
                "cd {$base} && git remote add origin {$remoteUrl} 2>&1",
                "cd {$base} && git fetch origin 2>&1",
                "cd {$base} && git reset --hard origin/main 2>&1",
            ];

            foreach ($cmdLine as $i => $cmd) {
                if ($i == 1) { 
                    $outputLog .= "\n> " . $cmd . "\n";
                    continue; 
                } // just log
                if ($i == 2) { 
                    shell_exec($cmd); 
                    continue; 
                } // execute secretly
                
                $outputLog .= "\n> $cmd\n";
                $res = shell_exec($cmd);
                $outputLog .= $res . "\n";
            }

            return response()->json(['success' => true, 'output' => $outputLog]);
        }
        
        // ensure execution time is extended
        set_time_limit(300);

        $outputLog = "";
        $base = base_path();

        $commands = [
            'echo "🚀 Starting System Update..."',
            'echo "--------------------------"',
            "cd {$base} && git fetch origin 2>&1",
            "cd {$base} && git pull origin main 2>&1",
            "cd {$base} && composer install --no-dev --optimize-autoloader 2>&1",
            "cd {$base} && php artisan migrate --force 2>&1",
            "cd {$base} && php artisan optimize:clear 2>&1",
            'echo "--------------------------"',
            'echo "✅ System Update Completed Successfully!"',
        ];

        foreach ($commands as $cmd) {
            $outputLog .= "\n> $cmd\n";
            $res = shell_exec($cmd);
            $outputLog .= $res . "\n";
        }

        return response()->json(['success' => true, 'output' => $outputLog]);
    }
}
