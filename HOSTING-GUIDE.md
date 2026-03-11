# FLAW Gaming - Hosting & Content Management Guide

## Table of Contents

1. [Hosting on cPanel](#hosting-on-cpanel)
2. [Content Management](#content-management)
3. [Customizer Settings](#customizer-settings)
4. [Post-Deployment Checklist](#post-deployment-checklist)

---

## Hosting on cPanel

### Step 1: Export Local Database

Export your local database using one of these methods:

**Option A: Command Line**
```bash
mysqldump -u root flaw-gaming > flaw-gaming.sql
```

**Option B: phpMyAdmin (Laragon)**
1. Open `http://localhost/phpmyadmin`
2. Select `flaw-gaming` database
3. Click **Export** tab
4. Choose **Quick** export method
5. Click **Go** to download the SQL file

---

### Step 2: Prepare wp-config.php for Production

A production-ready config file has been created: `wp-config-production.php`

**Update these values before uploading:**

| Setting | Description | Example |
|---------|-------------|---------|
| `DB_NAME` | Database name from cPanel | `username_flawgaming` |
| `DB_USER` | Database user from cPanel | `username_dbuser` |
| `DB_PASSWORD` | Password you created | `your_secure_password` |
| `WP_SITEURL` | Your domain with https | `https://flawgaming.com` |
| `WP_HOME` | Your domain with https | `https://flawgaming.com` |

**Generate new security keys:**
1. Visit: https://api.wordpress.org/secret-key/1.1/salt/
2. Copy all 8 lines
3. Replace the `GENERATE_NEW_KEY_HERE` placeholders

---

### Step 3: cPanel Database Setup

1. **Log into cPanel**

2. **Create MySQL Database:**
   - Go to **MySQL Databases**
   - Enter database name → Click **Create Database**
   - Create a new user with a strong password
   - Add user to database with **ALL PRIVILEGES**

3. **Import Database:**
   - Open **phpMyAdmin** from cPanel
   - Select your new database
   - Click **Import** tab
   - Upload your `flaw-gaming.sql` file
   - Click **Go**

4. **Update Site URLs** (run in phpMyAdmin SQL tab):
   ```sql
   UPDATE wp_options SET option_value = 'https://yourdomain.com' WHERE option_name = 'siteurl';
   UPDATE wp_options SET option_value = 'https://yourdomain.com' WHERE option_name = 'home';
   ```

---

### Step 4: Upload Files

**Option A: File Manager (Small sites)**
1. Go to cPanel → **File Manager** → `public_html`
2. Upload a ZIP of your entire site
3. Extract the files

**Option B: FTP (Recommended)**
1. Use FileZilla or similar FTP client
2. Connect using your cPanel FTP credentials
3. Upload all files to `public_html`

**Important:** Rename `wp-config-production.php` to `wp-config.php` on the server.

---

### Step 5: Set File Permissions

```
Folders: 755
Files: 644
wp-config.php: 600
```

In cPanel File Manager, right-click files/folders → **Change Permissions**

---

### Step 6: Post-Upload Tasks

1. **Run Setup Scripts** (if needed):
   - Visit `https://yourdomain.com/setup-menus.php`
   - **Delete the file after running**

2. **Flush Permalinks:**
   - Go to **Settings → Permalinks**
   - Click **Save Changes** (without changing anything)

3. **Install SSL Certificate:**
   - Go to cPanel → **SSL/TLS Status** or **Let's Encrypt**
   - Install free SSL certificate for your domain

---

## Content Management

All content is managed through the WordPress Admin Panel.

### Custom Post Types

| Content | Admin Location | Description |
|---------|----------------|-------------|
| **Players** | WP Admin → Players | Team members, gamertags, roles, photos |
| **Teams** | WP Admin → Teams | Competitive rosters (Alpha, Bravo, etc.) |
| **Events** | WP Admin → Events | Tournaments, scrims, community events |
| **Games** | WP Admin → Games | Games the organization plays |
| **Creators** | WP Admin → Creators | Content creators, streamers |
| **Partners** | WP Admin → Partners | Sponsors and partners |
| **Applications** | WP Admin → Applications | Join requests from website visitors |

---

### Adding Players

1. Go to **WP Admin → Players → Add New**
2. Fill in the fields:

| Field | Description |
|-------|-------------|
| Title | Player's display name |
| Gamertag | In-game username |
| Real Name | Optional real name |
| Photo | Player headshot/avatar |
| Team | Select from existing teams |
| Role | IGL, Fragger, Support, Flex, etc. |
| Status | Active, Inactive, Retired |
| Social Links | Twitter, Twitch URLs |

3. Click **Publish**

---

### Adding Teams

1. Go to **WP Admin → Teams → Add New**
2. Fill in:
   - Team name (e.g., "FLAW Alpha")
   - Team logo
   - Associated game
   - Team description
3. Click **Publish**

---

### Adding Events

1. Go to **WP Admin → Events → Add New**
2. Fill in:

| Field | Description |
|-------|-------------|
| Title | Event name |
| Start Date | When the event begins |
| End Date | When the event ends |
| Event Type | Tournament, Scrimmage, Community Event |
| Game | Which game this event is for |
| Prize Pool | Optional prize information |
| Stream URL | Twitch/YouTube link |
| Status | Upcoming, Live, Completed, Cancelled |

3. Click **Publish**

---

### Adding Partners

1. Go to **WP Admin → Partners → Add New**
2. Fill in:
   - Partner/Sponsor name
   - Logo image
   - Website URL
   - Partner Tier (Platinum, Gold, Silver, Bronze)
3. Click **Publish**

---

### Managing Applications

Applications are submitted through the Join page (`/join`) and stored in the database.

**View Applications:**
1. Go to **WP Admin → Applications**
2. See all applications with status, role, email, Discord

**Filter by Status:**
- Use the status dropdown to filter: Pending, Under Review, Approved, Rejected

**Review an Application:**
1. Click on an application to open it
2. View all applicant details (name, email, Discord, experience, links)
3. Update the status using the sidebar dropdown
4. Add optional notes (will be included in the email)
5. Check "Send email notification" to notify the applicant
6. Click **Update** to save

**Application Statuses:**

| Status | Description | Email Sent |
|--------|-------------|------------|
| Pending | New application, not reviewed | No |
| Under Review | Being considered | No |
| Approved | Accepted to join | Yes (with next steps) |
| Rejected | Not accepted | Yes (with feedback if provided) |

**Email Notifications:**
- **Admin**: Receives email when new application is submitted
- **Applicant**: Receives confirmation email on submission
- **Applicant**: Receives status email when approved or rejected

**Duplicate Prevention:**
- Same email address cannot submit more than one application within 30 days

**Application System Features:**

| Feature | Status |
|---------|--------|
| Form saves to database | Yes |
| Admin review panel | Yes |
| Email to admin on new application | Yes |
| Email confirmation to applicant | Yes |
| Email on approval/rejection | Yes |
| Duplicate prevention (30 days) | Yes |
| Status management | Yes |
| Filter by status | Yes |
| AJAX submission | Yes |
| Loading state | Yes |

**Application Workflow:**

```
1. User submits form on /join page
         ↓
2. Application saved to database
         ↓
3. Admin receives email notification
         ↓
4. Applicant receives confirmation email
         ↓
5. Admin reviews in WP Admin → Applications
         ↓
6. Admin changes status to Approved/Rejected
         ↓
7. Applicant receives status email with notes
```

---

## Customizer Settings

Access via **Appearance → Customize → FLAW Gaming Options**

### Hero Section

| Setting | Description |
|---------|-------------|
| Background Image | Hero background (1920x1080 recommended) |
| Background Video | Optional YouTube/video URL |
| Tagline | Small text above title |
| Title | Main headline (use `<span>` for red highlight) |
| Subtitle | Description paragraph |
| Primary Button | Text and URL (e.g., Discord link) |
| Secondary Button | Text and URL |

---

### Social Links

| Platform | Description |
|----------|-------------|
| Discord | Discord invite URL |
| Twitter/X | Twitter profile URL |
| YouTube | YouTube channel URL |
| Twitch | Twitch channel URL |
| TikTok | TikTok profile URL |
| Instagram | Instagram profile URL |

---

### Statistics / Achievements

The "By The Numbers" section on the homepage.

**For each of the 4 statistics:**

| Setting | Description | Example |
|---------|-------------|---------|
| Value | The number/stat | `50+`, `#1`, `100+` |
| Label | Description text | `Active Members` |
| Icon | Visual icon type | Members, Trophy, Events, Games |
| Color | Accent color | Primary (Red), Gold, Secondary (Cyan), Accent (Yellow) |

**To hide a stat:** Leave the Value field empty.

---

### Members Gallery

| Setting | Description |
|---------|-------------|
| Section Tagline | Small text above title |
| Section Title | Main heading |
| Section Description | Paragraph below title |
| Button Text | CTA button label |
| Button URL | Where button links to |

**Note:** The actual members are pulled from the **Players** post type. This section only controls the text around them.

---

### Discord CTA

| Setting | Description |
|---------|-------------|
| Title | Section heading |
| Description | Paragraph text |
| Discord URL | Invite link |

---

### Footer

| Setting | Description |
|---------|-------------|
| Footer Tagline | Text in footer (defaults to site tagline) |
| Copyright Text | Custom copyright line |

---

## Post-Deployment Checklist

### Security

- [ ] Delete `setup-menus.php` after running
- [ ] Delete `wp-config-production.php` template
- [ ] Generate new security salts
- [ ] Set `WP_DEBUG` to `false`
- [ ] Set proper file permissions (644/755)
- [ ] Install SSL certificate
- [ ] Update admin password

### Content

- [ ] Add real players via Players post type
- [ ] Add teams with logos
- [ ] Update events with real dates
- [ ] Add partner logos and links
- [ ] Update social media links in Customizer
- [ ] Update hero section text and images
- [ ] Update statistics with real numbers
- [ ] Create/update pages (About, Contact, Join, etc.)

### Technical

- [ ] Test all pages load correctly
- [ ] Test contact forms work
- [ ] Verify images display properly
- [ ] Check mobile responsiveness
- [ ] Test Discord/social links
- [ ] Flush permalinks if 404 errors occur
- [ ] Set up caching plugin (optional)
- [ ] Configure backup solution

---

## Troubleshooting

### 404 Errors on Pages
1. Go to **Settings → Permalinks**
2. Click **Save Changes**
3. Check `.htaccess` file exists and is writable

### White Screen / Error
1. Enable debug mode temporarily in `wp-config.php`:
   ```php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   ```
2. Check `wp-content/debug.log` for errors
3. Disable debug when done

### Database Connection Error
1. Verify database credentials in `wp-config.php`
2. Ensure database user has proper privileges
3. Check `DB_HOST` (usually `localhost` for cPanel)

### Images Not Loading
1. Check file permissions (644 for files, 755 for folders)
2. Verify uploads folder exists: `wp-content/uploads`
3. Check folder permissions: 755

---

## Support Files

| File | Purpose | Action |
|------|---------|--------|
| `wp-config-production.php` | Production config template | Rename to `wp-config.php` on server |
| `setup-menus.php` | Creates navigation menus | Run once, then DELETE |
| `.htaccess` | Apache rewrite rules | Keep, don't modify unless needed |

---

## Quick Reference

### Admin URLs

| Page | URL |
|------|-----|
| Dashboard | `/wp-admin/` |
| Players | `/wp-admin/edit.php?post_type=player` |
| Teams | `/wp-admin/edit.php?post_type=team` |
| Events | `/wp-admin/edit.php?post_type=event` |
| Customizer | `/wp-admin/customize.php` |
| Menus | `/wp-admin/nav-menus.php` |

### Content Priority

When displaying content, the site uses this priority:

1. **Database content** (Players, Teams, Events, etc.)
2. **Fallback demo data** (only shown if database is empty)

This means once you add real content, the demo data automatically disappears.
