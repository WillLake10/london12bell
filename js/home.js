// Renders the homepage from data/home.json. For roughly half the year
// this shows the upcoming competition's details ("mode": "upcoming");
// once that competition has happened, change "mode" to "results" in
// home.json and the home page shows the latest results instead (the
// same table/modal/audio as results.html, for the year at the start of
// RESULT_YEARS in layout.js - keep that list and the results data in
// sync as usual when a new year's results are added).

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
  } else {
    resultsSection.hidden = true;
    upcomingSection.hidden = false;
    upcomingSection.innerHTML = `<h1 style="text-align: center;">${escapeHtml(data.title)}</h1>${renderBlocks(data.blocks)}`;
    alignMethodDiagramRules(upcomingSection);
  }
}

document.addEventListener("DOMContentLoaded", initHomePage);
