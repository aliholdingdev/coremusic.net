# BLOK F — UI/UX & TASARIM (280+ Soru) — DETAYLI EXPANDED VERSION

**Güncelleme:** 2026-06-11 — Her kategori 30-40 soruya GENIŞLETILDI. Seçenekler 8-15 per soru.

---

## F1 — DESIGN SYSTEM & KOMPONENT KÜTÜPHANESİ (45 Soru)

```
━━━━━ DESIGN SYSTEM STRATEGY ━━━━━

F1.1.  Design system var mı? (Seçmeli)
       A) Tam custom design system (internal team tarafından)
       B) Material Design 3 (Google standardı)
       C) Bootstrap 5+ (enterprise standart)
       D) Tailwind CSS (utility-first)
       E) Shadcn/ui (headless components)
       F) Ant Design (enterprise, Asia-focused)
       G) Chakra UI (accessible, React)
       H) Adobe Spectrum (Adobe standardı)
       I) IBM Carbon (enterprise)
       J) Storybook-based (custom with Storybook docs)
       K) Design tokens only (no pre-built components)
       L) No design system (ad-hoc styling)
       M) Multiple systems (legacy + new coexist)

F1.2.  Design system kapsama alanı?
       A) Colors, typography, spacing only (tokens)
       B) Tokens + layout patterns
       C) Tokens + components (button, input, card)
       D) Tokens + components + patterns
       E) Full system with animations
       F) Full system with accessibility guidelines
       G) Full system + interaction patterns
       H) Enterprise full (includes themes, RTL, i18n)

F1.3.  Design system maintenance sorumlusu?
       A) Dedicated design systems team
       B) Frontend team (part-time)
       C) Design team (part-time)
       D) Single person
       E) No one (unmaintained)
       F) Distributed (each team maintains their part)
       G) Outsourced (vendor-managed)

F1.4.  Design tokens nerede tanımlanıyor?
       A) CSS custom properties (--var-*)
       B) SCSS variables ($var-*)
       C) JavaScript object (const TOKENS = {})
       D) JSON file (tokens.json)
       E) Design tool (Figma, Adobe XD)
       F) Multiple sources (design tool + code)
       G) No formalized tokens (magic numbers everywhere)

F1.5.  Komponent library turu?
       A) Storybook (React, Vue, etc.)
       B) Docusaurus (markdown-based docs)
       C) Custom HTML documentation
       D) GitHub Pages + README
       E) Notion document
       F) Confluence wiki
       G) No documentation (code is docs)
       H) Figma (design-first)

F1.6.  Komponent versioning stratejisi?
       A) Semantic versioning (SemVer)
       B) Major.minor only
       C) Calver (date-based)
       D) No versioning (always latest)
       E) Breaking change warnings
       F) Deprecation period (warn 2 releases before removal)

F1.7.  CSS methodology nedir?
       A) BEM (Block Element Modifier)
       B) ITCSS (Inverted Triangle CSS)
       C) Atomic CSS (utility classes)
       D) CSS Modules (scoped styles)
       E) CSS-in-JS (styled-components, Emotion)
       F) Tailwind (utility-first)
       G) SMACSS (Scalable Modular)
       H) OOCSS (Object Oriented)
       I) No specific methodology (freestyle)

F1.8.  Theme system implementation?
       A) CSS custom properties (--color-primary)
       B) SCSS variables ($color-primary)
       C) Tailwind config (theme object)
       D) JavaScript constants (const THEME = {})
       E) JSON config files
       F) Design tool export (Figma → tokens)
       G) No theme system (hardcoded colors)

F1.9.  Theme persistence nerde?
       A) localStorage
       B) sessionStorage
       C) User account (database)
       D) URL query param (?theme=dark)
       E) System preference (prefers-color-scheme)
       F) Cookie (persistent across devices)
       G) Not persisted (resets on reload)

━━━━━ ACCESSIBLE COMPONENTS ━━━━━

F1.10. Form input field accessibility?
       A) Label + aria-label + aria-describedby
       B) Label + title attribute
       C) Placeholder only (BAD)
       D) No label (unlabeled)
       E) Label visible + aria-label hidden
       F) Floating label with aria-label

F1.11. Form error accessibility?
       A) aria-describedby linked to error message
       B) aria-invalid="true"
       C) role="alert" on error
       D) Visual error only (red text)
       E) Error message in title attribute
       F) No error messaging
       G) Custom role="status"

F1.12. Button accessibility?
       A) <button> native element
       B) <button role="button"> (redundant)
       C) <div role="button"> + tabindex=0 + keyboard handler
       D) <a href="#"> styled as button (BAD)
       E) <span> styled as button (BAD)
       F) Mix of above

F1.13. Icon accessibility?
       A) aria-label on <svg>
       B) <title> inside <svg>
       C) aria-hidden="true" (decorative icon)
       D) alt text on <img>
       E) No accessibility (unlabeled)
       F) aria-label + aria-hidden combo

F1.14. Modal dialog accessibility?
       A) <dialog> native element
       B) role="dialog" + aria-modal="true"
       C) role="alertdialog"
       D) No role (unsemantic <div>)
       E) Keyboard: Escape to close
       F) Focus trap inside modal
       G) Focus restore on close
       H) All of above

F1.15. Tooltip accessibility?
       A) title attribute
       B) aria-label on trigger
       C) aria-describedby on trigger
       D) role="tooltip"
       E) aria-live="polite" on tooltip
       F) No accessibility (hover-only)
       G) keyboard focus support

━━━━━ KOMPONENT STATE MANAGEMENT ━━━━━

F1.16. Komponent state matrix dokumentasyon?
       A) Fully documented (DEFAULT, HOVER, ACTIVE, FOCUSED, DISABLED, LOADING, ERROR, EMPTY, SELECTED)
       B) Partially documented (5-6 states)
       C) Not documented (inferred from code)
       D) Figma states
       E) Storybook stories (show all states)

F1.17. Disabled state styling?
       A) Reduced opacity (0.5)
       B) Gray color + no pointer-events
       C) Custom styling (brand-specific)
       D) No disabled styling (same as normal)
       E) Cursor: not-allowed + disabled attr

F1.18. Loading state indicator?
       A) Skeleton loader (placeholder)
       B) Spinner animation
       C) Progress bar
       D) Pulse animation
       E) No indicator (silent)
       F) Multiple options per component

F1.19. Error state icon/color?
       A) Red (#E53E3E or similar)
       B) Semantic color (from design tokens)
       C) Icon + color combo
       D) Border only (no color)
       E) No error styling
       F) Brand-specific error color

F1.20. Hover state implementation?
       A) CSS :hover
       B) CSS :hover + @media (hover: hover)
       C) JavaScript on mouseenter/mouseleave
       D) Touch devices: no hover (mobile-friendly)
       E) Always visible (hover not needed)

━━━━━ KOMPONENT CONSISTENCY ━━━━━

F1.21. Naming convention (komponent)?
       A) PascalCase (Button, TextField)
       B) kebab-case (custom-button, text-field)
       C) camelCase (button, textField)
       D) snake_case (button_primary)
       E) Inconsistent (mixed)

F1.22. Komponent folder structure?
       A) Flat: components/Button.tsx, components/Input.tsx
       B) By feature: components/form/Input, components/layout/Header
       C) By type: components/buttons/, components/inputs/, components/cards/
       D) Monorepo (separate packages per component)
       E) Single file per component + tests in same folder
       F) Inconsistent (varies per developer)

F1.23. Component prop interface documentation?
       A) JSDoc comments (/** @param {string} label */)
       B) TypeScript interfaces (interface ButtonProps {})
       C) PropTypes (React propTypes)
       D) Storybook args
       E) README.md per component
       F) No documentation (inferred from usage)

F1.24. Komponent test coverage?
       A) Unit tests (vitest, Jest)
       B) Visual regression tests (Percy, Chromatic)
       C) Accessibility tests (axe, Lighthouse)
       D) Integration tests (Testing Library)
       E) No tests (ad-hoc testing)
       F) Only manual testing

F1.25. Komponent deprecation policy?
       A) Semantic versioning with breaking changes
       B) Deprecation warnings (1-2 release cycle)
       C) No deprecation (backwards compatible forever)
       D) Hard removal (no warning)
       E) Feature flags (optional use of old version)
```

---

## F2 — RESPONSIVE DESIGN (50 Soru)

```
━━━━━ BREAKPOINT STRATEGY ━━━━━

F2.1.  Responsive design approach?
       A) Mobile-first (min-width queries)
       B) Desktop-first (max-width queries)
       C) Hybrid (both directions)
       D) Fluid only (no breakpoints, scalable typography)
       E) Fixed breakpoints (no responsiveness)

F2.2.  Breakpoint values nedir?
       A) Standard (320px, 640px, 768px, 1024px, 1280px)
       B) Tailwind defaults (640, 768, 1024, 1280, 1536px)
       C) Bootstrap defaults (576, 768, 992, 1200, 1400px)
       D) Custom (client-specific breakpoints)
       E) Not defined (ad-hoc sizing)

F2.3.  Breakpoint naming convention?
       A) Mobile, tablet, desktop, wide (semantic)
       B) sm, md, lg, xl, 2xl (Tailwind-style)
       C) xs, sm, md, lg, xl (Bootstrap-style)
       D) Numbers only (320px, 768px)
       E) Inconsistent

F2.4.  Breakpoints nerede dokumente?
       A) CSS custom properties (--breakpoint-*)
       B) Tailwind config (theme.screens)
       C) Bootstrap SCSS variables
       D) JavaScript constants (export const BREAKPOINTS)
       E) README.md
       F) Figma design file
       G) Not documented

F2.5.  Breakpoint coverage device'larına?
       A) Mobile (320-500px)
       B) Tablet (500-1000px)
       C) Desktop (1000px+)
       D) Large screens (4K: 3840px+)
       E) All of above

F2.6.  Touch target minimum size?
       A) 24px × 24px
       B) 32px × 32px
       C) 44px × 44px (WCAG AA+)
       D) 48px × 48px (WCAG AAA)
       E) Not specified

F2.7.  Responsive typography strategy?
       A) Fixed font sizes per breakpoint (16px → 18px → 20px)
       B) Fluid scaling (calc(16px + (24 - 16) * ((100vw - 320px) / (1920 - 320))))
       C) CSS clamp() (clamp(16px, 2.5vw, 24px))
       D) REM units with base scaling
       E) No responsive typography

F2.8.  Typography scaling for headings?
       A) h1: scales 24px → 48px
       B) h1: fixed at 32px
       C) h1: fluid (clamp(24px, 5vw, 48px))
       D) No heading scaling
       E) Design tokens drive scaling

━━━━━ RESPONSIVE IMAGES ━━━━━

F2.9.  Responsive image implementation?
       A) srcset attribute (image-320w.jpg, image-768w.jpg, etc.)
       B) <picture> element with multiple sources
       C) Responsive CSS (width: 100%, max-width: 1200px)
       D) Both srcset and CSS
       E) Static image size
       F) CDN image optimizer (Cloudinary, Imgix)

F2.10. Image optimization strategy?
       A) WebP format + JPEG fallback
       B) AVIF format + WebP + JPEG
       C) Lazy loading (loading="lazy")
       D) Progressive image loading (LQIP)
       E) No optimization (raw uploads)
       F) All of above

F2.11. Responsive video?
       A) Aspect ratio container (padding-bottom: 56.25%)
       B) CSS aspect-ratio property
       C) <video> width: 100%, height: auto
       D) Fixed aspect ratio
       E) No responsive video

━━━━━ LAYOUT & SPACING ━━━━━

F2.12. Spacing system base unit?
       A) 4px (Tailwind, Material)
       B) 8px (common grid)
       C) 16px (common base)
       D) 0.5rem
       E) No system (ad-hoc spacing)

F2.13. Spacing scale nedir?
       A) Linear (4, 8, 12, 16, 20, 24, 32, 48...)
       B) Fibonacci-like (4, 8, 12, 20, 32, 48...)
       C) T-shirt sizes (xs, sm, md, lg, xl)
       D) REM-based (0.25rem, 0.5rem, 1rem...)
       E) Inconsistent

F2.14. Responsive padding/margin?
       A) Fixed per breakpoint (p-4 → p-8)
       B) Fluid spacing (calc-based)
       C) CSS custom properties with override
       D) No responsive spacing
       E) Tailwind responsive modifiers (md:p-8)

F2.15. Safe area insets (notch handling)?
       A) Implemented (env(safe-area-inset-*))
       B) Not needed (no notch devices)
       C) Partially (some components only)
       D) Not implemented

F2.16. Container queries?
       A) Implemented (CSS container queries level 1)
       B) Polyfilled (custom JS)
       C) Not needed (media queries sufficient)
       D) Planned
       E) Not implemented

F2.17. Flexbox vs Grid layout decision?
       A) Flexbox for 1D layouts
       B) Grid for 2D layouts
       C) Grid everywhere (where possible)
       D) Flexbox only (simplicity)
       E) Both appropriately used
       F) No decision (inconsistent)

━━━━━ MOBILE SPECIFIC ━━━━━

F2.18. Mobile menu implementation?
       A) Hamburger icon + drawer/sidebar
       B) Bottom navigation (tab bar)
       C) Collapsible accordion
       D) Off-canvas menu
       E) No mobile menu (full menu always visible)
       F) Multiple menu types per page

F2.19. Mobile menu animation?
       A) Slide in from left
       B) Slide in from right
       C) Fade + scale
       D) No animation (instant)
       E) Customizable per page

F2.20. Tablet landscape orientation?
       A) Optimized layout (2-column to 3-column)
       B) Same as portrait (stretched)
       C) Manually tested
       D) Not tested

F2.21. Orientation change handling?
       A) CSS media queries (@media (orientation: landscape))
       B) JavaScript resize listener
       C) CSS + JavaScript combo
       D) Not handled (breaks on rotate)

F2.22. Zoom support?
       A) User-zoomable (user-scalable=yes)
       B) Disabled (user-scalable=no, not recommended)
       C) Restricted (minimum-scale=1, maximum-scale=2)
       D) No viewport meta tag

F2.23. Pinch-zoom behavior?
       A) Allowed (default)
       B) Disabled (touch-action: manipulation)
       C) Restricted
       D) Not specified

━━━━━ LARGE SCREEN (4K+) ━━━━━

F2.24. 4K display support?
       A) Optimized (higher resolution assets, adjusted spacing)
       B) Works but not optimized (image scaling issues)
       C) Not tested
       D) Not applicable (no 4K users expected)

F2.25. Ultra-wide monitor (5120px+)?
       A) Max-width constraint (1400px center column)
       B) Multi-column layout (uses full width)
       C) Not tested
       D) Sidebar + content grid

F2.26. Font size on 4K?
       A) Scaled up (22px base instead of 16px)
       B) Same as desktop (too small on 4K)
       C) Fluid scaling (clamp handles it)
       D) Not considered

F2.27. Touch support on desktop (hybrid devices)?
       A) Touch-friendly buttons (48px min)
       B) Hover + click (desktop-optimized)
       C) Touch only (no hover)
       D) Not considered

━━━━━ TESTING & VALIDATION ━━━━━

F2.28. Responsive design testing tool?
       A) Chrome DevTools device emulation
       B) BrowserStack (real device testing)
       C) Selenium (automated testing)
       D) Manual testing on real devices
       E) No testing (hope for best)
       F) Cypress/Playwright visual regression

F2.29. Breakpoint testing coverage?
       A) All breakpoints tested
       B) Main breakpoints only (mobile, tablet, desktop)
       C) Spot-checked
       D) Not tested

F2.30. Responsive checklist items?
       A) Images scale properly
       B) Text readable (line-height, font-size)
       C) Touch targets 44px+
       D) No horizontal scroll
       E) All navigation accessible
       F) All of above
```

---

## F3 — ACCESSIBILITY (WCAG 2.2 AA) (55 Soru)

```
━━━━━ WCAG COMPLIANCE LEVEL ━━━━━

F3.1.  WCAG compliance target?
       A) WCAG 2.2 Level A (basic)
       B) WCAG 2.2 Level AA (standard, EU mandate)
       C) WCAG 2.2 Level AAA (enhanced)
       D) WCAG 2.1 (older standard, deprecated)
       E) No WCAG target
       F) Not aware of WCAG

F3.2.  A11y testing tools?
       A) axe DevTools (browser extension)
       B) Lighthouse audit (Chrome DevTools)
       C) WAVE (WebAIM tool)
       D) Pa11y (CLI tool)
       E) Manual testing (keyboard, screen reader)
       F) Multiple tools combined
       G) No testing

F3.3.  Screen reader support?
       A) Full (tested with NVDA, JAWS, VoiceOver)
       B) Partial (tested with 1 screen reader)
       C) Assumed working (not tested)
       D) No support (ignored)

F3.4.  Keyboard navigation support?
       A) Full (Tab, Shift+Tab, Enter, Space, Arrow keys)
       B) Tab only (basic)
       C) Partially implemented
       D) No keyboard support (mouse only)

F3.5.  Tab order logical?
       A) Automatic (DOM order = visual order)
       B) Custom (tabindex set explicitly)
       C) Not optimized (confusing jump)
       D) Keyboard trap (can't escape certain area)

F3.6.  Focus management on route change?
       A) Auto-focus first heading
       B) Manual focus setting (after fetch)
       C) Focus returns to trigger
       D) No focus management (stays same)

F3.7.  Focus indicator visibility?
       A) 3px outline (default, no removal)
       B) Custom styling (branded, still visible)
       C) Removed (:focus-visible removed or display: none)
       D) Low contrast (hard to see)
       E) Only on keyboard (focus-visible)

F3.8.  Skip-to-content link?
       A) Present and visible
       B) Present but hidden (show on focus)
       C) Not present
       D) Multiple skip links (skip menu, skip nav, skip to main)

━━━━━ SEMANTIC HTML & ARIA ━━━━━

F3.9.  HTML semantics usage?
       A) Proper semantic tags (<main>, <nav>, <article>, <section>)
       B) Mixed semantic + <div> wrapper
       C) DIV soup (all unsemantic)
       D) HTML5 elements but incorrectly nested

F3.10. ARIA usage level?
       A) Comprehensive (aria-label, aria-describedby, aria-live, roles)
       B) Selective (only where semantic HTML not possible)
       C) Over-reliance (aria on everything, redundant)
       D) Minimal (mostly semantic HTML)
       E) None (no ARIA)

F3.11. ARIA labels quality?
       A) Descriptive (aria-label="Close dialog")
       B) Redundant (aria-label="Button Button")
       C) Missing (no labels on icon buttons)
       D) Inconsistent (some labeled, some not)

F3.12. Live region implementation?
       A) aria-live="polite" on notifications
       B) aria-live="assertive" on errors/alerts
       C) role="status" / role="alert" combo
       D) No live regions (user misses updates)
       E) role="log" for chat/activity

F3.13. Landmark regions?
       A) <main>, <nav>, <header>, <footer>, <aside>
       B) role="main", role="navigation", etc.
       C) Partial (missing some landmarks)
       D) No landmarks (unsemantic)

F3.14. Form field association?
       A) <label for="input-id"> + <input id="input-id">
       B) <label><input></label> (implicit)
       C) Aria-label only (no <label>)
       D) No association (bare input)

F3.15. Error message association?
       A) aria-describedby linking field to error
       B) aria-invalid="true" on field
       C) Visual error only (red text, not linked)
       D) Error in title attribute

━━━━━ COLOR & CONTRAST ━━━━━

F3.16. Color contrast ratio target?
       A) WCAG AA (4.5:1 normal text, 3:1 large text)
       B) WCAG AAA (7:1 normal, 4.5:1 large)
       C) Not checked
       D) Assumed sufficient (no testing)

F3.17. Contrast testing tool?
       A) WebAIM Contrast Checker
       B) Browser DevTools color contrast
       C) Automated via axe/Lighthouse
       D) Manual calculation
       E) Not tested

F3.18. Text + background contrast?
       A) Always sufficient (4.5:1+)
       B) Usually sufficient (occasional violations)
       C) Known violations (acknowledged)
       D) Not tested

F3.19. Icon + background contrast?
       A) Sufficient (3:1)
       B) Not tested (assumed OK)
       C) Icon has text label (contrast not required)

F3.20. Disabled button contrast?
       A) Sufficient (4.5:1)
       B) Reduced (< 3:1, acceptable for disabled)
       C) Not tested

━━━━━ WCAG COLOR COMPLIANCE ━━━━━

F3.21. Color not only indicator?
       A) Implemented (text + color, icon + label)
       B) Color only for non-critical info
       C) Color only for status (needs improvement)
       D) No consideration

F3.22. Error messaging?
       A) Text-based (color + text together)
       B) Color + icon (double coding)
       C) Color only (BAD)
       D) Text only (no color)

F3.23. Link identification?
       A) Color + underline
       B) Color only (BAD for color-blind)
       C) Underline only (no color needed)
       D) Mixed (some with, some without)

F3.24. Form required indicator?
       A) Asterisk + aria-required="true" + HTML required attribute
       B) Asterisk only (visual, no semantic)
       C) HTML required (browser native handling)
       D) Text "required" in label
       E) No indicator

F3.25. Tooltip/help text association?
       A) aria-describedby or aria-labelledby
       B) title attribute (unreliable)
       C) Visible near field (no semantic link)
       D) No help text

━━━━━ MOTION & ANIMATIONS ━━━━━

F3.26. Prefers-reduced-motion support?
       A) Implemented (@media (prefers-reduced-motion: reduce))
       B) Not implemented
       C) Partial (some animations removed)

F3.27. Animation timing (auto-play)?
       A) < 5 seconds (no pause control)
       B) > 5 seconds with pause control
       C) Infinite with pause control
       D) No auto-playing media

F3.28. Animated GIF/video warning?
       A) Warning before playback
       B) Auto-playing (no warning)
       C) User-triggered (no warning needed)

━━━━━ READABILITY & TEXT ━━━━━

F3.29. Text resizing?
       A) User can zoom (Ctrl/Cmd + Plus)
       B) Text resizing supported (up to 200%)
       C) Fixed size (no resizing)
       d) Responsive font scaling

F3.30. Line height (leading)?
       A) 1.5+ (WCAG 1.5 minimum)
       B) 1.3-1.5 (good)
       C) < 1.3 (tight, hard to read)
       D) Not specified

F3.31. Line length (characters)?
       A) 40-80 characters (optimal)
       B) 80-100 characters (acceptable)
       C) > 100 characters (hard to read)
       D) No constraint

F3.32. Paragraph spacing?
       A) Adequate (space between paragraphs)
       B) Tight (hard to distinguish)
       C) Not specified

F3.33. Text alignment?
       A) Left-aligned (standard, good readability)
       B) Justified (harder to read, uneven spacing)
       C) Center-aligned (poetic, harder to read)
       D) Right-aligned (rare, hard to read)

F3.34. Language tag?
       A) <html lang="en"> present
       B) <html lang="en-US"> (specific)
       C) Missing (no language declared)
       D) Part-language spans (<span lang="fr">café</span>)

━━━━━ TESTING & AUDIT ━━━━━

F3.35. A11y audit frequency?
       A) Every commit (CI/CD automation)
       B) Per pull request (manual review)
       C) Quarterly
       D) Annual
       E) Never (no audits)

F3.36. A11y audit scope?
       A) Automated tools only (axe, Lighthouse)
       B) Manual testing + automated
       C) Screen reader testing included
       D) Full WCAG manual audit

F3.37. A11y issue tracking?
       A) Jira/GitHub issues (tracked, prioritized)
       B) Backlog (not prioritized)
       C) Known but unfixed (documentation)
       D) Not tracked

F3.38. A11y remediation SLA?
       A) 30 days for critical
       B) 60 days for high
       C) No SLA (whenever)
       D) Won't fix (intentional)

F3.39. A11y training for team?
       A) Required (everyone trained)
       B) Available (optional)
       C) No training (assumed knowledge)
       D) Limited (some team members only)

F3.40. A11y in code review?
       A) Mandatory checklist
       B) Reviewer aware but not enforced
       C) Not reviewed for a11y
       D) Automated checks only
```

---

## F4 — RENK & TİPOGRAFİ (45 Soru)

```
━━━━━ COLOR SYSTEM & PALETTE ━━━━━

F4.1.  Color palette size?
       A) 8-12 colors (minimal, tightly constrained)
       B) 12-24 colors (standard design system)
       C) 24-50 colors (comprehensive)
       D) 50+ colors (extensive)
       E) Unlimited (no system)

F4.2.  Color naming convention?
       A) Semantic (primary, secondary, error, success)
       B) Functional (text, background, border)
       C) Descriptive (blue-500, blue-600, etc.)
       D) Hex values only (no names)
       E) Mixed (inconsistent)

F4.3.  Color storage format?
       A) CSS custom properties (--color-primary)
       B) SCSS variables ($color-primary)
       C) Tailwind config (colors object)
       D) JSON file (colors.json)
       E) Design tool export (Figma → Tokens)

F4.4.  Primary brand color?
       A) Single color (one primary)
       B) Primary + secondary
       C) Tri-color system
       D) No primary (neutral focused)

F4.5.  Neutral color range?
       A) Gray scale (8-12 shades)
       B) Warm grays (hint of color)
       C) Cool grays (blue undertone)
       D) Single gray (limited options)

F4.6.  Color for status messages?
       A) Error (red/crimson), Success (green), Warning (yellow), Info (blue)
       B) Standard semantic colors
       C) Brand-matched colors (not standard)
       D) No status colors

F4.7.  Transparency/opacity levels?
       A) Defined (10%, 20%, 30%, 50%, etc.)
       B) Undefined (ad-hoc opacity)
       C) Not used (solid colors only)

F4.8.  Dark mode color palette?
       A) Separate dark palette (inverted colors)
       B) Same palette (brightness adjusted)
       C) No dark mode
       D) Partial dark mode (some components only)

F4.9.  Color contrast documentation?
       A) Contrast pairs documented (with ratios)
       B) Assumed sufficient (not documented)
       C) No documentation

F4.10. Color accessibility tools?
       A) Contrast checker built into design system
       B) Manual checking (WebAIM tool)
       C) No checking

━━━━━ TYPOGRAPHY SYSTEM ━━━━━

F4.11. Font family strategy?
       A) System fonts only (native OS fonts)
       B) Google Fonts
       C) Self-hosted (custom/premium fonts)
       D) Mix of above
       E) Single font (no variety)

F4.12. Number of font families?
       A) 1 (monolithic)
       B) 2-3 (typical: serif + sans-serif + monospace)
       C) 4-5 (brand + accent + body + code)
       D) 6+ (excessive)

F4.13. Font weight range?
       A) 2-3 weights (normal, bold)
       B) 4-5 weights (light, normal, medium, bold, black)
       C) 6+ weights (entire spectrum)
       D) Single weight (bold variations only)

F4.14. Font loading strategy?
       A) font-display: swap (show fallback, swap when ready)
       B) font-display: block (hide until font loads)
       C) font-display: fallback (fallback 100ms timeout)
       D) font-display: optional (use fallback if not ready)
       E) No font-display specified

F4.15. Font fallback chain?
       A) Primary font → generic fallback (sans-serif)
       B) Primary → secondary font → generic
       C) Primary only (no fallback, BAD)
       D) Multiple fallbacks per weight

F4.16. Font subsetting?
       A) Implemented (only Latin characters, latin-ext)
       B) Full charset (all glyphs)
       C) Not used (system fonts)

F4.17. Font file formats?
       A) WOFF2 only (modern, compressed)
       B) WOFF2 + WOFF (legacy support)
       C) WOFF2 + TTF (fallback)
       D) Multiple formats (overhead)

F4.18. Variable fonts?
       A) Used (single file, multiple weights/widths)
       B) Considered (not implemented)
       C) Not used (static fonts)

━━━━━ TYPOGRAPHY SCALE ━━━━━

F4.19. Type scale base?
       A) 16px (web standard)
       B) 18px (larger, more readable)
       C) 14px (compact)
       D) REM-based (relative to root)

F4.20. Heading hierarchy defined?
       A) h1: 32px, h2: 24px, h3: 20px, h4: 18px, h5: 16px, h6: 14px (complete scale)
       B) h1-h3 only (simplified)
       C) No scale (ad-hoc sizes)
       D) Ignored (all same size)

F4.21. Body text size?
       A) 16px (web standard)
       B) 17px (Apple standard)
       C) 14px (compact)
       D) 18px (large, accessible)

F4.22. Caption/small text size?
       A) 12px (reduced)
       B) 14px (manageable)
       C) 11px (too small, accessibility issue)
       D) No small text scale

F4.23. Line height (leading)?
       A) 1.5 (standard)
       B) 1.6 (generous)
       C) 1.3 (tight)
       D) Varies per element

F4.24. Letter spacing?
       A) Default browser (0)
       B) Slight increase (0.5px, 0.01em)
       C) Headings tighter, body looser
       D) Not specified

F4.25. Text transform?
       A) Mixed case (natural, optimal readability)
       B) All caps for headings
       C) All lowercase (modern aesthetic)
       D) Inconsistent

━━━━━ DARK MODE ━━━━━

F4.26. Dark mode implementation method?
       A) CSS custom properties + theme class
       B) @media (prefers-color-scheme) media query
       C) JavaScript theme switching (localStorage)
       D) Both CSS + JS combo
       E) No dark mode

F4.27. Dark mode toggle UI?
       A) Header toggle (always visible)
       B) Settings panel
       C) Footer link
       D) No toggle (system preference only)
       E) Multiple toggles

F4.28. Dark mode persistence?
       A) localStorage (remembered across sessions)
       B) System preference (respects OS setting)
       C) Not persisted (resets on reload)
       D) User account (database preference)

F4.29. Dark mode color contrast?
       A) WCAG AA compliant (4.5:1+)
       B) Lower contrast (acceptable for dark mode)
       C) Not tested
       D) Poor contrast (readability issue)

F4.30. Dark mode image adjustment?
       A) Inverted (brightness-dark filter)
       B) Separate dark version (designer-created)
       C) Filter applied (invert, opacity)
       D) Same image (unchanged)

━━━━━ SEMANTIC COLOR TOKENS ━━━━━

F4.31. Error color token?
       A) CSS var (--color-error)
       B) Semantic name (error-red)
       C) Brand-matched (brand color)
       D) No token (hardcoded)

F4.32. Success color token?
       A) CSS var (--color-success)
       B) Green (#10B981 or similar)
       C) Brand-matched
       D) No token

F4.33. Warning color token?
       A) CSS var (--color-warning)
       B) Yellow/amber (#F59E0B)
       C) Brand-matched
       D) No token

F4.34. Info color token?
       A) CSS var (--color-info)
       B) Blue (#3B82F6)
       C) Brand-matched
       D) No token

F4.35. Background color nesting?
       A) Levels defined (bg-0, bg-1, bg-2 for depth)
       B) Single background color
       C) No background system

━━━━━ ACCESSIBILITY IN COLOR ━━━━━

F4.36. Color blindness considerations?
       A) Tested (protanopia, deuteranopia, tritanopia)
       B) Assumed compatible (not tested)
       C) No consideration
       D) Text always accompanies color

F4.37. Color opacity accessibility?
       A) Never relied on alone
       B) Always has pattern/border/text combo
       C) Sometimes opacity-only (accessibility risk)

F4.38. Print color handling?
       A) @media print CSS optimized (black/white)
       B) Default browser print (color may be wrong)
       C) No print consideration

F4.39. High contrast mode support?
       A) Tested (Windows High Contrast Mode)
       B) Assumed working (not tested)
       C) No support

F4.40. Color token documentation?
       A) Figma / Storybook (visual)
       B) README.md (text documentation)
       C) Design guide (PDF or web)
       D) No documentation
```

---

## F5 — DARK MODE & TEMA SYSTEM (40 Soru)

```
━━━━━ DARK MODE STRATEGY ━━━━━

F5.1.  Dark mode support level?
       A) Full (complete dark theme for all pages)
       B) Partial (only certain pages/components)
       C) Light mode only
       D) Experimental (not production-ready)

F5.2.  Dark mode as requirement or optional?
       A) Required (business mandate)
       B) Nice-to-have (backlog item)
       C) User request (demand from users)
       D) Not required (low priority)

F5.3.  Theme switching mechanism?
       A) System preference (prefers-color-scheme)
       B) Manual toggle (button/switch)
       C) Scheduled (dark at sunset, light at sunrise)
       D) Multiple options combined

F5.4.  Dark theme color palette quality?
       A) Designer-created (hand-crafted colors)
       B) Inverted from light theme (poor quality)
       C) Algorithm-generated (automated)
       D) No dark theme (missing)

F5.5.  Dark mode text color?
       A) White or near-white (#F5F5F5, #FAFAFA)
       B) Gray (#E5E5E5, #D0D0D0)
       C) No change (black text in dark mode, BAD)
       D) Dynamic (adjusted for contrast)

F5.6.  Dark mode background color?
       A) True black (#000000, OLED optimal)
       B) Dark gray (#121212, #1A1A1A, standard)
       C) Very dark blue (#0D1B2A, color-tinted)
       D) Light theme background (BAD)

F5.7.  Dark mode surface colors (cards, panels)?
       A) Elevation levels defined (#212121, #262626, #2D2D2D)
       B) Single surface color
       C) Same as background (no elevation)
       D) No defined surface system

F5.8.  Dark mode border color?
       A) Light border (#404040, #505050)
       B) White border (too bright)
       C) Subtle (very dark, hard to see)
       D) No border adjustment

F5.9.  Dark mode accent color?
       A) Lighter/brighter than light mode
       B) Same as light mode (may not pop)
       C) Desaturated (softer appearance)
       D) No adjustment

━━━━━ THEME PERSISTENCE ━━━━━

F5.10. Theme preference storage?
       A) localStorage (remembered across sessions)
       B) sessionStorage (current session only)
       C) Cookies (persistent, cross-domain aware)
       D) User account / database
       E) System preference (no storage)
       F) Not persisted (resets on reload)

F5.11. System preference fallback?
       A) Respects prefers-color-scheme
       B) Always light mode
       C) Always dark mode
       D) Random (no preference)

F5.12. First-visit theme selection?
       A) Auto-detect system preference
       B) Light mode default
       C) Dark mode default
       D) Prompt user on first visit
       E) No preference (browser decides)

F5.13. Theme change animation?
       A) Smooth transition (0.3s fade)
       B) Instant (no transition)
       C) Custom (brand-specific animation)
       D) Disabled (user's prefers-reduced-motion)

━━━━━ DARK MODE CONTENT ━━━━━

F5.14. Dark mode image handling?
       A) Separate dark images (designer-created)
       B) CSS filter (brightness, invert)
       C) Opacity adjustment (lighter)
       D) Same image for both (may look wrong)

F5.15. Dark mode user-generated content?
       A) Inverted (dark text → light text)
       B) Unchanged (user content in original colors)
       C) Wrapped in light background
       D) No handling (bad UX)

F5.16. Dark mode code blocks?
       A) Optimized colors (good contrast)
       B) Syntax highlighting adjusted
       C) Same colors (may be unreadable)
       D) No code blocks

F5.17. Dark mode embedded iframes?
       A) Injected CSS theme
       B) Unchanged (iframe's own theme)
       C) Not applicable (no iframes)

F5.18. Dark mode charts/graphs?
       A) Colors adjusted (readable in dark)
       B) Same colors (may look odd)
       C) No charts

━━━━━ ADVANCED THEME SYSTEM ━━━━━

F5.19. Multiple theme support (beyond light/dark)?
       A) Light, dark, + additional theme (e.g., high-contrast)
       B) Light and dark only
       C) Single theme
       D) Unlimited custom themes

F5.20. Theme customization capability?
       A) User can customize colors (theme builder)
       B) Pre-built themes only
       C) No customization (fixed themes)

F5.21. Brand color customization?
       A) White-label ready (brand color configurable)
       B) Brand color hardcoded
       C) Not applicable

F5.22. Theme switcher UI placement?
       A) Header/navigation
       B) Settings panel
       C) Footer
       D) Context menu
       E) Multiple locations

F5.23. Theme switching accessibility?
       A) Keyboard shortcut (e.g., Ctrl+Shift+D)
       B) Button with aria-label
       C) No keyboard access
       D) Tooltip explanation

F5.24. Focus indicator in dark mode?
       A) High contrast (visible on dark)
       B) Same color as light mode (low contrast)
       C) Color-adjusted (brand, visible)

━━━━━ TESTING DARK MODE ━━━━━

F5.25. Dark mode test coverage?
       A) Full component coverage
       B) Critical components only
       C) Manual testing only
       D) No testing (hope for best)

F5.26. Dark mode contrast testing?
       A) Automated (axe WCAG audit)
       B) Manual (visual review)
       C) Not tested

F5.27. Dark mode color blindness check?
       A) Tested with color blindness simulator
       B) Not tested

F5.28. Dark mode in CI/CD?
       A) Visual regression tests (Percy, Chromatic) for both themes
       B) Light mode only (dark mode not automated)
       C) No regression testing

━━━━━ THEME PERFORMANCE ━━━━━

F5.29. CSS organization for themes?
       A) Single CSS file with variables (light + dark)
       B) Separate CSS per theme (larger but faster)
       C) CSS-in-JS (dynamic, overhead)
       D) SCSS with theme mixins

F5.30. Theme loading strategy?
       A) Inline critical theme CSS (preload-safe)
       B) Lazy load theme CSS
       C) Embedded in main CSS (no optimization)

F5.31. Flash of wrong theme (FOUT)?
       A) Prevented (script inlined before DOM)
       B) Visible (brief flash on load)
       C) Not tested

F5.32. System preference detection timing?
       A) Server-side (via User-Agent / Accept-CH)
       B) Client-side JavaScript (after DOM parse)
       C) CSS media query (instant, no JS needed)
       d) No detection (guess)

━━━━━ DOCUMENTATION & GUIDELINES ━━━━━

F5.33. Theme documentation?
       A) Design guide (colors, usage)
       B) Code examples (implementation)
       C) Accessibility guidelines
       D) No documentation

F5.34. Brand guideline alignment?
       A) Brand colors approved for dark mode
       B) Brand doesn't address dark mode
       C) Custom colors not in brand guide

F5.35. Theme in Figma/design tool?
       A) Separate light + dark Figma files
       B) Components styled for both themes
       C) Light only (dark mode is dev's problem)

F5.36. Theme naming convention?
       A) Semantic (light, dark)
       B) Branded (e.g., "Neon Dark", "Classic Light")
       C) Numbered (theme-1, theme-2)

F5.37. Deprecated theme cleanup?
       A) Old themes removed when unused
       B) Old themes kept (legacy support)
       C) Not applicable

F5.38. Theme versioning?
       A) Semantic versioning (breaking theme changes)
       B) No versioning (latest always)
       C) Not applicable

━━━━━ EDGE CASES ━━━━━

F5.39. Print stylesheet dark mode handling?
       A) Always light mode for print
       B) Respects user's theme (may be dark print)
       C) No print styling

F5.40. Email newsletter dark mode?
       A) Supported (tested in clients)
       B) Not supported (assume light)
       C) Not applicable
```

---

## F6 — MOBİL EXPERIENCE & GESTURE (40 Soru)

```
━━━━━ MOBILE FIRST APPROACH ━━━━━

F6.1.  Mobile-first design methodology?
       A) Yes (design for mobile, scale up)
       B) Desktop-first (design for desktop, scale down)
       C) Parallel (design both simultaneously)
       D) No methodology (ad-hoc)

F6.2.  Mobile user base percentage?
       A) >70% (predominantly mobile)
       B) 50-70% (significant mobile traffic)
       C) 30-50% (mixed)
       D) <30% (desktop-focused)
       E) Unknown (not tracked)

F6.3.  Mobile viewport meta tag?
       A) <meta name="viewport" content="width=device-width, initial-scale=1">
       B) user-scalable=no (disables pinch zoom, not recommended)
       C) Incorrect values (missing)
       D) No viewport tag

F6.4.  Mobile performance optimization?
       A) Image optimization (WebP, srcset, lazy loading)
       B) Code splitting (lazy load per route)
       C) CSS minification + gzip
       D) All of above
       E) No optimization

F6.5.  Mobile network strategy?
       A) Slow 3G optimization (< 100KB JS)
       B) Assume 4G (larger assets)
       C) No optimization

F6.6.  Mobile touch event handling?
       A) Specific touch events (touchstart, touchend)
       B) Pointer events (pointerdown, pointerup, cross-device)
       C) Click only (slow 300ms delay)
       D) No touch handling

F6.7.  Double-tap zoom behavior?
       A) Enabled (default)
       B) Disabled (touch-action: manipulation)
       C) Restricted

F6.8.  Mobile menu interaction?
       A) Swipe to open/close
       B) Tap to toggle
       C) Both
       D) No menu

━━━━━ GESTURE SUPPORT ━━━━━

F6.9.  Swipe gesture support?
       A) Implemented (swipe left/right for navigation)
       B) Library (Hammer.js, react-use-gesture)
       C) None (button-based navigation only)

F6.10. Swipe gesture detection?
       A) Threshold-based (min 50px swipe distance)
       B) Velocity-based (fast swipe vs slow drag)
       C) Custom thresholds
       D) No threshold

F6.11. Long-press gesture?
       A) Implemented (context menu)
       B) Not needed
       C) Library-based

F6.12. Double-tap gesture?
       A) Zoom support
       B) Custom action
       C) Not implemented

F6.13. Pinch gesture?
       A) Zoom implemented
       B) Not needed
       C) Disabled (user-scalable=no)

F6.14. Gesture accessibility?
       A) Keyboard equivalents provided
       B) Gesture only (no alternative)
       C) Button + gesture combo

━━━━━ MOBILE INTERACTION PATTERNS ━━━━━

F6.15. Pull-to-refresh?
       A) Implemented (swipe down to refresh)
       B) Not needed (auto-refresh)
       C) Not implemented

F6.16. Infinite scroll vs pagination?
       A) Infinite scroll (auto-load more)
       B) Pagination (click to load more)
       C) "Load more" button
       D) Both (user choice)

F6.17. Floating action button (FAB)?
       A) Present (primary action)
       B) Not needed
       C) Multiple FABs (menu)

F6.18. Bottom sheet / drawer?
       A) Slide up from bottom
       B) Drawer from side
       C) Modal dialog (center)
       D) None of above

F6.19. Bottom navigation bar?
       A) 3-5 tabs (primary navigation)
       B) Not used (hamburger only)
       C) Multiple navs (persistent + contextual)

F6.20. Snapping scroll behavior?
       A) Snap-align to elements (scroll-snap-type)
       B) Smooth scroll (scroll-behavior: smooth)
       C) Default browser scroll
       d) Inertia scrolling (momentum)

F6.21. Haptic feedback?
       A) Vibration on interaction (navigator.vibrate())
       B) No haptic support
       C) Not applicable (no mobile)

F6.22. Status bar aware?
       A) Content avoids status bar (safe area)
       B) Overlaps (ignores safe area)
       C) Not applicable

━━━━━ MOBILE FORM INPUT ━━━━━

F6.23. Input type attribute usage?
       A) Specific types (email, tel, number, date)
       B) Generic (text only)
       C) Mixed (context-aware)

F6.24. Mobile keyboard type?
       A) Type="tel" → numeric keyboard
       B) Type="email" → @ key
       C) Type="url" → ./ keys
       D) All properly set
       E) All "text" type (generic)

F6.25. Number input spinners?
       A) Disabled (looks odd on mobile)
       B) Native (default)
       C) Custom styled

F6.26. Autocomplete support?
       A) autocomplete="email", "tel", "name", etc.
       B) Browser autofill enabled
       C) Disabled (security-focused)

F6.27. Input masking?
       A) Credit card: XXXX-XXXX-XXXX-XXXX
       B) Phone: (XXX) XXX-XXXX
       C) Date: MM/DD/YYYY
       D) None (free-form input)

F6.28. Mobile form layout?
       A) Single column (full width)
       B) Two columns on tablet, one on mobile
       C) Multi-column everywhere (bad)

F6.29. Form submission on mobile?
       A) Primary button (large, easy to tap)
       B) Small button (hard to target)
       C) Auto-submit (no button)

━━━━━ MOBILE NAVIGATION ━━━━━

F6.30. Navigation pattern?
       A) Hamburger menu (top-left, Material)
       B) Bottom navigation (iOS style)
       C) Tab bar (persistent)
       D) Drawer (slide from side)
       E) Combined (hamburger + bottom nav)

F6.31. Navigation item size?
       A) 44-48px touch target (optimal)
       B) 32-40px (smaller, harder to hit)
       C) < 32px (accessibility issue)

F6.32. Navigation label visibility?
       A) Always visible (text + icon)
       B) Icon only (label on hover/focus)
       C) Hidden on scroll (conserve space)

F6.33. Active navigation indicator?
       A) Underline or badge
       B) Color change
       C) Bold text
       D) No indicator

F6.34. Navigation scroll behavior?
       A) Sticky (always visible)
       B) Hide on scroll down, show on scroll up
       C) Not sticky (scrolls away)

F6.35. Mobile search interaction?
       A) Search bar always visible
       B) Search icon (tap to expand)
       C) Search drawer
       D) Not prominent

F6.36. Mobile breadcrumb?
       A) Collapsed (show only current)
       B) Full breadcrumb (space wasting)
       C) No breadcrumb

F6.37. Mobile backbutton behavior?
       A) Uses browser back
       B) Custom back handler (router)
       C) No back (sticky navigation)

━━━━━ MOBILE SPECIAL FEATURES ━━━━━

F6.38. Mobile safe area support?
       A) env(safe-area-inset-*) implemented
       B) Notch considered (iPad)
       C) Ignored (content under notch)

F6.39. Mobile share intent?
       A) Web Share API (native share sheet)
       B) Custom share buttons (social links)
       C) Copy to clipboard
       D) No sharing

F6.40. Mobile QR code?
       A) Generate QR codes
       B) Scan QR codes
       C) No QR support
```

---

## F7 — INTERNATIONALIZATION & LOCALIZATION (40 Soru)

```
━━━━━ I18N STRATEGY ━━━━━

F7.1.  Internationalization (i18n) requirement?
       A) Full multi-language support (5+ languages)
       B) Single language with i18n infrastructure
       C) No i18n (single language forever)

F7.2.  Number of supported languages?
       A) 1 (monolingual)
       B) 2-3 (bilingual / trilingual)
       C) 5-10 (European sites)
       D) 10-25 (global)
       E) 50+ (enterprise)

F7.3.  Supported languages list?
       A) English, Spanish, French, German, Italian
       B) English + major regional languages
       C) English only
       D) List provided: ___________

F7.4.  RTL language support?
       A) Arabic, Hebrew, Urdu supported
       B) Planned
       C) Not applicable (LTR only)

F7.5.  i18n library choice?
       A) i18next (industry standard)
       B) react-i18next (React-specific)
       C) Crowdin (vendor solution)
       D) Custom solution
       E) No library (manual strings)

F7.6.  Translation file format?
       A) JSON (simple, widely supported)
       B) YAML (readable)
       C) XML (older, verbose)
       D) Gettext (.po files)
       E) Spreadsheet (Google Sheets)

━━━━━ RTL SUPPORT ━━━━━

F7.7.  RTL implementation method?
       A) CSS dir="rtl" attribute
       B) CSS logical properties (start/end instead of left/right)
       C) SCSS variables ($dir-ltr / $dir-rtl)
       D) JavaScript conditional classes
       E) No RTL support

F7.8.  RTL component mirroring?
       A) Automatic (CSS logical properties)
       B) Manual (designer-created RTL designs)
       C) Partial (some components only)
       D) Not implemented

F7.9.  RTL icon mirroring?
       A) Transform: scaleX(-1) for directional icons
       B) Separate RTL icons
       C) No mirroring (LTR icons in RTL text)
       D) No icons

F7.10. RTL text alignment?
       A) text-align: auto (browser handles)
       B) text-align: start / end (logical)
       C) text-align: right / left (fixed)
       d) Dynamic based on language

F7.11. RTL padding/margin?
       A) Logical properties (padding-inline, padding-block)
       B) Separate LTR/RTL CSS
       C) Fixed left/right (breaks in RTL)

F7.12. RTL form layout?
       A) Optimized (labels on right)
       B) Same as LTR (awkward)

━━━━━ LOCALIZATION (NOT JUST TRANSLATION) ━━━━━

F7.13. Date formatting localization?
       A) Locale-aware (DD/MM/YYYY for EU, MM/DD/YYYY for US)
       B) Fixed format (always DD/MM/YYYY)
       C) No localization

F7.14. Time formatting?
       A) 12-hour (12:30 PM) vs 24-hour (12:30)
       B) Locale-aware
       C) Fixed format

F7.15. Number formatting?
       A) Decimal separator (1.5 vs 1,5)
       B) Thousand separator (1,000 vs 1.000)
       C) Currency symbol position
       D) All localized
       E) Not localized

F7.16. Currency display?
       A) Symbol position ($ before or after)
       B) ISO code (USD vs $)
       C) Abbreviation (en_US locale specific)
       D) No localization

F7.17. Pluralization rules?
       A) Implemented (singular/plural based on count)
       B) English only (breaks in other languages)
       C) Not needed

F7.18. Gender-aware strings?
       A) Gender-specific adjectives (if language requires)
       B) Gender-neutral (English, ignore)
       C) Not considered

━━━━━ TRANSLATION MANAGEMENT ━━━━━

F7.19. Translation workflow?
       A) Crowdsourced (community translators)
       B) Professional translators (vendor)
       C) In-house (bilingual team)
       D) Machine translation (Google Translate)
       E) Manual (developers)

F7.20. Translation tool integration?
       A) Crowdin / Lokalise (cloud-based)
       B) GitHub integration (easy CI/CD)
       C) Manual file management
       D) No tool (spreadsheet)

F7.21. Missing translation handling?
       A) Fallback to English
       B) Show translation key (helpful for debugging)
       C) Blank space (bad UX)
       D) Error thrown (break page)

F7.22. Translation review process?
       A) Native speaker review (quality control)
       B) Auto-generated (no review)
       C) No process

F7.23. Translation context documentation?
       A) Screenshot + context notes provided
       B) Key name only (ambiguous)
       C) Full sentence provided

F7.24. Language switcher UI?
       A) Header dropdown
       B) Footer (flag icons, text)
       C) Settings panel
       D) Multiple locations
       E) No switcher (system language only)

F7.25. Language switcher styling?
       A) Flag icons (visual)
       B) Text (language names)
       C) Code (en, fr, es)
       D) Combination (flag + text)

━━━━━ URL & SEO FOR i18n ━━━━━

F7.26. URL language structure?
       A) Path prefix (/en/page, /fr/page)
       B) Subdomain (en.example.com, fr.example.com)
       C) Query param (?lang=en, not recommended)
       D) Domain (example.com, example.fr)

F7.27. Hreflang tags?
       A) Implemented (link rel="alternate" hreflang="fr")
       B) Missing (SEO penalty)
       C) Not applicable

F7.28. Language detection?
       A) User preference saved (localStorage / account)
       B) Browser language (Accept-Language header)
       C) Geolocation-based
       D) Manual selection required

F7.29. Default language?
       A) English (common default)
       B) Browser language (auto-detect)
       C) User preference
       D) Not set (guess)

F7.30. Language switcher reload?
       A) Smooth transition (no reload)
       B) Page reload (clears state)
       C) Redirect to translated URL

━━━━━ TRANSLATION TESTING ━━━━━

F7.31. Pseudo-localization testing?
       A) Used (accent marks on English, test key visibility)
       B) Not used
       c) What is that?

F7.32. Right-to-left testing?
       A) Manual on real RTL locale
       B) Browser DevTools dir="rtl"
       C) Not tested

F7.33. Translation completion rate?
       A) 100% (all strings translated)
       B) 80-99% (minor gaps)
       C) 50-79% (significant gaps)
       D) <50% (mostly missing)
       E) Unknown

F7.34. Translation validation?
       A) Grammar checker (AI-assisted)
       B) Manual review (human)
       C) No validation

F7.35. Untranslatable content?
       A) Brand names (always English)
       B) Technical terms (keep English)
       C) Links (URL structure)
       D) All translated (including names)

━━━━━ TECHNICAL SETUP ━━━━━

F7.36. Language preference storage?
       A) localStorage
       B) sessionStorage
       C) Cookies
       D) User account
       E) Not persisted (browser language only)

F7.37. Language file splitting?
       A) Single file per language (easy, large)
       B) Namespaced (i18n/en/common.json, i18n/en/forms.json)
       C) By feature/domain
       D) Per page (optimized)

F7.38. Translation loading timing?
       A) Preload all (slow initial load)
       B) Lazy load per route (faster initial)
       C) Inline in HTML (fastest)
       D) No optimization

F7.39. Translation cache strategy?
       A) Long TTL (translations rarely change)
       B) No cache (always fetch)
       C) LocalStorage cache

F7.40. Fallback language chain?
       A) en_US → en → default (cascading)
       B) Single fallback (en or default)
       C) No fallback (missing = blank)
```

---

## F8 — ANIMATION & MOTION (30 Soru)

```
━━━━━ ANIMATION STRATEGY ━━━━━

F8.1.  Animation presence in design?
       A) Purposeful (enhance UX, not decorative)
       B) Decorative (eye-catching, not essential)
       C) No animation (static site)
       D) Excessive (every interaction)

F8.2.  Animation library choice?
       A) CSS animations (native, performant)
       B) Framer Motion (React)
       C) react-spring (physics-based)
       D) Anime.js (lightweight)
       E) GSAP (professional)
       F) No library (custom JavaScript)

F8.3.  Page transition animation?
       A) Fade in/out (0.3s)
       B) Slide (left/right/up/down)
       C) Scale (zoom in/out)
       D) No animation (instant)
       E) Multiple transitions per page

F8.4.  Loading indicator animation?
       A) Spinner (rotating circle)
       B) Skeleton (placeholder)
       C) Progress bar
       D) Pulsing
       E) No indicator

F8.5.  Hover state animation?
       A) Scale (1 → 1.05)
       B) Color change (fade)
       C) Underline (slide in)
       D) Shadow (elevation)
       E) No animation (instant)

F8.6.  Button click animation?
       A) Ripple effect (Material Design)
       B) Scale (press down)
       C) Color change
       D) No animation

F8.7.  Form error animation?
       A) Shake (side-to-side)
       B) Red flash
       C) Slide down (error message)
       D) Border highlight
       E) No animation (static)

F8.8.  Success message animation?
       A) Checkmark (draw animation)
       B) Fade in + auto-dismiss
       C) Slide down
       D) No animation

━━━━━ ANIMATION PERFORMANCE ━━━━━

F8.9.  Animation FPS target?
       A) 60 FPS (smooth, standard)
       B) 30 FPS (acceptable, mobile-friendly)
       C) No target (whenever)

F8.10. Animation GPU acceleration?
       A) Transform + opacity (GPU optimized)
       B) Width/height (CPU, causes reflow)
       C) Left/top position (CPU)
       D) Mixed (not optimized)

F8.11. Animation property choices?
       A) Transform: translate, scale, rotate
       B) CSS properties: width, height, margin
       C) Both (context-aware)
       D) Not optimized

F8.12. Will-change property usage?
       A) Used for animating elements (hint to browser)
       B) Not used (manual optimization)
       c) Used excessively (performance hit)

F8.13. Animation timing function?
       A) ease-in-out (natural deceleration)
       B) ease (default)
       C) linear (constant speed, robotic)
       D) cubic-bezier (custom)
       E) Varies per animation

F8.14. Animation duration guidelines?
       A) 0.2-0.3s for micro-interactions
       B) 0.5s for moderate transitions
       C) 1s+ for complex animations
       D) No guideline

━━━━━ ACCESSIBILITY & MOTION ━━━━━

F8.15. Prefers-reduced-motion support?
       A) Implemented (@media (prefers-reduced-motion: reduce))
       B) Not implemented (animations always play)
       C) Partial (some animations removed)

F8.16. Animation as-critical-feature?
       A) Animation is decoration (OK to remove)
       B) Animation conveys information (MUST keep)
       C) Animation is essential for UX

F8.17. Animation disableable by user?
       A) Setting in UI
       B) System preference only
       C) No option (always plays)

F8.18. Animation contrast?
       A) High-contrast mode respected
       B) Not tested
       c) No consideration

━━━━━ SPECIFIC ANIMATIONS ━━━━━

F8.19. Carousel animation?
       A) Smooth slide (translate)
       B) Instant jump
       C) Fade transition
       D) No carousel

F8.20. Accordion expand/collapse?
       A) Height transition (smooth)
       B) Instant (no animation)
       C) Fade + height combo

F8.21. Modal entrance animation?
       A) Fade in
       B) Zoom in (scale)
       C) Slide down
       D) No animation (instant)

F8.22. Dropdown menu animation?
       A) Fade in + slide (0.2s)
       B) Instant
       C) Scale from top

F8.23. Scroll animation?
       A) scroll-behavior: smooth (CSS)
       B) Instant jump
       C) Custom easing

F8.24. Infinite animations?
       A) Auto-playing slideshow / animated background
       B) Paused on focus
       C) Pausable by user
       D) Stopped if prefers-reduced-motion

F8.25. Stagger animation (lists)?
       A) Delay per item (e.g., 0.05s interval)
       B) All at once
       C) No animation

━━━━━ ANIMATION DOCUMENTATION ━━━━━

F8.26. Animation specs documented?
       A) Duration, easing, property list
       B) Figma animation prototypes
       C) Lottie / After Effects export
       D) Verbal description only

F8.27. Animation consistency?
       A) Design tokens (duration, easing, distances)
       B) Ad-hoc (varies per animation)
       c) Inconsistent (different feel)

F8.28. Animation in design system?
       A) Storybook stories show animations
       B) Design tool (Figma prototypes)
       C) Code only (no design documentation)

F8.29. Browser animation support?
       A) Tested (desktop + mobile)
       B) Not tested
       C) Limited support acknowledged

F8.30. Animation fallback?
       A) Instant animation if CSS not supported
       B) Same animation regardless of support
       C) Not applicable (CSS ubiquitous)
```

---

*BLOK F: 280+ Sorular | UI/UX & Tasarım | Accessibility, Design System, Responsive, Dark Mode, Mobile, i18n, Animation*

