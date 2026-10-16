import base64
import hashlib
import hmac
import logging
import os
import posixpath
import socket
import stat
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

FULL_DEPLOY = (
    os.environ.get("FULL_DEPLOY", "false").lower() == "true"
)

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


def git_files():
    """Return all existing, tracked files eligible for deployment."""
    result = subprocess.run(
        ["git", "ls-files", "-z"],
        cwd=LOCAL_ROOT,
        check=True,
        capture_output=True,
    )

    files = []

    for relative in result.stdout.decode("utf-8").split("\0"):
        if not relative:
            continue

        relative_path = Path(relative)

        if any(part in EXCLUDED for part in relative_path.parts):
            continue

        local_file = LOCAL_ROOT / relative_path

        if local_file.is_file():
            files.append(local_file)

    return sorted(files)


def get_changed_files():
    """Return files to upload, using Git unless a full deploy is requested."""
    if FULL_DEPLOY:
        log("FULL DEPLOY enabled; uploading all tracked site files")
        return git_files()

    if not PREVIOUS_SHA:
        log("No previous deployment recorded; uploading all tracked files")
        return git_files()

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

    files = []

    for relative in result.stdout.decode("utf-8").split("\0"):
        if not relative:
            continue

        relative_path = Path(relative)

        if any(part in EXCLUDED for part in relative_path.parts):
            continue

        local_file = LOCAL_ROOT / relative_path

        # Deleted files are handled by remote cleanup.
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


def cleanup_remote(sftp, directory, expected_files, relative_prefix=""):
    """Remove remote files not tracked in Git and empty obsolete directories."""
    for entry in sftp.listdir_attr(directory):
        name = entry.filename
        remote_path = posixpath.join(directory, name)

        relative = (
            posixpath.join(relative_prefix, name)
            if relative_prefix
            else name
        )

        if stat.S_ISDIR(entry.st_mode):
            cleanup_remote(
                sftp,
                remote_path,
                expected_files,
                relative,
            )

            has_expected_descendant = any(
                path.startswith(relative + "/")
                for path in expected_files
            )

            if not has_expected_descendant:
                log(f"Removing obsolete directory: {relative}")
                sftp.rmdir(remote_path)

        elif relative not in expected_files:
            log(f"Removing obsolete file: {relative}")
            sftp.remove(remote_path)


def main():
    # Cleanup must never be allowed to traverse the entire server root.
    if REMOTE_ROOT == "/":
        raise ValueError(
            "SFTP_REMOTE_PATH must be a specific website directory, "
            "not '/'. Set it to your actual website root."
        )

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

            # Reconcile the remote website against the complete current
            # Git checkout, not just the files changed in this deployment.
            expected_files = {
                path.relative_to(LOCAL_ROOT).as_posix()
                for path in git_files()
            }

            log("Removing remote files not present in Git")
            cleanup_remote(sftp, REMOTE_ROOT, expected_files)

            log("Deployment and remote cleanup completed successfully")

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
