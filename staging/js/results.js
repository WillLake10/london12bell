// Renders a single year's results page: intro/outro text, the results
// table (with tied positions, optional speed/photo/certificate/audio
// columns), and the band-detail modal that replaces the old AJAX popup.

function ordinal(n) {
  const suffixes = { 1: "st", 2: "nd", 3: "rd" };
  return `${n}${suffixes[n] || "th"}`;
}

// Band names shown on mobile use these pattern-matched abbreviations
// (e.g. "Ancient Society of College Youths (Marchbank)" -> "ASCY
// (Marchbank)") so the narrow table column stays readable; desktop and
// the detail modal always show the full name. See
// data/band-name-abbreviations.json to add or edit patterns.
let bandNameAbbreviations = null;

async function loadBandNameAbbreviations() {
  if (bandNameAbbreviations) return bandNameAbbreviations;
  try {
    const response = await fetch("data/band-name-abbreviations.json");
    bandNameAbbreviations = await response.json();
  } catch (error) {
    console.warn("Could not load band name abbreviations", error);
    bandNameAbbreviations = [];
  }
  return bandNameAbbreviations;
}

function shortenBandName(name, abbreviations) {
  for (const { pattern, flags, replacement } of abbreviations) {
    const regex = new RegExp(pattern, flags || "");
    if (regex.test(name)) {
      return name.replace(regex, replacement).trim();
    }
  }
  return name;
}

function sortedBands(bands) {
  // Bands with no mark (did not complete) sort last, keeping their
  // original draw order among themselves.
  return [...bands].sort((a, b) => {
    if (a.mark === null && b.mark === null) return 0;
    if (a.mark === null) return 1;
    if (b.mark === null) return -1;
    return b.mark - a.mark;
  });
}

function positionsWithTies(bands) {
  const positions = [];
  let lastMark;
  let lastPosition;
  bands.forEach((band, index) => {
    if (band.mark !== null && band.mark === lastMark) {
      positions.push(`${lastPosition}=`);
    } else {
      lastPosition = index + 1;
      positions.push(String(lastPosition));
    }
    lastMark = band.mark;
  });
  return positions;
}

// Ringers 1-6 in the left column, 7-12 in the right (grid-auto-flow:
// column fills top-to-bottom then wraps to the next column, so this
// only needs the row count - the ringers are already bell-ordered).
function renderRingerGrid(band, ringers) {
  if (!ringers.length) return "";
  const rowCount = Math.ceil(ringers.length / 2);
  const items = ringers
    .map((r) => {
      const isConductor = band.conductor === r.name || band.conductor === String(r.bell);
      const missingNote = r.missing ? ' <span class="ringer-missing">(not in photo)</span>' : "";
      return `<div>${r.bell}&nbsp;${r.name}${isConductor ? " (C)" : ""}${missingNote}</div>`;
    })
    .join("");
  return `<div class="ringer-grid" style="grid-template-rows: repeat(${rowCount}, auto);">${items}</div>`;
}

function bandDetailHtml(band) {
  const parts = [`<h2>${band.name}</h2>`];

  const meta = [`Rang ${ordinal(band.rang)}`];
  if (band.speed) meta.push(`Speed ${band.speed}`);
  parts.push(`<p class="band-meta">${meta.join(" &middot; ")}</p>`);

  const ringers = band.ringers || [];

  if (band.photo) {
    parts.push(`<img class="bandphoto" src="../images/bands/${band.year}/${band.photo}" alt="${band.name}" />`);
    if (band.photocaption) parts.push(`<p class="bandcaption">${band.photocaption}</p>`);
  }
  parts.push(renderRingerGrid(band, ringers));

  if (band.touch) {
    parts.push(`<audio controls preload="metadata"><source src="../touches/${band.year}/${band.touch}" type="audio/mpeg" /></audio>`);
  }

  if (band.cert) {
    parts.push(`<p><a href="../certificates/${band.year}/${band.cert}" target="_blank">Download certificate</a></p>`);
  }

  return parts.join("\n");
}

function openBandModal(band) {
  const backdrop = document.getElementById("band-modal");
  const content = document.getElementById("band-modal-content");
  content.innerHTML = bandDetailHtml(band);
  backdrop.hidden = false;
}

function wireModal() {
  const backdrop = document.getElementById("band-modal");
  backdrop.addEventListener("click", (event) => {
    if (event.target === backdrop || event.target.classList.contains("modal-close")) {
      backdrop.hidden = true;
    }
  });
  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") backdrop.hidden = true;
  });
}

async function renderTable(container, year, venue, bands) {
  const ordered = sortedBands(bands);
  const positions = positionsWithTies(ordered);
  const abbreviations = await loadBandNameAbbreviations();

  // Full set of columns from the old site (Rang, Speed, Band Photos,
  // Certificates, Audio) show on wide screens via .col-desktop; on
  // narrow screens those are hidden and everything is reachable through
  // the single .col-mobile "More Details" button instead.
  const hasSpeed = bands.some((b) => b.speed);
  const hasPhotos = bands.some((b) => b.photo || b.ringers);
  const hasCert = bands.some((b) => b.cert);
  const hasTouch = bands.some((b) => b.touch);

  const detailButton = (index) => `
    <button type="button" class="detail-button" data-band-index="${index}">
      <svg viewBox="0 0 20 20" aria-hidden="true">
        <circle cx="10" cy="10" r="8.5" fill="none" stroke="currentColor" stroke-width="1.5" />
        <circle cx="10" cy="6.2" r="1.1" fill="currentColor" />
        <line x1="10" y1="9" x2="10" y2="14.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
      </svg>
      <span>Details</span>
    </button>
  `;

  const playButton = (band) => `
    <button type="button" class="play-button" data-src="../touches/${year}/${band.touch}" aria-label="Play touch for ${band.name}">
      <svg class="icon-play" viewBox="0 0 20 20" aria-hidden="true"><polygon points="6,4 16,10 6,16" fill="currentColor" /></svg>
      <svg class="icon-pause" viewBox="0 0 20 20" aria-hidden="true"><rect x="5" y="4" width="3.5" height="12" fill="currentColor" /><rect x="11.5" y="4" width="3.5" height="12" fill="currentColor" /></svg>
    </button>
  `;

  const headerCells = [
    "<th>Position</th>",
    '<th class="col-desktop">Rang</th>',
    "<th>Team</th>",
    "<th>Mark</th>",
    hasSpeed ? '<th class="col-desktop">Speed</th>' : "",
    hasPhotos ? '<th class="col-desktop">Band Photos</th>' : "",
    hasCert ? '<th class="col-desktop">Certificates</th>' : "",
    hasTouch ? '<th class="col-desktop"></th>' : "",
    '<th class="col-mobile">More Details</th>',
  ].join("");

  const rows = ordered.map((band, index) => {
    const markText = band.mark === null ? "DNC" : `${band.mark}%`;
    const cells = [
      `<td>${positions[index]}</td>`,
      `<td class="col-desktop">${ordinal(band.rang)}</td>`,
      `<td><span class="name-full">${band.name}</span><span class="name-short">${shortenBandName(band.name, abbreviations)}</span></td>`,
      `<td>${markText}</td>`,
      hasSpeed ? `<td class="col-desktop">${band.speed || ""}</td>` : "",
      hasPhotos
        ? `<td class="col-desktop detail-cell">${band.photo || band.ringers ? detailButton(index) : ""}</td>`
        : "",
      hasCert
        ? `<td class="col-desktop">${band.cert ? `<a href="../certificates/${year}/${band.cert}" target="_blank">${band.name}</a>` : ""}</td>`
        : "",
      hasTouch
        ? `<td class="col-desktop">${band.touch ? playButton(band) : ""}</td>`
        : "",
      `<td class="col-mobile detail-cell">${detailButton(index)}</td>`,
    ].join("");
    return `<tr>${cells}</tr>`;
  }).join("");

  container.innerHTML = `
    <p><strong>Venue:</strong> ${venue}</p>
    <div class="table-scroll">
      <table class="resultstable">
        <tr>${headerCells}</tr>
        ${rows}
      </table>
    </div>
  `;

  container.querySelectorAll("button[data-band-index]").forEach((button) => {
    button.addEventListener("click", () => {
      const band = ordered[Number(button.dataset.bandIndex)];
      openBandModal({ ...band, year });
    });
  });

  wirePlayButtons(container);
}

function wirePlayButtons(container) {
  let activeAudio = null;
  let activeButton = null;

  // Which icon shows is driven entirely by the "playing" class in CSS
  // (.play-button .icon-pause / .playing .icon-play) rather than the
  // hidden attribute/property, which some browsers don't reflect
  // consistently on SVG elements.
  const setPlaying = (button, playing) => {
    button.classList.toggle("playing", playing);
  };

  container.querySelectorAll(".play-button").forEach((button) => {
    const audio = new Audio(button.dataset.src);
    audio.preload = "none";

    audio.addEventListener("ended", () => setPlaying(button, false));

    button.addEventListener("click", () => {
      if (activeAudio && activeAudio !== audio) {
        activeAudio.pause();
        setPlaying(activeButton, false);
      }

      if (audio.paused) {
        audio.play();
        setPlaying(button, true);
        activeAudio = audio;
        activeButton = button;
      } else {
        audio.pause();
        setPlaying(button, false);
      }
    });
  });
}

async function loadJson(path) {
  const response = await fetch(path);
  if (!response.ok) return null;
  return response.json();
}

// Renders a year's results into the standard #results-intro/
// #results-table/#results-outro/#band-modal elements. Used by
// results.html (year from the URL) and, once the home page switches
// from "upcoming" to "results" mode (see home.js), by index.html too.
async function renderResultsForYear(year) {
  const introEl = document.getElementById("results-intro");
  const outroEl = document.getElementById("results-outro");
  const tableEl = document.getElementById("results-table");

  const results = await loadJson(`data/results/${year}.json`);

  document.title = `London 12 Bell Competition > ${year} Results`;

  if (results) {
    introEl.innerHTML = `<h1>${escapeHtml(results.year)} Competition</h1>${renderParagraphs(results.intro)}`;
    outroEl.innerHTML = renderParagraphs(results.outro);
    await renderTable(tableEl, results.year, results.venue, results.bands);
  } else {
    introEl.innerHTML = "";
    outroEl.innerHTML = "";
    tableEl.innerHTML = "<p>Results are not available for this year.</p>";
  }

  wireModal();
}

async function initResultsPage() {
  const year = new URLSearchParams(location.search).get("year");
  if (!year) {
    document.getElementById("results-intro").innerHTML = "<p>No year specified.</p>";
    return;
  }
  await renderResultsForYear(year);
}

// Only auto-run on results.html (a ?year= in the URL); when this script
// is loaded on index.html instead, home.js calls renderResultsForYear()
// itself once it knows whether home.json's mode calls for it.
document.addEventListener("DOMContentLoaded", () => {
  if (new URLSearchParams(location.search).has("year")) {
    initResultsPage();
  }
});
