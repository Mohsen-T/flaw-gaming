# FLAW Gaming

Custom WordPress site for **FLAW Gaming** — an esports and Web3 gaming organization. Built with a custom theme and plugin on top of WordPress + Pods Framework.

## Project Structure

This repo tracks only the custom code (theme + plugin), not WordPress core or third-party plugins.

```
wp-content/
├── themes/flaw-gaming/          # Custom theme
│   ├── assets/
│   │   ├── css/                 # Stylesheets (variables, components, layout)
│   │   └── js/                  # Countdown, hero effects, mobile menu, Twitch status
│   ├── inc/                     # Theme includes (customizer, template functions/tags)
│   ├── template-parts/
│   │   ├── cards/               # Reusable card components (event, team, player, game, etc.)
│   │   ├── event/               # Single event sections (bracket, broadcast, registration, etc.)
│   │   └── sections/            # Front page sections (hero, members, achievements, CTA, etc.)
│   ├── front-page.php           # Homepage
│   ├── archive-*.php            # Archive pages (team, event, game, creator, partner)
│   ├── single-*.php             # Single post pages (event, team, player, game, creator)
│   ├── page-*.php               # Static pages (join, contact, merch, about)
│   ├── header.php / footer.php
│   ├── functions.php
│   └── style.css
│
└── plugins/flaw-gaming-core/    # Custom plugin
    ├── includes/                # Active classes
    │   ├── class-helpers.php    # Query wrapper, helper functions, Pods utilities
    │   ├── class-post-types.php # CPT registration
    │   ├── class-taxonomies.php # Taxonomy registration
    │   ├── class-rest-api.php   # REST API endpoints
    │   ├── class-demo-data.php  # Demo data generator & manager
    │   └── class-applications.php # Join application system
    ├── inc/
    │   ├── pods-config.php      # Pods field definitions & auto-setup
    │   ├── card-data.php        # Card rendering system
    │   ├── class-event-state-manager.php # Event state (upcoming/live/completed)
    │   └── ...
    └── flaw-gaming-core.php     # Plugin entry point
```

## Features

- **Custom Post Types**: Game, Team, Player, Creator, Event, Partner, Application
- **Event Management**: State-based events (upcoming / live / completed) with countdown, bracket embeds, stream embeds, registration forms
- **Team Rosters**: Filterable by game with player profiles
- **Card System**: Reusable `flaw_render_card()` for consistent content display
- **Application System**: Front-end join form with admin review, status emails, duplicate protection
- **Demo Data Manager**: Generate/remove demo content with one click; toggleable placeholder cards for empty sections
- **Customizer Integration**: Hero section, social links, statistics, Discord CTA, members gallery, footer — all editable via WP Customizer
- **Dark Theme**: Gold (#D4A843) + Dark Crimson (#C9282D) on black, designed for esports aesthetics
- **Responsive**: Mobile-first with sticky nav, mobile menu, responsive grids

## Requirements

- WordPress 6.0+
- PHP 8.0+
- [Pods Framework](https://wordpress.org/plugins/pods/) plugin (for custom fields)

### Optional Plugins

- [WPForms Lite](https://wordpress.org/plugins/wpforms-lite/) — for event registration forms
- [Yoast SEO](https://wordpress.org/plugins/wordpress-seo/) — SEO management

## Local Development Setup

1. Install [Laragon](https://laragon.org/) (or any local WordPress environment)
2. Create a new WordPress site
3. Clone this repo into the WordPress root:
   ```bash
   cd /path/to/wordpress
   git clone https://github.com/YOUR_USERNAME/flaw-gaming.git .
   ```
4. Install required plugins via WP Admin:
   - **Pods** (required)
   - **WPForms Lite** (optional)
5. Activate the **FLAW Gaming Core** plugin
6. Activate the **FLAW Gaming** theme
7. Go to **Events > Demo Data** to generate sample content
8. Create pages with these slugs: `join`, `contact`, `merch`, `about`

## Deployment

Upload the following directories to your hosting via FTP/File Manager:

- `wp-content/themes/flaw-gaming/`
- `wp-content/plugins/flaw-gaming-core/`

Then install required plugins (Pods) on the live server via WP Admin.

See `HOSTING-GUIDE.md` for InfinityFree-specific deployment instructions.

## Custom Post Types

| Post Type | Admin Location | Description |
|-----------|---------------|-------------|
| Game | Games | Games the org competes in |
| Team | Teams | Competitive rosters |
| Player | Players | Individual player profiles |
| Creator | Creators | Content creators |
| Event | Events | Tournaments, scrims, showmatches |
| Partner | Partners | Sponsors and partners |
| Application | Applications | Join form submissions |

## Color Scheme

| Color | Hex | Usage |
|-------|-----|-------|
| Gold | `#D4A843` | Primary — buttons, accents, highlights |
| Dark Crimson | `#C9282D` | Secondary — gradients, badges |
| Electric Yellow | `#E3FC02` | Accent — prize pools, special callouts |
| Black | `#0A0A0A` | Background |
| Dark Gray | `#141414` | Secondary background |

## License

GPL v2 or later
