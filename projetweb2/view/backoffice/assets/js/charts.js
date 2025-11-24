// GRAPH 1 : Missions par thème
new Chart(document.getElementById("missionsThemeChart"), {
  type: "bar",
  data: {
    labels: window.dashboardData.themesLabels,
    datasets: [
      {
        label: "Nombre de missions",
        data: window.dashboardData.themesValues,
        backgroundColor: "#ff0066",
      },
    ],
  },
  options: {
    responsive: true,
    plugins: { legend: { labels: { color: "#fff" } } },
    scales: {
      x: { ticks: { color: "#fff" } },
      y: { ticks: { color: "#fff" } },
    },
  },
});

// GRAPH 2 : Candidatures par mission
new Chart(document.getElementById("candidaturesMissionChart"), {
  type: "bar",
  data: {
    labels: window.dashboardData.candidLabels,
    datasets: [
      {
        label: "Nombre de candidatures",
        data: window.dashboardData.candidValues,
        backgroundColor: "#b300ff",
      },
    ],
  },
  options: {
    responsive: true,
    plugins: { legend: { labels: { color: "#fff" } } },
    scales: {
      x: { ticks: { color: "#fff" } },
      y: { ticks: { color: "#fff" } },
    },
  },
});

// GRAPH 3 : Répartition des niveaux d'expérience
new Chart(document.getElementById("niveauExpChart"), {
  type: "pie",
  data: {
    labels: window.dashboardData.expLabels,
    datasets: [
      {
        data: window.dashboardData.expValues,
        backgroundColor: ["#ff0066", "#b300ff", "#ff9900", "#33cc33"],
      },
    ],
  },
});
