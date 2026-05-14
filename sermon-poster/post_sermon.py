import os
import sys
import time
from datetime import date

import requests
import yt_dlp
from bs4 import BeautifulSoup

# Adjust these if your Drupal site uses different machine names
SPEAKER_VOCAB = "speaker"
SPEAKER_TID = 28
MEDIA_BUNDLE = "remote_video"
OEMBED_FIELD = "field_media_oembed_video"
SUNDAY_MESSAGE_URL = "https://imrodmartin.github.io/sunday-message/"
YOUTUBE_CHANNEL = "https://www.youtube.com/@fbcaurora"

# Retry settings for YouTube (stream may not be live yet when the job fires)
MAX_VIDEO_RETRIES = 5
RETRY_SLEEP_SECONDS = 60


def get_latest_video_url() -> str:
    ydl_opts = {
        "quiet": True,
        "extract_flat": True,
        "playlist_items": "1",
    }
    for feed in ("streams", "videos"):
        try:
            with yt_dlp.YoutubeDL(ydl_opts) as ydl:
                info = ydl.extract_info(f"{YOUTUBE_CHANNEL}/{feed}", download=False)
                if info and info.get("entries"):
                    video_id = info["entries"][0]["id"]
                    return f"https://www.youtube.com/watch?v={video_id}"
        except Exception:
            pass
    return ""


def get_latest_video_url_with_retry() -> str:
    for attempt in range(1, MAX_VIDEO_RETRIES + 1):
        url = get_latest_video_url()
        if url:
            print(f"Found video URL: {url}")
            return url
        if attempt < MAX_VIDEO_RETRIES:
            print(f"Video not found (attempt {attempt}/{MAX_VIDEO_RETRIES}), retrying in {RETRY_SLEEP_SECONDS}s...")
            time.sleep(RETRY_SLEEP_SECONDS)
    raise RuntimeError(f"Could not find a video on {YOUTUBE_CHANNEL} after {MAX_VIDEO_RETRIES} attempts")


def get_sermon_info() -> tuple[str, str]:
    resp = requests.get(SUNDAY_MESSAGE_URL, timeout=30)
    resp.raise_for_status()
    soup = BeautifulSoup(resp.text, "html.parser")

    title_el = soup.find(id="message-title")
    scripture_el = soup.find(id="message-scripture")

    if not title_el:
        raise RuntimeError("Could not find #message-title on sunday-message page")
    if not scripture_el:
        raise RuntimeError("Could not find #message-scripture on sunday-message page")

    return title_el.get_text(strip=True), scripture_el.get_text(strip=True)


def get_speaker_uuid(session: requests.Session, base_url: str, headers: dict) -> str:
    url = f"{base_url}/jsonapi/taxonomy_term/{SPEAKER_VOCAB}?filter[drupal_internal__tid]={SPEAKER_TID}"
    resp = session.get(url, headers=headers, timeout=30)
    resp.raise_for_status()
    data = resp.json().get("data", [])
    if not data:
        raise RuntimeError(f"Speaker term {SPEAKER_TID} not found in vocabulary '{SPEAKER_VOCAB}'")
    return data[0]["id"]


def create_media_entity(
    session: requests.Session, base_url: str, headers: dict, video_url: str, title: str
) -> str:
    payload = {
        "data": {
            "type": f"media--{MEDIA_BUNDLE}",
            "attributes": {
                "name": title,
                OEMBED_FIELD: video_url,
            },
        }
    }
    resp = session.post(
        f"{base_url}/jsonapi/media/{MEDIA_BUNDLE}",
        json=payload,
        headers=headers,
        timeout=30,
    )
    if not resp.ok:
        raise RuntimeError(f"Failed to create media entity ({resp.status_code}): {resp.text}")
    return resp.json()["data"]["id"]


def create_sermon_node(
    session: requests.Session,
    base_url: str,
    headers: dict,
    title: str,
    scripture: str,
    media_uuid: str,
    speaker_uuid: str,
) -> str:
    payload = {
        "data": {
            "type": "node--sermons",
            "attributes": {
                "title": title,
                "body": {"value": scripture, "format": "plain_text"},
                "field_sermon_date": date.today().isoformat(),
                "status": True,
            },
            "relationships": {
                "field_sermon_video": {
                    "data": {"type": f"media--{MEDIA_BUNDLE}", "id": media_uuid}
                },
                "field_speaker": {
                    "data": {
                        "type": f"taxonomy_term--{SPEAKER_VOCAB}",
                        "id": speaker_uuid,
                    }
                },
            },
        }
    }
    resp = session.post(
        f"{base_url}/jsonapi/node/sermons",
        json=payload,
        headers=headers,
        timeout=30,
    )
    if not resp.ok:
        raise RuntimeError(f"Failed to create sermon node ({resp.status_code}): {resp.text}")
    return resp.json()["data"]["id"]


def main() -> None:
    base_url = os.environ.get("DRUPAL_BASE_URL", "").rstrip("/")
    user = os.environ.get("DRUPAL_USER", "")
    password = os.environ.get("DRUPAL_PASS", "")

    if not base_url or not user or not password:
        print("ERROR: DRUPAL_BASE_URL, DRUPAL_USER, and DRUPAL_PASS must all be set.")
        sys.exit(1)

    session = requests.Session()
    session.auth = (user, password)

    print("Fetching sermon title and scripture...")
    title, scripture = get_sermon_info()
    print(f"  Title: {title}")
    print(f"  Scripture: {scripture}")

    print("Finding latest YouTube video...")
    video_url = get_latest_video_url_with_retry()

    # Basic auth does not require a CSRF token — that's only for cookie sessions
    write_headers = {
        "Content-Type": "application/vnd.api+json",
        "Accept": "application/vnd.api+json",
    }
    read_headers = {"Accept": "application/vnd.api+json"}

    print(f"Looking up speaker UUID for '{SPEAKER_NAME}'...")
    speaker_uuid = get_speaker_uuid(session, base_url, read_headers)
    print(f"  Speaker UUID: {speaker_uuid}")

    print("Creating remote video media entity...")
    media_uuid = create_media_entity(session, base_url, write_headers, video_url, title)
    print(f"  Media UUID: {media_uuid}")

    print("Creating sermon node...")
    node_uuid = create_sermon_node(
        session, base_url, write_headers, title, scripture, media_uuid, speaker_uuid
    )
    print(f"  Node UUID: {node_uuid}")
    print(f"\nDone! Sermon '{title}' published at {base_url}")


if __name__ == "__main__":
    main()
