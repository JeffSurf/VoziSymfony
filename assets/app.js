/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';
const bootstrap = require('bootstrap');

const createToast = (avisNotif) => {
    const toast = document.createElement("div");
    toast.classList.add("toast");
    toast.setAttribute("role", "alert");
    toast.setAttribute("aria-live", "assertive");
    toast.setAttribute("aria-atomic", "true");

    const toastHeader = document.createElement("div");
    toastHeader.classList.add("toast-header");
    toast.append(toastHeader);

    const toastTitle = document.createElement("strong");
    toastTitle.classList.add("me-auto");
    toastTitle.innerHTML = "Nouvel avis";
    toastHeader.append(toastTitle);

    const deleteButtonToast = document.createElement("button");
    deleteButtonToast.classList.add("btn-close");
    deleteButtonToast.setAttribute("type", "button");
    deleteButtonToast.setAttribute("data-bs-dismiss", "toast");
    deleteButtonToast.setAttribute("aria-label", "Close");
    deleteButtonToast.addEventListener("click", (e) => {
       e.preventDefault();
       toast.remove();
    });
    toastHeader.append(deleteButtonToast);

    const toastBody = document.createElement("div");
    toastBody.classList.add("toast-body");
    toastBody.innerHTML = `${avisNotif.utilisateur} a donné son avis pour ${avisNotif.app}`;
    toast.append(toastBody);

    return toast;
}

if (eventSource)
{
    const toastContainer = document.getElementsByClassName("toast-container")[0];

    eventSource.onmessage = event => {
        // Will be called every time an update is published by the server
        const avisNotif = JSON.parse(event.data);
        const toast = createToast(avisNotif);
        toastContainer.append(toast);

        // Afficher les notifications
        document.querySelectorAll('.toast').forEach(el => bootstrap.Toast.getOrCreateInstance(el).show());

    }
}