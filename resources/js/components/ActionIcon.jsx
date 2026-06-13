import { Link } from "@inertiajs/react";

const ICONS = {
    edit: "fa-pen-to-square",
    delete: "fa-trash",
    details: "fa-eye",
    view: "fa-eye",
    print: "fa-print",
    remove: "fa-trash",
    cancel: "fa-ban",
    restore: "fa-rotate-left",
    trash: "fa-trash",
    confirm: "fa-check",
    reject: "fa-times",
};

const COLORS = {
    edit: "bg-violet-600 text-white hover:bg-violet-700",
    delete: "bg-red-600 text-white hover:bg-red-700",
    details: "bg-sky-600 text-white hover:bg-sky-700",
    view: "bg-blue-600 text-white hover:bg-blue-700",
    print: "bg-orange-500 text-white hover:bg-orange-600",
    remove: "bg-red-600 text-white hover:bg-red-700",
    cancel: "bg-red-600 text-white hover:bg-red-700",
    restore: "bg-emerald-600 text-white hover:bg-emerald-700",
    trash: "bg-red-600 text-white hover:bg-red-700",
    confirm: "bg-green-600 text-white hover:bg-green-700",
    reject: "bg-red-600 text-white hover:bg-red-700",
};

function classNames(action, className = "") {
    return `m-0.5 inline-flex h-7 w-7 items-center justify-center rounded-lg text-xs font-medium shadow-sm transition focus:outline-none focus:ring-2 focus:ring-orange-300 ${COLORS[action] ?? COLORS.view} ${className}`;
}

function iconClass(action) {
    return `fas ${ICONS[action] ?? ICONS.view}`;
}

export function ActionIconLink({
    href,
    action = "view",
    title,
    className = "",
    ...props
}) {
    const label = title ?? action;

    return (
        <Link
            href={href}
            className={classNames(action, className)}
            title={label}
            aria-label={label}
            {...props}
        >
            <i className={iconClass(action)}></i>
        </Link>
    );
}

export function ActionIconButton({
    type = "button",
    action = "view",
    title,
    className = "",
    ...props
}) {
    const label = title ?? action;

    return (
        <button
            type={type}
            className={classNames(action, className)}
            title={label}
            aria-label={label}
            {...props}
        >
            <i className={iconClass(action)}></i>
        </button>
    );
}
