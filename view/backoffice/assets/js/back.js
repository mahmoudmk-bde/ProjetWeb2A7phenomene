document.addEventListener("DOMContentLoaded", function () {

    const form = document.querySelector(".form-edit-engage, form");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        let errors = [];

        // ---- Récupérer les champs ----
        const titre = form.querySelector("input[name='titre']");
        const theme = form.querySelector("input[name='theme']");
        const jeu = form.querySelector("input[name='jeu']");
        const niveau = form.querySelector("select[name='niveau_difficulte']");
        const dateDebut = form.querySelector("input[name='date_debut']");
        const dateFin = form.querySelector("input[name='date_fin']");
        const description = form.querySelector("textarea[name='description']");

        // ---- REGEX ----
        const regexChaine = /^[A-Za-zÀ-ÖØ-öø-ÿ0-9 \-_,.!?]+$/;

        // ---- CONTRÔLES ----

        // titre
        if (!titre.value.trim()) {
            errors.push("Le titre est obligatoire.");
        } else if (!regexChaine.test(titre.value.trim())) {
            errors.push("Le titre ne doit contenir que des lettres, chiffres et espaces.");
        }

        // theme
        if (!theme.value.trim()) {
            errors.push("Le thème est obligatoire.");
        }

        // jeu
        if (!jeu.value.trim()) {
            errors.push("Le jeu est obligatoire.");
        }

        // diff
        if (!niveau.value.trim()) {
            errors.push("Veuillez sélectionner le niveau de difficulté.");
        }

        // date
        if (!dateDebut.value || !dateFin.value) {
            errors.push("Les dates sont obligatoires.");
        } else if (dateFin.value < dateDebut.value) {
            errors.push("La date de fin doit être supérieure à la date de début.");
        }

        // desc
        if (description.value.trim().length < 10) {
            errors.push("La description doit contenir au moins 10 caractères.");
        }

        // ---- aff erreur
        const errBox = document.querySelector("#error-box");
        if (errBox) errBox.remove();

        if (errors.length > 0) {
            e.preventDefault();

            const box = document.createElement("div");
            box.id = "error-box";
            box.style.background = "#ff0033";
            box.style.padding = "15px";
            box.style.borderRadius = "8px";
            box.style.marginBottom = "15px";
            box.style.color = "white";
            box.style.fontWeight = "600";

            box.innerHTML = errors.join("<br>");

            form.prepend(box);
        }

    });

});
