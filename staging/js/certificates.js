// Renders the certificate download list from data/certificates.json.

async function initCertificatesPage() {
  const list = document.getElementById("certificates-list");
  const response = await fetch("data/certificates.json");
  const certificates = await response.json();

  list.innerHTML = certificates
    .map((cert) => `<p><a href="../certificates/${cert.year}/${cert.file}">${cert.label}</a></p>`)
    .join("");
}

document.addEventListener("DOMContentLoaded", initCertificatesPage);
