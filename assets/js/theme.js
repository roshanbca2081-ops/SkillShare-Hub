
const themeSelect = document.getElementById("themeSelect");

function applyTheme(theme) {
    const root = document.documentElement;

    if (theme === "system") {
        const systemDark = window.matchMedia(
            "(prefers-color-scheme: dark)"
        ).matches;

        root.setAttribute(
            "data-theme",
            systemDark ? "dark" : "light"
        );
    } else {
        root.setAttribute("data-theme", theme);
    }

    localStorage.setItem("skillshare-theme", theme);

    if (themeSelect) {
        themeSelect.value = theme;
    }
}

// Load saved preference
const savedTheme =
    localStorage.getItem("skillshare-theme") || "system";

applyTheme(savedTheme);

// Listen for system theme changes
window.matchMedia("(prefers-color-scheme: dark)")
    .addEventListener("change", () => {
        if (
            localStorage.getItem("skillshare-theme") === "system"
        ) {
            applyTheme("system");
        }
    });

// Theme selection
if (themeSelect) {
    themeSelect.addEventListener("change", function () {
        applyTheme(this.value);
    });
}
