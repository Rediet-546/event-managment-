# Complete Attendee Module Creation Script
Write-Host "🎯 Creating Complete Attendee Module Structure" -ForegroundColor Cyan
Write-Host "=============================================" -ForegroundColor Cyan

$basePath = "app/Modules/Attendee"

# ============================================
# STEP 1: CREATE ALL DIRECTORY STRUCTURE
# ============================================
Write-Host "
📁 Step 1: Creating directories..." -ForegroundColor Yellow

$directories = @(
    "app/Modules/Attendee/Config",
    "app/Modules/Attendee/Console/Commands",
    "app/Modules/Attendee/Database/Migrations",
    "app/Modules/Attendee/Database/Seeders",
    "app/Modules/Attendee/Events",
    "app/Modules/Attendee/Exports",
    "app/Modules/Attendee/Http/Controllers/Admin",
    "app/Modules/Attendee/Http/Controllers/Api",
    "app/Modules/Attendee/Http/Controllers/Front",
    "app/Modules/Attendee/Http/Middleware",
    "app/Modules/Attendee/Http/Requests/Admin",
    "app/Modules/Attendee/Http/Requests/Front",
    "app/Modules/Attendee/Models",
    "app/Modules/Attendee/Observers",
    "app/Modules/Attendee/Providers",
    "app/Modules/Attendee/Repositories",
    "app/Modules/Attendee/Resources/views/admin/layouts",
    "app/Modules/Attendee/Resources/views/admin/dashboard",
    "app/Modules/Attendee/Resources/views/admin/bookings",
    "app/Modules/Attendee/Resources/views/admin/ticket-types",
    "app/Modules/Attendee/Resources/views/admin/discounts",
    "app/Modules/Attendee/Resources/views/admin/checkins",
    "app/Modules/Attendee/Resources/views/admin/email-templates",
    "app/Modules/Attendee/Resources/views/admin/settings",
    "app/Modules/Attendee/Resources/views/admin/partials",
    "app/Modules/Attendee/Resources/views/front/bookings",
    "app/Modules/Attendee/Resources/views/front/tickets",
    "app/Modules/Attendee/Resources/views/front/account",
    "app/Modules/Attendee/Routes",
    "app/Modules/Attendee/Services",
    "app/Modules/Attendee/Services/Gateways"
)

foreach ($dir in $directories) {
    if (-not (Test-Path $dir)) {
        New-Item -Path $dir -ItemType Directory -Force | Out-Null
        Write-Host "  ✓ Created: $dir" -ForegroundColor Green
    }
}

Write-Host "
✅ Directory structure created successfully!" -ForegroundColor Green
