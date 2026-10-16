# Oto Yikama - tum servisleri baslat (sag tik > PowerShell ile calistir)
$ErrorActionPreference = 'Continue'
$root = Split-Path -Parent $MyInvocation.MyCommand.Path

# 1. MySQL (XAMPP MariaDB) - calismiyorsa baslat
$p = & 'D:\xampp\mysql\bin\mysqladmin.exe' -u root ping 2>&1
if ($p -notmatch 'alive') {
    Write-Output 'MySQL baslatiliyor...'
    Start-Process -FilePath 'D:\xampp\mysql\bin\mysqld.exe' -ArgumentList '--defaults-file=D:\xampp\mysql\bin\my.ini'
    for ($i = 0; $i -lt 30; $i++) {
        $p = & 'D:\xampp\mysql\bin\mysqladmin.exe' -u root ping 2>&1
        if ($p -match 'alive') { break }
        Start-Sleep -Seconds 2
    }
}
Write-Output "MySQL: $p"

# 2. Backend (Spring Boot API :8080) - yeni pencerede
Start-Process powershell -ArgumentList '-NoExit', '-Command', "& 'C:\Users\acrom\tools\jdk-17\bin\java.exe' -jar '$root\backend\target\oto-yikama-api-0.0.1.jar'"

# 3. Frontend (React :5173) - yeni pencerede
Start-Process powershell -ArgumentList '-NoExit', '-Command', "`$env:Path += ';C:\Users\acrom\tools\node-v24.22.0-win-x64'; cd '$root\frontend'; npm run dev"

Write-Output 'Tamam. API: http://localhost:8080 - Arayuz: http://localhost:5173 (admin / admin123)'
