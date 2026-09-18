# Project: Laravel System Comprehensive Audit & Inspection

## Architecture
- Framework: Laravel 12.x (Laravel Framework 12.32.3)
- PHP: 8.2+
- Database: MySQL
- Areas:
  - Client-side (`resources/views/client/`, `resources/views/auth/`, `resources/views/layout/`)
  - Admin-side (`resources/views/admin/`, `resources/views/layout/admin_layout.blade.php`)
  - Routing & Backend Logic (`routes/web.php`, `routes/api.php`, `app/Http/Controllers/`, `app/Models/`)
  - Assets (`public/admin/`, `public/client/`, `public/uploads/`, Junction `public/public`)

## Feature Inventory
| # | Feature Area | Description | Milestone | Source | Status |
|---|--------------|-------------|-----------|--------|:------:|
| 1 | Client Layout & Views (R1) | Header, footer, nav, home, product list, product detail, cart, checkout, profile, auth | M1 | ORIGINAL_REQUEST § R1 | DONE |
| 2 | Admin Dashboard & CRUD Views (R2) | Dashboard, products, categories, orders, users, settings, tables, forms, modals, flash | M2 | ORIGINAL_REQUEST § R2 | DONE |
| 3 | UI-Backend Linkage (R3) | Form actions, route matching, controller methods, validation, undefined vars, exception safety | M3 | ORIGINAL_REQUEST § R3 | DONE |
| 4 | Audit Report Synthesis (R4) | Generate BAO_CAO_RA_SOAT_HE_THONG.md with classifications, line references, code fixes | M4 | ORIGINAL_REQUEST § R4 | DONE |
| 5 | Review & Forensic Integrity Audit | Multi-perspective review and forensic integrity audit of findings and solutions | M5 | Integrity Forensics | DONE |

## Milestones
| # | Name | Scope | Dependencies | Status |
|---|------|-------|-------------|--------|
| M1 | Client Views Inspection | Inspect resources/views/client, auth, layout, assets, responsive, forms | none | DONE |
| M2 | Admin Views Inspection | Inspect resources/views/admin, CRUD forms, tables, modals, flash, auth | none | DONE |
| M3 | UI-Backend Linkage Inspection | Inspect routes, controllers, validation, data passed to views, broken routes | none | DONE |
| M4 | Report Generation & Synthesis | Synthesize M1, M2, M3 into BAO_CAO_RA_SOAT_HE_THONG.md | M1, M2, M3 | DONE |
| M5 | Quality Review & Forensic Audit | Review accuracy, run verification checks, ensure no cheating/hallucinations | M4 | DONE |

## Official Deliverable
- Main Deliverable File: `c:\xampp\htdocs\appweb\BAO_CAO_RA_SOAT_HE_THONG.md` (1545 lines, 45 cataloged findings, 4-phase roadmap, verification guide).
- Subagent working directories: `c:\xampp\htdocs\appweb\.agents/`
