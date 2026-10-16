// Shared site header + navigation, injected into every page.
// Keeping this in one place means a new competition year only needs
// adding to YEARS below, instead of editing a nav list on every page.

const RESULT_YEARS = [
  2025, 2024, 2023, 2022, 2021, 2019, 2018, 2017, 2016, 2015,
  2014, 2013, 2012, 2011, 2010, 2009,
];

const STATIC_LINKS = [
  { page: "contacts", href: "contacts.html", label: "Contact" },
  { page: "rules", href: "rules.html", label: "Competition Rules" },
  { page: "safeguarding", href: "safeguarding.html", label: "Safeguarding" },
  { page: "facebook", href: "https://www.facebook.com/London-12-Bell-Competition-178006726438810/", label: "Facebook" },
];

function currentPageId() {
  return document.body.dataset.page || "";
}

async function renderHeader() {
  const mount = document.getElementById("site-header");
  if (!mount) return;

  let upcoming = null;
  try {
    const response = await fetch("data/upcoming.json");
    upcoming = await response.json();
  } catch (error) {
    console.warn("Could not load upcoming contest details", error);
  }

  mount.innerHTML = `
    <div class="header-top">
      <img class="trophy" src="../images/trophy.png" alt="Whitechapel Trophy" />
      <div>
        <h1>London 12 Bell Competition</h1>
        <h2>for the Whitechapel Trophy</h2>
      </div>
      ${upcoming ? `
        <div class="contest-banner">
          <p><strong>${upcoming.year} Contest:</strong></p>
          <p>${upcoming.date}</p>
          <p>at ${upcoming.venue}</p>
        </div>
      ` : ""}
    </div>
    <img class="skyline" src="../images/towers.png" alt="London skyline, including the London Eye" />
  `;
}

function renderNav() {
  const toggle = document.getElementById("nav-toggle");
  const nav = document.getElementById("site-nav");
  if (!nav) return;

  const current = currentPageId();

  const [latestYear, ...pastYears] = RESULT_YEARS;
  const yearLink = (year) => {
    const selected = current === `results-${year}` ? ' class="selected"' : "";
    return `<li${selected}><a href="results.html?year=${year}">${year} Results</a></li>`;
  };

  const isOnPastYear = pastYears.some((year) => current === `results-${year}`);
  const pastYearLinks = pastYears.map(yearLink).join("");

  const staticLinks = STATIC_LINKS.map((link) => {
    const selected = current === link.page ? ' class="selected"' : "";
    return `<li${selected}><a href="${link.href}">${link.label}</a></li>`;
  }).join("");

  const homeSelected = current === "home" ? ' class="selected"' : "";

  nav.innerHTML = `<ul>
    <li${homeSelected}><a href="index.html">Home Page</a></li>
    ${yearLink(latestYear)}
    <li class="nav-dropdown${isOnPastYear ? ' selected' : ''}">
      <details>
        <summary>Past Results</summary>
        <ul>${pastYearLinks}</ul>
      </details>
    </li>
    ${staticLinks}
  </ul>`;

  if (toggle) {
    toggle.addEventListener("click", () => {
      const isOpen = nav.classList.toggle("open");
      toggle.setAttribute("aria-expanded", String(isOpen));
    });
  }
}

document.addEventListener("DOMContentLoaded", () => {
  renderHeader();
  renderNav();
});
