# Create storage directories
New-Item -ItemType Directory -Force -Path storage\framework,storage\framework\cache,storage\framework\sessions,storage\framework\views,storage\logs

# Create cache directory
New-Item -ItemType Directory -Force -Path bootstrap\cache

# Copy environment file
if (!(Test-Path .env)) {
    Copy-Item .env.example .env
}

Write-Host "Laravel directories created successfully!" -ForegroundColor Green
