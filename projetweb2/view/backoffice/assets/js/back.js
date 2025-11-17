document.addEventListener("DOMContentLoaded", function () {
  const toggle = document.getElementById("sidebarCollapse");
  const sidebar = document.getElementById("sidebar");

  if (toggle && sidebar) {
    toggle.addEventListener("click", () => {
      sidebar.classList.toggle("active");
    });
  }
});
