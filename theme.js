let isDark;

const toggle = document.getElementById("theme-toggle");
const html = document.documentElement;

const savedTheme = localStorage.getItem("theme");

if (savedTheme) {
    html.dataset.theme = savedTheme;
    isDark = savedTheme === "dark";
} else {
    const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
    html.dataset.theme = prefersDark ? "dark" : "light";
    isDark = prefersDark;
}

if (isDark)
{
    toggle.textContent = langLight;
}
else
{
    toggle.textContent = langDark;
}

toggle.addEventListener("click", () => {    
    
    
    if (isDark)
    {
        html.dataset.theme = "light";
        localStorage.setItem("theme", "light");
        toggle.textContent = langDark;
    }
    else
    {
        html.dataset.theme = "dark";
        localStorage.setItem("theme", "dark");
        toggle.textContent = langLight;
    }
    isDark = !isDark;        
});
