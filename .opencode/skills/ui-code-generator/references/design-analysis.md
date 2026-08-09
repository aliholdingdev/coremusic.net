# Design Analysis Workflow

## Step-by-Step UI Mockup Analysis

### 1. Color Extraction (PNG → CSS Variables)

**Process:**
1. Identify all unique colors in mockup (use eyedropper tool)
2. Convert hex → CSS variables: `--color-[semantic-name]`
3. Categorize: primary, accent, neutral, success, error, warning
4. Document contrast ratios (must be ≥ 4.5:1)

**Example:**
```css
--color-primary: #9d4edd;        /* Purple, primary buttons */
--color-accent: #ff4fd8;         /* Pink, hover states */
--color-text: #ffffff;           /* White text on dark */
--color-text-secondary: rgba(255, 255, 255, 0.7);  /* 70% opacity */
```

### 2. Typography Analysis

**What to Extract:**
- Font name (AvalonMedium, System UI, etc.)
- Sizes for all text levels (h1-h6, body, caption, code)
- Font weights (400=normal, 600=semibold, 700=bold)
- Line heights (usually 1.2-1.6 for readability)

**WCAG 2.2 Requirements:**
- Minimum 16px for body text (mobile)
- Line height ≥ 1.5 for body text
- Contrast ≥ 4.5:1 for normal text, ≥ 3:1 for large text (18px+)

### 3. Spacing & Grid Analysis

**Extract from Mockup:**
- Padding (inner spacing)
- Margin (outer spacing)
- Gap between grid items
- Container max-width

**Create Spacing Scale:**
```
4px, 8px, 12px, 16px, 24px, 32px, 48px, 64px
```
Use multiples of base unit for consistency.

### 4. Layout Grid Identification

**For Responsive Grids:**
- Count columns in main layout
- Identify gap/gutter width
- Note how columns collapse at smaller screens
- Standard: 12-column grid (divisible: 1, 2, 3, 4, 6, 12)

**Layout Columns by Breakpoint:**
```
320px (mobile):   1 column
600px (mobile-w): 1-2 columns
768px (tablet):   2-3 columns
1024px (tablet-w): 3 columns
1280px (desktop):  3-4 columns
1920px (desktop-w): 3-4 columns
2560px (tv/4k):    3-4 columns
```

### 5. Component Library Extraction

**Identify All Components:**
- Buttons (sizes, states)
- Cards (layouts, overlays)
- Forms (inputs, labels, validation)
- Navigation (header, sidebar, footer)
- Typography (all heading levels)
- Special widgets (hero, modal, toast, tabs)

**State Matrix for Each:**
```
DEFAULT | HOVER | ACTIVE | FOCUSED | DISABLED | LOADING | ERROR
```

### 6. Breakpoint Transition Analysis

**For Each Component, Document:**
- What changes at each breakpoint (width, height, layout)
- When to hide/show elements
- Font size adjustments
- Spacing adjustments

**Example:**
```
Button:
  320px: width 100%, padding 12px
  768px: width auto, padding 12px 24px
  1920px: font-size 18px, padding 14px 28px
```

### 7. WCAG 2.2 AA Compliance Check

**Run Through Checklist:**
- [ ] Color contrast ≥ 4.5:1
- [ ] Touch targets ≥ 24×24px
- [ ] Focus indicator visible
- [ ] Semantic HTML possible (headings, landmarks)
- [ ] No color-only information
- [ ] Motion animations respectful of prefers-reduced-motion
- [ ] Text resizable to 200%
- [ ] Interactive elements keyboard accessible

---

## Output: Design Specification Template

```markdown
# Design Specification: [Project Name]

## Color Palette
| Semantic | Hex | CSS Variable | Usage | Contrast |
|----------|-----|--------------|-------|----------|
| Primary | #9d4edd | --color-primary | buttons, links | ✅ 6.5:1 |

## Typography
| Level | Font | Size | Weight | Line Height |
|-------|------|------|--------|-------------|
| H1 | AvalonMedium | 48px | 600 | 1.2 |

## Spacing Scale
Base unit: 8px
Scale: 4, 8, 12, 16, 24, 32, 48, 64px

## Breakpoints
| Name | Width | Columns | Font Base |
|------|-------|---------|-----------|
| Mobile | 320px | 1 | 16px |
| Mobile Wide | 600px | 2 | 16px |
| Tablet | 768px | 3 | 18px |
| ... | ... | ... | ... |

## Components (40+)
[Each component with state matrix]

## WCAG 2.2 AA Checklist
[✅/❌ for each requirement]
```
