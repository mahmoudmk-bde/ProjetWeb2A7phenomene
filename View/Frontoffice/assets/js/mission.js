// Fade-in animation on page load
document.addEventListener("DOMContentLoaded", () => {
  const fadeElements = document.querySelectorAll(".fade-in");
  fadeElements.forEach((el, i) => {
    el.style.animationDelay = i * 0.2 + "s";
    el.classList.add("visible");
  });
});

// Neon Glow on Buttons
const buttons = document.querySelectorAll(".btn, .btn_1");

buttons.forEach((btn) => {
  btn.addEventListener("mouseenter", () => {
    btn.style.boxShadow = "0 0 15px rgba(255,0,76,0.6)";
  });
  btn.addEventListener("mouseleave", () => {
    btn.style.boxShadow = "none";
  });
});

// Smooth scroll behavior
document.querySelectorAll("a[href^='#']").forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();
    let target = document.querySelector(this.getAttribute("href"));
    if (target) {
      target.scrollIntoView({
        behavior: "smooth",
        block: "center",
      });
    }
  });
});
