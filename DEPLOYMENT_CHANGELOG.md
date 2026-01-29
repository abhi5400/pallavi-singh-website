# Production Deployment Changelog

**Date:** January 29, 2025  
**Scope:** Invisible fixes only — no UI, layout, colors, fonts, or content wording changed.

---

## 1. HTML & Accessibility

- **Duplicate ID removed (index.html):** Hero section had both `id="hero"` and `id="main-content"`. Kept `id="hero"` only and added `aria-label="Hero"`.
- **Semantic main landmark:** Wrapped primary content (quotes section through blog section) in `<main id="main-content">` so skip link and screen readers have a single main region.
- **Skip link target:** Changed skip link from `#hero` to `#main-content` so “Skip to main content” lands on the main region.
- **Join modal ARIA:** Added `role="dialog"`, `aria-modal="true"`, `aria-labelledby="join-modal-title"`, `aria-describedby="join-modal-desc"` to the join-form modal; added `id="join-modal-title"` and `id="join-modal-desc"` to heading and description; close button has `aria-label="Close registration form"`; overlay has `aria-hidden="true"`.
- **Modal focus/ESC:** Join modal already had focus trap and ESC-to-close in `join-form.js`; no code change.

---

## 2. SEO (Invisible Only)

- **Canonical domain:** Replaced `pallavisinghoffical.com` with `pallavisingofficial.com` everywhere (canonical, OG, Twitter, sitemap, robots, schema, article `og:url` and JSON-LD).
- **Favicon & OG image:** Replaced non-existent `assets/images/pallavi-logo.png` with `assets/images/Pallavi Singh official.png` (relative) and with full URL `https://pallavisingofficial.com/assets/images/Pallavi%20Singh%20official.png` for OG/Twitter where needed.
- **Sitemap & robots:** `sitemap.xml` and `robots.txt` now use `https://pallavisingofficial.com/sitemap.xml`.
- **Structured data:** Index Person schema `url` and `image` updated to `pallavisingofficial.com` and URL-encoded image path; article-power-of-storytelling BlogPosting `author.url`, `mainEntityOfPage.@id` and all article `og:url` values updated to `pallavisingofficial.com`.
- **H1:** Single H1 per page retained; no tag changes.

---

## 3. AEO / Schema

- **Person schema (index):** Valid; `url` and `image` use correct domain and absolute image URL.
- **Article schemas:** Article `og:url` and JSON-LD URLs aligned to `pallavisingofficial.com`; no new visible FAQ or content blocks.

---

## 4. Forms & Backend (PHP)

- **process_contact.php:** Removed `$response['debug']` and `$response['trace']` from JSON output on exception; only generic message returned. Added header-injection check: reject if any of name/email/subject/message contain `\r` or `\n`.
- **process_join.php:** Added header-injection check: reject if any text field contains `\r` or `\n`. Debug output was already commented out.
- **Honeypot:** All processors (contact, join, newsletter, waitlist) already use honeypot; no change.
- **Sanitization:** All use `Database::sanitizeInput()` and validation; no change.

---

## 5. Security

- **.htaccess:** HTTPS redirect left commented with note: “Enforce HTTPS when SSL is active (uncomment after certificate is installed).”
- **robots.txt:** Already disallows `/admin/`, `/config/`, `/data/`, `test_`, `debug_`, and `*.php`; sitemap URL updated; no other change.
- **No sensitive paths indexed:** Disallow rules unchanged; no new exposure.

---

## 6. Performance

- **Lazy loading:** Added `loading="lazy"` to the first blog card image on index (image 1.jpg) that was missing it. Other below-fold images already had `loading="lazy"` where applicable.
- **Critical JS:** No scripts deferred; behavior unchanged.

---

## 7. Links & Navigation

- **Placeholder `#` links:** All footer/body social links that used `href="#"` now use either:
  - Real URLs: Facebook `https://www.facebook.com/lifecoachandstorytellerpallavi`, Instagram `https://www.instagram.com/lifecoachandstorytellerpallavi/`, or
  - Safe no-op: `href="javascript:void(0)"` with `aria-label="… (coming soon)"` and/or `title="Coming soon"` for LinkedIn and YouTube where no URL is set.
- **Connect page:** LinkedIn and YouTube set to `javascript:void(0)` with aria-labels; Instagram and Facebook already had real URLs; added `rel="noopener"` where missing.
- **Mentorship footer:** “Privacy Policy” and “Terms of Service” changed from `href="#"` to `privacy-policy.html` and `terms-and-conditions.html`.
- **Copyright:** Connect and mentorship footers updated from 2024 to 2025 for consistency with index.
- **External links:** `target="_blank"` social links given `rel="noopener"` where missing.

---

## 8. Error Handling

- **404:** Custom 404 page and `.htaccess` `ErrorDocument 404 /404.html` already in place; no change.
- **Server errors:** PHP no longer returns stack trace or debug strings to the client; only generic messages.

---

## Files Modified (Summary)

| File / group | Changes |
|--------------|--------|
| **index.html** | Skip link, hero id, `<main>`, modal ARIA, favicon/OG/schema, lazy load, domain |
| **about.html** | Canonical, favicon, OG/twitter image (full URL), domain, social links |
| **connect.html** | Canonical, domain (mailto + URLs), social placeholders + aria, copyright |
| **blog.html** | Canonical, favicon, fallback image path, social links + rel/aria |
| **mentorship.html** | Favicon, footer Privacy/Terms links, copyright, social links |
| **wisdom-vault.html** | Social links |
| **coaching-mentoring.html** | Favicon, two footer social blocks |
| **habit-mastery.html** | Favicon, social links |
| **storytelling.html, public-speaking.html, relationship-mentoring.html, overcome-anxiety.html** | Social links |
| **collaborate.html, story-tree-community.html** | Social links |
| **404.html, terms-and-conditions.html, privacy-policy.html** | Favicon, canonical, domain |
| **welcome.html, thank_you.php** | Favicon path |
| **story-tree.html** | Favicon, OG/twitter image |
| **article-*.html (8 files)** | Social links; article-power-of-storytelling + og:url/schema; all articles og:url domain |
| **sitemap.xml** | Domain → pallavisingofficial.com |
| **robots.txt** | Sitemap URL |
| **.htaccess** | Comment clarification for HTTPS |
| **process_contact.php** | No debug/trace in response; header-injection check |
| **process_join.php** | Header-injection check |
| **blogs.js** | Fallback image path |

---

## Production Checklist (Mental Pass)

- [x] Unique IDs (hero/main fixed; no duplicate IDs).
- [x] Skip link targets valid `#main-content`.
- [x] Modal has role, aria, focus trap, ESC close.
- [x] Canonical and OG/Twitter use correct domain and existing image.
- [x] Sitemap and robots reference correct domain; admin/config/data blocked.
- [x] No debug or stack trace in API responses; generic error messages only.
- [x] Honeypot and header-injection checks on contact and join.
- [x] No `href="#"` for social; real URLs or `javascript:void(0)` with aria.
- [x] 404 configured; HTTPS rule present but commented until SSL is on.
- [x] Lazy loading on previously missing below-fold image.

**Verdict:** Safe to deploy. Enable HTTPS redirect in `.htaccess` after SSL is active. Confirm production domain is `pallavisingofficial.com` (or adjust domain strings if different).
