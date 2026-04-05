import { Head } from "@inertiajs/react";
import { useEffect } from "react";

const STYLE_TEXT = `
    @page {
        size: A4 landscape;
        margin: 20mm;
    }

    td,
    th {
        white-space: nowrap;
    }

    tr:hover {
        background-color: #f3f4f6;
    }

    tr:nth-child(even) {
        background-color: #e2e2e2;
    }
`;

function loadScript(src) {
    return new Promise((resolve, reject) => {
        const existing = document.querySelector(`script[src="${src}"]`);

        if (existing) {
            if (existing.dataset.loaded === "true") {
                resolve();
                return;
            }

            existing.addEventListener("load", resolve, { once: true });
            existing.addEventListener("error", reject, { once: true });
            return;
        }

        const script = document.createElement("script");
        script.src = src;
        script.async = true;
        script.addEventListener(
            "load",
            () => {
                script.dataset.loaded = "true";
                resolve();
            },
            { once: true }
        );
        script.addEventListener("error", reject, { once: true });
        document.body.appendChild(script);
    });
}

function doPdfFromCanvas(canvas, jsPDF) {
    try {
        const imgData = canvas.toDataURL("image/png");
        const pdf = new jsPDF({
            orientation: "p",
            unit: "mm",
            format: "a4",
        });

        const pageWidth = pdf.internal.pageSize.getWidth();
        const imgWidth = pageWidth;
        const imgHeight = (canvas.height * imgWidth) / canvas.width;

        pdf.addImage(imgData, "PNG", 0, 0, imgWidth, imgHeight);

        const blob = pdf.output("blob");
        const url = URL.createObjectURL(blob);
        window.open(url, "_blank");
        window.close();
    } catch (error) {
        console.error("doPdfFromCanvas failed:", error);
        alert(`Failed to create PDF from canvas: ${error.message}`);
    }
}

async function generatePdf() {
    try {
        if (typeof window.html2canvas === "undefined") {
            alert("html2canvas not loaded. Make sure the CDN script tag is present.");
            return;
        }

        if (typeof window.jspdf === "undefined") {
            alert("jsPDF not loaded. Make sure the CDN script tag is present.");
            return;
        }

        const { jsPDF } = window.jspdf || {};

        if (typeof jsPDF !== "function") {
            alert("jsPDF not available. Check console for window.jspdf object.");
            return;
        }

        const element = document.querySelector("#pdf-content");

        if (!element) {
            alert('No #pdf-content element found. Please add id="pdf-content" to the container you want to export.');
            return;
        }

        const canvas = await window.html2canvas(element, {
            scale: 2,
            useCORS: true,
        });

        if (!canvas) {
            throw new Error("html2canvas returned null/undefined.");
        }

        if (typeof canvas.toDataURL !== "function") {
            if (canvas?.canvas && typeof canvas.canvas.toDataURL === "function") {
                doPdfFromCanvas(canvas.canvas, jsPDF);
                return;
            }

            throw new Error("The object returned by html2canvas does not support toDataURL.");
        }

        doPdfFromCanvas(canvas, jsPDF);
    } catch (error) {
        console.error("PDF generation failed:", error);
        alert(`PDF generation error - see console for details: ${error?.message ?? error}`);
    }
}

export default function Print({ title = "nolicx", children }) {
    useEffect(() => {
        let mounted = true;

        Promise.all([
            loadScript("https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"),
            loadScript("https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"),
        ]).catch((error) => {
            if (mounted) {
                console.error("Failed to load print scripts:", error);
            }
        });

        const handler = () => {
            generatePdf();
        };

        window.makepdf = generatePdf;
        window.addEventListener("open-printable", handler);

        return () => {
            mounted = false;
            window.removeEventListener("open-printable", handler);
            delete window.makepdf;
        };
    }, []);

    return (
        <>
            <Head title={title}>
                <meta charSet="utf-8" />
                <meta name="viewport" content="width=device-width, initial-scale=1" />
                <style>{STYLE_TEXT}</style>
            </Head>

            <div>{children}</div>
        </>
    );
}
