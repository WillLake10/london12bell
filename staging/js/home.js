// Renders the homepage body from data/home.json.

async function initHomePage() {
  const content = document.getElementById("home-content");
  const response = await fetch("data/home.json");
  if (!response.ok) return;
  const data = await response.json();
  content.innerHTML = `<h1 style="text-align: center;">${escapeHtml(data.title)}</h1>${renderBlocks(data.blocks)}`;
  alignMethodDiagramRules(content);
}

document.addEventListener("DOMContentLoaded", initHomePage);
