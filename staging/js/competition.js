// Renders a competition-year page from data/competitions/<year>.json.

async function initCompetitionPage() {
  const year = new URLSearchParams(location.search).get("year");
  document.body.dataset.page = `competition-${year}`;

  const content = document.getElementById("competition-content");
  if (!year) {
    content.innerHTML = "<p>No year specified.</p>";
    return;
  }

  const response = await fetch(`data/competitions/${year}.json`);
  if (!response.ok) {
    content.innerHTML = "<p>No competition page is available for this year.</p>";
    return;
  }

  const data = await response.json();
  document.title = `London 12 Bell Competition > ${year} Competition`;
  content.innerHTML = `<h1>${escapeHtml(data.year)} Competition</h1>${renderBlocks(data.blocks)}`;
  alignMethodDiagramRules(content);
}

document.addEventListener("DOMContentLoaded", initCompetitionPage);
