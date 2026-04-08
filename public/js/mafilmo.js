// MAFILMO — JavaScript

// RECHERCHE AJAX (déclenchée au clic sur "Rechercher")

function initSearch() {
    const input = document.getElementById("search-input");
    const searchForm = document.getElementById("search-form");

    if (!input) return; // Pas sur la page recherche

    // Afficher/masquer le bouton clear selon le contenu de l'input
    input.addEventListener("input", function () {
        const clearBtn = document.getElementById("clear-search");
        if (clearBtn) clearBtn.style.display = this.value.length > 0 ? "block" : "none";
    });

    if (searchForm) {
        searchForm.addEventListener("submit", function (e) {
            const query = input.value.trim();
            if (query.length >= 2) {
                e.preventDefault();
                fetchMovies(query);
            }
        });
    }
}

function fetchMovies(query) {
    const resultsContainer = document.getElementById("search-results");
    const resultsCount = document.getElementById("results-count");

    fetch(`/search/ajax?q=${encodeURIComponent(query)}`, {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
    })
        .then((response) => {
            if (!response.ok) throw new Error("Erreur réseau");
            return response.json();
        })
        .then((data) => {
            if (data.results && data.results.length > 0) {
                if (resultsCount) {
                    resultsCount.innerHTML = `<strong>${data.results.length} résultats</strong> pour "${escapeHtml(query)}"`;
                }
                resultsContainer.innerHTML = renderMovieGrid(data.results);
            } else {
                if (resultsCount) resultsCount.textContent = "";
                resultsContainer.innerHTML = noResultsHTML(query);
            }
        })
        .catch(() => {
            resultsContainer.innerHTML = errorHTML();
        });
}

function renderMovieGrid(movies) {
    const searchQuery = document.getElementById("search-input")?.value || "";
    const backUrl = encodeURIComponent(
        window.location.href.split("?")[0] + "?q=" + encodeURIComponent(searchQuery)
    );
    const cards = movies.map((movie) => `
        <div class="col-6 col-md-4 col-lg-3">
            <div class="movie-card">
                <a href="/movies/tmdb/${movie.tmdb_id}?back=${backUrl}" style="text-decoration:none;">
                    ${movie.poster_path
                        ? `<img src="https://image.tmdb.org/t/p/w500${movie.poster_path}" alt="${escapeHtml(movie.title)}" class="movie-poster">`
                        : `<div class="movie-poster-placeholder">🎬</div>`
                    }
                    <div class="p-3">
                        <div class="movie-title">${escapeHtml(movie.title)}</div>
                        <div class="movie-year">${movie.release_year || "Année inconnue"}</div>
                    </div>
                </a>
                <div class="movie-actions mt-2 movie-actions-card">
                    <button onclick="window.location.href='/movies/tmdb/${movie.tmdb_id}?back=' + encodeURIComponent(window.location.href.split('?')[0] + '?q=' + encodeURIComponent(document.getElementById('search-input').value))"
                        class="btn-action btn-detail">🔍 Détails</button>
                    <button onclick="addMovie(event, ${movie.tmdb_id}, 'seen')"
                        class="btn-action btn-seen">✅ Vu</button>
                    <button onclick="addMovie(event, ${movie.tmdb_id}, 'watchlist')"
                        class="btn-action btn-watchlist">📌 À voir</button>
                </div>
            </div>
        </div>
    `).join("");
    return `<div class="row g-3">${cards}</div>`;
}

function addMovie(event, tmdbId, status) {
    event.preventDefault();
    event.stopPropagation();

    const btn = event.currentTarget;
    const originalText = btn.innerHTML;
    btn.innerHTML = "⏳";
    btn.disabled = true;

    const formData = new FormData();
    formData.append("tmdb_id", tmdbId);
    formData.append("status", status);
    formData.append("_token", getCsrfToken());

    fetch("/movies/add", {
        method: "POST",
        body: formData,
        headers: { "X-Requested-With": "XMLHttpRequest" },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.already_exists) {
                btn.innerHTML = originalText;
                btn.disabled = false;
                showFlashMessage(data.message, "info");
            } else {
                btn.innerHTML = status === "seen" ? "✅ Ajouté !" : "📌 Ajouté !";
                btn.style.opacity = "0.7";
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    btn.style.opacity = "1";
                }, 2000);
                showFlashMessage(data.message || "Film ajouté !", "success");
            }
        })
        .catch(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            showFlashMessage("Une erreur est survenue", "error");
        });
}

function noResultsHTML(query) {
    return `
        <div class="empty-state">
            <div class="empty-icon">🎬</div>
            <p>Aucun film trouvé pour "${escapeHtml(query)}"</p>
        </div>`;
}

function errorHTML() {
    return `
        <div class="empty-state">
            <div class="empty-icon">⚠️</div>
            <p>Une erreur est survenue. Veuillez réessayer.</p>
        </div>`;
}

function clearSearch() {
    const input = document.getElementById("search-input");
    if (!input) return;
    input.value = "";
    input.focus();
    const resultsContainer = document.getElementById("search-results");
    const resultsCount = document.getElementById("results-count");
    const clearBtn = document.getElementById("clear-search");
    if (clearBtn) clearBtn.style.display = "none";
    if (resultsCount) resultsCount.textContent = "";
    if (resultsContainer) resultsContainer.innerHTML = `
        <div class="empty-state">
            <div class="empty-icon">🎬</div>
            <p>Recherchez un film par son titre</p>
        </div>`;
}

// CONFIRMATION DE SUPPRESSION

function initDeleteConfirm() {
    document.querySelectorAll(".btn-remove, .btn-remove-list").forEach((btn) => {
        const form = btn.closest("form");
        if (!form) return;

        btn.addEventListener("click", function (e) {
            e.preventDefault();

            const modal = document.createElement("div");
            modal.style.cssText = "position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;";
            modal.innerHTML = `
                <div style="background:white;border-radius:16px;padding:32px;max-width:380px;width:90%;text-align:center;box-shadow:0 20px 40px rgba(0,0,0,0.2);">
                    <div style="font-size:48px;margin-bottom:12px;">🗑️</div>
                    <p style="font-size:16px;font-weight:600;color:#1F2937;margin-bottom:24px;">Supprimer ce film de votre liste ?</p>
                    <div style="display:flex;gap:10px;justify-content:center;">
                        <button id="modal-cancel" style="padding:10px 24px;border-radius:8px;border:none;background:#F3F4F6;color:#374151;font-weight:700;cursor:pointer;">Annuler</button>
                        <button id="modal-confirm" style="padding:10px 24px;border-radius:8px;border:none;background:#EF4444;color:white;font-weight:700;cursor:pointer;">Supprimer</button>
                    </div>
                </div>`;

            document.body.appendChild(modal);

            modal.querySelector("#modal-cancel").addEventListener("click", () => modal.remove());
            modal.querySelector("#modal-confirm").addEventListener("click", () => { modal.remove(); form.submit(); });
            modal.addEventListener("click", (e) => { if (e.target === modal) modal.remove(); });
        });
    });
}

// MESSAGES FLASH DYNAMIQUES

function showFlashMessage(message, type = "success") {
    const existing = document.getElementById("flash-dynamic");
    if (existing) existing.remove();

    const bg     = type === "success" ? "#D1FAE5" : type === "info" ? "#DBEAFE" : "#FEE2E2";
    const border = type === "success" ? "#6EE7B7" : type === "info" ? "#93C5FD" : "#FCA5A5";
    const color  = type === "success" ? "#065F46" : type === "info" ? "#1E40AF" : "#991B1B";
    const icon   = type === "success" ? "✅"      : type === "info" ? "ℹ️"      : "❌";

    const flash = document.createElement("div");
    flash.id = "flash-dynamic";
    flash.style.cssText = `
        position:fixed; top:20px; right:20px; z-index:10000;
        background:${bg}; border:1px solid ${border}; color:${color};
        padding:14px 20px; border-radius:12px; font-weight:500;
        box-shadow:0 10px 25px rgba(0,0,0,0.1);
        transform:translateX(120%); transition:transform 0.3s ease;
        max-width:350px; font-family:'Inter',sans-serif;
    `;
    flash.innerHTML = `${icon} ${escapeHtml(message)}`;
    document.body.appendChild(flash);

    setTimeout(() => { flash.style.transform = "translateX(0)"; }, 10);
    setTimeout(() => {
        flash.style.transform = "translateX(120%)";
        setTimeout(() => flash.remove(), 300);
    }, 3000);
}

// MODALES DASHBOARD (genres & notes)

function openModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.style.display = "flex";
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.style.display = "none";
}

function initDashboardModals() {
    document.querySelectorAll("#genre-modal, #rating-modal").forEach((modal) => {
        if (!modal) return;
        modal.addEventListener("click", function (e) {
            if (e.target === this) closeModal(this.id);
        });
    });
}

// RECHERCHE DANS LA LISTE (filtrage côté client — vue Liste)

function initListSearch() {
    const input = document.getElementById("list-search");
    if (!input) return; // Pas sur la page liste

    input.addEventListener("input", function () {
        filterListMovies(this.value);
    });
}

function filterListMovies(query) {
    const q = query.toLowerCase().trim();
    const cards = document.querySelectorAll(".col-6.col-md-4.col-lg-3");

    cards.forEach((col) => {
        const title = col.querySelector(".movie-title")?.textContent.toLowerCase() || "";
        col.classList.toggle("movie-card-hidden", q.length > 0 && !title.includes(q));
    });

    const visible = [...cards].filter((c) => !c.classList.contains("movie-card-hidden"));
    const emptyMsg = document.getElementById("list-search-empty");
    if (visible.length === 0 && q.length > 0) {
        if (!emptyMsg) {
            const msg = document.createElement("div");
            msg.id = "list-search-empty";
            msg.className = "col-12";
            msg.innerHTML = `<div class="empty-state"><div class="icon">🔎</div><p>Aucun film trouvé pour "${escapeHtml(query)}"</p></div>`;
            document.querySelector(".row.g-3").appendChild(msg);
        }
    } else if (emptyMsg) {
        emptyMsg.remove();
    }
}

// UTILITAIRES

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || "";
}

function escapeHtml(str) {
    if (!str) return "";
    return str
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function toggleDropdown() {
    document.getElementById("dropdownMenu")?.classList.toggle("open");
}

function toggleMobileMenu() {
    document.getElementById("mobileMenu")?.classList.toggle("open");
    document.getElementById("burgerBtn")?.classList.toggle("open");
}

// Fermer le dropdown en cliquant ailleurs
document.addEventListener("click", function (e) {
    const dropdown = document.getElementById("userDropdown");
    if (dropdown && !dropdown.contains(e.target)) {
        document.getElementById("dropdownMenu")?.classList.remove("open");
    }
});

// Réinitialisation au retour via bfcache — page Recherche
window.addEventListener("pageshow", function (e) {
    if (e.persisted) {
        initSearch();
        initDeleteConfirm();
        initListSearch();
    }
});

// INITIALISATION
document.addEventListener("DOMContentLoaded", () => {
    initSearch();
    initDeleteConfirm();
    initListSearch();
    initDashboardModals();
});
