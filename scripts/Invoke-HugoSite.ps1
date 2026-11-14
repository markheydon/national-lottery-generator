# Build or preview the Hugo documentation site via Docker/Podman (no local Hugo install required).
# Usage: .\scripts\Invoke-HugoSite.ps1 build|serve|preview

param(
    [Parameter(Position = 0)]
    [ValidateSet("build", "serve", "preview")]
    [string]$Command = "build",

    [string]$Runtime = "podman",
    [int]$ServePort = 1313,
    [int]$PreviewPort = $(if ($env:LOTTERY_HUGO_PREVIEW_PORT) { [int]$env:LOTTERY_HUGO_PREVIEW_PORT } else { 1314 })
)

$ErrorActionPreference = "Stop"

$repoRoot = Split-Path -Parent $PSScriptRoot
$rootForMount = $repoRoot -replace '\\', '/'
$siteDir = Join-Path $repoRoot "website"
$publicPath = Join-Path $siteDir "public"
$hugoImage = "ghcr.io/gohugoio/hugo:v0.169.0"

function Test-ContainerRuntime {
    param([string]$Name)
    if (-not (Get-Command $Name -ErrorAction SilentlyContinue)) {
        Write-Error "Container runtime '$Name' not found. Install Podman Desktop or Docker Desktop, or pass -Runtime docker."
    }
}

function Invoke-HugoBuild {
    param(
        [string]$BaseURL
    )

    $hugoArgs = @("build", "--minify")
    if ($BaseURL) {
        $hugoArgs += @("--baseURL", $BaseURL)
    }

    & $Runtime run --rm --entrypoint hugo `
        -v "${rootForMount}:/src:Z" `
        -w /src/website `
        $hugoImage `
        @hugoArgs
    if ($LASTEXITCODE -ne 0) {
        Write-Error "Hugo build failed (exit $LASTEXITCODE). Fix errors above before previewing."
    }
    if (-not (Test-Path (Join-Path $publicPath "index.html"))) {
        Write-Error "Hugo did not produce website/public/index.html."
    }
}

Test-ContainerRuntime -Name $Runtime

switch ($Command) {
    "build" {
        Write-Host "Building Hugo site to website/public..." -ForegroundColor Cyan
        Invoke-HugoBuild
        Write-Host "Done. Output: $publicPath" -ForegroundColor Green
    }

    "serve" {
        Write-Host "Starting Hugo dev server at http://localhost:$ServePort ..." -ForegroundColor Cyan
        & $Runtime run --rm --entrypoint hugo -p "${ServePort}:1313" `
            -v "${rootForMount}:/src:Z" `
            -w /src/website `
            $hugoImage `
            server --bind 0.0.0.0 --baseURL "http://localhost:$ServePort"
        if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
    }

    "preview" {
        Write-Host "Building Hugo site..." -ForegroundColor Cyan
        Invoke-HugoBuild -BaseURL "http://localhost:$PreviewPort/"
        Write-Host "Serving website/public at http://localhost:$PreviewPort ..." -ForegroundColor Cyan
        & $Runtime run --rm -p "${PreviewPort}:80" `
            -v "${rootForMount}/website/public:/usr/share/nginx/html:ro,Z" `
            docker.io/library/nginx:alpine
    }
}