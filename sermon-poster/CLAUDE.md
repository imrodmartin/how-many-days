# Sunday Sermon Auto-Poster

Automatically posts a published sermon node to fbcaurora.in (Drupal 10) every Sunday by pulling the YouTube video URL and sermon details from two sources.

## What it does

1. Fetches the latest video URL from the @fbcaurora YouTube channel via yt-dlp
2. Scrapes the sermon title (`#message-title`) and scripture reference (`#message-scripture`) from https://imrodmartin.github.io/sunday-message/
3. Creates a remote video media entity in Drupal via JSON:API
4. Creates a published `sermons` node with all fields populated

## Running it

Triggered automatically every Sunday via GitHub Actions cron. To run manually:

- GitHub: **Actions → Post Sunday Sermon → Run workflow**
- Local: set the three env vars below and run `python post_sermon.py`

## Required GitHub Secrets

| Secret | Description |
|---|---|
| `DRUPAL_BASE_URL` | `https://fbcaurora.in` |
| `DRUPAL_USER` | Drupal admin username |
| `DRUPAL_PASS` | Drupal admin password |

## Schedule

Cron is set to `0 15 * * 0` (15:00 UTC every Sunday):
- **Winter (EST):** 10:00 AM
- **Summer (EDT):** 11:00 AM

To change it, edit the `cron` line in `.github/workflows/post_sermon.yml`. Use `0 14 * * 0` for 10:00 AM EDT in summer.

## Drupal field names

Defined as constants at the top of `post_sermon.py` — adjust here if the Drupal site changes:

| Constant | Value | What it maps to |
|---|---|---|
| `SPEAKER_VOCAB` | `speaker` | Taxonomy vocabulary machine name |
| `SPEAKER_TID` | `28` | Term ID for Pastor Bill Secrest |
| `MEDIA_BUNDLE` | `remote_video` | Media type bundle machine name |
| `OEMBED_FIELD` | `field_media_oembed_video` | Remote video URL field |

Drupal content type fields used: `title`, `body`, `field_sermon_date`, `field_sermon_video`, `field_speaker`.

## Drupal prerequisite

JSON:API must be set to allow write operations at:
`https://fbcaurora.in/admin/config/services/jsonapi`
