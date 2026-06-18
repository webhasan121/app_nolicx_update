import { useEffect } from "react";
import { usePage } from "@inertiajs/react";

const SKIP_TAGS = new Set(["SCRIPT", "STYLE", "TEXTAREA", "INPUT", "SELECT", "OPTION"]);

function replaceCurrencyText(root, symbol) {
    if (!root || !symbol || symbol === "TK") {
        return;
    }

    const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, {
        acceptNode(node) {
            const parent = node.parentElement;

            if (!parent || SKIP_TAGS.has(parent.tagName) || parent.closest("[data-no-currency-sync]")) {
                return NodeFilter.FILTER_REJECT;
            }

            return /\bT[Kk]\b/.test(node.nodeValue)
                ? NodeFilter.FILTER_ACCEPT
                : NodeFilter.FILTER_REJECT;
        },
    });

    const nodes = [];
    while (walker.nextNode()) {
        nodes.push(walker.currentNode);
    }

    nodes.forEach((node) => {
        node.nodeValue = node.nodeValue.replace(/\bT[Kk]\b/g, symbol);
    });
}

export default function CurrencyTextSync() {
    const { appConfig = {} } = usePage().props;
    const symbol = appConfig?.currency?.symbol ?? "TK";

    useEffect(() => {
        replaceCurrencyText(document.body, symbol);

        const observer = new MutationObserver(() => {
            replaceCurrencyText(document.body, symbol);
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
            characterData: true,
        });

        return () => observer.disconnect();
    }, [symbol]);

    return null;
}
