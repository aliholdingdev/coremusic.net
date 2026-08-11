# WCAG 2.2 AA Accessibility Checklist

## Success Criterion 1.4.3: Contrast (Minimum)

**Level AA requirement:** 4.5:1 contrast ratio for normal text, 3:1 for large text (18px+)

```
[ ] Body text: 4.5:1 minimum
[ ] Links: 4.5:1 against background
[ ] Buttons: 4.5:1 text contrast
[ ] Placeholder text: readable (not too light)
[ ] Icons: 3:1 when meaningful
[ ] Focus indicators: 3:1 against background
```

**Tools:** WebAIM contrast checker, Chrome DevTools

---

## Success Criterion 1.4.10: Reflow

**Level AA requirement:** Content reflows at 320px width without loss of functionality

```
[ ] No horizontal scrolling at 320px
[ ] Text resizes to 200% without breaking
[ ] Two-column layout reflowed to single at mobile
[ ] Tables not overly wide (responsive or scrollable)
[ ] No fixed-width containers forcing scroll
```

---

## Success Criterion 2.5.5: Target Size (Enhanced)

**Level AAA requirement:** 44×44px (Level AA = no requirement, but 24×24px recommended)

```
[ ] Buttons: at least 24×24px (preferably 44×44px)
[ ] Links: adequate padding around text
[ ] Form inputs: 24×24px minimum (including label)
[ ] Touch-friendly spacing on mobile
```

---

## Success Criterion 2.5.8: Target Size (Minimum)

**Level AA requirement:** Interactive elements 24×24px minimum

```
[ ] Buttons: ≥ 24×24px
[ ] Links: ≥ 24×24px tappable area
[ ] Form controls: ≥ 24×24px
[ ] Icon buttons: adequate padding
[ ] Close buttons: ≥ 24×24px
```

---

## Success Criterion 3.2.4: Consistent Identification

**Level AA requirement:** Components with same function are identified consistently

```
[ ] "Next" button looks the same across pages
[ ] Icons always mean the same thing
[ ] Navigation consistent across pages
[ ] Form field labels consistent style
```

---

## Keyboard Navigation

**Level A requirement:** All functionality keyboard accessible

```
[ ] Tab key navigates through all interactive elements
[ ] Tab order logical (left-to-right, top-to-bottom)
[ ] Focus indicator visible (≥3px, contrasting)
[ ] Modals: focus trap (Tab wraps at last element)
[ ] Escape closes modals
[ ] Enter/Space activates buttons
[ ] Arrow keys for sliders/menus
```

---

## Semantic HTML

**Level A requirement:** Use semantic elements appropriately

```
[ ] Single <h1> per page
[ ] Heading hierarchy: h1 → h2 → h3 (no skipping)
[ ] <nav> for navigation
[ ] <header> for page header
[ ] <main> for main content
[ ] <article> for self-contained content
[ ] <section> for logical sections
[ ] <footer> for page footer
[ ] <label> associated with form inputs (for="id")
```

---

## ARIA Labels & Roles

**When semantic HTML insufficient:**

```
[ ] Icon buttons have aria-label
[ ] Form fields have labels (aria-label or <label>)
[ ] Modals have aria-modal="true"
[ ] Alerts have role="alert"
[ ] Live regions have aria-live="polite"
[ ] Disabled buttons have aria-disabled="true"
[ ] Required fields: aria-required="true"
[ ] Error messages linked: aria-describedby="error-id"
```

---

## Images & Icons

```
[ ] Meaningful images have alt text
[ ] Decorative images have alt="" (empty)
[ ] Icon fonts have aria-label or title
[ ] SVG icons have <title> or aria-label
[ ] Image text readable at 200% zoom
```

---

## Color

```
[ ] Not relying on color alone to convey meaning
[ ] Error messages use color + icon + text
[ ] Links identifiable not just by color
[ ] Charts have patterns in addition to colors
```

---

## Motion & Animation

```
[ ] @media (prefers-reduced-motion) for all animations
[ ] No auto-playing videos (user must click)
[ ] No flashing content (more than 3x per second)
[ ] Animations can be paused/stopped
```

---

## Forms

```
[ ] All inputs have labels (visible or aria-label)
[ ] Error messages visible and associated
[ ] Error fields marked with aria-invalid="true"
[ ] Required fields marked visually + aria-required="true"
[ ] Placeholder not used as label substitute
[ ] Autocomplete attributes present
[ ] Date inputs: clear format hints (MM/DD/YYYY)
```

---

## Testing Tools

- **Contrast:** WebAIM, Contrast Checker
- **Keyboard:** Manual Tab/Enter/Escape testing
- **Screen Reader:** NVDA (Windows), JAWS, VoiceOver (Mac)
- **Automated:** axe DevTools, Lighthouse
- **Mobile:** Test on actual devices (touch, screen reader)

---

## Pass/Fail Checklist

Once you've checked all items above, count:
- ✅ Checkmarks: **Passed items**
- ❌ Unchecked: **Failed items**

**Target:** 100% of items checked (100% WCAG 2.2 AA compliant)

If you have failed items, prioritize:
1. **Critical:** Contrast, keyboard navigation, form labels
2. **High:** Touch targets, semantic HTML, focus indicators
3. **Medium:** ARIA labels, error messaging, motion respect
