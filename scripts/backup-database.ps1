param(
    [string]$Database = 'cslprac',
    [string]$HostName = '127.0.0.1',
    [string]$UserName = 'root',
    [string]$Password = '',
    [string]$MySqlDumpPath = 'C:\xampp\mysql\bin\mysqldump.exe'
)

$ErrorActionPreference = 'Stop'

if (-not (Test-Path -LiteralPath $MySqlDumpPath)) {
    throw "No se encontró mysqldump en: $MySqlDumpPath"
}

$projectRoot = Split-Path -Parent $PSScriptRoot
$backupDirectory = Join-Path $projectRoot 'storage\app\backups'
New-Item -ItemType Directory -Path $backupDirectory -Force | Out-Null

$date = Get-Date -Format 'yyyy-MM-dd_HH-mm-ss'
$backupFile = Join-Path $backupDirectory ("$Database`_$date.sql")
$arguments = @(
    "--host=$HostName",
    "--user=$UserName",
    '--single-transaction',
    '--routines',
    '--events',
    '--add-drop-table',
    "--result-file=$backupFile",
    $Database
)

if ($Password) {
    $env:MYSQL_PWD = $Password
}

try {
    & $MySqlDumpPath @arguments

    if ($LASTEXITCODE -ne 0) {
        throw "mysqldump terminó con código $LASTEXITCODE."
    }

    Write-Host "Respaldo creado correctamente: $backupFile"
}
finally {
    if ($Password) {
        Remove-Item Env:MYSQL_PWD -ErrorAction SilentlyContinue
    }
}
