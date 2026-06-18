import { Link, usePage } from "@inertiajs/react";
import { useEffect, useState } from "react";
import useTranslation from "../../hooks/useTranslation";

export default function CatLoop({
    item,
    active = false,
    cat = "",
    style = "",
    depth = 0,
    variant = "default",
}) {
    if (!item || item.slug === "default-category") return null;

    const hasActiveDescendant = (category) =>
        (category.children ?? []).some(
            (child) => child.slug === cat || hasActiveDescendant(child)
        );

    const isActive = active || cat === item.slug;
    const hasActiveChild = hasActiveDescendant(item);
    const [open, setOpen] = useState(isActive || hasActiveChild);
    const { t } = useTranslation();

    const hasChildren = item.children && item.children.length > 0;
    const rawLabel = item.name ? item.name.charAt(0).toUpperCase() + item.name.slice(1) : "";
    const label = t(rawLabel);
    const shouldHighlight = isActive || hasActiveChild;

    const isSidebar = variant === "sidebar";
    const selectedCountry = usePage().props?.selectedCountry;
    const categoryHref = route("category.products", {
        cat: item.slug,
        country: selectedCountry || undefined,
    });

    const rowClass = isSidebar
        ? depth === 0
            ? isActive
                ? "bg-slate-100 border border-slate-200 text-slate-900"
                : hasActiveChild
                    ? "bg-slate-100 border border-slate-200 text-slate-900"
                    : "bg-white border border-transparent text-slate-900"
            : "bg-transparent border-transparent text-slate-900"
        : isActive
            ? "bg-orange-50 border-orange-200 text-orange-700"
            : hasActiveChild
                ? "bg-slate-100 border-slate-200 text-slate-900"
                : "bg-white border-transparent text-slate-800 hover:bg-slate-50";

    const itemPadding = isSidebar
        ? depth === 0
            ? "px-3.5 py-1"
            : "px-0 py-0"
        : depth === 0
            ? "px-3.5 py-1"
            : "px-2.5 py-1";
    const textClass = isSidebar
        ? depth === 0
            ? "text-[14px] font-semibold"
            : "text-[13px] font-medium"
        : depth === 0
            ? "text-[14px] font-semibold"
            : "text-sm font-medium";
    const wrapperClass = isSidebar
        ? depth === 0
            ? "mb-1"
            : "mt-1"
        : depth === 0
            ? "mb-1"
            : "mt-1";
    const childTreeClass = isSidebar
        ? "pt-0.5 pl-2 ml-3 border-l border-slate-200"
        : "pt-0 pl-2 ml-3 border-l border-slate-200";
    const linkClass = isSidebar
        ? `flex-1 no-underline hover:no-underline ${textClass} ${style}`
        : `flex-1 no-underline hover:no-underline ${textClass} ${style}`;
    const rowBaseClass = isSidebar
        ? `flex items-center gap-2 rounded-2xl transition ${rowClass} ${itemPadding}`
        : `flex items-center gap-2 rounded-xl border transition ${rowClass} ${itemPadding}`;
    const buttonClass = isSidebar
        ? `${hasChildren ? "inline-flex" : "hidden"} items-center justify-center w-6 h-6 ${
              open ? "text-slate-500" : "text-slate-500"
          } rounded-full hover:bg-slate-100`
        : `${hasChildren ? "inline-flex" : "hidden"} items-center justify-center w-6 h-6 text-slate-500 rounded-full hover:bg-white/70`;
    const sidebarChildLinkClass = isActive
        ? `flex-1 rounded-2xl border border-orange-300 bg-orange-50 px-3.5 py-1.5 text-[13px] font-medium text-orange-600 no-underline hover:no-underline ${style}`
        : `flex-1 px-3.5 py-1.5 text-[13px] font-medium text-slate-900 no-underline hover:no-underline ${style}`;
    const sidebarChildRowClass =
        depth > 0
            ? "flex items-center gap-2 rounded-none border-0 bg-transparent py-0"
            : rowBaseClass;

    useEffect(() => {
        if (shouldHighlight) {
            setOpen(true);
        }
    }, [shouldHighlight]);

    return (
        <div className={wrapperClass}>
            <div className={isSidebar ? sidebarChildRowClass : rowBaseClass}>
                <Link
                    className={isSidebar && depth > 0 ? sidebarChildLinkClass : linkClass}
                    href={categoryHref}
                >
                    {label}
                </Link>
                <button
                    type="button"
                    className={buttonClass}
                    onClick={() => setOpen((v) => !v)}
                >
                    <i
                        className={`fas ${
                            open ? "fa-chevron-down" : "fa-chevron-right"
                        } text-xs`}
                    ></i>
                </button>
            </div>

            {hasChildren && open && (
                <div className={childTreeClass}>
                    {item.children.map((child) => (
                        <CatLoop
                            key={child.id}
                            item={child}
                            active={cat === child.slug}
                            cat={cat}
                            depth={depth + 1}
                            variant={variant}
                        />
                    ))}
                </div>
            )}
        </div>
    );
}
