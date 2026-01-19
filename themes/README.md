# HyggeStack Themes

Three cozy theme presets designed for comfortable reading.

## Available Themes

### Nordic Minimal
**Clean, light, and airy**

- Background: Soft white (#fafafa)
- Text: Deep charcoal (#2c3e50)
- Accent: Muted slate (#34495e)
- **Best for**: Daytime reading, productivity

### Warm Coffeehouse
**Cozy and inviting**

- Background: Warm beige (#f5f1eb)
- Text: Rich brown (#3d2817)
- Accent: Coffee bean (#8b7355)
- **Best for**: Evening reading, comfort

### Soft Evening
**Gentle and calming**

- Background: Deep charcoal (#2d2d2d)
- Text: Soft gray (#e8e8e8)
- Accent: Warm gold (#c9a961)
- **Best for**: Night reading, reduced eye strain

## Theme System

Themes are JSON configuration files in `/themes` that define:
- Theme metadata (id, name, description)
- Color palette with CSS variable mappings
- Best use case information

The system automatically:
- Loads themes from JSON files
- Applies CSS variables dynamically
- Persists selection in localStorage
- Provides theme selector UI

## Creating Custom Themes

### Step 1: Create Theme JSON

Create a new file in `/themes` (e.g., `my-theme.json`):

```json
{
  "id": "my-theme",
  "name": "My Theme",
  "description": "A cozy custom theme",
  "bestFor": "Custom use case",
  "colors": {
    "bg-primary": "#your-color",
    "bg-secondary": "#your-color",
    "text-primary": "#your-color",
    "text-secondary": "#your-color",
    "accent": "#your-color",
    "accent-light": "#your-color",
    "border": "#your-color",
    "shadow": "rgba(0, 0, 0, 0.1)"
  }
}
```

### Step 2: Register Theme

Add to `frontend/src/utils/themeLoader.js`:

```javascript
import myTheme from '@themes/my-theme.json'

export const themes = [
  nordicMinimal,
  warmCoffeehouse,
  softEvening,
  myTheme, // Add your theme
]
```

### Step 3: Generate CSS (Optional)

```bash
cd frontend
npm run generate:themes
```

This generates CSS classes from theme JSON files.

### Step 4: Test Accessibility

Ensure WCAG AA compliance:
- Text on background: **4.5:1** minimum
- Large text: **3:1** minimum
- Clear focus states for interactive elements

## Design Principles

1. **Contrast**: Ensure text is readable
2. **Warmth**: Use inviting colors
3. **Consistency**: Maintain visual hierarchy
4. **Calm**: Avoid jarring colors
5. **Comfort**: Test for extended reading

## Theme Variables

| Variable | Purpose | Example |
|----------|---------|---------|
| `--bg-primary` | Main background | `#fafafa` |
| `--bg-secondary` | Card background | `#ffffff` |
| `--text-primary` | Main text | `#2c3e50` |
| `--text-secondary` | Secondary text | `#7f8c8d` |
| `--accent` | Primary accent | `#34495e` |
| `--accent-light` | Light accent | `#ecf0f1` |
| `--border` | Border color | `#e0e0e0` |
| `--shadow` | Shadow color | `rgba(0, 0, 0, 0.05)` |

## Typography

- **UI Font**: Inter (sans-serif)
- **Content Font**: Crimson Pro (serif)
- **Line Height**: 1.8 for comfortable reading
- **Transitions**: 600ms for smooth theme changes

## Theme Files

- `nordic-minimal.json` - Light, clean theme
- `warm-coffeehouse.json` - Warm, cozy theme
- `soft-evening.json` - Dark, gentle theme

## Contributing Themes

1. Create JSON file following the format above
2. Follow design principles
3. Test on multiple devices
4. Ensure accessibility compliance
5. Register in `frontend/src/utils/themeLoader.js`
6. Submit via Pull Request

---

*"The best themes are invisible — they let the content shine while providing a comfortable reading experience."*
