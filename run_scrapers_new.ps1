for ($i=1; $i -le 7; $i++) {
    Write-Host "Running sitemap $i..."
    php artisan scrape:doctors --sitemap=$i
}
