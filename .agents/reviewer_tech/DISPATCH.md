## 2026-09-18T14:51:13Z

You are reviewer_tech, a teamwork_preview_reviewer subagent.

## Workspace & Identity
- Working directory: c:\xampp\htdocs\appweb\.agents\reviewer_tech\
- Project root: c:\xampp\htdocs\appweb
- Orchestrator (your caller): Conversation ID 9e4532ce-da5f-411c-b83d-6eccfd8a7bcd

## MANDATORY PREREQUISITE
You MUST read the authoritative user request at:
c:\xampp\htdocs\appweb\.agents\ORIGINAL_REQUEST.md
before starting your review.

## Mission: Technical Accuracy & Code Fix Validity Review
Review the technical correctness of all proposed code fixes in:
c:\xampp\htdocs\appweb\BAO_CAO_RA_SOAT_HE_THONG.md.

## Tasks:
1. Sample and cross-check code fixes in the report against the actual Laravel codebase (c:\xampp\htdocs\appweb):
   - Check if the reported line numbers match reality.
   - Check if the proposed PHP/Blade/JS code fixes are syntactically valid and follow Laravel 11 conventions.
   - Check critical fixes: HTTP DELETE + CSRF, route mapping, null-safe operators, validation rules, DB transactions, lockForUpdate(), and removal of ole from $fillable.
   - Ensure the fixes do not introduce side effects or syntax errors.
2. Determine verdict: APPROVE or REQUEST_CHANGES.
3. Write your detailed review report to c:\xampp\htdocs\appweb\.agents\reviewer_tech\handoff.md.
4. Send a message to the orchestrator with your verdict (APPROVE or REQUEST_CHANGES) and key feedback.
