import './bootstrap';
import '@tabler/core/dist/js/tabler.min.js';
import { Dropdown, Modal } from 'bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('readable-mode-toggle');
    if (!toggle) return;

    const storageKey = 'csl-readable-mode';
    const applyMode = (enabled) => {
        document.body.classList.toggle('csl-readable-mode', enabled);
        toggle.setAttribute('aria-pressed', String(enabled));
        toggle.setAttribute('aria-label', enabled ? 'Desactivar modo de lectura fácil' : 'Activar modo de lectura fácil');
        toggle.title = enabled ? 'Desactivar modo de lectura fácil' : 'Modo de lectura fácil';
    };

    applyMode(localStorage.getItem(storageKey) === 'true');
    toggle.addEventListener('click', () => {
        const enabled = !document.body.classList.contains('csl-readable-mode');
        localStorage.setItem(storageKey, String(enabled));
        applyMode(enabled);
    });
});

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach((trigger) => {
        Dropdown.getOrCreateInstance(trigger);
    });
});

// Mobile sidebar: use an explicit toggle so it works the same in the local
// compiled build and on devices connected through the school network.
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('[data-sidebar-toggle]');
    const sidebar = document.getElementById('csl-sidebar-navigation');

    if (!toggle || !sidebar) return;

    const update = (open) => {
        sidebar.classList.toggle('show', open);
        toggle.classList.toggle('collapsed', !open);
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? 'Cerrar navegación' : 'Abrir navegación');
        const icon = toggle.querySelector('i');
        if (icon) icon.className = open ? 'fas fa-xmark' : 'fas fa-bars';
    };

    update(sidebar.classList.contains('show'));
    toggle.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        update(!sidebar.classList.contains('show'));
    });

    sidebar.querySelectorAll('a.nav-link').forEach((link) => link.addEventListener('click', () => {
        if (window.innerWidth < 992) update(false);
    }));
});

document.addEventListener('DOMContentLoaded', () => {
    const settings = window.schoolNotifications;

    if (!settings?.userId || !window.Echo) {
        return;
    }

    const addLiveNotification = (notification) => {
        const data = notification.data ?? notification;
        const title = data.titulo ?? 'Nuevo aviso';
        const message = data.mensaje ?? 'Tienes un aviso nuevo en el sistema.';
        const count = document.getElementById('notification-count');
        const list = document.getElementById('notification-list');

        if (count) {
            const nextCount = Number.parseInt(count.dataset.count ?? '0', 10) + 1;
            count.dataset.count = String(nextCount);
            count.textContent = String(Math.min(nextCount, 99));
            count.classList.remove('d-none');
        }

        document.getElementById('notification-empty')?.remove();

        if (list) {
            const item = document.createElement('a');
            item.href = settings.indexUrl;
            item.className = 'dropdown-item text-wrap py-2';

            const itemTitle = document.createElement('strong');
            itemTitle.className = 'd-block';
            itemTitle.textContent = title;

            const itemMessage = document.createElement('span');
            itemMessage.className = 'small text-secondary d-block mt-1';
            itemMessage.textContent = message.length > 76 ? `${message.slice(0, 73)}...` : message;

            item.append(itemTitle, itemMessage);
            list.prepend(item);
        }

        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-bg-primary border-0 position-fixed bottom-0 end-0 m-3';
        toast.setAttribute('role', 'status');
        toast.setAttribute('aria-live', 'polite');
        toast.innerHTML = '<div class="d-flex"><div class="toast-body"></div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button></div>';
        toast.querySelector('.toast-body').textContent = `${title}: ${message}`;
        document.body.append(toast);
        window.bootstrap?.Toast?.getOrCreateInstance(toast, { delay: 7000 }).show();
        toast.addEventListener('hidden.bs.toast', () => toast.remove());

        window.dispatchEvent(new CustomEvent('school-notification', { detail: data }));
    };

    window.Echo.private(`App.Models.User.${settings.userId}`)
        .notification(addLiveNotification);
});

document.addEventListener('DOMContentLoaded', () => {
    const modalElement = document.getElementById('school-confirm-modal');
    const title = document.getElementById('school-confirm-title');
    const message = document.getElementById('school-confirm-message');
    const button = document.getElementById('school-confirm-button');

    if (!modalElement || !title || !message || !button) {
        return;
    }

    const modal = Modal.getOrCreateInstance(modalElement);
    let action = null;

    const openConfirmation = ({ confirmation, confirmationTitle = 'Confirmar acción', confirmationButton = 'Confirmar', confirmationVariant = 'danger', onConfirm, onCancel }) => {
        title.textContent = confirmationTitle;
        message.textContent = confirmation;
        button.textContent = confirmationButton;
        button.className = `btn btn-${confirmationVariant}`;
        action = { onConfirm, onCancel };
        modal.show();
    };

    window.schoolConfirm = ({ message: confirmation, title: confirmationTitle, confirmLabel: confirmationButton, variant: confirmationVariant } = {}) => new Promise((resolve) => {
        openConfirmation({
            confirmation,
            confirmationTitle,
            confirmationButton,
            confirmationVariant,
            onConfirm: () => resolve(true),
            onCancel: () => resolve(false),
        });
    });

    document.addEventListener('submit', (event) => {
        const form = event.target.closest('form[data-confirm]');
        if (!form || form.dataset.confirmed === '1') {
            return;
        }

        event.preventDefault();
        openConfirmation({
            confirmation: form.dataset.confirm,
            confirmationTitle: form.dataset.confirmTitle,
            confirmationButton: form.dataset.confirmButton,
            confirmationVariant: form.dataset.confirmVariant,
            onConfirm: () => {
                form.dataset.confirmed = '1';
                form.requestSubmit();
            },
        });
    }, true);

    button.addEventListener('click', () => {
        const confirmedAction = action;
        action = null;
        modal.hide();
        confirmedAction?.onConfirm?.();
    });

    modalElement.addEventListener('hidden.bs.modal', () => {
        const cancelledAction = action;
        action = null;
        cancelledAction?.onCancel?.();
    });
});
