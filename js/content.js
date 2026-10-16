// Turns the HTML-free content shapes used across data/results/*.json,
// data/competitions/*.json and data/home.json into markup. Keeping this
// in one place means every page renders "paragraphs", "runs" (text with
// an occasional inline link) and the other block types the same way.

function escapeHtml(value) {
  return String(value)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
}

// A "run" is a plain string, or {text, href} for an inline link.
function renderRun(run) {
  if (typeof run === "string") return escapeHtml(run);
  return `<a href="${escapeHtml(run.href)}">${escapeHtml(run.text)}</a>`;
}

// A "paragraph" is a plain string, or an array of runs (joined with a
// single space - that's the word-boundary the source text was split on).
function renderParagraph(paragraph) {
  const inner = Array.isArray(paragraph)
    ? paragraph.map(renderRun).join(" ")
    : escapeHtml(paragraph);
  return `<p>${inner}</p>`;
}

function renderParagraphs(paragraphs) {
  return (paragraphs || []).map(renderParagraph).join("\n");
}

// "info" block value: a plain string (one line), a bare array of runs
// (one line, joined like a paragraph), or {lines: [...]} for several
// separate lines - each line itself a string or an array of runs.
function renderInfoValue(value) {
  if (value && typeof value === "object" && Array.isArray(value.lines)) {
    return value.lines
      .map((line) => (Array.isArray(line) ? line.map(renderRun).join(" ") : escapeHtml(line)))
      .join("<br />");
  }
  if (Array.isArray(value)) {
    return value.map(renderRun).join(" ");
  }
  return escapeHtml(value);
}

function renderTouchRow(row) {
  if (row.length === 1) {
    return `<tr><td colspan="2">${escapeHtml(row[0])}</td></tr>`;
  }
  return `<tr>${row.map((cell) => `<td>${escapeHtml(cell)}</td>`).join("")}</tr>`;
}

function renderMethodDiagramRow(row) {
  // The rule's width is set in CSS to 100% of the diagram box, which is
  // itself sized (via width: fit-content) to the widest row - so the
  // rule always spans exactly as wide as the diagram, no wider.
  if (row.rule) {
    return '<div class="diagram-row diagram-rule"><hr /></div>';
  }

  const change = row.change ? `<span class="diagram-change">${escapeHtml(row.change)}</span>` : "";
  const markers = (row.markers || [])
    .map((marker) => `<span class="diagram-marker">${escapeHtml(marker)}</span>`)
    .join("");

  return `<div class="diagram-row${row.bold ? " diagram-bold" : ""}">${change}${markers}</div>`;
}

// Composition layouts (e.g. complib.org) pad every unbracketed row with
// a leading/trailing space so its digits line up with the digits inside
// a bracketed row of the same length - the bracket "(" and ")" take the
// place of those spaces. We do the same: for each distinct change
// length, if any row of that length is bracketed, pad the unbracketed
// ones with a matching space on each side so the monospace columns -
// and the rule lines, which track the padded width - line up too.
function padMethodDiagramChanges(rows) {
  const isBracketed = (text) => /^\(.*\)$/.test(text);
  const bracketedLengthByInner = new Map();
  rows.forEach((row) => {
    if (row.change && isBracketed(row.change)) {
      bracketedLengthByInner.set(row.change.length - 2, row.change.length);
    }
  });

  return rows.map((row) => {
    if (!row.change || isBracketed(row.change)) return row;
    const paddedLength = bracketedLengthByInner.get(row.change.length);
    if (!paddedLength) return row;
    //   (non-breaking space) survives HTML whitespace collapsing at
    // the edge of the <span>, unlike a plain leading/trailing space.
    return { ...row, change: ` ${row.change} ` };
  });
}

function renderMethodDiagramRows(rows) {
  return padMethodDiagramChanges(rows).map(renderMethodDiagramRow).join("");
}

// The box (.method-diagram) is sized to fit its widest content, which
// can now be the legend or notes text since they live inside it too.
// The rule lines should only ever span the row/marker content though,
// so size them explicitly to the widest actual row - measured in the
// DOM, since that's the one thing a CSS percentage can't express once
// legend/notes may be wider than the rows. Call after the diagram HTML
// is in the document.
function alignMethodDiagramRules(root) {
  root.querySelectorAll(".method-diagram").forEach((diagram) => {
    const rows = diagram.querySelectorAll(".diagram-row:not(.diagram-rule)");
    const maxWidth = Math.max(0, ...[...rows].map((row) => row.scrollWidth));
    diagram.querySelectorAll(".diagram-rule hr").forEach((hr) => {
      hr.style.width = `${maxWidth}px`;
    });
  });
}

function renderBlock(block) {
  switch (block.type) {
    case "paragraph":
      return renderParagraph(block.runs);

    case "heading":
      return `<h3>${escapeHtml(block.text)}</h3>`;

    case "list":
      return `<ul class="bands">${block.items.map((item) => `<li>${escapeHtml(item)}</li>`).join("")}</ul>`;

    case "image":
      return `<img class="intext" src="images/${escapeHtml(block.src)}" alt="${escapeHtml(block.alt || "")}"${block.height ? ` style="height: ${Number(block.height)}px"` : ""} />`;

    case "touch":
      return `
        ${block.intro ? `<p>${escapeHtml(block.intro)}</p>` : ""}
        <table class="touch">${block.rows.map(renderTouchRow).join("")}</table>
      `;

    case "info":
      return `<dl class="event-info">${block.items
        .map((item) => `<dt>${escapeHtml(item.label)}</dt><dd>${renderInfoValue(item.value)}</dd>`)
        .join("")}</dl>`;

    case "method-diagram":
      return `
        <div class="method-diagram">
          ${renderMethodDiagramRows(block.rows)}
          ${block.legend && block.legend.length ? `<p class="diagram-legend">${block.legend.map(escapeHtml).join("<br />")}</p>` : ""}
          ${block.notes && block.notes.length ? `<p class="diagram-notes">${block.notes.map(escapeHtml).join("<br />")}</p>` : ""}
        </div>
      `;

    default:
      console.warn("Unknown content block type", block.type);
      return "";
  }
}

function renderBlocks(blocks) {
  return (blocks || []).map(renderBlock).join("\n");
}
