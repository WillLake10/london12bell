
import logging
import os
import posixpath
import socket
import sys
import base64
import hashlib
import hmac
import hashlib
from pathlib import Path

import paramiko

logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s %(levelname)s %(name)s: %(message)s",
    stream=sys.stdout,
)

# Enable Paramiko's connection and protocol diagnostics.
logging.getLogger("paramiko").setLevel(logging.DEBUG)

HOST = os.environ["SFTP_HOST"]
USERNAME = os.environ["SFTP_USERNAME"]
PASSWORD = os.environ["SFTP_PASSWORD"]
REMOTE_ROOT = os.environ["SFTP_REMOTE_PATH"].rstrip("/") or "/"
LOCAL_ROOT = Path(".").resolve()

EXCLUDED = {".git", ".github", ".gitignore"}

def log(message):
    logging.info(message)

class FingerprintPolicy(paramiko.MissingHostKeyPolicy):
    def missing_host_key(self, client, hostname, key):
        expected = os.environ[
            "SFTP_HOST_KEY_FINGERPRINT"
        ].strip()

        actual = "SHA256:" + base64.b64encode(
            hashlib.sha256(key.asbytes()).digest()
        ).decode("ascii").rstrip("=")

        if not hmac.compare_digest(actual, expected):
            raise paramiko.SSHException(
                f"Host key fingerprint mismatch for {hostname}. "
                "Check the configured fingerprint."
            )

        log(f"Verified SSH host key for {hostname}")

def sha256_local(path):
    digest = hashlib.sha256()

    with open(path, "rb") as file:
        for chunk in iter(lambda: file.read(1024 * 1024), b""):
            digest.update(chunk)

    return digest.hexdigest()


def sha256_remote(sftp, path):
    digest = hashlib.sha256()

    with sftp.open(path, "rb") as file:
        for chunk in iter(lambda: file.read(1024 * 1024), b""):
            digest.update(chunk)

    return digest.hexdigest()

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

        log("SSH authentication successful; opening SFTP")
        sftp = client.open_sftp()

        try:
            log(f"Checking remote directory: {REMOTE_ROOT}")
            sftp.stat(REMOTE_ROOT)
            log("Remote directory exists")

            files = [
                p for p in LOCAL_ROOT.rglob("*")
                if p.is_file()
                and not any(part in EXCLUDED for part in p.relative_to(LOCAL_ROOT).parts)
            ]

            log(f"Found {len(files)} files to upload")


            for local_file in files:
                relative = local_file.relative_to(
                    LOCAL_ROOT
                ).as_posix()
                remote_file = posixpath.join(
                    REMOTE_ROOT, relative
                )
                parent = posixpath.dirname(remote_file)

                # Create any missing parent directories.
                missing = []
                current = parent

                while current and current != REMOTE_ROOT:
                    try:
                        sftp.stat(current)
                        break
                    except OSError as exc:
                        if getattr(exc, "errno", None) not in (2, 3):
                            raise

                        missing.append(current)
                        next_dir = posixpath.dirname(current)

                        if next_dir == current:
                            raise

                        current = next_dir

                for directory in reversed(missing):
                    log(f"Creating directory: {directory}")
                    sftp.mkdir(directory)

                # Check whether the remote file already exists.
                try:
                    remote_stat = sftp.stat(remote_file)
                except OSError as exc:
                    if getattr(exc, "errno", None) not in (2, 3):
                        raise

                    remote_stat = None

                local_size = local_file.stat().st_size


                if remote_stat is not None and remote_stat.st_size == local_size:
                    local_hash = sha256_local(local_file)
                    remote_hash = sha256_remote(sftp, remote_file)

                    if local_hash == remote_hash:
                        continue

                log(f"Uploading {relative} ({local_size} bytes)")
                sftp.put(str(local_file), remote_file)
                log(f"Uploaded: {relative}")

            log("Deployment completed successfully")

        finally:
            sftp.close()

    except (paramiko.SSHException, OSError, socket.timeout) as exc:
        log(f"DEPLOYMENT FAILED: {type(exc).__name__}: {exc}")
        raise
    finally:
        client.close()

if __name__ == "__main__":
    main()
