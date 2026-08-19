(function () {
    'use strict';

    const TINYMCE_BASE = 'https://cdn.jsdelivr.net/npm/tinymce@6.8.4';

    const resolveTheme = () => {
        const stored = localStorage.getItem('theme');
        if (stored === 'light' || stored === 'dark') {
            return stored;
        }
        const attr = document.documentElement.getAttribute('data-bs-theme');
        if (attr === 'light' || attr === 'dark') {
            return attr;
        }
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    };

    const seoLinkPlugin = (editor) => {
        const openDialog = () => {
            const selected = editor.selection.getContent({ format: 'text' }) || '';
            const node = editor.dom.getParent(editor.selection.getNode(), 'a[href]');
            const currentHref = node ? editor.dom.getAttrib(node, 'href') : '';
            const currentRel = node ? editor.dom.getAttrib(node, 'rel') : '';
            const currentTarget = node ? editor.dom.getAttrib(node, 'target') : '';
            const currentText = node ? (node.textContent || selected) : selected;
            const hasNofollow = /\bnofollow\b/i.test(currentRel);

            editor.windowManager.open({
                title: 'SEO-Link',
                body: {
                    type: 'panel',
                    items: [
                        { type: 'input', name: 'href', label: 'URL', placeholder: 'https://' },
                        { type: 'input', name: 'text', label: 'Linktext' },
                        {
                            type: 'selectbox',
                            name: 'follow',
                            label: 'Rel (SEO)',
                            items: [
                                { text: 'dofollow (Standard – kein rel)', value: 'dofollow' },
                                { text: 'nofollow', value: 'nofollow' },
                            ],
                        },
                        {
                            type: 'checkbox',
                            name: 'blank',
                            label: 'In neuem Tab öffnen (target="_blank")',
                        },
                    ],
                },
                initialData: {
                    href: currentHref,
                    text: currentText,
                    follow: hasNofollow ? 'nofollow' : 'dofollow',
                    blank: currentTarget === '_blank',
                },
                buttons: [
                    { type: 'cancel', text: 'Abbrechen' },
                    { type: 'submit', text: 'Einfügen', primary: true },
                ],
                onSubmit: (api) => {
                    const data = api.getData();
                    const href = (data.href || '').trim();
                    const text = (data.text || href).trim();

                    if (!href) {
                        api.close();
                        return;
                    }

                    const relParts = [];
                    if (data.follow === 'nofollow') {
                        relParts.push('nofollow');
                    }
                    if (data.blank) {
                        relParts.push('noopener', 'noreferrer');
                    }

                    const attrs = { href };
                    if (data.blank) {
                        attrs.target = '_blank';
                    }
                    if (relParts.length) {
                        attrs.rel = [...new Set(relParts)].join(' ');
                    }

                    const attrString = Object.entries(attrs)
                        .map(([key, value]) => `${key}="${editor.dom.encode(value)}"`)
                        .join(' ');

                    const html = `<a ${attrString}>${editor.dom.encode(text)}</a>`;

                    if (node) {
                        editor.selection.select(node);
                    }

                    editor.insertContent(html);
                    api.close();
                },
            });
        };

        editor.ui.registry.addButton('seolink', {
            icon: 'link',
            tooltip: 'SEO-Link einfügen (nofollow / dofollow / neuer Tab)',
            onAction: openDialog,
        });

        editor.ui.registry.addMenuItem('seolink', {
            icon: 'link',
            text: 'SEO-Link',
            onAction: openDialog,
        });
    };

    const initEditor = (selector) => {
        if (!window.tinymce || !document.querySelector(selector)) {
            return;
        }

        const dark = resolveTheme() === 'dark';

        window.tinymce.baseURL = TINYMCE_BASE;
        window.tinymce.suffix = '.min';
        window.tinymce.remove(selector);
        window.tinymce.init({
            selector,
            base_url: TINYMCE_BASE,
            suffix: '.min',
            promotion: false,
            branding: false,
            menubar: false,
            min_height: 420,
            plugins: 'lists link table code autolink',
            toolbar: 'undo redo | blocks | bold italic | bullist numlist | blockquotebtn | table | seolink link | code',
            block_formats: 'Absatz=p; Überschrift 1=h1; Überschrift 2=h2; Überschrift 3=h3; Überschrift 4=h4; Zitat=blockquote',
            skin: dark ? 'oxide-dark' : 'oxide',
            content_css: dark ? 'dark' : 'default',
            content_style: 'body { font-family: Inter, system-ui, sans-serif; font-size: 16px; line-height: 1.65; }',
            relative_urls: false,
            remove_script_host: false,
            convert_urls: false,
            default_link_target: '',
            rel_list: [
                { title: 'dofollow (Standard)', value: '' },
                { title: 'nofollow', value: 'nofollow' },
                { title: 'nofollow noopener', value: 'nofollow noopener' },
            ],
            link_target_list: [
                { title: 'Gleiches Fenster', value: '' },
                { title: 'Neuer Tab (_blank)', value: '_blank' },
            ],
            extended_valid_elements: 'a[href|target|rel|title],blockquote,code,pre,table[class],th[colspan|rowspan],td[colspan|rowspan]',
            setup: (editor) => {
                seoLinkPlugin(editor);
                editor.ui.registry.addToggleButton('blockquotebtn', {
                    icon: 'quote',
                    tooltip: 'Zitat',
                    onAction: () => editor.execCommand('FormatBlock', false, 'blockquote'),
                    onSetup: (api) => {
                        const handler = () => api.setActive(editor.queryCommandValue('FormatBlock') === 'blockquote');
                        editor.on('NodeChange', handler);
                        return () => editor.off('NodeChange', handler);
                    },
                });
                editor.on('change keyup', () => editor.save());
            },
        });
    };

    const updateCounter = (field) => {
        const max = parseInt(field.getAttribute('maxlength') || field.dataset.maxlength || '160', 10);
        const value = field.value || '';
        const length = [...value].length;
        const output = document.getElementById(field.dataset.counter);
        if (!output) {
            return;
        }
        output.textContent = `${length} / ${max}`;
        output.classList.remove('text-success', 'text-warning', 'text-danger');
        if (length === 0) {
            return;
        }
        if (length > max) {
            output.classList.add('text-danger');
        } else if (length >= max - 10) {
            output.classList.add('text-success');
        } else if (length < 80) {
            output.classList.add('text-warning');
        } else {
            output.classList.add('text-success');
        }
    };

    const validateJson = (field) => {
        const status = document.getElementById(field.dataset.status);
        const raw = (field.value || '').trim();
        field.classList.remove('is-valid', 'is-invalid');
        if (status) {
            status.textContent = '';
            status.className = 'form-text schema-status';
        }
        if (!raw) {
            if (status) {
                status.textContent = 'Optional. Leer lassen, wenn das automatische BlogPosting-Schema verwendet werden soll.';
            }
            return true;
        }
        try {
            const parsed = JSON.parse(raw);
            if (parsed === null || typeof parsed !== 'object') {
                throw new Error('JSON-Objekt oder Array erwartet.');
            }
            const hasType = (node) => node && typeof node === 'object' && ('@type' in node || '@graph' in node || '@context' in node);
            const valid = Array.isArray(parsed) ? parsed.every(hasType) : hasType(parsed);
            if (!valid) {
                throw new Error('JSON-LD benötigt @type, @graph oder @context.');
            }
            field.classList.add('is-valid');
            if (status) {
                status.textContent = 'JSON-LD ist gültig.';
                status.classList.add('text-success');
            }
            return true;
        } catch (error) {
            field.classList.add('is-invalid');
            if (status) {
                status.textContent = error.message || 'Ungültiges JSON.';
                status.classList.add('text-danger');
            }
            return false;
        }
    };

    const boot = () => {
        initEditor('textarea.js-wysiwyg');

        document.querySelectorAll('[data-counter]').forEach((field) => {
            updateCounter(field);
            field.addEventListener('input', () => updateCounter(field));
        });

        document.querySelectorAll('textarea.js-schema-json').forEach((field) => {
            validateJson(field);
            field.addEventListener('input', () => validateJson(field));
            field.addEventListener('blur', () => validateJson(field));
        });

        document.querySelectorAll('form').forEach((form) => {
            form.addEventListener('submit', (event) => {
                if (window.tinymce) {
                    window.tinymce.triggerSave();
                }
                const schema = form.querySelector('textarea.js-schema-json');
                if (schema && !validateJson(schema)) {
                    event.preventDefault();
                    schema.focus();
                }
            });
        });
    };

    document.addEventListener('DOMContentLoaded', boot);

    window.addEventListener('admin-theme-change', () => {
        if (document.querySelector('textarea.js-wysiwyg')) {
            initEditor('textarea.js-wysiwyg');
        }
    });
})();
