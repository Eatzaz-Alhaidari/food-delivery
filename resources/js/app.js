import './bootstrap';

import { createIcons, icons } from 'lucide';

window.lucideIcons = icons;

/**
 * دالة تهيئة وتحديث أيقونات Lucide بسُمك خط موحد (1.75)
 * تتوافق مع قاعدة التصميم: أيقونات Lucide بسُمك موحد وبدون إيموجي
 */
const initLucide = () => {
    createIcons({
        icons,
        attrs: {
            'stroke-width': 1.75,
            class: 'lucide-icon inline-block align-middle',
        },
    });
};

window.refreshIcons = initLucide;

document.addEventListener('DOMContentLoaded', () => {
    initLucide();
});

// إعادة تهيئة الأيقونات مع أحداث Livewire 3
document.addEventListener('livewire:init', () => {
    initLucide();
    if (window.Livewire) {
        window.Livewire.hook('morph.updated', () => {
            initLucide();
        });
        window.Livewire.hook('commit', ({ succeed }) => {
            succeed(() => {
                setTimeout(initLucide, 10);
            });
        });
    }
});

document.addEventListener('livewire:navigated', () => {
    initLucide();
});

// إعادة تهيئة الأيقونات عند حدوث أي تعديل ديناميكي
window.addEventListener('lucide:refresh', () => {
    initLucide();
});
