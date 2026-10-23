// Renders the homepage from data/home.json. For roughly half the year
// this shows the upcoming competition's details ("mode": "upcoming");
// once that competition has happened, change "mode" to "results" in
// home.json and the home page shows the latest results instead (the
// same table/modal/audio as results.html, for the year at the start of
// RESULT_YEARS in layout.js - keep that list and the results data in
// sync as usual when a new year's results are added).
//
// On competition day, two switches in home.json (both only apply in
// "upcoming" mode) use data/provisional-results.json:
//   "showBandTimings": true - replaces the "11:00 - First Band" timing
//       with each team's start time, in "rang" (ring) order, 30 minutes
//       apart from 11:00.
//   "showResults": true - shows a simple results table (position, rang,
//       team, mark, speed) and hides the Timings and Pub sections and
//       the test piece diagram (the Test Piece link stays).

const FIRST_BAND_LINE = "11:00 - First Band";
const FIRST_BAND_MINUTES = 11 * 60;
const MINUTES_BETWEEN_BANDS = 30;
const HIDDEN_WHEN_RESULTS_SHOWN = ["Timings", "Pub"];

function startTime(index) {
  const total = FIRST_BAND_MINUTES + index * MINUTES_BETWEEN_BANDS;
  const hours = String(Math.floor(total / 60)).padStart(2, "0");
  const minutes = String(total % 60).padStart(2, "0");
  return `${hours}:${minutes}`;
}

async function loadProvisionalBands() {
  const response = await fetch("data/provisional-results.json");
  if (!response.ok) return [];
  const data = await response.json();
  return data.bands || [];
}

function withBandTimings(blocks, bands) {
  const schedule = bands
    .filter((band) => band.rang)
    .sort((a, b) => a.rang - b.rang)
    .map((band, index) => `${startTime(index)} - ${band.name}`);

  return blocks.map((block) => {
    if (block.type !== "info") return block;
    return {
      ...block,
      items: block.items.map((item) => {
        if (item.label !== "Timings") return item;
        const lines = item.value.lines.flatMap((line) => (line === FIRST_BAND_LINE ? schedule : [line]));
        return { ...item, value: { lines } };
      }),
    };
  });
}

function withoutResultsDaySections(blocks) {
  return blocks
    .filter((block) => block.type !== "method-diagram")
    .map((block) => {
      if (block.type !== "info") return block;
      return { ...block, items: block.items.filter((item) => !HIDDEN_WHEN_RESULTS_SHOWN.includes(item.label)) };
    });
}

// Position comes from the marks (highest first, ties shown like "2=")
// using the same helpers as the full results table. Teams with no mark
// yet come next with no position, and "DNF" teams are last, alphabetical,
// all sharing the next position (shown with "=" if more than one).
function provisionalRows(bands) {
  const scored = sortedBands(bands.filter((band) => typeof band.mark === "number"));
  const scoredPositions = positionsWithTies(scored);
  const pending = bands.filter((band) => band.mark === null || band.mark === undefined);
  const dnf = bands
    .filter((band) => band.mark === "DNF")
    .sort((a, b) => a.name.localeCompare(b.name));

  const dnfPosition = `${scored.length + 1}${dnf.length > 1 ? "=" : ""}`;
  return [
    ...scored.map((band, index) => ({ band, position: scoredPositions[index], mark: `${band.mark}%` })),
    ...pending.map((band) => ({ band, position: "", mark: "" })),
    ...dnf.map((band) => ({ band, position: dnfPosition, mark: "DNF" })),
  ];
}

async function renderProvisionalResults(bands) {
  const abbreviations = await loadBandNameAbbreviations();

  const rows = provisionalRows(bands)
    .map(({ band, position, mark }) => {
      return `<tr>
        <td>${position}</td>
        <td class="col-desktop">${band.rang ? ordinal(band.rang) : ""}</td>
        <td><span class="name-full">${escapeHtml(band.name)}</span><span class="name-short">${escapeHtml(shortenBandName(band.name, abbreviations))}</span></td>
        <td>${mark}</td>
        <td class="col-desktop">${escapeHtml(band.speed ?? "")}</td>
      </tr>`;
    })
    .join("");

  return `
    <h2 style="text-align: center;">Results</h2>
    <div class="table-scroll">
      <table class="resultstable">
        <tr><th>Position</th><th class="col-desktop">Rang</th><th>Team</th><th>Mark</th><th class="col-desktop">Speed</th></tr>
        ${rows}
      </table>
    </div>
  `;
}

async function initHomePage() {
  const upcomingSection = document.getElementById("home-content");
  const resultsSection = document.getElementById("home-results");

  const response = await fetch("data/home.json");
  if (!response.ok) return;
  const data = await response.json();

  if (data.mode === "results") {
    upcomingSection.hidden = true;
    resultsSection.hidden = false;
    await renderResultsForYear(RESULT_YEARS[0]);
    return;
  }

  resultsSection.hidden = true;
  upcomingSection.hidden = false;

  let blocks = data.blocks;
  let provisionalBands = null;

  if (data.showResults) {
    blocks = withoutResultsDaySections(blocks);
    provisionalBands = await loadProvisionalBands();
  } else if (data.showBandTimings) {
    blocks = withBandTimings(blocks, await loadProvisionalBands());
  }

  upcomingSection.innerHTML = `<h1 style="text-align: center;">${escapeHtml(data.title)}</h1>${renderBlocks(blocks)}`;
  alignMethodDiagramRules(upcomingSection);

  if (provisionalBands) {
    upcomingSection.insertAdjacentHTML("beforeend", await renderProvisionalResults(provisionalBands));
  }
}

document.addEventListener("DOMContentLoaded", initHomePage);
