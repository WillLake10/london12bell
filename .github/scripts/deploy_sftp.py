import base64
import hashlib
import hmac
import logging
import os
import posixpath
import socket
import subprocess
import sys
from pathlib import Path

import paramiko


logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s %(levelname)s %(name)s: %(message)s",
    stream=sys.stdout,
)

logging.getLogger("paramiko").setLevel(logging.WARNING)

HOST = os.environ["SFTP_HOST"]
USERNAME = os.environ["SFTP_USERNAME"]
PASSWORD = os.environ["SFTP_PASSWORD"]
REMOTE_ROOT = os.environ["SFTP_REMOTE_PATH"].rstrip("/") or "/"
EXPECTED_FINGERPRINT = os.environ[
    "SFTP_HOST_KEY_FINGERPRINT"
].strip()

PREVIOUS_SHA = os.environ.get("PREVIOUS_SHA", "").strip()
CURRENT_SHA = os.environ["CURRENT_SHA"]

LOCAL_ROOT = Path(".").resolve()
EXCLUDED = {".git", ".github", ".gitignore"}


def log(message):
    logging.info(message)


class FingerprintPolicy(paramiko.MissingHostKeyPolicy):
    def missing_host_key(self, client, hostname, key):
        actual = "SHA256:" + base64.b64encode(
            hashlib.sha256(key.asbytes()).digest()
        ).decode("ascii").rstrip("=")

        if not hmac.compare_digest(
            actual, EXPECTED_FINGERPRINT
        ):
            raise paramiko.SSHException(
                f"Host key fingerprint mismatch for {hostname}. "
                "Verify SFTP_HOST_KEY_FINGERPRINT."
            )

        log(f"Verified SSH host key for {hostname}")


def get_changed_files():
    if PREVIOUS_SHA:
        log(f"Finding changes since {PREVIOUS_SHA}")

        result = subprocess.run(
            [
                "git",
                "diff",
                "--name-only",
                "-z",
                "--diff-filter=ACMRT",
                PREVIOUS_SHA,
                CURRENT_SHA,
                "--",
            ],
            cwd=LOCAL_ROOT,
            check=True,
            capture_output=True,
        )

        paths = result.stdout.decode("utf-8").split("\0")

    else:
        log("No previous deployment recorded; deploying all tracked files")

        result = subprocess.run(
            ["git", "ls-files", "-z"],
            cwd=LOCAL_ROOT,
            check=True,
            capture_output=True,
        )

        paths = result.stdout.decode("utf-8").split("\0")

    files = []

    for relative in paths:
        if not relative:
            continue

        relative_path = Path(relative)

        if any(part in EXCLUDED for part in relative_path.parts):
            continue

        local_file = LOCAL_ROOT / relative_path

        # Deleted files are deliberately ignored.
        if local_file.is_file():
            files.append(local_file)

    return sorted(files)


def ensure_remote_directory(sftp, directory):
    missing = []
    current = directory

    while current and current != REMOTE_ROOT:
        try:
            sftp.stat(current)
            break
        except OSError as exc:
            if getattr(exc, "errno", None) not in (2, 3):
                raise

            missing.append(current)
            parent = posixpath.dirname(current)

            if parent == current:
                raise

            current = parent

    for path in reversed(missing):
        log(f"Creating directory: {path}")
        sftp.mkdir(path)


def main():
    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(FingerprintPolicy())

    try:
        log(f"Connecting to {HOST}:22")

        client.connect(
            hostname=HOST,
            port=22,
            username=USERNAME,
            password=PASSWORD,
            timeout=10,
            banner_timeout=10,
            auth_timeout=10,
            allow_agent=False,
            look_for_keys=False,
        )

        log("SFTP connection authenticated")
        sftp = client.open_sftp()

        try:
            log(f"Checking remote directory: {REMOTE_ROOT}")
            sftp.stat(REMOTE_ROOT)

            files = get_changed_files()
            log(f"Files to upload: {len(files)}")

            for local_file in files:
                relative = local_file.relative_to(
                    LOCAL_ROOT
                ).as_posix()

                remote_file = posixpath.join(
                    REMOTE_ROOT, relative
                )

                ensure_remote_directory(
                    sftp,
                    posixpath.dirname(remote_file),
                )

                size = local_file.stat().st_size
                log(f"Uploading {relative} ({size} bytes)")

                sftp.put(
                    str(local_file),
                    remote_file,
                    confirm=True,
                )

                log(f"Uploaded: {relative}")

            log("Deployment completed successfully")

        finally:
            sftp.close()

    except (
        paramiko.SSHException,
        OSError,
        socket.timeout,
        subprocess.CalledProcessError,
    ) as exc:
        log(f"DEPLOYMENT FAILED: {type(exc).__name__}: {exc}")
        raise

    finally:
        client.close()


if __name__ == "__main__":
    main()
