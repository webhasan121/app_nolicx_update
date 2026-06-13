import { useEffect } from "react";
import useTranslation from "../hooks/useTranslation";

const SKIP_TEXT_SELECTOR = [
    "script",
    "style",
    "noscript",
    "code",
    "pre",
    "textarea",
    "input",
    "select",
    "option",
    "[contenteditable='true']",
    "[data-no-translate]",
    "trix-editor",
].join(",");

const SKIP_ATTRIBUTE_SELECTOR = [
    "script",
    "style",
    "noscript",
    "code",
    "pre",
    "[contenteditable='true']",
    "[data-no-translate]",
    "trix-editor",
].join(",");

const ATTRIBUTES = ["placeholder", "title", "aria-label"];

function translateText(value, messages) {
    const original = String(value ?? "");
    const key = original.trim();

    if (!key || !messages[key]) {
        return original;
    }

    return original.replace(key, messages[key]);
}

function shouldSkipTextNode(node) {
    const element = node.nodeType === Node.ELEMENT_NODE ? node : node.parentElement;

    return !element || Boolean(element.closest(SKIP_TEXT_SELECTOR));
}

function shouldSkipAttributeNode(node) {
    const element = node.nodeType === Node.ELEMENT_NODE ? node : node.parentElement;

    return !element || Boolean(element.closest(SKIP_ATTRIBUTE_SELECTOR));
}

function translateElement(root, messages) {
    if (!root || !messages || Object.keys(messages).length === 0) {
        return;
    }

    const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, {
        acceptNode(node) {
            if (shouldSkipTextNode(node) || !node.nodeValue?.trim()) {
                return NodeFilter.FILTER_REJECT;
            }

            return NodeFilter.FILTER_ACCEPT;
        },
    });

    const textNodes = [];
    let node = walker.nextNode();

    while (node) {
        textNodes.push(node);
        node = walker.nextNode();
    }

    textNodes.forEach((textNode) => {
        textNode.__i18nOriginal ??= textNode.nodeValue;
        textNode.nodeValue = translateText(textNode.__i18nOriginal, messages);
    });

    const elements = root.nodeType === Node.ELEMENT_NODE
        ? [root, ...root.querySelectorAll("*")]
        : [...root.querySelectorAll("*")];

    elements.forEach((element) => {
        if (shouldSkipAttributeNode(element)) {
            return;
        }

        ATTRIBUTES.forEach((attribute) => {
            const value = element.getAttribute(attribute);

            if (!value?.trim()) {
                return;
            }

            const originalAttribute = `data-i18n-original-${attribute}`;
            const original = element.getAttribute(originalAttribute) ?? value;

            element.setAttribute(originalAttribute, original);
            element.setAttribute(attribute, translateText(original, messages));
        });
    });
}

export default function AutoTranslate() {
    const { current, messages } = useTranslation();

    useEffect(() => {
        if (typeof document === "undefined") {
            return undefined;
        }

        const root = document.body;
        let frame = null;

        const run = () => {
            if (frame) {
                cancelAnimationFrame(frame);
            }

            frame = requestAnimationFrame(() => {
                translateElement(root, messages);
            });
        };

        run();

        const observer = new MutationObserver((mutations) => {
            if (mutations.some((mutation) => mutation.type === "childList")) {
                run();
            }
        });

        observer.observe(root, {
            childList: true,
            subtree: true,
        });

        return () => {
            if (frame) {
                cancelAnimationFrame(frame);
            }

            observer.disconnect();
        };
    }, [current, messages]);

    return null;
}
