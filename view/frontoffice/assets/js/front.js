document.addEventListener("DOMContentLoaded", function () {

    const form = document.querySelector("#condidature-form");
    if (!form) return;

    form.addEventListener("submit", function (e) {

        let errors = [];

        // Champs
        const pseudo = form.querySelector("[name='pseudo_gaming']");
        const email = form.querySelector("[name='email']");
        const dispo = form.querySelector("[name='disponibilites']");

        // REGEX
        const regexPseudo = /^[A-Za-zÀ-ÖØ-öø-ÿ]+$/;                // lettres uniquement
        const regexDispo = /^[A-Za-zÀ-ÖØ-öø-ÿ0-9 \-_.!?]+$/;       // lettres + chiffres
        const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;           // email

        // ---- VALIDATIONS ----

        // PSEUDO GAMING : lettres uniquement
        if (!regexPseudo.test(pseudo.value.trim())) {
            errors.push("Le pseudo gaming doit contenir uniquement des lettres.");
        }

        // EMAIL
        if (!regexEmail.test(email.value.trim())) {
            errors.push("Veuillez saisir un email valide.");
        }

        // DISPONIBILITÉ
        if (!regexDispo.test(dispo.value.trim())) {
            errors.push("La disponibilité doit contenir seulement lettres et chiffres.");
        }

        // AFFICHER ERREURS
        const err = document.querySelector("#error-box");
        if (err) err.remove();

        if (errors.length > 0) {
            e.preventDefault();

            let box = document.createElement("div");
            box.id = "error-box";
            box.style.background = "#ff0033";
            box.style.padding = "12px";
            box.style.color = "white";
            box.style.borderRadius = "8px";
            box.style.marginBottom = "12px";
            box.style.fontWeight = "600";
            box.innerHTML = errors.join("<br>");

            form.prepend(box);
        }

    });

});
