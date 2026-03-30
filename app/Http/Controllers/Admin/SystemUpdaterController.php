<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use ZipArchive;
use Exception;

class SystemUpdaterController extends Controller
{
    public function index()
    {
        $updaterDir = storage_path('app/updater');
        if (!is_dir($updaterDir)) {
            mkdir($updaterDir, 0755, true);
        }

        $configFile = $updaterDir . '/config.json';
        $hasConfig = file_exists($configFile);
        
        $config = $hasConfig ? json_decode(file_get_contents($configFile), true) : null;

        $historyFile = $updaterDir . '/history.json';
        $history = file_exists($historyFile) ? json_decode(file_get_contents($historyFile), true) : [];

        // Check for updates if config exists
        $latestCommit = null;
        $currentCommit = $history[0]['sha'] ?? null;
        $updateAvailable = false;

        if ($hasConfig) {
            try {
                $response = Http::withToken($config['token'])
                    ->withHeaders(['User-Agent' => 'DoctorBD24-Updater'])
                    ->get("https://api.github.com/repos/{$config['repo']}/commits/main");
                
                if ($response->successful()) {
                    $latestCommit = $response->json();
                    if ($currentCommit !== $latestCommit['sha']) {
                        $updateAvailable = true;
                    }
                }
            } catch (Exception $e) {
                // handle silently
            }
        }

        return view('admin.updater.index', compact('hasConfig', 'config', 'history', 'latestCommit', 'updateAvailable', 'currentCommit'));
    }

    public function run(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            return response()->json(['success' => false, 'output' => 'Permission denied. Only Admins can run the updater.']);
        }

        $updaterDir = storage_path('app/updater');
        if (!is_dir($updaterDir)) {
            mkdir($updaterDir, 0755, true);
        }
        $configFile = $updaterDir . '/config.json';

        // Initialize Git if requested
        if ($request->has('init_git') && $request->init_git) {
            $user = trim($request->git_username);
            $token = trim($request->git_token);
            $repo = trim($request->git_repo);

            if (empty($user) || empty($token) || empty($repo)) {
                return response()->json(['success' => false, 'output' => 'Missing GitHub credentials for initialization.']);
            }

            // Remove full URLs and just get owner/repo
            $repo = str_replace(['https://github.com/', 'http://github.com/', 'github.com/', '.git'], '', $repo);

            file_put_contents($configFile, json_encode([
                'username' => $user,
                'token' => $token,
                'repo' => $repo
            ]));

            return response()->json(['success' => true, 'output' => "GitHub Api Connected Successfully! Repo: {$repo}"]);
        }
        
        // ensure execution time is extended
        set_time_limit(300);

        if (!file_exists($configFile)) {
            return response()->json(['success' => false, 'output' => "GitHub connection not configured."]);
        }
        
        $config = json_decode(file_get_contents($configFile), true);
        $outputLog = "🚀 Starting Native System Update...\n--------------------------\n";

        try {
            $outputLog .= "> Fetching latest commit details from GitHub...\n";
            $response = Http::withToken($config['token'])
                ->withHeaders(['User-Agent' => 'DoctorBD24-Updater'])
                ->get("https://api.github.com/repos/{$config['repo']}/commits/main");

            if (!$response->successful()) {
                throw new Exception("Failed to fetch commits. Check your GitHub PAT and Repo name.");
            }

            $commitData = $response->json();
            $sha = $commitData['sha'];
            $message = $commitData['commit']['message'];
            
            $historyFile = $updaterDir . '/history.json';
            $history = file_exists($historyFile) ? json_decode(file_get_contents($historyFile), true) : [];
            
            if (isset($history[0]) && $history[0]['sha'] === $sha) {
                $outputLog .= "✅ System is already up to date.\n";
                return response()->json(['success' => true, 'output' => $outputLog]);
            }

            $outputLog .= "> Downloading update bundle ({$sha})...\n";
            
            $zipResponse = Http::withToken($config['token'])
                ->withHeaders(['User-Agent' => 'DoctorBD24-Updater'])
                ->get("https://api.github.com/repos/{$config['repo']}/zipball/main");

            if (!$zipResponse->successful()) {
                throw new Exception("Failed to download ZIP file from GitHub.");
            }

            $zipPath = $updaterDir . '/update.zip';
            file_put_contents($zipPath, $zipResponse->body());

            $outputLog .= "> Extracting update & merging files...\n";
            
            $zip = new ZipArchive;
            if ($zip->open($zipPath) === TRUE) {
                // Zip from github always has a single root folder (owner-repo-hash/)
                $rootFolder = $zip->getNameIndex(0);
                
                $extractPath = base_path();
                for ($i = 1; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    // Remove root folder from path to get relative path
                    $relativePath = substr($filename, strlen($rootFolder));
                    if (empty($relativePath)) continue;

                    $targetPath = $extractPath . DIRECTORY_SEPARATOR . $relativePath;
                    
                    if (substr($filename, -1) == '/') {
                        if (!is_dir($targetPath)) {
                            mkdir($targetPath, 0755, true);
                        }
                    } else {
                        $dirname = dirname($targetPath);
                        if (!is_dir($dirname)) {
                            mkdir($dirname, 0755, true);
                        }
                        file_put_contents($targetPath, $zip->getFromIndex($i));
                    }
                }
                $zip->close();
                unlink($zipPath); // clean up downloaded zip
            } else {
                throw new Exception("Failed to open downloaded ZIP package.");
            }

            $outputLog .= "> Running Database Migrations...\n";
            Artisan::call('migrate', ['--force' => true]);
            $outputLog .= Artisan::output() . "\n";

            $outputLog .= "> Clearing Application Caches...\n";
            Artisan::call('optimize:clear');
            $outputLog .= Artisan::output() . "\n";

            // Save history
            array_unshift($history, [
                'sha' => $sha,
                'message' => $message,
                'date' => date('Y-m-d H:i:s'),
                'author' => $commitData['commit']['author']['name'] ?? 'Unknown'
            ]);
            // Keep only latest 20 updates
            $history = array_slice($history, 0, 20);
            file_put_contents($historyFile, json_encode($history));

            $outputLog .= "--------------------------\n✅ System Update Completed Successfully!";
            return response()->json(['success' => true, 'output' => $outputLog]);

        } catch (Exception $e) {
            $outputLog .= "❌ ERROR: " . $e->getMessage() . "\n";
            // Clean up zip if failed
            if (isset($zipPath) && file_exists($zipPath)) {
                unlink($zipPath);
            }
            return response()->json(['success' => false, 'output' => $outputLog]);
        }
    }
}
