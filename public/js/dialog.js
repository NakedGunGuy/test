document.body.addEventListener('htmx:afterSwap', (event) => {
    if (event.detail.target.id === 'dialog') {
        const dlg = document.getElementById('dialog');
        if (!dlg.open) {
            dlg.showModal();
        }

        // Set up enhanced dialog functionality
        setupDialogClose(dlg);
    }
});

function setupDialogClose(dlg) {
    // Skip if already set up
    if (dlg.dataset.dialogListenersSetup) {
        return;
    }

    // Close on backdrop click
    dlg.addEventListener('click', (event) => {
        // Only close if clicking on the dialog itself, not its children
        if (event.target === dlg) {
            dlg.close();
        }
    });

    // Close on Escape key
    dlg.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            dlg.close();
        }
    });

    // Mark as set up
    dlg.dataset.dialogListenersSetup = 'true';
}

document.body.addEventListener("htmx:afterRequest", function(event) {
    const elt = event.detail.elt;
    if (elt.matches("[data-toast], [data-close-modal]")) {
        if (event.detail.xhr.status >= 200 && event.detail.xhr.status < 300) {
            if (elt.dataset.toast) {
                showToast(elt.dataset.toast);
            }
            if (elt.dataset.closeModal === "true") {
                const dlg = document.getElementById('dialog');
                if (dlg.open) {
                    dlg.close();
                }
            }
        }

        if (event.detail.xhr.status >= 400) {
            showToast(event.detail.xhr.responseText);
        }
    }
});

function showToast(message) {
    const toast = document.createElement("div");
    toast.className = "toast";
    toast.textContent = message;
    document.body.appendChild(toast);
    requestAnimationFrame(() => {
        toast.classList.add("visible");
    });
    setTimeout(() => {
        toast.classList.remove("visible");
        toast.addEventListener("transitionend", () => {
            toast.remove();
        });
    }, 3000);
}
