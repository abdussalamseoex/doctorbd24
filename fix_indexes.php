<?php

// Fix Ambulances Index
$ambFile = __DIR__ . '/resources/views/admin/ambulances/index.blade.php';
if (file_exists($ambFile)) {
    $content = file_get_contents($ambFile);
    $content = preg_replace('/@if\(\$amb->active\).*?@endif/s', "<span class=\"px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ \$amb->status === 'published' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : (\$amb->status === 'scheduled' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300') }}\">\n                                {{ \$amb->status }}\n                            </span>", $content);
    file_put_contents($ambFile, $content);
}

// Fix Pages Index
$pagesFile = __DIR__ . '/resources/views/admin/pages/index.blade.php';
if (file_exists($pagesFile)) {
    $content = file_get_contents($pagesFile);
    $content = preg_replace('/@if\(\$page->is_active\).*?@else.*?@endif/s', "<span class=\"px-2.5 py-1 rounded-full text-xs font-medium {{ \$page->status === 'published' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : (\$page->status === 'scheduled' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300') }}\">\n                        {{ ucfirst(\$page->status) }}\n                    </span>", $content);
    file_put_contents($pagesFile, $content);
}

// Fix Seo Landing Pages Index
$seoFile = __DIR__ . '/resources/views/admin/seo-landing-pages/index.blade.php';
if (file_exists($seoFile)) {
    $content = file_get_contents($seoFile);
    $content = preg_replace('/<span.*?\$page->is_active \?.*?<\/span>/s', "<span class=\"px-2 py-1 text-[10px] font-bold uppercase rounded-full {{ \$page->status === 'published' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : (\$page->status === 'scheduled' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300') }}\">\n                            {{ \$page->status }}\n                        </span>", $content);
    file_put_contents($seoFile, $content);
}

echo "Done indexing updates.\n";
