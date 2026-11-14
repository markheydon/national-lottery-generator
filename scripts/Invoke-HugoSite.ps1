# Build or preview the Hugo documentation site via Docker/Podman (no local Hugo install required).
# Usage: .\scripts\Invoke-HugoSite.ps1 build|serve|preview [-Runtime docker|podman]

param(
    [Parameter(Position = 0)]
    [ValidateSet("build", "serve", "preview")]
    [string]$Command = "build",

    [string]$Runtime = "",
    [int]$ServePort = 1313,
    [int]$PreviewPort = $(if ($env:LOTTERY_HUGO_PREVIEW_PORT) { [int]$env:LOTTERY_HUGO_PREVIEW_PORT } else { 1314 })
)

$ErrorActionPreference = "Stop"

$repoRoot = Split-Path -Parent $PSScriptRoot
$rootForMount = $repoRoot -replace '\\', '/'
$siteDir = Join-Path $repoRoot "website"
$publicPath = Join-Path $siteDir "public"
$hugoImage = "ghcr.io/gohugoio/hugo:v0.169.0"
$nginxImage = "docker.io/library/nginx:alpine"

function Resolve-ContainerRuntime {
    if ($Runtime) {
        if (-not (Get-Command $Runtime -ErrorAction SilentlyContinue)) {
            Write-Error "Container runtime '$Runtime' not found. Install Docker or Podman, or pass -Runtime."
        }
        return
    }

    if (Get-Command docker -ErrorAction SilentlyContinue) {
        $script:Runtime = "docker"
    }
    elseif (Get-Command podman -ErrorAction SilentlyContinue) {
        $script:Runtime = "podman"
    }
    else {
        Write-Error "No container runtime found. Install Docker or Podman, or pass -Runtime."
    }
}

function Get-VolumeMount {
    param(
        [string]$HostPath,
        [string]$ContainerPath,
        [string]$Opts = ""
    )

    $hostForMount = $HostPath -replace '\\', '/'
    if ($Runtime -eq "podman") {
        if ($Opts) {
            return "${hostForMount}:${ContainerPath}:${Opts},Z"
        }
        return "${hostForMount}:${ContainerPath}:Z"
    }

    if ($Opts) {
        return "${hostForMount}:${ContainerPath}:${Opts}"
    }
    return "${hostForMount}:${ContainerPath}"
}

function Test-BindMountWorks {
    param(
        [string]$HostPath,
        [string]$WorkDir,
        [string]$TestFile
    )

  $mount = Get-VolumeMount -HostPath $HostPath -ContainerPath "/src"
    & $Runtime run --rm --entrypoint sh -v $mount -w $WorkDir $hugoImage -c "test -f `"$TestFile`"" | Out-Null
    return $LASTEXITCODE -eq 0
}

function Test-HugoExtendedAvailable {
    if (-not (Get-Command hugo -ErrorAction SilentlyContinue)) {
        return $false
    }
    $version = hugo version 2>&1
    return ($version -match "extended")
}

function Assert-PublicIndex {
    if (-not (Test-Path (Join-Path $publicPath "index.html"))) {
        Write-Error "Hugo did not produce website/public/index.html."
    }
}

function Invoke-HugoBuildBindMount {
    param([string]$BaseURL)

    $hugoArgs = @("build", "--minify")
    if ($BaseURL) {
        $hugoArgs += @("--baseURL", $BaseURL)
    }

    $mount = Get-VolumeMount -HostPath $rootForMount -ContainerPath "/src"
    & $Runtime run --rm --entrypoint hugo -v $mount -w /src/website $hugoImage @hugoArgs
    if ($LASTEXITCODE -ne 0) {
        Write-Error "Hugo build failed (exit $LASTEXITCODE)."
    }
}

function Invoke-HugoBuildLocal {
    param([string]$BaseURL)

    $hugoArgs = @("build", "--minify")
    if ($BaseURL) {
        $hugoArgs += @("--baseURL", $BaseURL)
    }

    Push-Location $siteDir
    try {
        & hugo @hugoArgs
        if ($LASTEXITCODE -ne 0) {
            Write-Error "Hugo build failed (exit $LASTEXITCODE)."
        }
    }
    finally {
        Pop-Location
    }
}

function Invoke-HugoBuildStreamed {
    param([string]$BaseURL)

    $containerName = "lottery-hugo-build-$PID"
    $containerCmd = "cd /site && hugo build --minify"
    $dockerEnv = @("-e", "HUGO_ENABLEGITINFO=false")

    if ($BaseURL) {
        $dockerEnv += @("-e", "HUGO_BASE_URL=$BaseURL")
        $containerCmd = 'cd /site && hugo build --minify --baseURL "$HUGO_BASE_URL"'
    }

    Write-Host "note: using docker cp build (bind mounts unavailable on this Docker host)" -ForegroundColor Yellow

    if (Test-Path $publicPath) {
        Remove-Item -Recurse -Force $publicPath
    }

    & $Runtime create @dockerEnv --user 0 --name $containerName -w /site --entrypoint sh $hugoImage -c $containerCmd | Out-Null
  if ($LASTEXITCODE -ne 0) {
        Write-Error "Failed to create Hugo build container."
    }

    & $Runtime cp "$siteDir/." "${containerName}:/site" | Out-Null
    if ($LASTEXITCODE -ne 0) {
        & $Runtime rm -f $containerName 2>$null
        Write-Error "Failed to copy site into Hugo build container."
    }

    & $Runtime start -a $containerName
    if ($LASTEXITCODE -ne 0) {
        & $Runtime rm -f $containerName 2>$null
        Write-Error "Hugo build failed inside container."
    }

    New-Item -ItemType Directory -Force -Path $publicPath | Out-Null
    & $Runtime cp "${containerName}:/site/public/." $publicPath | Out-Null
    & $Runtime rm -f $containerName 2>$null | Out-Null

    if ($LASTEXITCODE -ne 0) {
        Write-Error "Failed to copy Hugo output from container."
    }
}

function Invoke-HugoBuild {
    param([string]$BaseURL)

    if (Test-BindMountWorks -HostPath $rootForMount -WorkDir "/src/website" -TestFile "hugo.yaml") {
        Invoke-HugoBuildBindMount -BaseURL $BaseURL
    }
    elseif (Test-HugoExtendedAvailable) {
        Invoke-HugoBuildLocal -BaseURL $BaseURL
    }
    else {
        Invoke-HugoBuildStreamed -BaseURL $BaseURL
    }

    Assert-PublicIndex
}

function Start-StaticPreview {
    $publicMount = Get-VolumeMount -HostPath "$rootForMount/website/public" -ContainerPath "/usr/share/nginx/html" -Opts "ro"

    if (Test-BindMountWorks -HostPath $publicPath -WorkDir "/usr/share/nginx/html" -TestFile "index.html") {
        & $Runtime run --rm -p "${PreviewPort}:80" -v $publicMount $nginxImage
        return
    }

    $containerName = "lottery-hugo-preview-$PID"
    Write-Host "note: serving preview via docker cp (bind mounts unavailable on this Docker host)" -ForegroundColor Yellow

    & $Runtime create --name $containerName -p "${PreviewPort}:80" --entrypoint sh $nginxImage -c "exec nginx -g `"daemon off;`"" | Out-Null
    & $Runtime cp "$publicPath/." "${containerName}:/usr/share/nginx/html" | Out-Null

    & $Runtime start -a $containerName
    if ($LASTEXITCODE -ne 0) {
        & $Runtime rm -f $containerName 2>$null
        exit $LASTEXITCODE
    }

    & $Runtime rm -f $containerName 2>$null | Out-Null
}

function Start-HugoServe {
    $mount = Get-VolumeMount -HostPath $rootForMount -ContainerPath "/src"

    if (Test-BindMountWorks -HostPath $rootForMount -WorkDir "/src/website" -TestFile "hugo.yaml") {
        & $Runtime run --rm --entrypoint hugo -p "${ServePort}:1313" -v $mount -w /src/website $hugoImage `
            server --bind 0.0.0.0 --baseURL "http://localhost:$ServePort"
        if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
        return
    }

    if (Test-HugoExtendedAvailable) {
        Push-Location $siteDir
        try {
            & hugo server --bind 0.0.0.0 --baseURL "http://localhost:$ServePort"
            if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
        }
        finally {
            Pop-Location
        }
        return
    }

    Write-Error @"
Cannot start Hugo live reload in this environment.

Bind mounts from the dev container workspace are not visible to the host Docker daemon
(Docker-outside-of-Docker). Install Hugo Extended locally, or use:

  .\scripts\Invoke-HugoSite.ps1 preview

for a static build and browser preview.
"@
}

Resolve-ContainerRuntime

switch ($Command) {
    "build" {
        Write-Host "Building Hugo site to website/public..." -ForegroundColor Cyan
        Invoke-HugoBuild
        Write-Host "Done. Output: $publicPath" -ForegroundColor Green
    }

    "serve" {
        Write-Host "Starting Hugo dev server at http://localhost:$ServePort ..." -ForegroundColor Cyan
        Start-HugoServe
    }

    "preview" {
        Write-Host "Building Hugo site..." -ForegroundColor Cyan
        Invoke-HugoBuild -BaseURL "http://localhost:$PreviewPort/"
        Write-Host "Serving website/public at http://localhost:$PreviewPort ..." -ForegroundColor Cyan
        Start-StaticPreview
    }
}